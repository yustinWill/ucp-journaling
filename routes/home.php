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
    // Sending a simple object instead of a model
    $message = [
        'user' => 'John Doe',
        'text' => 'Hello from Laravel Reverb!',
        'timestamp' => now()->toDateTimeString(),
    ];

    // Fire the event
    broadcast(new MessageSent($message));

    return response()->json(['status' => 'Message broadcasted!']);
});

Route::get('/receive-data', function () {
    return view('receive');
});
