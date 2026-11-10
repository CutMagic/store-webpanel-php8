<?php

namespace App\Models;

use DB;

/**
 * [multimod] Restriction par serveur, pour le schéma du Store 1.2.
 *
 * Les serveurs sont décrits dans la base du panneau (webpanel_servers), mais les restrictions
 * sont lues par le plugin dans la base du store (store_servers_items / store_servers_categories).
 * Une relation Eloquent ne peut pas joindre deux bases différentes : on passe donc par ces méthodes.
 *
 * La classe qui utilise ce trait définit $serverPivotTable (sans préfixe) et $serverPivotKey.
 */
trait RestrictedToServers
{
    /** Numéros des serveurs autorisés pour cet élément. */
    public function getServerIds()
    {
        return DB::connection('store')->table($this->serverPivotTable)
            ->where($this->serverPivotKey, $this->id)
            ->whereNotNull('server_id')
            ->pluck('server_id')->all();
    }

    /** Serveurs (base du panneau) autorisés pour cet élément. */
    public function getServers()
    {
        $ids = $this->getServerIds();
        return empty($ids) ? collect() : StoreServer::whereIn('id', $ids)->get();
    }

    /** Remplace la liste des serveurs autorisés. */
    public function syncServers($serverIds)
    {
        $serverIds = array_unique(array_map('intval', (array) $serverIds));
        DB::connection('store')->transaction(function () use ($serverIds) {
            DB::connection('store')->table($this->serverPivotTable)
                ->where($this->serverPivotKey, $this->id)->delete();
            foreach ($serverIds as $sid) {
                DB::connection('store')->table($this->serverPivotTable)
                    ->insert([$this->serverPivotKey => $this->id, 'server_id' => $sid]);
            }
        });
    }

    public function getServerListAttribute()
    {
        return $this->getServerIds();
    }

    protected function deleteServerRestrictions()
    {
        DB::connection('store')->table($this->serverPivotTable)
            ->where($this->serverPivotKey, $this->id)->delete();
    }
}
