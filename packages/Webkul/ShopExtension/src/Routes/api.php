<?php

use Illuminate\Support\Facades\Route;
use Webkul\ShopExtension\Http\Controllers\API\FlashSaleController;

Route::group(['prefix' => 'api'], function () {
    Route::controller(FlashSaleController::class)->prefix('products')->group(function () {
        Route::get('flash-sale', 'index')->name('shop.api.products.flash_sale.index');
    });
});
