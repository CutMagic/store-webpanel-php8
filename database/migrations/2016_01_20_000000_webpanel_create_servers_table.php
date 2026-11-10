<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * [multimod] Liste des serveurs de jeu, rangée dans la base du PANNEAU (webpanel_servers).
 *
 * Le Store 1.2 n'a pas de table "servers" : il ne connaît que des numéros de serveur
 * (store_servers_items.server_id / store_servers_categories.server_id = server_id de la config du Store).
 * L'"id" de cette table doit donc être le MÊME numéro que dans la config du Store de chaque serveur.
 */
class WebpanelCreateServersTable extends Migration
{
    public function up()
    {
        Schema::connection('webpanel')->create('servers', function (Blueprint $table) {
            $table->engine = "InnoDB";
            $table->integer('id')->unsigned()->primary();
            $table->string('name', 64);
            $table->string('display_name', 64)->nullable();
            $table->string('ip', 64)->nullable();
            $table->integer('port')->unsigned()->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::connection('webpanel')->drop('servers');
    }
}
