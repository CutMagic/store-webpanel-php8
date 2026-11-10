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
// [multimod] Adapté au Store 1.2 : les loadouts sont communs à tous les joueurs
// (store_loadouts : display_name, game, class, team). Chaque joueur y équipe ses propres
// exemplaires via store_users_items_loadouts.

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreLoadout extends Model
{
    use CleansUtf8; // [multimod] textes coupés par le plugin

    protected $connection = 'store';
    protected $table = 'loadouts';
    public $timestamps = false;
    protected $fillable = ['display_name', 'game', 'class', 'team'];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function (StoreLoadout $loadout) {
            $loadout->userItems()->detach();
        });
    }

    /** Exemplaires équipés dans ce loadout (tous joueurs confondus). */
    public function userItems()
    {
        return $this->belongsToMany('App\Models\StoreUserItem', 'users_items_loadouts', 'loadout_id', 'useritem_id');
    }
}
