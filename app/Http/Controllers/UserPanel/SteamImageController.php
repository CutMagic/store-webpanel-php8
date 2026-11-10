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

namespace App\Http\Controllers\UserPanel;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Storage;
use App\Http\Controllers\UserPanel\SteamConvertController;

use Illuminate\Support\Facades\Response;

class SteamImageController extends Controller
{
    /**
     * Returns the User Image for a specific auth ID
     *
     * Searches the Cache if the image exists already
     * And if it doesnt it fetches it from steam and adds it to the cache
     *
     * @param $auth
     * @return Image Returns a Image of the Steam User
     */
    public static function getUserImageForAuth($auth)
    {
        // [multimod] page publique : seulement des numéros de joueurs connus du store
        // (sinon n'importe qui ferait télécharger et stocker des images pour n'importe quel numéro)
        if (!preg_match('/^\d{1,10}$/', (string) $auth) || !\App\Models\StoreUser::where('auth', (int) $auth)->exists()) {
            return self::imageResponse(file_get_contents(public_path('templates/adminlte205/dist/img/avatar5.png')));
        }
        $auth = (string) (int) $auth;
        //Check if the image exists in the cache
        if (Storage::disk('local')->exists('pics/steam/'.$auth.'.jpg'))
        {
            $image = Storage::get('pics/steam/'.$auth.'.jpg');
            return self::imageResponse($image); // [php82] plus d'Intervention Image
        }
        else
        {
            $image = SteamImageController::get_image_from_steam($auth);
            Storage::put('pics/steam/'.$auth.'.jpg', $image);

            //Return the Image
            return self::imageResponse($image); // [php82] plus d'Intervention Image
        }
    }


    public static function getUserImageForCurrent(Request $request)
    {
        //Check if auth is set; If not revert back to session auth; If thats not set throw 404
        $auth = $request->session()->get('store_user_auth','not-set');

        //Check if session auth is set
        if($auth == 'not-set') abort(404);

        return SteamImageController::getUserImageForAuth($auth);
}

    public static function refreshUserImage($auth, Request $request)
    {
        //Check if auth is set; If not revert back to session auth; If thats not set throw 404
        $auth = $request->session()->get('store_user_auth','not-set');

        //Check if session auth is set
        if($auth == 'not-set') abort(404);

        //Get the steam Image and store it
        $image = SteamImageController::get_image_from_steam($auth);
        Storage::put('pics/steam/'.$auth.'.jpg', $image);

        return redirect()->route('userpanel.dashboard');
    }

    private static function get_image_from_steam($auth)
    {
        // [multimod] si Steam ne répond pas (ou joueur no-steam), on renvoie l'avatar par défaut au lieu de planter
        try {
            return self::download_image_from_steam($auth);
        } catch (\Exception $e) {
            return file_get_contents(public_path('templates/adminlte205/dist/img/avatar5.png'));
        }
    }

    private static function download_image_from_steam($auth)
    {
        //If not calculate the steamid64
        $steam64 = SteamConvertController::steamid_to_community(SteamConvertController::auth_to_steamid($auth));
        //Get the image from steam
        $url = "http://steamcommunity.com/profiles/" . $steam64 . "?xml=1";
        $ctx = stream_context_create(['http' => ['timeout' => 5]]);
        $xml = simplexml_load_string(file_get_contents($url, false, $ctx));
        $avatar_url = (string) $xml->avatarMedium;
        // [multimod] l'image doit venir des serveurs d'avatars de Steam
        if (!preg_match('#^https://(avatars\.(steamstatic|akamai\.steamstatic|cloudflare\.steamstatic|fastly\.steamstatic)\.com|steamcdn-a\.akamaihd\.net)/#', $avatar_url)) throw new \Exception('adresse d avatar inattendue');
        if ($avatar_url == '') throw new \Exception('avatar Steam introuvable');

        //Store it in the cache
        return file_get_contents($avatar_url, false, $ctx);
    }

    /** [php82] renvoie l'image telle quelle, avec le bon type (jpg ou png par défaut). */
    private static function imageResponse($bytes)
    {
        $type = (substr($bytes, 0, 8) === "\x89PNG\r\n\x1a\n") ? 'image/png' : 'image/jpeg';
        return response($bytes, 200)->header('Content-Type', $type)->header('Cache-Control', 'public, max-age=86400');
    }
}
