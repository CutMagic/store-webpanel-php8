<?php
//Copyright (c) 2015 "Werner Maisl"
//
//This file is part of the Store Webpanel V2.
//
//The Store Webpanel V2 is free software: you can redistribute it and/or modify
//it under the terms of the GNU Affero General Public License as
//published by the Free Software Foundation, either version 3 of the
//License, or (at your option) any later version.
//
//This program is distributed in the hope that it will be useful,
//but WITHOUT ANY WARRANTY; without even the implied warranty of
//MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//GNU Affero General Public License for more details.
//
//You should have received a copy of the GNU Affero General Public License
//along with this program. If not, see <http://www.gnu.org/licenses/>.
//
// [php82] Porté pour Laravel 12 (plus d'Entrust : User::hasPermission()).

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class Authorize
{
    /**
     * Vérifie que le compte admin a la permission demandée par la route (config/route_perms.php).
     */
    public function handle($request, Closure $next)
    {
        if (config('webpanel.ignore_permissions')) {
            return $next($request);
        }

        $routeName = $request->route()->getName();
        $routePermission = config('route_perms.' . $routeName);

        if ($routePermission == '' || $routePermission == null) {
            // [multimod] refus par défaut : une route admin sans permission déclarée n'est ouverte
            // qu'au tableau de bord (avant : ouverte à n'importe quel compte admin, même sans rôle)
            if ($routeName === 'webpanel.dashboard') {
                return $next($request);
            }
            abort(403, 'Aucune permission définie pour cette page');
        }

        if (!Auth::user() || !Auth::user()->hasPermission($routePermission)) {
            return redirect()->route('webpanel.dashboard')
                ->withErrors(['You do not have the permission ' . $routePermission . ' that is required to perform this action']);
        }

        return $next($request);
    }
}
