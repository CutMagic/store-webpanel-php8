<?php
// [multimod] Un exemplaire d'objet possédé par un joueur (table store_users_items du Store 1.2).
// Les objets équipés sont dans store_users_items_loadouts (useritem_id, loadout_id).

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreUserItem extends Model
{
    use CleansUtf8; // [multimod] textes coupés par le plugin

    protected $connection = 'store';
    protected $table = 'users_items';
    public $timestamps = false;
    protected $fillable = ['user_id', 'item_id', 'acquire_date', 'acquire_method'];

    protected static function boot()
    {
        parent::boot();

        // Un objet vendu ou supprimé ne doit plus apparaître comme équipé.
        static::deleting(function (StoreUserItem $userItem) {
            $userItem->loadouts()->detach();
        });
    }

    public function user()
    {
        return $this->belongsTo('App\Models\StoreUser', 'user_id', 'id');
    }

    public function item()
    {
        return $this->belongsTo('App\Models\StoreItem', 'item_id', 'id');
    }

    /** Loadouts dans lesquels cet exemplaire est équipé. */
    public function loadouts()
    {
        return $this->belongsToMany('App\Models\StoreLoadout', 'users_items_loadouts', 'useritem_id', 'loadout_id');
    }
}
