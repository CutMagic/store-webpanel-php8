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

use App\Http\Controllers\Controller;

class SteamConvertController extends Controller
{
    //TODO: Move that to its own library
    //TODO: Add Tests
    public static function steamid_to_auth($steamid)
    {
        if (substr_count($steamid, ":") != 2)
            return 0;
        //from https://forums.alliedmods.net/showpost.php?p=1890083&postcount=234
        $toks = explode(":", $steamid);
        $odd = (int) $toks[1];
        $halfAID = (int) $toks[2];
        return ($halfAID * 2) + $odd;
    }
    // [php82] calculs en entiers 64 bits : plus besoin de l'extension bcmath
    public static function steamid_to_community($steamid)
    {
        if (substr_count($steamid, ":") != 2)
            return 0;
        $parts = explode(':', str_replace('STEAM_', '', $steamid));
        return (string) (76561197960265728 + (int) $parts[1] + 2 * (int) $parts[2]);
    }

    public static function communityid_to_steam($i64friendID)
    {
        $acc = (int) $i64friendID - 76561197960265728;
        return "STEAM_0:" . ($acc % 2) . ":" . intdiv($acc, 2);
    }

    public static function auth_to_steamid($authid)
    {
        $steam = array();
        $steam[0] = "STEAM_0";
        if ($authid % 2 == 0)
        {
            $steam[1] = 0;
        }
        else
        {
            $steam[1] = 1;
            $authid -= 1;
        }
        $steam[2] = intdiv($authid, 2); // [php82]
        return $steam[0] . ":" . $steam[1] . ":" . $steam[2];
    }
}
