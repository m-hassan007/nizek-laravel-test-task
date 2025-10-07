<?php

use Illuminate\Support\Facades\Route;
use Modules\Stock\Http\Controllers\Backend\StockPricesController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['middleware' => ['auth', 'can:view_backend'], 'prefix' => 'admin', 'as' => 'backend.'], function () {
    Route::resource('stock_prices', StockPricesController::class);
});
