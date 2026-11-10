<?php

use App\Providers\AppServiceProvider;
use App\Providers\ViewComposerServiceProvider;

return [
    AppServiceProvider::class,
    ViewComposerServiceProvider::class, // [php82] données des en-têtes, menus et formulaires
];
