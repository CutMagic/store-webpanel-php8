<?php

namespace App\Support;

use Illuminate\Http\Request;

/**
 * [php82] Connexion "Se connecter avec Steam" (OpenID 2.0), sans clé API.
 * Remplace ehesp/steam-login (incompatible PHP 8 / Laravel 12).
 *
 * La réponse de Steam est revérifiée directement auprès de steamcommunity.com
 * (openid.mode=check_authentication) : un joueur ne peut pas se faire passer pour un autre
 * en modifiant l'adresse de retour.
 */
class SteamOpenId
{
    const ENDPOINT = 'https://steamcommunity.com/openid/login';

    /** Adresse de la page de connexion Steam ; Steam renverra le joueur vers $returnTo. */
    public static function url($returnTo)
    {
        $parts = parse_url($returnTo);
        $realm = $parts['scheme'] . '://' . $parts['host'] . (isset($parts['port']) ? ':' . $parts['port'] : '');

        return self::ENDPOINT . '?' . http_build_query([
            'openid.ns' => 'http://specs.openid.net/auth/2.0',
            'openid.mode' => 'checkid_setup',
            'openid.return_to' => $returnTo,
            'openid.realm' => $realm,
            'openid.identity' => 'http://specs.openid.net/auth/2.0/identifier_select',
            'openid.claimed_id' => 'http://specs.openid.net/auth/2.0/identifier_select',
        ]);
    }

    /**
     * Vérifie le retour de Steam et renvoie le SteamID64 du joueur.
     * @throws \RuntimeException si la connexion n'est pas valide
     */
    public static function validate(Request $request)
    {
        // PHP remplace les "." des paramètres par des "_" : on relit la requête brute.
        $params = [];
        foreach (explode('&', (string) $request->server('QUERY_STRING')) as $pair) {
            if ($pair === '' || strpos($pair, '=') === false) continue;
            [$k, $v] = explode('=', $pair, 2);
            $k = urldecode($k);
            if (strpos($k, 'openid.') === 0) $params[$k] = urldecode($v);
        }

        if (($params['openid.mode'] ?? '') !== 'id_res') {
            throw new \RuntimeException('Connexion Steam annulée.');
        }
        if (($params['openid.op_endpoint'] ?? '') !== self::ENDPOINT) {
            throw new \RuntimeException('Réponse qui ne vient pas de Steam.');
        }
        // L'adresse de retour signée doit être celle de ce site.
        $expected = url('/userpanel/auth/steamlogin');
        if (strpos($params['openid.return_to'] ?? '', $expected) !== 0) {
            throw new \RuntimeException('Adresse de retour invalide.');
        }
        if (!preg_match('#^https?://steamcommunity\.com/openid/id/(7656\d{13})/?$#', $params['openid.claimed_id'] ?? '', $m)) {
            throw new \RuntimeException('Identifiant Steam invalide.');
        }
        if (($params['openid.identity'] ?? '') !== $params['openid.claimed_id']) {
            throw new \RuntimeException('Identifiant Steam incohérent.');
        }
        // Les champs qui comptent doivent faire partie de la signature de Steam
        $signed = explode(',', $params['openid.signed'] ?? '');
        foreach (['op_endpoint', 'claimed_id', 'identity', 'return_to', 'response_nonce', 'assoc_handle'] as $f) {
            if (!in_array($f, $signed, true)) {
                throw new \RuntimeException('Réponse Steam incomplète.');
            }
        }
        // Anti-rejeu : jeton récent (10 min) et utilisable une seule fois
        $nonce = $params['openid.response_nonce'] ?? '';
        if (!preg_match('/^(\d{4}-\d\d-\d\dT\d\d:\d\d:\d\dZ)/', $nonce, $t) || abs(time() - strtotime($t[1])) > 600) {
            throw new \RuntimeException('Connexion Steam expirée, recommence.');
        }
        if (!\Illuminate\Support\Facades\Cache::add('steam_nonce_' . sha1($nonce), 1, 3600)) {
            throw new \RuntimeException('Cette connexion Steam a déjà été utilisée.');
        }

        // Revérification auprès de Steam
        $check = $params;
        $check['openid.mode'] = 'check_authentication';
        $ctx = stream_context_create(['http' => [
            'method' => 'POST',
            'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
            'content' => http_build_query($check),
            'timeout' => 10,
        ]]);
        $answer = @file_get_contents(self::ENDPOINT, false, $ctx);
        if ($answer === false || !preg_match('/^is_valid\s*:\s*true\s*$/m', $answer)) {
            throw new \RuntimeException('Steam n\'a pas confirmé la connexion.');
        }

        return $m[1];
    }
}
