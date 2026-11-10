<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Http\Request;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // [php82] alias "Form" utilisé par les vues (remplace illuminate/html)
        AliasLoader::getInstance()->alias('Form', \App\Support\Form::class);

        // [php82] limites par IP du "mot de passe oublié" (compteurs séparés de ceux de la connexion)
        RateLimiter::for('password-email', fn (Request $request) => Limit::perMinutes(10, 5)->by($request->ip()));
        RateLimiter::for('password-reset', fn (Request $request) => Limit::perMinutes(10, 10)->by($request->ip()));

        // [php82] mail "mot de passe oublié" en français. Le lien part de APP_URL (jamais de l'adresse
        // envoyée par le navigateur : un en-tête Host truqué ne peut pas détourner le lien vers un autre site).
        ResetPassword::toMailUsing(function ($notifiable, $token) {
            $url = rtrim(config('app.url'), '/') . route('password.reset', ['token' => $token], false);
            $minutes = config('auth.passwords.users.expire');

            return (new MailMessage)
                ->subject(config('mail.from.name') . ' : changer le mot de passe de ton compte admin')
                ->greeting('Bonjour ' . $notifiable->name . ',')
                ->line('Une demande de nouveau mot de passe a été faite pour ton compte admin du panneau du Store.')
                ->action('Choisir un nouveau mot de passe', $url)
                ->line("Ce lien ne marche qu'une fois, pendant {$minutes} minutes. Il faudra retaper ton adresse e-mail.")
                ->line("Si tu n'as rien demandé, ignore ce mail : ton mot de passe ne change pas.")
                ->salutation(config('mail.from.name'));
        });

        // [php82] paramètres de route liés aux modèles (comme $router->model() en Laravel 5.1)
        Route::model('items', \App\Models\StoreItem::class);
        Route::model('categories', \App\Models\StoreCategory::class);
        Route::model('store_user', \App\Models\StoreUser::class);
        Route::model('servers', \App\Models\StoreServer::class);
        Route::model('versions', \App\Models\StoreVersion::class);
        Route::model('roles', \App\Models\Role::class);
        Route::model('permissions', \App\Models\Permission::class);
        Route::model('panel_user', \App\Models\User::class);
        Route::model('loadout', \App\Models\StoreLoadout::class);
    }
}
