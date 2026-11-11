<?php
// This file is part of the Store Webpanel V2 (GNU Affero General Public License v3, see LICENSE).
//
// [multimod] SteamID d'un joueur du store. Le plugin Store 1.2 range dans store_users.auth le
// numéro de compte Steam (GetSteamAccountID, "SteamID32") : on en tire tous les formats.

namespace App\Support;

class SteamIds
{
    /** SteamID64 du compte numéro 0. */
    const BASE64 = 76561197960265728;

    /**
     * Tous les formats pour un numéro de compte, ou null si le numéro n'est pas un vrai compte Steam
     * (0 ou négatif : joueur non-Steam, bot...).
     * @return array{account:int, steam2:string, steam3:string, steam64:string, url:string}|null
     */
    public static function fromAccountId($auth): ?array
    {
        if (!is_numeric($auth)) return null;
        $a = (int) $auth;
        if ($a <= 0) return null;
        $id64 = (string) (self::BASE64 + $a);
        return [
            'account' => $a,
            'steam2'  => 'STEAM_0:' . ($a % 2) . ':' . intdiv($a, 2),
            'steam3'  => '[U:1:' . $a . ']',
            'steam64' => $id64,
            'url'     => 'https://steamcommunity.com/profiles/' . $id64,
        ];
    }

    /**
     * Numéro de compte (store_users.auth) à partir d'un SteamID tapé par un joueur, sinon null.
     * Formats acceptés : STEAM_0:1:123 (ou STEAM_1), [U:1:247] ou U:1:247, SteamID64 (7656119...),
     * adresse steamcommunity.com/profiles/7656119..., numéro de compte seul (SteamID32).
     */
    public static function parseToAccountId(string $s): ?int
    {
        $s = trim($s);
        if (preg_match('/^STEAM_[0-5]:([01]):(\d{1,10})$/i', $s, $m)) {
            $a = (int) $m[2] * 2 + (int) $m[1];
        } elseif (preg_match('/^\[?U:1:(\d{1,10})\]?$/i', $s, $m)) {
            $a = (int) $m[1];
        } elseif (preg_match('#^(?:https?://)?(?:www\.)?(?:steamcommunity\.com/profiles/)?(7656119\d{10})/?$#i', $s, $m)) {
            $a = (int) $m[1] - self::BASE64;
        } elseif (preg_match('/^\d{1,10}$/', $s)) {
            $a = (int) $s;
        } else {
            return null;
        }
        return ($a > 0 && $a <= 2147483647) ? $a : null;
    }

    /** Vrai si le texte est un SteamID écrit en entier (pas un simple nombre qui pourrait être un pseudo). */
    public static function isExplicit(string $s): bool
    {
        return !preg_match('/^\d{1,10}$/', trim($s)) && self::parseToAccountId($s) !== null;
    }
}
