<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProjectActivityLog;
use App\Models\TaskActivityLog;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class LogController extends Controller
{
    public function index(Request $request)
    {
        $currentUser = auth()->user();

        $allowedScopes = ['projects', 'tasks'];

        $scope = in_array($request->scope, $allowedScopes)
            ? $request->scope
            : 'projects';

        $users = collect();
        $taskTypes = collect();
        $availableActions = collect();

        // =====================================================
        // PROJECT LOGS
        // =====================================================
        if ($scope === 'projects') {

            $query = ProjectActivityLog::with(['project', 'user']);

            if (!$currentUser->isAdmin()) {
                $query->whereHas('project.tasks', function ($q) use ($currentUser) {
                    $q->where('assigned_user_id', $currentUser->id);
                });
            }

            /*
|--------------------------------------------------------------------------
| MAIN DATA QUERY (All Filters Applied)
|--------------------------------------------------------------------------
*/

            $mainQuery = clone $query;

            if ($request->filled('search')) {
                $mainQuery->whereHas('project', function ($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->search . '%');
                });
            }

            if ($request->filled('user')) {
                $mainQuery->where('user_id', $request->user);
            }

            if ($request->filled('action')) {
                $mainQuery->where('action', $request->action);
            }

            if ($request->filled('date_from')) {
                $mainQuery->whereDate('created_at', '>=', $request->date_from);
            }

            if ($request->filled('date_to')) {
                $mainQuery->whereDate('created_at', '<=', $request->date_to);
            }

            $data = $mainQuery
                ->latest()
                ->paginate(10)
                ->appends($request->all());

            /*
|--------------------------------------------------------------------------
| ACTION DROPDOWN (Ignore action)
|--------------------------------------------------------------------------
*/

            $actionQuery = clone $query;

            $this->applyProjectFilters($actionQuery, $request, 'action');

            $availableActions = $actionQuery
                ->distinct()
                ->pluck('action')
                ->sort()
                ->values();

            /*
|--------------------------------------------------------------------------
| USER DROPDOWN (Ignore user)
|--------------------------------------------------------------------------
*/

            $userQuery = clone $query;

            $this->applyProjectFilters($userQuery, $request, 'user');

            $visibleUserIds = $userQuery
                ->distinct()
                ->pluck('user_id')
                ->filter()
                ->unique();

            $users = User::whereIn('id', $visibleUserIds)
                ->orderBy('name')
                ->get();
        }

        // =====================================================
        // TASK LOGS
        // =====================================================
        else {

            $standardTypes = [
                'Perspective',
                'Architectural',
                'Structural',
                'Mechanical',
                'Electrical',
                'Plumbing'
            ];

            $baseQuery = TaskActivityLog::with(['task.project', 'user']);

            if (!$currentUser->isAdmin()) {
                $baseQuery->whereHas('task.project.tasks', function ($q) use ($currentUser) {
                    $q->where('assigned_user_id', $currentUser->id);
                });
            }

            /*
|--------------------------------------------------------------------------
| MAIN DATA QUERY (All Filters Applied)
|--------------------------------------------------------------------------
*/

            $query = clone $baseQuery;

            if ($request->filled('search')) {
                $query->where(function ($q) use ($request) {
                    $q->whereHas('task', function ($t) use ($request) {
                        $t->where('task_type', 'like', '%' . $request->search . '%');
                    })
                        ->orWhereHas('task.project', function ($p) use ($request) {
                            $p->where('name', 'like', '%' . $request->search . '%');
                        });
                });
            }

            if ($request->filled('user')) {
                $query->where('user_id', $request->user);
            }

            if ($request->filled('action')) {
                $query->where('action', $request->action);
            }

            if ($request->filled('task_type')) {

                if ($request->task_type === 'Custom') {

                    $query->whereHas('task', function ($q) use ($standardTypes) {
                        $q->whereNotIn('task_type', $standardTypes);
                    });
                } else {

                    $query->whereHas('task', function ($q) use ($request) {
                        $q->where('task_type', $request->task_type);
                    });
                }
            }

            if ($request->filled('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }

            if ($request->filled('date_to')) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }

            $data = $query
                ->latest()
                ->paginate(10)
                ->appends($request->all());

            /*
|--------------------------------------------------------------------------
| ACTION DROPDOWN (Ignore action filter)
|--------------------------------------------------------------------------
*/

            $actionQuery = clone $baseQuery;

            $this->applyCommonFilters($actionQuery, $request, $standardTypes, 'action');

            $availableActions = $actionQuery
                ->distinct()
                ->pluck('action')
                ->sort()
                ->values();

            /*
|--------------------------------------------------------------------------
| USER DROPDOWN (Ignore user filter)
|--------------------------------------------------------------------------
*/

            $userQuery = clone $baseQuery;

            $this->applyCommonFilters($userQuery, $request, $standardTypes, 'user');

            $visibleUserIds = $userQuery
                ->distinct()
                ->pluck('user_id')
                ->filter()
                ->unique();

            $users = User::whereIn('id', $visibleUserIds)
                ->orderBy('name')
                ->get();

            /*
|--------------------------------------------------------------------------
| TASK TYPE DROPDOWN (Ignore task_type filter)
|--------------------------------------------------------------------------
*/

            $typeQuery = clone $baseQuery;

            $this->applyCommonFilters($typeQuery, $request, $standardTypes, 'tas_type');

            $existingTypes = $typeQuery
                ->join('tasks', 'task_activity_logs.task_id', '=', 'tasks.id')
                ->select('tasks.task_type')
                ->distinct()
                ->pluck('tasks.task_type');

            foreach ($standardTypes as $type) {
                if ($existingTypes->contains($type)) {
                    $taskTypes->push($type);
                }
            }

            if ($existingTypes->diff($standardTypes)->isNotEmpty()) {
                $taskTypes->push('Custom');
            }
        }

        // =====================================================
        // AJAX RESPONSE
        // =====================================================
        if ($request->ajax()) {

            return response()->json([
                'desktopFilters' => view('logs.partials.filters.desktop', compact(
                    'scope',
                    'users',
                    'taskTypes',
                    'availableActions'
                ))->render(),

                'mobileFilters' => view('logs.partials.filters.mobile', compact(
                    'scope',
                    'users',
                    'taskTypes',
                    'availableActions'
                ))->render(),

                'cards' => view(
                    $scope === 'projects'
                        ? 'logs.partials.project-cards'
                        : 'logs.partials.task-cards',
                    compact('data')
                )->render(),
            ]);
        }

        // =====================================================
        // NORMAL RESPONSE
        // =====================================================
        return view('logs.index', compact(
            'data',
            'scope',
            'users',
            'taskTypes',
            'availableActions'
        ));
    }

    private function applyCommonFilters($query, $request, $standardTypes, $ignore = null)
    {
        if ($ignore !== 'search' && $request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->whereHas('task', function ($t) use ($request) {
                    $t->where('task_type', 'like', '%' . $request->search . '%');
                })
                    ->orWhereHas('task.project', function ($p) use ($request) {
                        $p->where('name', 'like', '%' . $request->search . '%');
                    });
            });
        }

        if ($ignore !== 'user' && $request->filled('user')) {
            $query->where('user_id', $request->user);
        }

        if ($ignore !== 'action' && $request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($ignore !== 'task_type' && $request->filled('task_type')) {

            if ($request->task_type === 'Custom') {

                $query->whereHas('task', function ($q) use ($standardTypes) {
                    $q->whereNotIn('task_type', $standardTypes);
                });
            } else {

                $query->whereHas('task', function ($q) use ($request) {
                    $q->where('task_type', $request->task_type);
                });
            }
        }

        if ($ignore !== 'date_from' && $request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($ignore !== 'date_to' && $request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        return $query;
    }

    private function applyProjectFilters($query, $request, $ignore = null)
    {
        if ($ignore !== 'search' && $request->filled('search')) {
            $query->whereHas('project', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            });
        }

        if ($ignore !== 'user' && $request->filled('user')) {
            $query->where('user_id', $request->user);
        }

        if ($ignore !== 'action' && $request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($ignore !== 'date_from' && $request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($ignore !== 'date_to' && $request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        return $query;
    }
}
