<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\ChangePasswordController;
use App\Http\Controllers\Auth\ChangeCompanyController;
use Illuminate\Support\Facades\Route;

Route::controller(LoginController::class)->group(function () {
    Route::get('/', 'login')->name('login');
    Route::post('/login', 'authenticate')->name('authenticate');
});

Route::controller(LogoutController::class)->group(function () {
    Route::post('/logout', 'logout')->name('logout');
});

Route::controller(ChangePasswordController::class)->group(function () {
    Route::get('/change-password', 'index')->name('change_password.index');
    Route::post('/change-password', 'update')->name('change_password.update');
});

Route::controller(ChangeCompanyController::class)->group(function () {
    Route::get('/change-company', 'index')->name('change_company.index');
});

Route::controller(ChangePasswordController::class)->group(function () {
    Route::get('/change-password', 'index')->name('change_password.index');
    Route::post('/change-password', 'update')->name('change_password.update');
});
