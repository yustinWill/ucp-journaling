<?php

use App\Http\Controllers\MasterData\ContactController;
use App\Http\Middleware\EnsureTokenIsValid;
use Illuminate\Support\Facades\Route;

$base_segment = '/master_data';
$contact_segment = '/contact';

Route::middleware([EnsureTokenIsValid::class])->group(function () use ($base_segment, $contact_segment) {
    Route::controller(ContactController::class)->group(function () use ($base_segment, $contact_segment) {
        Route::get($base_segment . $contact_segment . '/index', 'index')->name('master_data.contact.index')->middleware(EnsureTokenIsValid::class);
        Route::get($base_segment . $contact_segment . '/index_data_table', 'index_data_table')->name('master_data.contact.index_data_table')->middleware(EnsureTokenIsValid::class);
        Route::get($base_segment . $contact_segment . '/add', 'add')->name('master_data.contact.add')->middleware(EnsureTokenIsValid::class);
        Route::get($base_segment . $contact_segment . '/export', 'export')->name('master_data.contact.export')->middleware(EnsureTokenIsValid::class);
        Route::get($base_segment . $contact_segment . '/import', 'import')->name('master_data.contact.import')->middleware(EnsureTokenIsValid::class);
    });
});
