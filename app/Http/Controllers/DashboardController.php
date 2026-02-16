<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->isAdmin()) {

            $today = Carbon::today();
            $nextWeek = Carbon::today()->addDays(7);

            /*
            |--------------------------------------------------------------------------
            | PROJECT DATA (progress is computed, not DB column)
            |--------------------------------------------------------------------------
            */

            $projects = Project::whereNull('archived_at')->get();

            $totalProjects = $projects->count();

            $completedProjects = $projects->filter(function ($project) {
                return $project->progress == 100;
            })->count();

            $ongoingProjects = $projects->filter(function ($project) {
                return $project->progress > 0 && $project->progress < 100;
            })->count();

            $overdueProjectsCount = $projects->filter(function ($project) use ($today) {
                return $project->due_date &&
                    $project->due_date < $today &&
                    $project->progress < 100;
            })->count();


            /*
            |--------------------------------------------------------------------------
            | TASK STATS (tasks have progress column in DB)
            |--------------------------------------------------------------------------
            */

            $totalTasks = Task::whereNull('archived_at')->count();

            $ongoingTasks = Task::whereNull('archived_at')
                ->where('progress', '>', 0)
                ->where('progress', '<', 100)
                ->count();

            $overdueTasksCount = Task::whereNull('archived_at')
                ->whereDate('due_date', '<', $today)
                ->where('progress', '<', 100)
                ->count();


            /*
            |--------------------------------------------------------------------------
            | TOP 5 OVERDUE PROJECTS
            |--------------------------------------------------------------------------
            */

            $overdueProjects = $projects->filter(function ($project) use ($today) {
                return $project->due_date &&
                    $project->due_date < $today &&
                    $project->progress < 100;
            })
                ->sortBy('due_date')
                ->take(3);


            /*
            |--------------------------------------------------------------------------
            | TOP 5 OVERDUE TASKS
            |--------------------------------------------------------------------------
            */

            $overdueTasks = Task::with(['project', 'assignedUser'])
                ->whereNull('archived_at')
                ->whereDate('due_date', '<', $today)
                ->where('progress', '<', 100)
                ->orderBy('due_date')
                ->limit(3)
                ->get();


            /*
            |--------------------------------------------------------------------------
            | TOP 5 PROJECTS DUE SOON
            |--------------------------------------------------------------------------
            */

            $dueSoonProjects = $projects->filter(function ($project) use ($today, $nextWeek) {
                return $project->due_date &&
                    $project->due_date >= $today &&
                    $project->due_date <= $nextWeek &&
                    $project->progress < 100;
            })
                ->sortBy('due_date')
                ->take(3);


            /*
            |--------------------------------------------------------------------------
            | TOP 5 TASKS DUE SOON
            |--------------------------------------------------------------------------
            */

            $dueSoonTasks = Task::with(['project', 'assignedUser'])
                ->whereNull('archived_at')
                ->whereBetween('due_date', [$today, $nextWeek])
                ->where('progress', '<', 100)
                ->orderBy('due_date')
                ->limit(3)
                ->get();


            /*
            |--------------------------------------------------------------------------
            | WORKLOAD DISTRIBUTION (TOP 5 USERS)
            |--------------------------------------------------------------------------
            */

            $workload = User::withCount([
                'tasks as active_tasks_count' => function ($query) {
                    $query->whereNull('archived_at')
                        ->where('progress', '<', 100);
                },
                'tasks as overdue_tasks_count' => function ($query) use ($today) {
                    $query->whereNull('archived_at')
                        ->whereDate('due_date', '<', $today)
                        ->where('progress', '<', 100);
                }
            ])
                ->orderByDesc('active_tasks_count')
                ->limit(5)
                ->get();


            return view('dashboard.index', compact(
                'totalProjects',
                'ongoingProjects',
                'completedProjects',
                'overdueProjectsCount',

                'totalTasks',
                'ongoingTasks',
                'overdueTasksCount',

                'overdueProjects',
                'overdueTasks',

                'dueSoonProjects',
                'dueSoonTasks',

                'workload'
            ));
        }

        /*
|--------------------------------------------------------------------------
| USER DASHBOARD
|--------------------------------------------------------------------------
*/

        $today = Carbon::today();
        $nextWeek = Carbon::today()->addDays(7);

        $userTasksQuery = Task::with('project')
            ->where('assigned_user_id', $user->id)
            ->whereNull('archived_at');

        $userTasks = $userTasksQuery->get();

        /*
|--------------------------------------------------------------------------
| USER TASK STATS
|--------------------------------------------------------------------------
*/

        $userTotalTasks = $userTasks->count();

        $userCompletedTasks = $userTasks->where('progress', 100)->count();

        $userOngoingTasks = $userTasks->filter(function ($task) {
            return $task->progress > 0 && $task->progress < 100;
        })->count();

        $userOverdueTasksCount = $userTasks->filter(function ($task) use ($today) {
            return $task->due_date &&
                $task->due_date < $today &&
                $task->progress < 100;
        })->count();

        /*
|--------------------------------------------------------------------------
| TOP 5 USER OVERDUE TASKS
|--------------------------------------------------------------------------
*/

        $userOverdueTasks = $userTasks->filter(function ($task) use ($today) {
            return $task->due_date &&
                $task->due_date < $today &&
                $task->progress < 100;
        })
            ->sortBy('due_date')
            ->take(3);

        /*
|--------------------------------------------------------------------------
| TOP 5 USER TASKS DUE SOON
|--------------------------------------------------------------------------
*/

        $userDueSoonTasks = $userTasks->filter(function ($task) use ($today, $nextWeek) {
            return $task->due_date &&
                $task->due_date >= $today &&
                $task->due_date <= $nextWeek &&
                $task->progress < 100;
        })
            ->sortBy('due_date')
            ->take(3);

        /*
|--------------------------------------------------------------------------
| USER ASSIGNED PROJECTS (Top 5)
|--------------------------------------------------------------------------
*/

        $userProjects = $userTasks->pluck('project')
            ->filter()
            ->unique('id')
            ->take(5);

        return view('dashboard.index', compact(
            'userTotalTasks',
            'userCompletedTasks',
            'userOngoingTasks',
            'userOverdueTasksCount',

            'userOverdueTasks',
            'userDueSoonTasks',
            'userProjects'
        ));
    }
}
