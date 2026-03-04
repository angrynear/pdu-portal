<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PersonnelController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\Admin\SlideController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ArchiveController;
use App\Http\Controllers\LogController;
use App\Models\Slide;

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    $slides = Slide::where('is_active', true)
        ->orderBy('display_order')
        ->get();

    return view('welcome', compact('slides'));
});


/*
|--------------------------------------------------------------------------
| AUTHENTICATED + ACTIVE USERS
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'active'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | DASHBOARD
    |--------------------------------------------------------------------------
    */

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');


    /*
    |--------------------------------------------------------------------------
    | PROJECTS (Admin + User)
    |--------------------------------------------------------------------------
    */

    Route::get('/projects', [ProjectController::class, 'index'])
        ->name('projects.index');


    /*
    |--------------------------------------------------------------------------
    | TASKS (Admin + User)
    |--------------------------------------------------------------------------
    */

    Route::get('/tasks', [TaskController::class, 'index'])
        ->name('tasks.index');

    Route::post('/tasks', [TaskController::class, 'store'])
        ->name('tasks.store');

    Route::patch('/tasks/update-progress', [TaskController::class, 'updateProgress'])
        ->name('tasks.updateProgress');

    Route::patch('/tasks/set-dates', [TaskController::class, 'setDates'])
        ->name('tasks.setDates');

    Route::patch('/tasks/{task}/archive', [TaskController::class, 'archive'])
        ->name('tasks.archive');

    Route::patch('/archives/tasks/{task}/restore', [TaskController::class, 'restore'])
        ->name('tasks.restore');


    /*
    |--------------------------------------------------------------------------
    | ARCHIVES (Admin + User Access Controlled in Controller)
    |--------------------------------------------------------------------------
    */

    Route::get('/archives', [ArchiveController::class, 'index'])
        ->name('archives.index');

    Route::get('/archives/tasks', [TaskController::class, 'archived'])
        ->name('tasks.archived');


    /*
    |--------------------------------------------------------------------------
    | LOGS (Filtered inside controllers)
    |--------------------------------------------------------------------------
    */

    Route::get('/logs', [LogController::class, 'index'])
        ->name('logs.index');

    Route::get('/logs/projects', [ProjectController::class, 'activityLogs'])
        ->name('logs.projects');

    Route::get('/logs/tasks', [TaskController::class, 'taskLogs'])
        ->name('logs.tasks');


    /*
    |--------------------------------------------------------------------------
    | PROFILE
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
    | ADMIN ONLY
    |--------------------------------------------------------------------------
    */

    Route::middleware(['admin'])->group(function () {

        /*
        |--------------------------------------------------------------------------
        | PERSONNEL MANAGEMENT
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

        Route::get('/personnel/{user}', [PersonnelController::class, 'show'])
            ->name('personnel.show');


        /*
        |--------------------------------------------------------------------------
        | PROJECT MANAGEMENT
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
        | TASK MANAGEMENT
        |--------------------------------------------------------------------------
        */

        Route::patch('/tasks/assign', [TaskController::class, 'assign'])
            ->name('tasks.assign');

        Route::put('/tasks/update', [TaskController::class, 'update'])
            ->name('tasks.update');


        /*
        |--------------------------------------------------------------------------
        | SLIDES MANAGEMENT
        |--------------------------------------------------------------------------
        */

        Route::get('/slides', [SlideController::class, 'index'])
            ->name('slides.index');

        Route::get('/slides/create', [SlideController::class, 'create'])
            ->name('slides.create');

        Route::post('/slides', [SlideController::class, 'store'])
            ->name('slides.store');

        Route::get('/slides/{slide}/edit', [SlideController::class, 'edit'])
            ->name('slides.edit');

        Route::put('/slides/{slide}', [SlideController::class, 'update'])
            ->name('slides.update');

        Route::patch('/slides/{slide}/archive', [SlideController::class, 'archive'])
            ->name('slides.archive');
    });


    /*
    |--------------------------------------------------------------------------
    | SHOW ROUTES (LAST TO AVOID CONFLICT)
    |--------------------------------------------------------------------------
    */

    Route::get('/projects/{project}', [ProjectController::class, 'show'])
        ->name('projects.show');

    Route::get('/tasks/{task}', [TaskController::class, 'show'])
        ->name('tasks.show');
});


/*
|--------------------------------------------------------------------------
| ARCHIVES (ADMIN ONLY)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'admin'])->group(function () {

    Route::get('/archives/projects', [ProjectController::class, 'archived'])
        ->name('projects.archived');

    Route::patch('/archives/projects/{project}/restore', [ProjectController::class, 'restore'])
        ->name('projects.restore');

    Route::get('/archives/personnel', [PersonnelController::class, 'archived'])
        ->name('personnel.archived');

    Route::get('/archives/slides', [SlideController::class, 'archived'])
        ->name('slides.archived');

    Route::patch('/archives/slides/{slide}/restore', [SlideController::class, 'restore'])
        ->name('slides.restore');
});


/*
|--------------------------------------------------------------------------
| BACKWARD FRIENDLY URLS
|--------------------------------------------------------------------------
*/

Route::redirect('/my-profile', '/profile');
Route::redirect('/my-profile/edit', '/profile/edit');

require __DIR__.'/auth.php';