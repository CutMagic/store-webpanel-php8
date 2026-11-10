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
// [multimod] Les serveurs sont rangés dans la base du PANNEAU (webpanel_servers) :
// le Store 1.2 n'a pas de table servers. L'id = le numéro de serveur de la config du Store.

namespace App\Models;

use DB;
use Illuminate\Database\Eloquent\Model;

class StoreServer extends Model
{
    protected $connection = 'webpanel';
    protected $table = 'servers';
    public $incrementing = false;
    protected $fillable = ['id', 'name', 'display_name', 'ip', 'port'];

    protected static function boot()
    {
        parent::boot();

        // Retire les restrictions qui visent ce serveur dans la base du store.
        static::deleting(function (StoreServer $server) {
            DB::connection('store')->table('servers_items')->where('server_id', $server->id)->delete();
            DB::connection('store')->table('servers_categories')->where('server_id', $server->id)->delete();
        });
    }
}
