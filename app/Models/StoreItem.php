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
// [multimod] Adapté au schéma du Store 1.2 (store_items, sans dates ni clés étrangères).

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreItem extends Model
{
    use RestrictedToServers, CleansUtf8;

    protected $connection = 'store';
    protected $table = 'items';
    public $timestamps = false;
    protected $fillable = ['priority','name','display_name','description','web_description','type','loadout_slot','price','category_id','attrs','is_buyable','is_tradeable','is_refundable','expiry_time','flags','enable_server_restriction'];

    protected $serverPivotTable = 'servers_items';
    protected $serverPivotKey = 'item_id';

    protected static function boot()
    {
        parent::boot();

        // Supprime aussi les exemplaires possédés par les joueurs (et leurs équipements).
        static::deleting(function (StoreItem $item) {
            foreach ($item->userItems()->get() as $userItem) {
                $userItem->delete();
            }
            $item->deleteServerRestrictions();
        });
    }

    /** Exemplaires de cet objet possédés par les joueurs (une ligne store_users_items par exemplaire). */
    public function userItems()
    {
        return $this->hasMany('App\Models\StoreUserItem', 'item_id', 'id');
    }

    public function users()
    {
        return $this->belongsToMany('App\Models\StoreUser', 'users_items', 'item_id', 'user_id')
            ->withPivot('id', 'acquire_date', 'acquire_method');
    }

    public function category()
    {
        return $this->belongsTo('App\Models\StoreCategory', 'category_id', 'id');
    }
}
