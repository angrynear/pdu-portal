<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PersonnelController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;


/*
|--------------------------------------------------------------------------
| Public
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| Authenticated + Active Users
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'active'])->group(function () {

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | Admin (Active)
    |--------------------------------------------------------------------------
    */
    Route::middleware(['admin'])->group(function () {

        // ===== PERSONNEL =====
        Route::get('/personnel', [PersonnelController::class, 'index'])
            ->name('personnel.index');

        Route::get('/personnel/create', [PersonnelController::class, 'create'])
            ->name('personnel.create');

        Route::post('/personnel', [PersonnelController::class, 'store'])
            ->name('personnel.store');

        Route::patch('/personnel/{user}/deactivate', [PersonnelController::class, 'deactivate'])
            ->name('personnel.deactivate');

        Route::get('/personnel/{user}/edit', [PersonnelController::class, 'edit'])
            ->name('personnel.edit');

        Route::put('/personnel/{user}', [PersonnelController::class, 'update'])
            ->name('personnel.update');

        // ===== PROJECTS (ACTIVE) =====
        Route::get('/projects', [ProjectController::class, 'index'])
            ->name('projects.index');

        Route::get('/projects/create', [ProjectController::class, 'create'])
            ->name('projects.create');

        Route::post('/projects', [ProjectController::class, 'store'])
            ->name('projects.store');

        Route::get('/projects/{project}', [ProjectController::class, 'show'])
            ->name('projects.show');

        Route::get('/projects/{project}/edit', [ProjectController::class, 'edit'])
            ->name('projects.edit');

        Route::put('/projects/{project}', [ProjectController::class, 'update'])
            ->name('projects.update');

        Route::patch('/projects/{project}/archive', [ProjectController::class, 'archive'])
            ->name('projects.archive');

        // ===== TASKS =====
        Route::post('/tasks', [TaskController::class, 'store'])
            ->name('tasks.store');

        Route::get('/tasks', [TaskController::class, 'index'])
            ->name('tasks.index');

        Route::patch('/tasks/{task}/archive', [TaskController::class, 'archive'])
            ->name('tasks.archive');

        Route::patch('/tasks/update-progress', [TaskController::class, 'updateProgress'])
            ->name('tasks.updateProgress');

        // ===== LOGS =====
        Route::get('/logs/projects', function () {
            return view('logs.projects');
        })->name('logs.projects');

        Route::get('/logs/tasks', function () {
            return view('logs.tasks');
        })->name('logs.tasks');
    });

    /*
    |--------------------------------------------------------------------------
    | My Profile (Admin + User)
    |--------------------------------------------------------------------------
    */
    Route::get('/my-profile', [ProfileController::class, 'show'])
        ->name('profile.show');

    Route::get('/my-profile/edit', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::put('/my-profile', [ProfileController::class, 'update'])
        ->name('profile.update');
});

/*
|--------------------------------------------------------------------------
| ARCHIVES (Auth + Admin ONLY, NOT Active-filtered)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])->group(function () {

    // ===== ARCHIVED PROJECTS =====
    Route::get('/archives/projects', [ProjectController::class, 'archived'])
        ->name('projects.archived');

    Route::patch('/archives/projects/{project}/restore', [ProjectController::class, 'restore'])
        ->name('projects.restore');

    // ===== ARCHIVED TASKS =====
    Route::get('/archives/tasks', [TaskController::class, 'archived'])
        ->name('tasks.archived');

    Route::patch('/archives/tasks/{task}/restore', [TaskController::class, 'restore'])
        ->name('tasks.restore');

    // ===== ARCHIVED PERSONNEL =====
    Route::get('/archives/personnel', [PersonnelController::class, 'archived'])
        ->name('personnel.archived');

    // ===== REACTIVATE PERSONNEL =====
    Route::patch('/personnel/{user}/reactivate', [PersonnelController::class, 'reactivate'])
        ->name('personnel.reactivate');

    // ===== EDIT TASK DETAILS =====
    Route::put('/tasks/update', [TaskController::class, 'update'])
        ->name('tasks.update');
});

require __DIR__ . '/auth.php';
