<?php

namespace Webkul\ShopExtension\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Webkul\Core\Http\Middleware\NoCacheMiddleware;
use Webkul\Core\Http\Middleware\PreventRequestsDuringMaintenance;
use Webkul\ShopExtension\Http\View\Composers\JustForYouComposer;

class ShopExtensionServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Route::middleware(['web', 'shop', PreventRequestsDuringMaintenance::class])
            ->group(__DIR__.'/../Routes/api.php');

        Route::middleware(['web', 'admin', NoCacheMiddleware::class])
            ->group(__DIR__.'/../Routes/admin.php');

        $this->loadViewsFrom(__DIR__.'/../Resources/views', 'shopext');

        // Register view composer for homepage → binds $justForYouProducts
        View::composer('shop::home.index', JustForYouComposer::class);
    }
}
