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
// [multimod] Réécrit pour le Store 1.2 :
//  - un exemplaire = une ligne store_users_items (plusieurs exemplaires possibles du même objet) ;
//  - les tables du Store sont en MyISAM (pas de transactions) : chaque opération sur les crédits
//    est une seule requête atomique, comme dans le plugin (credits = credits - prix WHERE credits >= prix) ;
//  - les objets réservés à des flags admin (colonne flags) ne sont pas vendus sur le site,
//    car le site ne peut pas vérifier les droits SourceMod du joueur.

namespace App\Http\Controllers\UserPanel;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\StoreUser;
use App\Models\StoreItem;
use App\Models\StoreUserItem;
use Carbon\Carbon;
use DB;
use App\Support\Utf8;
use Log;

class UserItemsController extends Controller
{
    private function currentUser(Request $request)
    {
        return StoreUser::findOrFail($request->session()->get('store_user_id'));
    }

    /** Objet achetable sur le site ? */
    public static function webBuyable(StoreItem $item)
    {
        return $item->is_buyable == 1 && trim((string) $item->flags) == '' && $item->price >= 0;
    }

    /** Inventaire du joueur, regroupé par objet. */
    public function getIndex(Request $request)
    {
        $user = $this->currentUser($request);
        // [multimod] Requêtes sans colonne non agrégée : valables même avec ONLY_FULL_GROUP_BY (MySQL 8 / MariaDB).
        $counts = DB::connection('store')->table('users_items')->where('user_id', $user->id)
            ->groupBy('item_id')
            ->get(['item_id', DB::raw('COUNT(*) as quantity'), DB::raw('MAX(acquire_date) as last_acquired')]);
        $byItem = [];
        foreach ($counts as $c) $byItem[$c->item_id] = $c;
        $owned = [];
        if (!empty($byItem)) {
            foreach (StoreItem::with('category')->whereIn('id', array_keys($byItem))->get() as $it) {
                $owned[] = (object) [
                    'id' => $it->id, 'display_name' => $it->display_name, 'description' => $it->description,
                    'type' => $it->type, 'loadout_slot' => $it->loadout_slot, 'price' => $it->price,
                    'is_refundable' => $it->is_refundable,
                    'category' => $it->category ? $it->category->display_name : '',
                    'quantity' => $byItem[$it->id]->quantity, 'last_acquired' => $byItem[$it->id]->last_acquired,
                ];
            }
            usort($owned, function ($x, $y) { return strcasecmp($x->category . $x->display_name, $y->category . $y->display_name); });
        }
        $refundfee = (float) \Config::get('userpanel.items_refundfee');

        return view('templates.' . \Config::get('userpanel.template') . 'userpanel.useritems.index', compact('owned', 'refundfee', 'user'));
    }

    /** Objets en vente sur le site. */
    public function getBuyOverview(Request $request)
    {
        $user = $this->currentUser($request);
        $items = StoreItem::with('category')->where('is_buyable', 1)->where('price', '>=', 0) // [multimod] comme webBuyable()
            ->where(function ($q) { $q->whereNull('flags')->orWhere('flags', ''); })
            ->orderBy('category_id')->orderBy('priority', 'desc')->orderBy('display_name')->get();

        return view('templates.' . \Config::get('userpanel.template') . 'userpanel.useritems.buyoverview', compact('items', 'user'));
    }

    public function getBuy(Request $request, $item_id)
    {
        $user = $this->currentUser($request);
        $item = StoreItem::findOrFail($item_id);
        $owned = $user->userItems()->where('item_id', $item->id)->count();
        $buyable = self::webBuyable($item);

        return view('templates.' . \Config::get('userpanel.template') . 'userpanel.useritems.buyconfirm', compact('item', 'user', 'owned', 'buyable'));
    }

    /** Achat d'un exemplaire. */
    public function postBuy(Request $request, $item_id)
    {
        $user = $this->currentUser($request);
        $item = StoreItem::findOrFail($item_id);

        if (!self::webBuyable($item)) {
            return redirect()->route('userpanel.useritems.buyoverview')->withErrors([__('This item cannot be bought on the website.')]);
        }

        $st = DB::connection('store');
        // Retire les crédits seulement s'il en a assez (une seule requête : pas de double achat possible).
        $paid = $st->table('users')->where('id', $user->id)->where('credits', '>=', $item->price)
            ->decrement('credits', $item->price);
        if ($paid != 1) {
            return redirect()->route('userpanel.useritems.buyconf', ['item_id' => $item->id])->withErrors([__("You don't have enough credits.")]);
        }

        try {
            $st->table('users_items')->insert([
                'user_id' => $user->id,
                'item_id' => $item->id,
                'acquire_date' => Carbon::now()->toDateTimeString(),
                'acquire_method' => 'web',
            ]);
        } catch (\Exception $e) {
            // L'objet n'a pas pu être ajouté : on rend les crédits.
            $st->table('users')->where('id', $user->id)->increment('credits', $item->price);
            Log::error('[userpanel] achat échoué', ['user' => $user->id, 'item' => $item->id, 'err' => $e->getMessage()]);
            return redirect()->route('userpanel.useritems.buyoverview')->withErrors([__('The purchase failed, your credits were given back.')]);
        }

        Log::info('[userpanel] achat', ['user' => $user->id, 'auth' => $user->auth, 'item' => $item->name, 'price' => $item->price]);
        return redirect()->route('userpanel.useritems.index')->with('message', __('Purchase successful: :item', ['item' => $item->display_name]));
    }

    /**
     * Vend (ou jette, si l'objet n'est pas remboursable) un exemplaire, ou tous les exemplaires.
     * On retire d'abord les exemplaires NON équipés.
     */
    public function postSell(Request $request, $item_id)
    {
        $user = $this->currentUser($request);
        $item = StoreItem::findOrFail($item_id);
        $all = $request->input('all') == '1';

        $st = DB::connection('store');
        $p = $st->getTablePrefix();
        $ids = $st->table('users_items')->where('user_id', $user->id)->where('item_id', $item->id)
            ->orderBy(DB::raw('(SELECT COUNT(*) FROM ' . $p . 'users_items_loadouts WHERE ' . $p . 'users_items_loadouts.useritem_id = ' . $p . 'users_items.id)'))
            ->orderBy('id', 'desc')
            ->pluck('id')->all();
        if (empty($ids)) {
            return redirect()->route('userpanel.useritems.index')->withErrors([__("You don't own this item.")]);
        }
        if (!$all) $ids = array_slice($ids, 0, 1);

        $refundEach = $item->is_refundable == 1 ? (int) floor($item->price * (float) \Config::get('userpanel.items_refundfee')) : 0;
        $removed = 0;
        foreach ($ids as $id) {
            // Supprime l'exemplaire seulement s'il appartient toujours au joueur (une requête atomique).
            if ($st->table('users_items')->where('id', $id)->where('user_id', $user->id)->delete() == 1) {
                $st->table('users_items_loadouts')->where('useritem_id', $id)->delete();
                $removed++;
            }
        }
        if ($removed > 0 && $refundEach > 0) {
            $st->table('users')->where('id', $user->id)->increment('credits', $refundEach * $removed);
        }

        Log::info('[userpanel] vente', ['user' => $user->id, 'item' => $item->name, 'nb' => $removed, 'refund' => $refundEach * $removed]);
        $msg = $refundEach > 0
            ? __('Sold: :count × :item (+:credits credits)', ['count' => $removed, 'item' => $item->display_name, 'credits' => $refundEach * $removed])
            : __('Thrown away: :count × :item', ['count' => $removed, 'item' => $item->display_name]);
        return redirect()->route('userpanel.useritems.index')->with('message', $msg);
    }
}
