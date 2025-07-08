<?php

use App\Http\Middleware\EnsureTokenIsValid;
use Illuminate\Support\Facades\Route;

//Controllers
use App\Http\Controllers\MainTransaction\JournalController;

$base_segment = '/main_transaction';
$journal_segment = '/jurnal';

Route::middleware([EnsureTokenIsValid::class])->group(function () use ($base_segment, $journal_segment) {
    Route::controller(JournalController::class)->group(function () use ($base_segment, $journal_segment) {
        Route::get($base_segment . $journal_segment . '/index', 'index')->name('main_transaction.jurnal.index')->middleware(EnsureTokenIsValid::class);
        Route::get($base_segment . $journal_segment . '/index_data_table', 'index_data_table')->name('main_transaction.jurnal.index_data_table')->middleware(EnsureTokenIsValid::class);
        Route::get($base_segment . $journal_segment . '/add', 'add')->name('main_transaction.jurnal.add')->middleware(EnsureTokenIsValid::class);
    });
});
