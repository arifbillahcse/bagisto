<?php

namespace Webkul\ShopExtension\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Webkul\Core\Http\Middleware\PreventRequestsDuringMaintenance;

class ShopExtensionServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Route::middleware(['web', 'shop', PreventRequestsDuringMaintenance::class])
            ->group(__DIR__.'/../Routes/api.php');
    }
}
