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
// [multimod] Adapté au schéma du Store 1.2 (store_users : id, auth, name, credits).

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreUser extends Model
{
    use CleansUtf8; // [multimod] textes coupés par le plugin

    protected $connection = 'store';
    protected $table = 'users';
    public $timestamps = false;
    protected $fillable = ['auth', 'name', 'credits'];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function (StoreUser $user) {
            foreach ($user->userItems()->get() as $userItem) {
                $userItem->delete();
            }
        });
    }

    /** Exemplaires possédés (une ligne par exemplaire). */
    public function userItems()
    {
        return $this->hasMany('App\Models\StoreUserItem', 'user_id', 'id');
    }

    public function items()
    {
        return $this->belongsToMany('App\Models\StoreItem', 'users_items', 'user_id', 'item_id')
            ->withPivot('id', 'acquire_date', 'acquire_method');
    }
}
