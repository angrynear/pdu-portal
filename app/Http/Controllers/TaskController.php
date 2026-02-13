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

        return view('tasks.index', compact('tasks'));
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

        // 🔒 ENFORCE PROJECT DATE RANGE
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
            'start_date' => 'nullable|date',
            'due_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $task = Task::findOrFail($request->task_id);

        // If task has no dates yet AND user didn't set them now → block progress update
        if (
            (!$task->start_date || !$task->due_date) &&
            (!$request->start_date || !$request->due_date)
        ) {
            return back()->with(
                'error',
                'Please set task start and due dates before updating progress.'
            );
        }

        if ($task->archived_at || $task->project->archived_at) {
            abort(403, 'Cannot update archived task.');
        }

        $progressChanged = (int)$task->progress !== (int)$request->progress;
        $hasRemark = $request->filled('remark');
        $hasFiles = $request->hasFile('attachments');

        if (!$progressChanged && !$hasRemark && !$hasFiles) {
            return back()->with(
                'warning',
                FlashMessage::warning('task_update_no_changes')
            );
        }

        DB::transaction(function () use ($request, $task, $progressChanged, $hasRemark, $hasFiles) {

            $changes = [];

            // ===== PROGRESS =====
            if ($progressChanged) {
                $changes['progress'] = [
                    'old' => $task->progress,
                    'new' => (int) $request->progress,
                ];

                $task->update([
                    'progress' => $request->progress,
                ]);
            }

            // ===== DATE =====
            if ($request->filled('start_date')) {
                $task->start_date = $request->start_date;
            }

            if ($request->filled('due_date')) {
                $task->due_date = $request->due_date;
            }

            // ===== REMARK =====
            if ($hasRemark) {
                $changes['remark'] = [
                    'old' => optional($task->latestRemark)->remark,
                    'new' => $request->remark,
                ];
            }

            // ===== CREATE TASK REMARK RECORD =====
            $remarkData = [
                'task_id' => $task->id,
                'user_id' => auth()->id(),
            ];

            if ($hasRemark) {
                $remarkData['remark'] = $request->remark;
            }

            if ($progressChanged) {
                $remarkData['progress'] = $request->progress;
            }

            $remark = TaskRemark::create($remarkData);

            // ===== FILES =====
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

            // ===== CREATE ACTIVITY LOG =====
            TaskActivityLog::create([
                'task_id'    => $task->id,
                'user_id'    => auth()->id(),
                'action'     => 'updated',
                'description' => 'Task updated',
                'changes'    => $changes,   // ← THIS IS THE KEY FIX
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

        $taskType = $request->task_type_select === 'Custom'
            ? $request->custom_task_name
            : $request->task_type_select;

        $task = Task::findOrFail($request->task_id);

        if ($task->archived_at || $task->project->archived_at) {
            abort(403);
        }

        $task->update([
            'task_type' => $taskType,
            'assigned_user_id' => $request->assigned_user_id,
            'start_date' => $request->start_date,
            'due_date' => $request->due_date,
        ]);

        TaskActivityLog::create([
            'task_id' => $task->id,
            'user_id' => auth()->id(),
            'action'  => 'details_updated',
            'description' => 'Task details updated',
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

        $task->update([
            'start_date' => $request->start_date,
            'due_date' => $request->due_date,
        ]);

        return back()->with('success', 'Task dates successfully set.');
    }
}
