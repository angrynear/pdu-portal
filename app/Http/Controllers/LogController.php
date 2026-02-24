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

            // 👤 Filter by User
            if ($request->filled('user')) {
                $query->where('user_id', $request->user);
            }

            // 🏷 Filter by Action
            if ($request->filled('action')) {
                $query->where('action', $request->action);
            }

            // 🔍 Search by Project Name
            if ($request->filled('search')) {
                $query->whereHas('project', function ($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->search . '%');
                });
            }

            // 📅 Date Range
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

            // Only users with project logs
            $users = User::whereIn('id', function ($sub) {
                $sub->select('user_id')
                    ->from('project_activity_logs')
                    ->whereNotNull('user_id');
            })
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

            // -------------------------------------------------
            // Base Query (Visibility Only — No Filters)
            // -------------------------------------------------
            $baseQuery = TaskActivityLog::with(['task.project', 'user']);
            $query     = TaskActivityLog::with(['task.project', 'user']);

            if (!$currentUser->isAdmin()) {

                $baseQuery->whereHas('task.project.tasks', function ($q) use ($currentUser) {
                    $q->where('assigned_user_id', $currentUser->id);
                });

                $query->whereHas('task.project.tasks', function ($q) use ($currentUser) {
                    $q->where('assigned_user_id', $currentUser->id);
                });
            }

            // -------------------------------------------------
            // 🔍 Search
            // -------------------------------------------------
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

            // -------------------------------------------------
            // 👤 User filter
            // -------------------------------------------------
            if ($request->filled('user')) {
                $query->where('user_id', $request->user);
            }

            // -------------------------------------------------
            // 🏷 Action filter
            // -------------------------------------------------
            if ($request->filled('action')) {
                $query->where('action', $request->action);
            }

            // -------------------------------------------------
            // 📝 Task Type filter
            // -------------------------------------------------
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

            // -------------------------------------------------
            // 📅 Date Range
            // -------------------------------------------------
            if ($request->filled('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }

            if ($request->filled('date_to')) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }

            // -------------------------------------------------
            // Paginate
            // -------------------------------------------------
            $data = $query
                ->latest()
                ->paginate(10)
                ->appends($request->all());

            // =====================================================
            // USER DROPDOWN (FROM BASE QUERY — NO COLLAPSING)
            // =====================================================

            $visibleUserIds = $baseQuery->clone()
                ->pluck('user_id')
                ->filter()
                ->unique();

            $users = User::whereIn('id', $visibleUserIds)
                ->orderBy('name')
                ->get();

            // =====================================================
            // TASK TYPE DROPDOWN (RESPECT VISIBILITY)
            // =====================================================

            $existingTypes = $baseQuery->clone()
                ->join('tasks', 'task_activity_logs.task_id', '=', 'tasks.id')
                ->select('tasks.task_type')
                ->distinct()
                ->pluck('tasks.task_type');

            $taskTypes = collect();

            foreach ($standardTypes as $type) {
                if ($existingTypes->contains($type)) {
                    $taskTypes->push($type);
                }
            }

            $hasCustom = $existingTypes
                ->diff($standardTypes)
                ->isNotEmpty();

            if ($hasCustom) {
                $taskTypes->push('Custom');
            }
        }

        return view('logs.index', compact(
            'data',
            'scope',
            'users',
            'taskTypes'
        ));
    }
}
