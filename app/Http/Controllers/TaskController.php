<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\TaskRemark;
use App\Models\TaskFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Support\FlashMessage;
use App\Models\TaskActivityLog;
use App\Models\User;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $scope = $request->get('scope', 'all');
        $user  = auth()->user();

        $baseQuery = Task::active();

        /*
    |--------------------------------------------------------------------------
    | Scope Handling
    |--------------------------------------------------------------------------
    | - Normal users → always see only their tasks
    | - Admin + scope=my → see only their tasks
    | - Admin + scope=all → see all tasks
    */

        if (!$user->isAdmin() || $scope === 'my') {
            $baseQuery->assignedTo($user->id);
        }

        return $this->buildTaskIndex($baseQuery, $request);
    }

    public function store(Request $request)
    {
        $project = null;

        if (!empty($validated['project_id'])) {
            $project = Project::findOrFail($validated['project_id']);

            if ($project->archived_at) {
                abort(403, 'Cannot add tasks to an archived project.');
            }
        }

        $validated = $request->validate([
            'form_context'      => ['required'],
            'project_id'        => 'nullable|exists:projects,id',
            'task_type_select'  => ['required', 'string'],
            'custom_task_name'  => [
                'required_if:task_type_select,Custom',
                'nullable',
                'string',
                'max:255',
            ],
            'assigned_user_id'   => ['required', 'exists:users,id'],
            'start_date'         => ['nullable', 'date'],
            'due_date'           => ['nullable', 'date', 'after_or_equal:start_date'],
        ]);

        // ENFORCE PROJECT DATE RANGE
        if ($project && !empty($validated['start_date']) && $validated['start_date'] < $project->start_date) {
            return back()
                ->withErrors(['start_date' => 'Task start date cannot be before project start date.'])
                ->withInput();
        }

        if ($project && !empty($validated['due_date']) && $validated['due_date'] > $project->due_date) {
            return back()
                ->withErrors(['due_date' => 'Task due date cannot exceed project due date.'])
                ->withInput();
        }

        $taskType = $validated['task_type_select'] === 'Custom'
            ? $validated['custom_task_name']
            : $validated['task_type_select'];

        $task = Task::create([
            'project_id'        => $validated['project_id'],
            'task_type'         => $taskType,
            'assigned_user_id'  => $validated['assigned_user_id'],
            'start_date'        => $validated['start_date'] ?? null,
            'due_date'          => $validated['due_date'] ?? null,
            'progress'          => 0,
            'created_by'        => auth()->id(),
        ]);

        TaskActivityLog::create([
            'task_id'   => $task->id,
            'user_id'   => auth()->id(),
            'action'    => 'created',
            'description' => 'Task created',
        ]);

        if ($project) {
            $project->recalculateProgress();
        }

        return back()->with('success', FlashMessage::success('task_created'));
    }


    public function archive(Task $task)
    {

        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        if ($task->project && $task->project->archived_at !== null) {
            abort(403, 'Cannot archive task under an archived project.');
        }

        $task->update(['archived_at' => now()]);

        TaskActivityLog::create([
            'task_id' => $task->id,
            'user_id' => auth()->id(),
            'action'  => 'archived',
            'description' => 'Task archived',
        ]);

        if ($task->project) {
            $task->project->recalculateProgress();
        }

        return back()->with('success', FlashMessage::success('task_archived'));
    }

    public function archived()
    {
        $tasks = Task::with(['project', 'assignedUser'])
            ->whereNotNull('archived_at')
            ->latest('archived_at')
            ->paginate(20)
            ->withQueryString();

        return view('archives.tasks', compact('tasks'));
    }

    public function restore(Task $task)
    {
        if ($task->project && $task->project->archived_at) {
            abort(403, 'Cannot restore task while its project is archived.');
        }

        $task->update(['archived_at' => null]);

        TaskActivityLog::create([
            'task_id' => $task->id,
            'user_id' => auth()->id(),
            'action'  => 'restored',
            'description' => 'Task restored',
        ]);

        if ($task->project) {
            $task->project->recalculateProgress();
        }

        return redirect()
            ->route('archives.index', ['scope' => 'tasks'])
            ->with('success', FlashMessage::success('task_restored'));
    }

    public function updateProgress(Request $request)
    {

        $request->validate([
            'task_id'   => ['required', 'exists:tasks,id'],
            'progress'  => ['required', 'integer', 'min:0', 'max:100'],
            'remark'    => ['nullable', 'string'],
            'attachments.*' => ['nullable', 'file', 'max:5120'],
            'start_date' => ['nullable', 'date'],
            'due_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ]);

        $task = Task::findOrFail($request->task_id);

        if ($task->archived_at || ($task->project && $task->project->archived_at)) {
            abort(403, 'Cannot update archived task.');
        }

        // Only assigned user or admin can update progress
        if (!auth()->user()->isAdmin() && $task->assigned_user_id !== auth()->id()) {
            abort(403);
        }

        $project = $task->project;

        $newProgress = (int)$request->progress;
        $newRemark   = trim($request->remark ?? '');
        $newStart    = $request->start_date ?: null;
        $newDue      = $request->due_date ?: null;

        $oldStart = $task->start_date ? $task->start_date->format('Y-m-d') : null;
        $oldDue   = $task->due_date ? $task->due_date->format('Y-m-d') : null;

        // Enforce project date range
        if ($project && $newStart && $newStart < $project->start_date) {
            return back()->withErrors([
                'start_date' => 'Task start date cannot be before project start date.'
            ]);
        }

        if ($project && $newDue && $newDue > $project->due_date) {
            return back()->withErrors([
                'due_date' => 'Task due date cannot exceed project due date.'
            ]);
        }

        $progressChanged = ((int)$task->progress !== $newProgress);
        $remarkChanged   = ($newRemark !== '');
        $dateChanged     = ($oldStart !== $newStart) || ($oldDue !== $newDue);
        $hasFiles        = $request->hasFile('attachments');

        if (!$progressChanged && !$remarkChanged && !$dateChanged && !$hasFiles) {
            return back()->with(
                'warning',
                FlashMessage::warning('task_progress_updated')
            );
        }

        DB::transaction(function () use (
            $task,
            $project,
            $newProgress,
            $newRemark,
            $newStart,
            $newDue,
            $progressChanged,
            $remarkChanged,
            $dateChanged,
            $hasFiles,
            $request,
            $oldStart,
            $oldDue
        ) {

            $changes = [];

            // ===== PROGRESS =====
            if ($progressChanged) {
                $changes['progress'] = [
                    'old' => $task->progress,
                    'new' => $newProgress,
                ];
                $task->progress = $newProgress;
            }

            // ===== DATES =====
            if ($dateChanged) {

                if ($oldStart !== $newStart) {
                    $changes['start_date'] = [
                        'old' => $oldStart,
                        'new' => $newStart,
                    ];
                    $task->start_date = $newStart;
                }

                if ($oldDue !== $newDue) {
                    $changes['due_date'] = [
                        'old' => $oldDue,
                        'new' => $newDue,
                    ];
                    $task->due_date = $newDue;
                }
            }

            // not sure //
            if ($progressChanged || $dateChanged) {
                $task->save();
                if ($project) {
                    $project->recalculateProgress();
                }
            }

            // ===== REMARK =====
            if ($remarkChanged) {
                $changes['remark'] = [
                    'old' => null,
                    'new' => $newRemark,
                ];
            }

            // ===== CREATE ACTIVITY LOG FIRST =====
            $log = TaskActivityLog::create([
                'task_id' => $task->id,
                'user_id' => auth()->id(),
                'action'  => 'updated',
                'description' => 'Task updated',
                'changes' => $changes,
            ]);

            // ===== FILES =====
            if ($hasFiles) {

                foreach ($request->file('attachments') as $file) {

                    $path = $file->store('task_attachments', 'public');

                    $log->files()->create([
                        'file_path'     => $path,
                        'original_name' => $file->getClientOriginalName(),
                    ]);
                }
            }
        });

        return back()->with(
            'success',
            FlashMessage::success('task_progress_updated')
        );
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'task_id' => ['required', 'exists:tasks,id'],
            'task_type_select' => ['required', 'string'],
            'custom_task_name' => [
                'required_if:task_type_select,Custom',
                'nullable',
                'string',
                'max:255',
            ],
            'assigned_user_id' => ['required', 'exists:users,id'],
            'start_date' => ['nullable', 'date'],
            'due_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('form_context', 'edit_task');
        }

        $task = Task::findOrFail($request->task_id);

        if ($task->archived_at || ($task->project && $task->project->archived_at)) {
            abort(403);
        }

        // Normalize values
        $taskType = $request->task_type_select === 'Custom'
            ? trim($request->custom_task_name ?? '')
            : trim($request->task_type_select);

        $newAssigned = (int)$request->assigned_user_id;

        $newStart = $request->start_date ?: null;
        $newDue   = $request->due_date ?: null;

        $oldStart = $task->start_date ? $task->start_date->format('Y-m-d') : null;
        $oldDue   = $task->due_date ? $task->due_date->format('Y-m-d') : null;

        $changes = [];

        // Compare strictly
        if ((string)$task->task_type !== (string)$taskType) {
            $changes['task_type'] = [
                'old' => $task->task_type,
                'new' => $taskType,
            ];
        }

        if ((int)$task->assigned_user_id !== $newAssigned) {
            $changes['assigned_user_id'] = [
                'old' => $task->assigned_user_id,
                'new' => $newAssigned,
            ];
        }

        if ($oldStart !== $newStart) {
            $changes['start_date'] = [
                'old' => $oldStart,
                'new' => $newStart,
            ];
        }

        if ($oldDue !== $newDue) {
            $changes['due_date'] = [
                'old' => $oldDue,
                'new' => $newDue,
            ];
        }

        if (empty($changes)) {
            return back()->with('warning', FlashMessage::warning('task_updated'));
        }

        $task->update([
            'task_type' => $taskType,
            'assigned_user_id' => $newAssigned,
            'start_date' => $newStart,
            'due_date' => $newDue,
        ]);

        TaskActivityLog::create([
            'task_id' => $task->id,
            'user_id' => auth()->id(),
            'action'  => 'details_updated',
            'description' => 'Task details updated',
            'changes' => $changes,
        ]);

        return back()->with('success', FlashMessage::success('task_updated'));
    }

    public function show(Request $request, Task $task)
    {
        $user = auth()->user();

        /*
    |--------------------------------------------------------------------------
    | SECURITY CHECK
    |--------------------------------------------------------------------------
    */

        if (!$user->isAdmin()) {

            if (!$task->project) {
                // Personal task
                if ($task->assigned_user_id !== $user->id) {
                    abort(403);
                }
            } else {
                // Project task
                $userId = $user->id;

                $isAssigned = $task->assigned_user_id === $userId;

                $isInRelatedProject = $task->project()
                    ->whereHas('tasks', function ($query) use ($userId) {
                        $query->where('assigned_user_id', $userId);
                    })
                    ->exists();

                if (!$isAssigned && !$isInRelatedProject) {
                    abort(403);
                }
            }
        }

        /*
    |--------------------------------------------------------------------------
    | LOAD MAIN RELATIONS
    |--------------------------------------------------------------------------
    */

        $task->load([
            'project',
            'assignedUser'
        ]);

        /*
    |--------------------------------------------------------------------------
    | LOAD ACTIVITY LOGS (EAGER LOADED)
    |--------------------------------------------------------------------------
    */

        $activityLogs = TaskActivityLog::with([
            'user',
            'files'
        ])
            ->where('task_id', $task->id)
            ->latest()
            ->paginate(10);

        /*
    |--------------------------------------------------------------------------
    | PRELOAD USERS USED IN assigned_user_id CHANGES
    |--------------------------------------------------------------------------
    */

        $userIds = collect($activityLogs->items())
            ->flatMap(function ($log) {
                return [
                    $log->changes['assigned_user_id']['old'] ?? null,
                    $log->changes['assigned_user_id']['new'] ?? null,
                ];
            })
            ->filter()
            ->unique()
            ->values();

        $changedUsers = \App\Models\User::whereIn('id', $userIds)
            ->pluck('name', 'id');

        $from = $request->query('from');

        return view('tasks.show', compact(
            'task',
            'activityLogs',
            'changedUsers',
            'from'
        ));
    }


    public function taskLogs()
    {
        $query = TaskActivityLog::with(['task.project', 'user'])
            ->latest();

        if (!auth()->user()->isAdmin()) {
            $query->whereHas('task', function ($q) {
                $q->where('assigned_user_id', auth()->id());
            });
        }

        $logs = $query->paginate(20);

        return view('logs.tasks', compact('logs'));
    }

    public function setDates(Request $request)
    {
        $request->validate([
            'task_id' => 'required|exists:tasks,id',
            'start_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:start_date',
        ]);

        $task = Task::findOrFail($request->task_id);
        $project = $task->project;

        // Only assigned user or admin can set dates
        if (!auth()->user()->isAdmin() && $task->assigned_user_id !== auth()->id()) {
            abort(403);
        }

        if ($project && $request->start_date < $project->start_date) {
            return back()->withErrors([
                'start_date' => 'Task start date cannot be before project start date.'
            ]);
        }

        if ($project && $request->due_date > $project->due_date) {
            return back()->withErrors([
                'due_date' => 'Task due date cannot exceed project due date.'
            ]);
        }

        if (
            $task->start_date == $request->start_date &&
            $task->due_date == $request->due_date
        ) {
            return back()->with('warning', FlashMessage::warning('task_set_dates'));
        }

        $task->update([
            'start_date' => $request->start_date,
            'due_date' => $request->due_date,
        ]);

        TaskActivityLog::create([
            'task_id' => $task->id,
            'user_id' => auth()->id(),
            'action'  => 'dates_set',
            'description' => 'Task dates updated',
            'changes' => [
                'start_date' => [
                    'old' => $task->getOriginal('start_date'),
                    'new' => $request->start_date
                ],
                'due_date' => [
                    'old' => $task->getOriginal('due_date'),
                    'new' => $request->due_date
                ],
            ]
        ]);

        return back()->with('success', FlashMessage::success('task_set_dates'));
    }

    public function assign(Request $request)
    {
        $request->validate([
            'task_id' => 'required|exists:tasks,id',
            'assigned_user_id' => 'required|exists:users,id'
        ]);

        $task = Task::findOrFail($request->task_id);



        if ((int)$task->assigned_user_id === (int)$request->assigned_user_id) {
            return back()->with('warning', FlashMessage::warning('task_re-assign'));
        }

        $oldUser = $task->assigned_user_id;

        $task->update([
            'assigned_user_id' => $request->assigned_user_id
        ]);

        TaskActivityLog::create([
            'task_id' => $task->id,
            'user_id' => auth()->id(),
            'action' => 'assigned',
            'description' => 'Task re-assigned to personnel',
            'changes' => [
                'assigned_user_id' => [
                    'old' => $oldUser,
                    'new' => $request->assigned_user_id
                ]
            ]
        ]);

        return back()->with('success', FlashMessage::success('task_re-assign'));
    }

    private function buildTaskIndex($baseQuery, Request $request)
    {
        $status     = $request->get('filter', 'all');
        $type       = $request->get('type');
        $personnel  = $request->get('personnel');

        $predefined = [
            'Perspective',
            'Architectural',
            'Structural',
            'Mechanical',
            'Electrical',
            'Plumbing'
        ];

        /*
    |--------------------------------------------------------------------------
    | BASE QUERY WITH PERSONNEL FILTER (if selected)
    |--------------------------------------------------------------------------
    */

        $filteredBase = clone $baseQuery;

        if (!empty($personnel)) {
            $filteredBase->where('assigned_user_id', $personnel);
        }

        /*
    |--------------------------------------------------------------------------
    | MAIN FILTERED QUERY (Personnel + Status + Type)
    |--------------------------------------------------------------------------
    */

        $query = clone $filteredBase;

        // STATUS FILTER
        if ($status === 'completed') {
            $query->completed();
        } elseif ($status === 'overdue') {
            $query->overdue();
        } elseif ($status === 'not_started') {
            $query->notStarted();
        } elseif ($status === 'ongoing') {
            $query->ongoing();
        } elseif ($status === 'due_soon') {
            $query->dueSoon();
        }

        // TYPE FILTER
        if ($type === 'Custom') {
            $query->whereNotIn('task_type', $predefined);
        } elseif (!empty($type)) {
            $query->where('task_type', $type);
        }

        $tasks = $query
            ->with(['project', 'assignedUser', 'activityLogs'])
            ->latest()
            ->paginate(20)
            ->withQueryString();

        /*
    |--------------------------------------------------------------------------
    | PRELOAD LATEST REMARK
    |--------------------------------------------------------------------------
    */

        foreach ($tasks as $task) {
            $task->latest_remark = null;

            foreach ($task->activityLogs as $log) {
                $remark = data_get($log->changes, 'remark.new');

                if (!empty($remark)) {
                    $task->latest_remark = $remark;
                    break;
                }
            }
        }

        /*
    |--------------------------------------------------------------------------
    | STATUS COUNTS (Respect Personnel + Type)
    |--------------------------------------------------------------------------
    */

        $statusCountQuery = clone $filteredBase;

        if ($type === 'Custom') {
            $statusCountQuery->whereNotIn('task_type', $predefined);
        } elseif (!empty($type)) {
            $statusCountQuery->where('task_type', $type);
        }

        $statusCounts = [
            'all' => (clone $statusCountQuery)->count(),
            'not_started' => (clone $statusCountQuery)->notStarted()->count(),
            'ongoing' => (clone $statusCountQuery)->ongoing()->count(),
            'completed' => (clone $statusCountQuery)->completed()->count(),
            'overdue' => (clone $statusCountQuery)->overdue()->count(),
            'due_soon' => (clone $statusCountQuery)->dueSoon()->count(),
        ];

        /*
    |--------------------------------------------------------------------------
    | TYPE COUNTS (Respect Personnel + Status)
    |--------------------------------------------------------------------------
    */

        $typeCountQuery = clone $filteredBase;

        if ($status === 'completed') {
            $typeCountQuery->where('progress', 100);
        } elseif ($status === 'overdue') {
            $typeCountQuery->where('progress', '<', 100)
                ->whereDate('due_date', '<', today());
        } elseif ($status === 'not_started') {
            $typeCountQuery->where('progress', 0)
                ->where(function ($q) {
                    $q->whereNull('due_date')
                        ->orWhereDate('due_date', '>=', today());
                });
        } elseif ($status === 'ongoing') {
            $typeCountQuery->whereBetween('progress', [1, 99])
                ->where(function ($q) {
                    $q->whereNull('due_date')
                        ->orWhereDate('due_date', '>=', today());
                });
        }

        $rawTypes = $typeCountQuery
            ->select('task_type')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('task_type')
            ->pluck('total', 'task_type')
            ->toArray();

        $taskTypes   = [];
        $customCount = 0;

        foreach ($rawTypes as $key => $value) {
            if (in_array($key, $predefined)) {
                $taskTypes[$key] = $value;
            } else {
                $customCount += $value;
            }
        }

        if ($customCount > 0) {
            $taskTypes['Custom'] = $customCount;
        }

        /*
    |--------------------------------------------------------------------------
    | PERSONNEL COUNTS (Respect Status + Type)
    |--------------------------------------------------------------------------
    */

        $personnelCountQuery = clone $baseQuery;

        // apply status filter
        if ($status === 'completed') {
            $personnelCountQuery->where('progress', 100);
        } elseif ($status === 'overdue') {
            $personnelCountQuery->where('progress', '<', 100)
                ->whereDate('due_date', '<', today());
        } elseif ($status === 'not_started') {
            $personnelCountQuery->where('progress', 0)
                ->where(function ($q) {
                    $q->whereNull('due_date')
                        ->orWhereDate('due_date', '>=', today());
                });
        } elseif ($status === 'ongoing') {
            $personnelCountQuery->whereBetween('progress', [1, 99])
                ->where(function ($q) {
                    $q->whereNull('due_date')
                        ->orWhereDate('due_date', '>=', today());
                });
        }

        // apply type filter
        if ($type === 'Custom') {
            $personnelCountQuery->whereNotIn('task_type', $predefined);
        } elseif (!empty($type)) {
            $personnelCountQuery->where('task_type', $type);
        }

        $personnelCounts = $personnelCountQuery
            ->select('assigned_user_id')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('assigned_user_id')
            ->pluck('total', 'assigned_user_id')
            ->toArray();

        $personnelList = User::whereIn('id', array_keys($personnelCounts))
            ->pluck('name', 'id')
            ->toArray();

        $totalTasksCount = $statusCounts['all'];

        $users = User::where('account_status', 'active')->get();

        return view('tasks.index', [
            'tasks'            => $tasks,
            'statusCounts'     => $statusCounts,
            'taskTypes'        => $taskTypes,
            'personnelCounts'  => $personnelCounts,
            'personnelList'    => $personnelList,
            'totalTasksCount'  => $totalTasksCount,
            'users'            => $users,
        ]);
    }
}
