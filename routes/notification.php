<?php

// Controllers
use App\Http\Controllers\Notification\HeaderListController;
use Illuminate\Support\Facades\Route;

$base_segment = '/notification';
$header_list_segment = '/header-list';

Route::middleware(['auth'])->group(function () use ($base_segment, $header_list_segment) {
    Route::controller(HeaderListController::class)->group(function () use ($base_segment, $header_list_segment) {
        Route::get($base_segment.$header_list_segment.'/notification-list', 'notification_list')->name('notification.header_list.notification_list');
    });

});
