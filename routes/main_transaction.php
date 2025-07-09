<?php

use App\Http\Middleware\EnsureTokenIsValid;
use Illuminate\Support\Facades\Route;

//Controllers
use App\Http\Controllers\MainTransaction\JournalController;
use App\Http\Controllers\MainTransaction\PurchaseController;
use App\Http\Controllers\MainTransaction\SalesController;

$base_segment = '/main_transaction';
$journal_segment = '/jurnal';
$purchase_segment = '/pembelian';
$sales_segment = '/penjualan';

Route::middleware([EnsureTokenIsValid::class])->group(function () use ($base_segment, $journal_segment, $purchase_segment, $sales_segment) {
    Route::controller(JournalController::class)->group(function () use ($base_segment, $journal_segment) {
        Route::get($base_segment . $journal_segment . '/index', 'index')->name('main_transaction.jurnal.index')->middleware(EnsureTokenIsValid::class);
        Route::get($base_segment . $journal_segment . '/index_data_table', 'index_data_table')->name('main_transaction.jurnal.index_data_table')->middleware(EnsureTokenIsValid::class);
        Route::get($base_segment . $journal_segment . '/add', 'add')->name('main_transaction.jurnal.add')->middleware(EnsureTokenIsValid::class);
    });

    Route::controller(PurchaseController::class)->group(function () use ($base_segment, $purchase_segment) {
        Route::get($base_segment . $purchase_segment . '/index', 'index')->name('main_transaction.purchase.index')->middleware(EnsureTokenIsValid::class);
        Route::get($base_segment . $purchase_segment . '/index_data_table', 'index_data_table')->name('main_transaction.purchase.index_data_table')->middleware(EnsureTokenIsValid::class);
        Route::get($base_segment . $purchase_segment . '/add', 'add')->name('main_transaction.purchase.add')->middleware(EnsureTokenIsValid::class);
    });

    Route::controller(SalesController::class)->group(function () use ($base_segment, $sales_segment) {
        Route::get($base_segment . $sales_segment . '/index', 'index')->name('main_transaction.sales.index')->middleware(EnsureTokenIsValid::class);
        Route::get($base_segment . $sales_segment . '/index_data_table', 'index_data_table')->name('main_transaction.sales.index_data_table')->middleware(EnsureTokenIsValid::class);
        Route::get($base_segment . $sales_segment . '/add', 'add')->name('main_transaction.sales.add')->middleware(EnsureTokenIsValid::class);
    });
});
