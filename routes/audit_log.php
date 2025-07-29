<?php

use Illuminate\Support\Facades\Route;

//Controllers
use App\Http\Controllers\AuditLog\AuditLogController;

$base_segment = '/audit_log';

Route::middleware(['auth'])->group(function () use ($base_segment) {
    Route::controller(AuditLogController::class)->group(function () use ($base_segment) {
        Route::get($base_segment . '/detail/{audit_log_type}/{id}', 'detail')->name('audit_log.detail');
    });
});
