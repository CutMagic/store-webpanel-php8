<?php
// This file is part of the Store Webpanel V2 (GNU Affero General Public License v3, see LICENSE).
//
// [multimod] Bouton FR / EN : enregistre la langue choisie puis revient sur la page d'avant
// (seulement une page de CE site : jamais de redirection vers un autre site).

namespace App\Http\Controllers;

use App\Http\Middleware\SetLocale;
use Illuminate\Http\Request;

class LocaleController extends Controller
{
    public function switch(Request $request, string $locale)
    {
        if (!isset(SetLocale::LOCALES[$locale])) abort(404);
        $request->session()->put('locale', $locale);

        $back = url('/');
        $prev = (string) $request->headers->get('referer', '');
        if ($prev !== '') {
            $p = parse_url($prev);
            $base = parse_url(url('/'));
            if (is_array($p) && isset($p['host']) && strcasecmp($p['host'], $request->getHost()) === 0
                && ($p['scheme'] ?? '') === $request->getScheme() && ($p['port'] ?? null) == ($base['port'] ?? null)
                && !str_contains($p['path'] ?? '/', '/lang/')) {
                $back = $prev;
            }
        }
        return redirect()->to($back)->withCookie(cookie('locale', $locale, 60 * 24 * 365));
    }
}
