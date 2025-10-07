<?php

use Illuminate\Support\Facades\Route;
use Modules\Stock\Http\Controllers\StockAnalysisController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('stock')->group(function () {
    // Get stock price change between two custom dates
    Route::get('custom-date-change', [StockAnalysisController::class, 'getCustomDateChange']);
    
    // Get stock price change for predefined periods
    Route::get('period-change', [StockAnalysisController::class, 'getPeriodChange']);
    
    // Get all periods data for a symbol
    Route::get('all-periods', [StockAnalysisController::class, 'getAllPeriods']);
    
    // Get available companies
    Route::get('companies', [StockAnalysisController::class, 'getCompanies']);
    
    // Get latest stock price for a company
    Route::get('latest-price', [StockAnalysisController::class, 'getLatestPrice']);
});
