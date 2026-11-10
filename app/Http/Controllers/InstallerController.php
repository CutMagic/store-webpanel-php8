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
// [php82] Installateur porté pour Laravel 12 : clé "base64:", noms de réglages Laravel 12,
// valeurs entre guillemets dans le .env (un mot de passe avec # ou espace reste correct),
// "migrate --force" (obligatoire en production). Ne crée que les tables webpanel_* du panneau.

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class InstallerController extends Controller
{
    public function showWelcome()
    {
        return view('templates.installer.welcome');
    }

    public function postWelcome()
    {
        return redirect()->route('installer.settings.show');
    }

    public function showSettings()
    {
        return view('templates.installer.settings');
    }

    public function postSettings(Request $request)
    {
        [$hostPanel, $portPanel] = $this->hostPort($request->input('db_host_panel'));
        [$hostStore, $portStore] = $this->hostPort($request->input('db_host_store'));

        $settings = [
            'APP_NAME' => 'Store',
            'APP_ENV' => 'production',
            'APP_DEBUG' => 'false',
            'APP_KEY' => 'base64:' . base64_encode(random_bytes(32)),
            'APP_URL' => $request->getSchemeAndHttpHost() . $request->getBasePath(),
            'APP_LOCALE' => 'fr',

            'DB_CONNECTION' => 'webpanel',
            'DB_HOST_PANEL' => $hostPanel,
            'DB_PORT_PANEL' => $portPanel,
            'DB_DATABASE_PANEL' => $request->input('db_database_panel'),
            'DB_USERNAME_PANEL' => $request->input('db_username_panel'),
            'DB_PASSWORD_PANEL' => $request->input('db_password_panel'),
            'DB_PREFIX_PANEL' => $request->input('db_prefix_panel', 'webpanel_'),

            'DB_HOST_STORE' => $hostStore,
            'DB_PORT_STORE' => $portStore,
            'DB_DATABASE_STORE' => $request->input('db_database_store'),
            'DB_USERNAME_STORE' => $request->input('db_username_store'),
            'DB_PASSWORD_STORE' => $request->input('db_password_store'),
            'DB_PREFIX_STORE' => $request->input('db_prefix_store', 'store_'),

            'CACHE_STORE' => $request->input('cache_driver') ?: 'file',
            'SESSION_DRIVER' => $request->input('session_driver') ?: 'file',
            'QUEUE_CONNECTION' => $request->input('queue_driver') ?: 'sync',
            'LOG_CHANNEL' => 'daily',

            'MAIL_MAILER' => $request->input('mail_driver') ?: 'log',
            'MAIL_HOST' => $request->input('mail_host'),
            'MAIL_PORT' => $request->input('mail_port'),
            'MAIL_USERNAME' => $request->input('mail_username'),
            'MAIL_PASSWORD' => $request->input('mail_password'),
            'MAIL_FROM_ADDRESS' => $request->input('mail_from_adr'),
            'MAIL_FROM_NAME' => $request->input('mail_from_name') ?: 'Store',

            'UP_SERVERLOGIN_IGNORE_IPMISMATCH' => 'false',
            'UP_ITEMS_REFUNDFEE' => '0.8',
        ];

        $this->writeEnvFile($settings);

        return redirect()->route('installer.fill_db.show');
    }

    public function showFillDb()
    {
        return view('templates.installer.fill');
    }

    public function postFillDb()
    {
        Artisan::call('migrate', ['--force' => true]);
        $output = Artisan::output();

        return view('templates.installer.fillresult', compact('output'));
    }

    public function showFinish()
    {
        file_put_contents(base_path('INSTALLED'), 'INSTALLED');

        return view('templates.installer.finish');
    }

    /** "hote:port" ou "hote" -> [hote, port] */
    private function hostPort($value)
    {
        $value = trim((string) $value);
        if (preg_match('/^(.+):(\d+)$/', $value, $m)) {
            return [$m[1], $m[2]];
        }
        return [$value === '' ? '127.0.0.1' : $value, '3306'];
    }

    /** Écrit le .env ; chaque valeur est entre guillemets (\, " et $ échappés). */
    private function writeEnvFile(array $settings)
    {
        $content = '';
        foreach ($settings as $key => $value) {
            $value = str_replace(["\r", "\n", "\0"], '', (string) $value); // pas de nouvelle ligne injectée dans le .env
            $escaped = str_replace(['\\', '"', '$'], ['\\\\', '\\"', '\\$'], $value);
            $content .= $key . '="' . $escaped . '"' . PHP_EOL;
        }
        file_put_contents(base_path('.env'), $content);
    }
}
