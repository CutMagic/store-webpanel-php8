<?php
// This file is part of the Store Webpanel V2 (GNU Affero General Public License v3, see LICENSE).
//
// [multimod] Réglages du site modifiables dans l'admin (menu "Réglages du site").
// Stockés dans storage/app/panel-settings.json : pas de table à créer, rien à migrer
// pour un panneau déjà installé. Sans fichier, les valeurs par défaut s'appliquent
// (page Joueurs publique DÉSACTIVÉE).

namespace App\Support;

class PanelSettings
{
    const DEFAULTS = [
        'public_players'   => false, // page Joueurs visible sans connexion Steam (et par Google)
        'site_name'        => '',    // nom du site (titre de l'accueil et des pages publiques)
        'site_description' => '',    // description affichée sous le titre et donnée aux moteurs de recherche
    ];

    private static $values = null;

    private static function path()
    {
        return storage_path('app/panel-settings.json');
    }

    public static function all()
    {
        if (self::$values === null) {
            $data = [];
            if (is_file(self::path())) {
                $json = json_decode((string) @file_get_contents(self::path()), true);
                if (is_array($json)) $data = array_intersect_key($json, self::DEFAULTS);
            }
            self::$values = array_merge(self::DEFAULTS, $data);
        }
        return self::$values;
    }

    public static function get($key)
    {
        return self::all()[$key] ?? null;
    }

    /** Enregistre les réglages donnés (les autres sont gardés). Écriture atomique (fichier temporaire + renommage). */
    public static function save(array $values)
    {
        $data = array_merge(self::all(), array_intersect_key($values, self::DEFAULTS));
        $tmp = self::path() . '.' . bin2hex(random_bytes(4)) . '.tmp';
        if (@file_put_contents($tmp, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), LOCK_EX) === false
            || !@rename($tmp, self::path())) {
            @unlink($tmp);
            throw new \RuntimeException('storage/app/panel-settings.json cannot be written');
        }
        self::$values = $data;
    }

    /** Page Joueurs publique : réglage admin activé ET page Joueurs autorisée dans le .env. */
    public static function publicPlayers()
    {
        return (bool) config('userpanel.player_profiles') && (bool) self::get('public_players');
    }

    public static function siteName()
    {
        $name = trim((string) self::get('site_name'));
        return $name !== '' ? $name : 'Store WebPanel';
    }

    public static function siteDescription()
    {
        return trim((string) self::get('site_description'));
    }
}
