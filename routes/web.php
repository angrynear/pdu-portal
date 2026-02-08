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

    // PROJECTS
    Route::get('/projects', function () {
        return view('projects.index');
    })->name('projects.index');

    // TASKS
    Route::get('/tasks', function () {
        return view('tasks.index');
    })->name('tasks.index');

    // LOGS
    Route::get('/logs/projects', function () {
        return view('logs.projects');
    })->name('logs.projects');

    Route::get('/logs/tasks', function () {
        return view('logs.tasks');
    })->name('logs.tasks');

    // ADMIN ONLY
    Route::middleware(['admin'])->group(function () {

        Route::get('/admin', function () {
            return 'Admin Area';
        });

        Route::get('/personnel', function () {
            return view('personnel.index');
        })->name('personnel.index');

        Route::get('/content/slideshow', function () {
            return view('content.slideshow');
        })->name('content.slideshow');

        Route::get('/archives/projects', function () {
            return view('archives.projects');
        })->name('archives.projects');

        Route::get('/archives/tasks', function () {
            return view('archives.tasks');
        })->name('archives.tasks');

        Route::get('/archives/personnel', function () {
            return view('archives.personnel');
        })->name('archives.personnel');
    });
});

/*
|--------------------------------------------------------------------------
| Authentication Routes (Laravel 12)
|--------------------------------------------------------------------------
*/
require __DIR__ . '/auth.php';
