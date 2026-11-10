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
// [multimod] Adapté au schéma du Store 1.2 (store_categories, sans dates ni clés étrangères).

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreCategory extends Model
{
    use RestrictedToServers, CleansUtf8;

    protected $connection = 'store';
    protected $table = 'categories';
    public $timestamps = false;
    protected $fillable = ['priority', 'display_name', 'description', 'require_plugin', 'web_description', 'web_color', 'enable_server_restriction'];

    protected $serverPivotTable = 'servers_categories';
    protected $serverPivotKey = 'category_id';

    protected static function boot()
    {
        parent::boot();

        // Le Store 1.2 n'a pas de ON DELETE CASCADE : on supprime nous-mêmes les objets de la catégorie
        // (chaque objet supprime à son tour ses exemplaires possédés et équipés).
        static::deleting(function (StoreCategory $category) {
            foreach ($category->items()->get() as $item) {
                $item->delete();
            }
            $category->deleteServerRestrictions();
        });
    }

    public function items()
    {
        return $this->hasMany('App\Models\StoreItem', 'category_id', 'id');
    }
}
