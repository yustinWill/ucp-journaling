<?php

use App\Http\Middleware\EnsureTokenIsValid;
use Illuminate\Support\Facades\Route;

//Controllers
use App\Http\Controllers\MasterData\ContactController;
use App\Http\Controllers\MasterData\StorageController;

$base_segment = '/master_data';
$contact_segment = '/contact';
$storage_segment = '/gudang';

Route::middleware([EnsureTokenIsValid::class])->group(function () use ($base_segment, $contact_segment, $storage_segment) {
    Route::controller(ContactController::class)->group(function () use ($base_segment, $contact_segment) {
        Route::get($base_segment . $contact_segment . '/index', 'index')->name('master_data.contact.index')->middleware(EnsureTokenIsValid::class);
        Route::get($base_segment . $contact_segment . '/index_data_table', 'index_data_table')->name('master_data.contact.index_data_table')->middleware(EnsureTokenIsValid::class);
        Route::get($base_segment . $contact_segment . '/add', 'add')->name('master_data.contact.add')->middleware(EnsureTokenIsValid::class);
        Route::get($base_segment . $contact_segment . '/export', 'export')->name('master_data.contact.export')->middleware(EnsureTokenIsValid::class);
        Route::get($base_segment . $contact_segment . '/import', 'import')->name('master_data.contact.import')->middleware(EnsureTokenIsValid::class);
    });

    Route::controller(StorageController::class)->group(function () use ($base_segment, $storage_segment) {
        Route::get($base_segment . $storage_segment . '/index', 'index')->name('master_data.storage.index')->middleware(EnsureTokenIsValid::class);
        Route::get($base_segment . $storage_segment . '/index_data_table', 'index_data_table')->name('master_data.storage.index_data_table')->middleware(EnsureTokenIsValid::class);
        Route::get($base_segment . $storage_segment . '/add', 'add')->name('master_data.storage.add')->middleware(EnsureTokenIsValid::class);
        Route::get($base_segment . $storage_segment . '/detail', 'detail')->name('master_data.storage.detail')->middleware(EnsureTokenIsValid::class);
        Route::get($base_segment . $storage_segment . '/detail_product_data_table', 'detail_product_data_table')->name('master_data.storage.detail_product_data_table')->middleware(EnsureTokenIsValid::class);
        Route::get($base_segment . $storage_segment . '/detail_transaction_data_table', 'detail_transaction_data_table')->name('master_data.storage.detail_transaction_data_table')->middleware(EnsureTokenIsValid::class);
        Route::get($base_segment . $storage_segment . '/detail_batch_data_table', 'detail_batch_data_table')->name('master_data.storage.detail_batch_data_table')->middleware(EnsureTokenIsValid::class);
        Route::get($base_segment . $storage_segment . '/export', 'export')->name('master_data.storage.export')->middleware(EnsureTokenIsValid::class);
        Route::get($base_segment . $storage_segment . '/import', 'import')->name('master_data.storage.import')->middleware(EnsureTokenIsValid::class);
    });
});
