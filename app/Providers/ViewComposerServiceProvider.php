<?php
// [php82] repris de la version Laravel 5.1 (données des en-têtes, menus et formulaires)

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\StoreUser;
use App\Models\StoreItem;
use App\Models\StoreCategory;
use App\Models\StoreServer;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;

class ViewComposerServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->ComposeWebPanelSidebar();
        $this->ComposeWebPanelForms();
        $this->ComposeWebPanelHeader();
        $this->ComposeUserPanelHeader();
        $this->ComposeUserPanelForms();
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }


    /**
     * Passes the required variables to the sidebar
     */
    public function ComposeWebPanelSidebar()
    {
        // [multimod] aussi pour le tableau de bord admin ; count() en SQL au lieu de charger toutes les lignes
        view()->composer([
            'templates.' . \Config::get('webpanel.template') . 'webpanel.includes.sidebar',
            'templates.' . \Config::get('webpanel.template') . 'webpanel.includes.dashboard-boxes',
        ], function ($view) {
            $view->with('storeItemCount', StoreItem::count());
            $view->with('storeCategoryCount', StoreCategory::count());
            $view->with('storeUserCount', StoreUser::count());
            $view->with('storeServerCount', StoreServer::count());
            $view->with('panelUserCount', User::count());
            $view->with('panelRoleCount', Role::count());
            $view->with('panelPermissionCount', Permission::count());
        });
    }

    /**
     * Passes the required variables to the sidebar
     */
    public function ComposeUserPanelSidebar()
    {
        view()->composer('templates.' . \Config::get('webpanel.template') . 'userpanel.includes.sidebar', function ($view) {
            $view->with('storeItemCount', StoreItem::all()->count()); //Not used
        });
    }


    public function ComposeWebPanelForms()
    {
        view()->composer('templates.' . \Config::get('webpanel.template') . 'webpanel.store.items._form', function ($view) {
            $view->with('categories', StoreCategory::pluck('display_name', 'id'));
            $view->with('servers', StoreServer::pluck('display_name', 'id'));
        });

        view()->composer('templates.' . \Config::get('webpanel.template') . 'webpanel.store.categories._form', function ($view) {
            $view->with('servers', StoreServer::pluck('display_name', 'id'));
        });

        view()->composer('templates.' . \Config::get('webpanel.template') . 'webpanel.panel.users._form', function ($view) {
            $view->with('roles', Role::pluck('display_name', 'id'));
        });

        view()->composer('templates.' . \Config::get('webpanel.template') . 'webpanel.panel.roles._form', function ($view) {
            $view->with('permissions', Permission::pluck('display_name', 'id'));
        });

        view()->composer('templates.' . \Config::get('webpanel.template') . 'webpanel.panel.permissions._form', function ($view) {
            $view->with('roles', Role::pluck('display_name', 'id'));
        });
    }

    public function ComposeUserPanelForms()
    {
        // [multimod] anciennes pages de loadouts personnels supprimées (pas dans le Store 1.2)
    }


    /**
     * Passes the required variables to the header
     */
    public function ComposeWebPanelHeader()
    {
        view()->composer('templates.' . \Config::get('webpanel.template') . 'webpanel.includes.header', function ($view) {
            $view->with('username', Auth::user()->name);
        });
    }

    public function ComposeUserPanelHeader()
    {
        view()->composer('templates.' . \Config::get('webpanel.template') . 'userpanel.includes.header', function ($view) {
            // [multimod] Store 1.2 : pas de loadouts personnels ; on compte les objets équipés.
            $store_user = StoreUser::find(Session("store_user_id"));
            $credits = $store_user ? $store_user->credits : 0;
            $owned_item_count = $store_user ? $store_user->userItems()->count() : 0;
            $latest_items = $store_user ? $store_user->items()->orderBy('acquire_date', 'desc')->take(5)->get() : collect();
            $equipped_count = $store_user ? \DB::connection('store')->table('users_items_loadouts')
                ->join('users_items', 'users_items.id', '=', 'users_items_loadouts.useritem_id')
                ->where('users_items.user_id', $store_user->id)->count() : 0;

            $view->with('latest_items', $latest_items);
            $view->with('username', Session("store_user_name"));
            $view->with('credits', $credits);
            $view->with('owned_item_count', $owned_item_count);
            $view->with('equipped_count', $equipped_count);
        });
    }
}
