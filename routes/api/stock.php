<?php

use App\Http\Controllers\Api\StockInformationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Stock Information API Routes
|--------------------------------------------------------------------------
|
| Routes for stock management, batch information, and stock reporting APIs
|
*/

Route::prefix('stock')->group(function () {

    // Stock Information Routes
    Route::get('/', [StockInformationController::class, 'overallStock'])
        ->name('api.stock.overall');

    Route::get('/warehouse/{warehouseId}', [StockInformationController::class, 'stockByWarehouse'])
        ->name('api.stock.by-warehouse');

    // Batch Information Routes
    Route::get('/product/{productId}/batches', [StockInformationController::class, 'productBatchInfo'])
        ->name('api.stock.product-batches');

    Route::post('/product/{productId}/cogs', [StockInformationController::class, 'calculateCOGS'])
        ->name('api.stock.calculate-cogs');

    // Stock Movement Routes
    Route::get('/product/{productId}/movements', [StockInformationController::class, 'movementHistory'])
        ->name('api.stock.movement-history');

    // Stock Reports Routes
    Route::get('/low-stock', [StockInformationController::class, 'lowStock'])
        ->name('api.stock.low-stock');

    Route::get('/valuation', [StockInformationController::class, 'stockValuation'])
        ->name('api.stock.valuation');

    // Dashboard Route
    Route::get('/dashboard', [StockInformationController::class, 'dashboard'])
        ->name('api.stock.dashboard');
});
