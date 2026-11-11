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
// [multimod] Réécrit pour le Store 1.2 : les loadouts sont communs (store_loadouts) et chaque joueur
// y équipe ses exemplaires (store_users_items_loadouts). Même règle que le plugin (store-backend) :
// équiper un objet déséquipe d'abord, dans ce loadout, les objets du même loadout_slot.
// Les changements faits ici sont pris en compte en jeu à la prochaine apparition du joueur.

namespace App\Http\Controllers\UserPanel;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\StoreUser;
use App\Models\StoreItem;
use App\Models\StoreLoadout;
use DB;
use App\Support\Utf8;

class LoadoutController extends Controller
{
    private function currentUser(Request $request)
    {
        return StoreUser::findOrFail($request->session()->get('store_user_id'));
    }

    /** Liste des loadouts, avec ce que le joueur y a équipé et ce qu'il peut y équiper. */
    public function getIndex(Request $request)
    {
        $user = $this->currentUser($request);
        $st = DB::connection('store');
        $loadouts = StoreLoadout::orderBy('id')->get();

        // Objets possédés (un par objet) avec emplacement.
        // [multimod] sans GROUP BY sur des colonnes non agrégées (ONLY_FULL_GROUP_BY)
        $ownedIds = $st->table('users_items')->where('user_id', $user->id)->distinct()->pluck('item_id')->all();
        $owned = empty($ownedIds) ? [] : StoreItem::whereIn('id', $ownedIds)
            ->whereNotNull('loadout_slot')->where('loadout_slot', '<>', '')
            ->orderBy('loadout_slot')->orderBy('display_name')
            ->get(['id', 'display_name', 'type', 'loadout_slot'])->all();

        // Ce qui est équipé : [loadout_id][item_id] = true
        $rows = $st->table('users_items_loadouts')
            ->join('users_items', 'users_items.id', '=', 'users_items_loadouts.useritem_id')
            ->where('users_items.user_id', $user->id)
            ->get(['users_items_loadouts.loadout_id', 'users_items.item_id']);
        $equipped = [];
        foreach ($rows as $r) $equipped[$r->loadout_id][$r->item_id] = true;

        return view('templates.' . \Config::get('userpanel.template') . 'userpanel.loadouts.index', compact('loadouts', 'owned', 'equipped', 'user'));
    }

    public function postEquip(Request $request, $loadout, $item_id)
    {
        $user = $this->currentUser($request);
        $item = StoreItem::findOrFail($item_id);
        $st = DB::connection('store');

        $useritem = $st->table('users_items')->where('user_id', $user->id)->where('item_id', $item->id)->orderBy('id')->first();
        if ($useritem == null) {
            return redirect()->route('userpanel.loadouts.index')->withErrors([__("You don't own this item.")]);
        }

        $this->unequipSlot($user->id, $loadout->id, $item->loadout_slot);
        $st->table('users_items_loadouts')->insert(['loadout_id' => $loadout->id, 'useritem_id' => $useritem->id]);

        return redirect()->route('userpanel.loadouts.index')->with('message', __(':item equipped in ":loadout".', ['item' => $item->display_name, 'loadout' => $loadout->display_name]));
    }

    public function postUnequip(Request $request, $loadout, $item_id)
    {
        $user = $this->currentUser($request);
        $item = StoreItem::findOrFail($item_id);
        $st = DB::connection('store');

        $ids = $st->table('users_items')->where('user_id', $user->id)->where('item_id', $item->id)->pluck('id')->all();
        if (!empty($ids)) {
            $st->table('users_items_loadouts')->where('loadout_id', $loadout->id)->whereIn('useritem_id', $ids)->delete();
        }

        return redirect()->route('userpanel.loadouts.index')->with('message', __(':item removed from ":loadout".', ['item' => $item->display_name, 'loadout' => $loadout->display_name]));
    }

    /** Déséquipe, pour ce joueur et ce loadout, tous les objets d'un emplacement. */
    private function unequipSlot($userId, $loadoutId, $slot)
    {
        $st = DB::connection('store');
        $ids = $st->table('users_items')->join('items', 'items.id', '=', 'users_items.item_id')
            ->where('users_items.user_id', $userId)->where('items.loadout_slot', $slot)
            ->pluck('users_items.id')->all();
        if (!empty($ids)) {
            $st->table('users_items_loadouts')->where('loadout_id', $loadoutId)->whereIn('useritem_id', $ids)->delete();
        }
    }
}
