<?php
// [php82] Routes du Store WebPanel 2 (version multimod), portées de Laravel 5.1 vers Laravel 12.
// Mêmes adresses et mêmes noms de routes qu'avant (les vues et config/route_perms.php n'ont pas changé).

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InstallerController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\WebPanel\DashboardController;
use App\Http\Controllers\WebPanel\SettingsController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\WebPanel\Store\ItemsController;
use App\Http\Controllers\WebPanel\Store\CategoriesController;
use App\Http\Controllers\WebPanel\Store\UsersController as StoreUsersController;
use App\Http\Controllers\WebPanel\Store\ServersController;
use App\Http\Controllers\WebPanel\Store\VersionsController;
use App\Http\Controllers\WebPanel\Store\ToolsController;
use App\Http\Controllers\WebPanel\Panel\PermissionsController;
use App\Http\Controllers\WebPanel\Panel\RolesController;
use App\Http\Controllers\WebPanel\Panel\UsersController as PanelUsersController;
use App\Http\Controllers\UserPanel\AuthController as UserAuthController;
use App\Http\Controllers\UserPanel\DashboardController as UserDashboardController;
use App\Http\Controllers\UserPanel\UserItemsController;
use App\Http\Controllers\UserPanel\LoadoutController;
use App\Http\Controllers\UserPanel\PlayersController;
use App\Http\Controllers\UserPanel\SteamImageController;

Route::get('/', [WelcomeController::class, 'index']);

// [multimod] bouton de langue FR / EN
Route::get('lang/{locale}', [\App\Http\Controllers\LocaleController::class, 'switch'])->where('locale', 'fr|en')->name('lang.switch');
Route::get('home', [HomeController::class, 'index'])->middleware('auth');

// [multimod] page Joueurs publique (sans connexion Steam), si l'admin l'a activée dans
// Admin > Réglages du site (sinon : 404). Limite : 120 pages par minute et par adresse IP.
Route::middleware('throttle:120,1')->group(function () {
    Route::get('players', [PlayersController::class, 'getPublicIndex'])->name('players.index');
    Route::get('players/{id}', [PlayersController::class, 'getPublicShow'])->where('id', '[0-9]{1,10}')->name('players.show');
    Route::get('sitemap.xml', [SitemapController::class, 'sitemap'])->name('sitemap');
});
Route::get('robots.txt', [SitemapController::class, 'robots'])->name('robots');

// Installateur (seulement tant que le fichier INSTALLED n'existe pas)
Route::prefix('installer')->name('installer.')->group(function () {
    Route::middleware('notinstalled')->group(function () {
        Route::get('', [InstallerController::class, 'showWelcome'])->name('welcome.show');
        Route::post('', [InstallerController::class, 'postWelcome'])->name('welcome.post');
        Route::get('settings', [InstallerController::class, 'showSettings'])->name('settings.show');
        Route::post('settings', [InstallerController::class, 'postSettings'])->name('settings.post');
        Route::get('fill_db', [InstallerController::class, 'showFillDb'])->name('fill_db.show');
        Route::post('fill_db', [InstallerController::class, 'postFillDb'])->name('fill_db.post');
    });
    Route::get('finish', [InstallerController::class, 'showFinish'])->name('finish.show');
});

// Administration
// [php82] auth.session : si le mot de passe d'un compte change (mot de passe oublié, Panel > Users),
// les autres sessions ouvertes sur ce compte sont déconnectées
Route::middleware(['auth', 'auth.session', 'authorize'])->prefix('webpanel')->name('webpanel.')->group(function () {
    Route::get('', [DashboardController::class, 'showDashboard'])->name('dashboard');

    // [multimod] réglages du site (page Joueurs publique, nom et description du site)
    Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('settings', [SettingsController::class, 'update'])->name('settings.update');

    Route::prefix('store')->name('store.')->group(function () {
        Route::get('items/data', [ItemsController::class, 'getData'])->name('items.data');
        Route::resource('items', ItemsController::class)->parameters(['items' => 'items']);
        Route::get('categories/data', [CategoriesController::class, 'getData'])->name('categories.data');
        Route::resource('categories', CategoriesController::class)->parameters(['categories' => 'categories']);
        Route::get('users/data', [StoreUsersController::class, 'getData'])->name('users.data');
        Route::resource('users', StoreUsersController::class)->parameters(['users' => 'store_user']);
        Route::resource('servers', ServersController::class)->parameters(['servers' => 'servers']);

        Route::prefix('versions')->name('versions.')->group(function () {
            Route::get('', [VersionsController::class, 'index'])->name('index');
            Route::get('update', [VersionsController::class, 'update'])->name('update');
            Route::get('{versions}', [VersionsController::class, 'show'])->name('show');
        });

        Route::prefix('tools')->name('tools.')->group(function () {
            Route::get('', [ToolsController::class, 'index'])->name('index');
            Route::post('json_shrinker', [ToolsController::class, 'JsonShrinker'])->name('json_shrinker');
            Route::post('json_checker', [ToolsController::class, 'JsonChecker'])->name('json_checker');
            Route::post('import', [ToolsController::class, 'VerifyImport'])->name('import');
            Route::post('export', [ToolsController::class, 'VerifyExport'])->name('export');
            Route::post('do_import', [ToolsController::class, 'PerformImport'])->name('do_import');
        });
    });

    Route::prefix('panel')->name('panel.')->group(function () {
        Route::resource('permissions', PermissionsController::class)->parameters(['permissions' => 'permissions']);
        Route::resource('roles', RolesController::class)->parameters(['roles' => 'roles']);
        Route::resource('users', PanelUsersController::class)->parameters(['users' => 'panel_user']);
    });
});

// Connexion admin (pas d'inscription libre)
Route::get('login', [LoginController::class, 'getLogin'])->name('login')->middleware('guest');
Route::get('auth/login', [LoginController::class, 'getLogin'])->middleware('guest');
Route::post('auth/login', [LoginController::class, 'postLogin'])->middleware('guest');
Route::get('logout', [LoginController::class, 'getLogout'])->name('logout');
Route::get('auth/logout', [LoginController::class, 'getLogout']);

// [php82] Mot de passe oublié (comptes admin) : lien à usage unique par mail.
// Limites par IP (AppServiceProvider) : 5 demandes de lien / 10 min, 10 essais de changement / 10 min
// (+ 1 mail par minute et par compte, réglage "throttle" de config/auth.php).
Route::middleware('guest')->group(function () {
    Route::get('password/email', [PasswordResetController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('password/email', [PasswordResetController::class, 'sendResetLink'])->name('password.email')->middleware('throttle:password-email');
    Route::get('password/reset/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
    Route::post('password/reset', [PasswordResetController::class, 'reset'])->name('password.update')->middleware('throttle:password-reset');
});

// Espace joueur
Route::prefix('userpanel')->name('userpanel.')->group(function () {
    Route::get('', function () {
        return redirect()->route('userpanel.dashboard');
    });

    Route::prefix('auth')->name('auth.')->group(function () {
        Route::get('', [UserAuthController::class, 'getIndex'])->name('index');
        Route::any('serverlogin', [UserAuthController::class, 'serverlogin'])->name('serverlogin');
        Route::get('steamlogin', [UserAuthController::class, 'steamlogin'])->name('steamlogin');
        Route::get('logout', [UserAuthController::class, 'logout'])->name('logout');
        Route::get('devlogin/{userid}', [UserAuthController::class, 'devlogin'])->name('devlogin'); // test local seulement
    });

    Route::middleware('storeuserauth')->group(function () {
        Route::get('dashboard', [UserDashboardController::class, 'getIndex'])->name('dashboard');

        Route::prefix('useritems')->name('useritems.')->group(function () {
            Route::get('', [UserItemsController::class, 'getIndex'])->name('index');
            Route::get('buyoverview', [UserItemsController::class, 'getBuyOverview'])->name('buyoverview');
            Route::get('buy/{item_id}', [UserItemsController::class, 'getBuy'])->name('buyconf');
            Route::post('buy/{item_id}', [UserItemsController::class, 'postBuy'])->name('buyproc');
            Route::post('sell/{item_id}', [UserItemsController::class, 'postSell'])->name('sell');
        });

        Route::prefix('loadouts')->name('loadouts.')->group(function () {
            Route::get('', [LoadoutController::class, 'getIndex'])->name('index');
            Route::post('{loadout}/equip/{item_id}', [LoadoutController::class, 'postEquip'])->name('equip');
            Route::post('{loadout}/unequip/{item_id}', [LoadoutController::class, 'postUnequip'])->name('unequip');
        });

        // [multimod] classement et profils des autres joueurs (lecture seule)
        Route::prefix('players')->name('players.')->group(function () {
            Route::get('', [PlayersController::class, 'getIndex'])->name('index');
            Route::get('{id}', [PlayersController::class, 'getShow'])->where('id', '[0-9]{1,10}')->name('show');
        });
    });

    Route::get('steamimage', [SteamImageController::class, 'getUserImageForCurrent'])->name('steamimage.current');
    Route::get('steamimage/refresh', [SteamImageController::class, 'refreshUserImage'])->name('steamimage.refresh');
    Route::get('steamimage/{auth}', [SteamImageController::class, 'getUserImageForAuth'])->name('steamimage.auth');
});
