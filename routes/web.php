<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PersonnelController;
use App\Http\Controllers\ProfileController;

Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes (ACTIVE USERS)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'active'])->group(function () {

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/projects', function () {
        return view('projects.index');
    })->name('projects.index');

    Route::get('/tasks', function () {
        return view('tasks.index');
    })->name('tasks.index');

    Route::get('/logs/projects', function () {
        return view('logs.projects');
    })->name('logs.projects');

    Route::get('/logs/tasks', function () {
        return view('logs.tasks');
    })->name('logs.tasks');

    // ADMIN (ACTIVE)
    Route::middleware(['admin'])->group(function () {

        Route::get('/personnel', [PersonnelController::class, 'index'])
            ->name('personnel.index');

        Route::get('/personnel/create', [PersonnelController::class, 'create'])
            ->name('personnel.create');

        Route::post('/personnel', [PersonnelController::class, 'store'])
            ->name('personnel.store');

        Route::patch('/personnel/{user}/deactivate', [PersonnelController::class, 'deactivate'])
            ->name('personnel.deactivate');

        Route::patch('/personnel/{user}/reactivate', [PersonnelController::class, 'reactivate'])
            ->name('personnel.reactivate');

        Route::get('/content/slideshow', function () {
            return view('content.slideshow');
        })->name('content.slideshow');
    });

    // My Profile Admin and User
    Route::get('/my-profile', [\App\Http\Controllers\ProfileController::class, 'show'])
        ->name('profile.show');

    Route::get('/my-profile/edit', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::put('/my-profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    // My Profile + Admin Edit (controlled in controller)
    Route::get('/personnel/{user}/edit', [PersonnelController::class, 'edit'])
        ->name('personnel.edit');

    Route::put('/personnel/{user}', [PersonnelController::class, 'update'])
        ->name('personnel.update');
});


/*
|--------------------------------------------------------------------------
| ARCHIVES (AUTH + ADMIN ONLY, NOT ACTIVE-FILTERED)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])->group(function () {

    Route::get('/archives/personnel', [PersonnelController::class, 'archived'])
        ->name('personnel.archived');

    Route::get('/archives/projects', function () {
        return view('archives.projects');
    })->name('archives.projects');

    Route::get('/archives/tasks', function () {
        return view('archives.tasks');
    })->name('archives.tasks');
});

require __DIR__ . '/auth.php';
