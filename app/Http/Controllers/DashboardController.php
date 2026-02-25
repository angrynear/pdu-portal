<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user  = auth()->user();
        $scope = $request->get('scope', $user->isAdmin() ? 'all' : 'my');

        /*
        |--------------------------------------------------------------------------
        | SYSTEM DASHBOARD (ADMIN - ALL)
        |--------------------------------------------------------------------------
        */

        if ($user->isAdmin() && $scope === 'all') {

            $today = Carbon::today();

            /*
            |--------------------------------------------------------------------------
            | PROJECT STATS
            |--------------------------------------------------------------------------
            */

            $projectsQuery = Project::active();

            $totalProjects        = (clone $projectsQuery)->count();
            $completedProjects    = (clone $projectsQuery)->completed()->count();
            $ongoingProjects      = (clone $projectsQuery)->ongoing()->count();
            $overdueProjectsCount = (clone $projectsQuery)->overdue()->count();

            /*
            |--------------------------------------------------------------------------
            | TASK STATS
            |--------------------------------------------------------------------------
            */

            $tasksQuery = Task::active();

            $totalTasks        = (clone $tasksQuery)->count();
            $completedTasks    = (clone $tasksQuery)->completed()->count();
            $ongoingTasks      = (clone $tasksQuery)->ongoing()->count();
            $overdueTasksCount = (clone $tasksQuery)->overdue()->count();

            /*
            |--------------------------------------------------------------------------
            | SUMMARIES
            |--------------------------------------------------------------------------
            */

            $overdueProjects = Project::active()
                ->overdue()
                ->orderBy('due_date')
                ->limit(3)
                ->get();

            $overdueTasks = Task::active()
                ->overdue()
                ->with(['project', 'assignedUser'])
                ->orderBy('due_date')
                ->limit(3)
                ->get();

            $dueSoonProjects = Project::active()
                ->dueSoon()
                ->orderBy('due_date')
                ->limit(3)
                ->get();

            $dueSoonTasks = Task::active()
                ->dueSoon()
                ->with(['project', 'assignedUser'])
                ->orderBy('due_date')
                ->limit(3)
                ->get();

            /*
            |--------------------------------------------------------------------------
            | WORKLOAD DISTRIBUTION
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
                'scope',

                'totalProjects',
                'completedProjects',
                'ongoingProjects',
                'overdueProjectsCount',

                'totalTasks',
                'completedTasks',
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
        | MY DASHBOARD (ADMIN scope=my OR NORMAL USER)
        |--------------------------------------------------------------------------
        */

        return $this->buildMyDashboard($user, $scope);
    }

    /*
    |--------------------------------------------------------------------------
    | SHARED MY DASHBOARD LOGIC
    |--------------------------------------------------------------------------
    */

    private function buildMyDashboard($user, $scope)
    {
        $tasksQuery = Task::active()->assignedTo($user->id);

        $userTotalTasks        = (clone $tasksQuery)->count();
        $userCompletedTasks    = (clone $tasksQuery)->completed()->count();
        $userOngoingTasks      = (clone $tasksQuery)->ongoing()->count();
        $userOverdueTasksCount = (clone $tasksQuery)->overdue()->count();

        $userOverdueTasks = (clone $tasksQuery)
            ->overdue()
            ->with('project')
            ->orderBy('due_date')
            ->limit(3)
            ->get();

        $userDueSoonTasks = (clone $tasksQuery)
            ->dueSoon()
            ->with('project')
            ->orderBy('due_date')
            ->limit(3)
            ->get();

        $userProjects = (clone $tasksQuery)
            ->with('project')
            ->get()
            ->pluck('project')
            ->filter()
            ->unique('id')
            ->take(5);

        return view('dashboard.index', compact(
            'scope',

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