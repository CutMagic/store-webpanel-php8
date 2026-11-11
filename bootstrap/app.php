<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // [php82] middlewares du WebPanel
        $middleware->alias([
            'authorize' => \App\Http\Middleware\Authorize::class,
            'storeuserauth' => \App\Http\Middleware\StoreUserAuth::class,
            'installed' => \App\Http\Middleware\Installed::class,
            'notinstalled' => \App\Http\Middleware\NotInstalled::class,
        ]);
        // visiteur non connecté sur /webpanel -> /login ; admin déjà connecté sur /login -> tableau de bord
        $middleware->append(\App\Http\Middleware\SecurityHeaders::class); // [multimod] anti clickjacking, nosniff
        $middleware->web(append: [\App\Http\Middleware\SetLocale::class]); // [multimod] langue FR / EN
        $middleware->redirectGuestsTo(fn () => route('login'));
        $middleware->redirectUsersTo(fn () => route('webpanel.dashboard'));
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
