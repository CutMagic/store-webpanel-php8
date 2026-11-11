<?php
// This file is part of the Store Webpanel V2 (GNU Affero General Public License v3, see LICENSE).
//
// [multimod] Langue du site (français / anglais) : choix du visiteur (bouton FR / EN, gardé en
// session et dans un cookie d'un an), sinon APP_LOCALE du .env.

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;

class SetLocale
{
    public const LOCALES = ['fr' => 'Français', 'en' => 'English'];

    public function handle($request, Closure $next)
    {
        $locale = $request->hasSession() ? $request->session()->get('locale') : null;
        if (!is_string($locale) || !isset(self::LOCALES[$locale])) {
            $locale = $request->cookie('locale');
        }
        if (!is_string($locale) || !isset(self::LOCALES[$locale])) {
            $locale = config('app.locale');
        }
        if (isset(self::LOCALES[$locale])) {
            App::setLocale($locale);
        }
        return $next($request);
    }
}
