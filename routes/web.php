<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'active'])->group(function () {

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::middleware(['admin'])->group(function () {
        Route::get('/admin', function () {
            return 'Admin Area';
        });
    });
});

/*
|--------------------------------------------------------------------------
| Authentication Routes (Laravel 12)
|--------------------------------------------------------------------------
*/
require __DIR__ . '/auth.php';
