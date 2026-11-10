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
// [multimod] Tableau de bord du joueur : crédits, objets, objets équipés, derniers achats.

namespace App\Http\Controllers\UserPanel;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\StoreUser;
use DB;
use App\Support\Utf8;

class DashboardController extends Controller
{
    public function getIndex(Request $request)
    {
        $user = StoreUser::findOrFail($request->session()->get('store_user_id'));
        $st = DB::connection('store');

        $itemCount = $user->userItems()->count();
        $equippedCount = $st->table('users_items_loadouts')
            ->join('users_items', 'users_items.id', '=', 'users_items_loadouts.useritem_id')
            ->where('users_items.user_id', $user->id)->count();
        $latest = $st->table('users_items')->join('items', 'items.id', '=', 'users_items.item_id')
            ->where('users_items.user_id', $user->id)
            ->orderBy('users_items.acquire_date', 'desc')->orderBy('users_items.id', 'desc')->take(10)
            ->get(['items.display_name', 'items.type', 'users_items.acquire_date', 'users_items.acquire_method']);
        $latest = Utf8::cleanRows($latest); // [multimod]
        $rank = $st->table('users')->where('credits', '>', $user->credits)->count() + 1;

        return view('templates.' . \Config::get('userpanel.template') . 'userpanel.dashboard',
            compact('user', 'itemCount', 'equippedCount', 'latest', 'rank'));
    }
}
