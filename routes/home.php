<?php

use App\Http\Controllers\HomeController;
use App\Http\Middleware\EnsureTokenIsValid;
use Illuminate\Support\Facades\Route;

Route::middleware([EnsureTokenIsValid::class])->group(function () {
    Route::controller(HomeController::class)->group(function () {
        Route::get('/home', 'index')->name('home.view')->middleware(EnsureTokenIsValid::class);
        Route::get('/dashboard', 'dashboard')->name('dashboard.index')->middleware(EnsureTokenIsValid::class);
    });
});
