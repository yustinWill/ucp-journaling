<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;
use App\Events\MessageSent;

Route::middleware(['auth'])->group(function () {
    Route::controller(HomeController::class)->group(function () {
        Route::get('/home', 'index')->name('home.view');
        Route::get('/dashboard', 'dashboard')->name('dashboard.index');
    });
});


Route::get('/send-message', function () {
    $message = [
        'icon' => 'ti tabler-shopping-cart',
        'title' => 'TITLE',
        'message' => 'MESSAGE',
        'metadata' => json_encode(['type' => 'SALES_ORDER', 'id' => 5]),
        'timestamp_text' => date('y-m-d H:i:s'),
    ];

    // Fire the event
    broadcast(new MessageSent($message));

    return response()->json(['status' => 'Message broadcasted!']);
});

Route::get('/receive-data', function () {
    return view('receive');
});
