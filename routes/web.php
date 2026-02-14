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
    | PROJECTS (Admin + User)
    |--------------------------------------------------------------------------
    */

    // List
    Route::get('/projects', [ProjectController::class, 'index'])
        ->name('projects.index');

    /*
    |--------------------------------------------------------------------------
    | TASKS (Admin + User)
    |--------------------------------------------------------------------------
    */

    // List
    Route::get('/tasks', [TaskController::class, 'index'])
        ->name('tasks.index');

    // Progress & Date update (user can update assigned tasks)
    Route::patch('/tasks/update-progress', [TaskController::class, 'updateProgress'])
        ->name('tasks.updateProgress');

    Route::patch('/tasks/set-dates', [TaskController::class, 'setDates'])
        ->name('tasks.setDates');

    /*
    |--------------------------------------------------------------------------
    | LOGS (Admin + User — filtered inside controller)
    |--------------------------------------------------------------------------
    */

    Route::get('/logs/projects', [ProjectController::class, 'activityLogs'])
        ->name('logs.projects');

    Route::get('/logs/tasks', [TaskController::class, 'taskLogs'])
        ->name('logs.tasks');

    /*
    |--------------------------------------------------------------------------
    | PROFILE (Admin + User)
    |--------------------------------------------------------------------------
    */

    Route::get('/profile', [ProfileController::class, 'show'])
        ->name('profile.show');

    Route::get('/profile/edit', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::match(['put', 'patch'], '/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy']);

    /*
    |--------------------------------------------------------------------------
    | ADMIN ONLY (Active)
    |--------------------------------------------------------------------------
    */

    Route::middleware(['admin'])->group(function () {

        /*
        |--------------------------------------------------------------------------
        | PERSONNEL
        |--------------------------------------------------------------------------
        */

        Route::get('/personnel', [PersonnelController::class, 'index'])
            ->name('personnel.index');

        Route::get('/personnel/create', [PersonnelController::class, 'create'])
            ->name('personnel.create');

        Route::post('/personnel', [PersonnelController::class, 'store'])
            ->name('personnel.store');

        Route::get('/personnel/{user}/edit', [PersonnelController::class, 'edit'])
            ->name('personnel.edit');

        Route::put('/personnel/{user}', [PersonnelController::class, 'update'])
            ->name('personnel.update');

        Route::patch('/personnel/{user}/deactivate', [PersonnelController::class, 'deactivate'])
            ->name('personnel.deactivate');

        Route::patch('/personnel/{user}/reactivate', [PersonnelController::class, 'reactivate'])
            ->name('personnel.reactivate');

        /*
        |--------------------------------------------------------------------------
        | PROJECT MANAGEMENT (ADMIN)
        |--------------------------------------------------------------------------
        */

        Route::get('/projects/create', [ProjectController::class, 'create'])
            ->name('projects.create');

        Route::post('/projects', [ProjectController::class, 'store'])
            ->name('projects.store');

        Route::get('/projects/{project}/edit', [ProjectController::class, 'edit'])
            ->name('projects.edit');

        Route::put('/projects/{project}', [ProjectController::class, 'update'])
            ->name('projects.update');

        Route::patch('/projects/{project}/archive', [ProjectController::class, 'archive'])
            ->name('projects.archive');

        /*
        |--------------------------------------------------------------------------
        | TASK MANAGEMENT (ADMIN)
        |--------------------------------------------------------------------------
        */

        Route::post('/tasks', [TaskController::class, 'store'])
            ->name('tasks.store');

        Route::patch('/tasks/{task}/archive', [TaskController::class, 'archive'])
            ->name('tasks.archive');

        Route::patch('/tasks/assign', [TaskController::class, 'assign'])
            ->name('tasks.assign');

        Route::put('/tasks/update', [TaskController::class, 'update'])
            ->name('tasks.update');
    });

    /*
    |--------------------------------------------------------------------------
    | SHOW ROUTES (MUST BE LAST TO AVOID CONFLICT)
    |--------------------------------------------------------------------------
    */

    // Project show
    Route::get('/projects/{project}', [ProjectController::class, 'show'])
        ->name('projects.show');

    // Task show
    Route::get('/tasks/{task}', [TaskController::class, 'show'])
        ->name('tasks.show');
});

/*
|--------------------------------------------------------------------------
| ARCHIVES (Auth + Admin ONLY)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'admin'])->group(function () {

    Route::get('/archives/projects', [ProjectController::class, 'archived'])
        ->name('projects.archived');

    Route::patch('/archives/projects/{project}/restore', [ProjectController::class, 'restore'])
        ->name('projects.restore');

    Route::get('/archives/tasks', [TaskController::class, 'archived'])
        ->name('tasks.archived');

    Route::patch('/archives/tasks/{task}/restore', [TaskController::class, 'restore'])
        ->name('tasks.restore');

    Route::get('/archives/personnel', [PersonnelController::class, 'archived'])
        ->name('personnel.archived');
});

/*
|--------------------------------------------------------------------------
| Backward Friendly URLs
|--------------------------------------------------------------------------
*/

Route::redirect('/my-profile', '/profile');
Route::redirect('/my-profile/edit', '/profile/edit');

require __DIR__ . '/auth.php';
