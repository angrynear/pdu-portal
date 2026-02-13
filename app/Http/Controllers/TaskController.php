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
    public function index()
    {
        $tasks = Task::with([
            'project',
            'assignedUser',
            'latestRemark'
        ])
            ->whereNull('archived_at')
            ->paginate(20)
            ->withQueryString();

        $users = User::where('account_status', 'active')->get();

        return view('tasks.index', compact('tasks', 'users'));
    }

    public function store(Request $request)
    {
        $project = Project::findOrFail($request->project_id);

        if ($project->archived_at) {
            abort(403, 'Cannot add tasks to an archived project.');
        }

        $validated = $request->validate([
            'form_context'       => ['required'],
            'project_id'         => ['required', 'exists:projects,id'],
            'task_type_select'   => ['required', 'string'],
            'custom_task_name'   => [
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
        if (!empty($validated['start_date']) && $validated['start_date'] < $project->start_date) {
            return back()
                ->withErrors(['start_date' => 'Task start date cannot be before project start date.'])
                ->withInput();
        }

        if (!empty($validated['due_date']) && $validated['due_date'] > $project->due_date) {
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

        return back()->with('success', FlashMessage::success('task_created'));
    }


    public function archive(Task $task)
    {
        if ($task->project->archived_at !== null) {
            abort(403, 'Cannot archive task under an archived project.');
        }

        $task->update(['archived_at' => now()]);

        TaskActivityLog::create([
            'task_id' => $task->id,
            'user_id' => auth()->id(),
            'action'  => 'archived',
            'description' => 'Task archived',
        ]);

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
        if ($task->project->archived_at) {
            abort(403, 'Cannot restore task while its project is archived.');
        }

        $task->update(['archived_at' => null]);

        TaskActivityLog::create([
            'task_id' => $task->id,
            'user_id' => auth()->id(),
            'action'  => 'restored',
            'description' => 'Task restored',
        ]);

        return back()->with('success', FlashMessage::success('task_restored'));
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

        if ($task->archived_at || $task->project->archived_at) {
            abort(403, 'Cannot update archived task.');
        }

        // 🔒 Require dates before updating progress
        if (
            (!$task->start_date || !$task->due_date) &&
            (!$request->start_date || !$request->due_date)
        ) {
            return back()->with(
                'error',
                FlashMessage::error('task_progress_updated')
            );
        }

        $project = $task->project;

        // Normalize new values
        $newProgress = (int)$request->progress;
        $newRemark   = trim($request->remark ?? '');
        $newStart    = $request->start_date ?: null;
        $newDue      = $request->due_date ?: null;

        $oldStart = $task->start_date ? $task->start_date->format('Y-m-d') : null;
        $oldDue   = $task->due_date ? $task->due_date->format('Y-m-d') : null;

        // 🔒 Enforce project date range
        if ($newStart && $newStart < $project->start_date) {
            return back()->withErrors([
                'start_date' => 'Task start date cannot be before project start date.'
            ]);
        }

        if ($newDue && $newDue > $project->due_date) {
            return back()->withErrors([
                'due_date' => 'Task due date cannot exceed project due date.'
            ]);
        }

        // Detect changes strictly
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

            if ($progressChanged || $dateChanged) {
                $task->save();
            }

            // ===== REMARK OR FILE =====
            if ($remarkChanged || $progressChanged || $hasFiles) {

                $remarkData = [
                    'task_id' => $task->id,
                    'user_id' => auth()->id(),
                ];

                if ($remarkChanged) {
                    $changes['remark'] = [
                        'old' => optional($task->latestRemark)->remark,
                        'new' => $newRemark,
                    ];
                    $remarkData['remark'] = $newRemark;
                }

                if ($progressChanged) {
                    $remarkData['progress'] = $newProgress;
                }

                $remark = TaskRemark::create($remarkData);

                if ($hasFiles) {

                    $changes['files'] = [
                        'old' => null,
                        'new' => 'File(s) uploaded',
                    ];

                    foreach ($request->file('attachments') as $file) {

                        $path = $file->store('task_attachments', 'public');

                        $remark->files()->create([
                            'file_path'     => $path,
                            'original_name' => $file->getClientOriginalName(),
                        ]);
                    }
                }
            }

            // ===== ACTIVITY LOG =====
            TaskActivityLog::create([
                'task_id' => $task->id,
                'user_id' => auth()->id(),
                'action'  => 'updated',
                'description' => 'Task updated',
                'changes' => $changes,
            ]);
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

        if ($task->archived_at || $task->project->archived_at) {
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
        $task->load(['project', 'assignedUser']);

        $remarks = $task->remarks()
            ->with(['user', 'files'])
            ->latest()
            ->paginate(5);

        $activityLogs = TaskActivityLog::with('user')
            ->where('task_id', $task->id)
            ->latest()
            ->paginate(5);

        $from = $request->query('from');

        return view('tasks.show', compact(
            'task',
            'remarks',
            'activityLogs',
            'from'
        ));
    }

    public function taskLogs()
    {
        $logs = TaskActivityLog::with(['task.project', 'user'])
            ->latest()
            ->paginate(20);

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

        if ($request->start_date < $project->start_date) {
            return back()->withErrors([
                'start_date' => 'Task start date cannot be before project start date.'
            ]);
        }

        if ($request->due_date > $project->due_date) {
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
}
