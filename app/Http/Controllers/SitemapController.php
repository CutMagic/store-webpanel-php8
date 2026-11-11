<?php
// This file is part of the Store Webpanel V2 (GNU Affero General Public License v3, see LICENSE).
//
// [multimod] Pour les moteurs de recherche :
//  - /robots.txt : interdit l'admin, l'espace joueur, l'installateur et les pages de connexion ;
//    donne l'adresse du plan du site quand la page Joueurs publique est activée ;
//  - /sitemap.xml : accueil, page Joueurs et profil de chaque joueur (404 si la page publique est désactivée).
// (le fichier public/robots.txt fixe a été retiré pour que /robots.txt passe par ici)

namespace App\Http\Controllers;

use App\Models\StoreUser;
use App\Support\PanelSettings;

class SitemapController extends Controller
{
    const MAX_PROFILES = 49000; // limite de Google : 50 000 adresses par plan du site

    public function robots()
    {
        $lines = ['User-agent: *'];
        foreach (['/webpanel', '/userpanel', '/installer', '/lang/', '/login', '/logout', '/auth/', '/password/', '/home', '/players?q='] as $path) {
            $lines[] = 'Disallow: ' . $path;
        }
        if (PanelSettings::publicPlayers()) {
            $lines[] = '';
            $lines[] = 'Sitemap: ' . route('sitemap');
        }
        return response(implode("\n", $lines) . "\n", 200)
            ->header('Content-Type', 'text/plain; charset=UTF-8')
            ->header('Cache-Control', 'public, max-age=3600');
    }

    public function sitemap()
    {
        if (!PanelSettings::publicPlayers()) abort(404);

        $query = StoreUser::query();
        if (config('userpanel.player_profiles_credits')) {
            $query->orderBy('credits', 'desc'); // les plus riches d'abord (comme le classement)
        }
        $ids = $query->orderBy('id')->limit(self::MAX_PROFILES)->pluck('id');

        $esc = function ($url) { return htmlspecialchars($url, ENT_XML1 | ENT_QUOTES, 'UTF-8'); };
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
             . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n"
             . '<url><loc>' . $esc(url('/')) . '</loc></url>' . "\n"
             . '<url><loc>' . $esc(route('players.index')) . '</loc><changefreq>daily</changefreq></url>' . "\n";
        foreach ($ids as $id) {
            $xml .= '<url><loc>' . $esc(route('players.show', ['id' => $id])) . '</loc></url>' . "\n";
        }
        $xml .= '</urlset>' . "\n";

        return response($xml, 200)
            ->header('Content-Type', 'application/xml; charset=UTF-8')
            ->header('Cache-Control', 'public, max-age=3600');
    }
}
