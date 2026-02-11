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


class TaskController extends Controller
{

    public function index()
    {
        $tasks = Task::with(['project', 'assignedUser'])
            ->whereNull('archived_at')
            ->latest()
            ->get();

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

            'start_date'         => ['required', 'date'],
            'due_date'           => ['required', 'date', 'after_or_equal:start_date'],
        ]);

        // Resolve final task type
        $taskType = $validated['task_type_select'] === 'Custom'
            ? $validated['custom_task_name']
            : $validated['task_type_select'];

        Task::create([
            'project_id'        => $validated['project_id'],
            'task_type'         => $taskType,
            'assigned_user_id'  => $validated['assigned_user_id'],
            'start_date'        => $validated['start_date'],
            'due_date'          => $validated['due_date'],
            'progress'          => 0,
            'created_by'        => auth()->id(),
        ]);

        return back()
            ->with('success', FlashMessage::success('task_created'));
    }

    public function archive(Task $task)
    {
        // Prevent archiving task if parent project is archived
        if ($task->project->archived_at !== null) {
            abort(403, 'Cannot archive task under an archived project.');
        }

        $task->update([
            'archived_at' => now(),
        ]);

        return back()->with('success', FlashMessage::success('task_archived'));
    }

    public function archived()
    {
        $tasks = Task::with(['project', 'assignedUser'])
            ->whereNotNull('archived_at')
            ->latest('archived_at')
            ->get();

        return view('archives.tasks', compact('tasks'));
    }

    public function restore(Task $task)
    {
        // Prevent restoring task if parent project is archived
        if ($task->project->archived_at) {
            abort(403, 'Cannot restore task while its project is archived.');
        }

        $task->update([
            'archived_at' => null,
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
        ]);

        DB::transaction(function () use ($request) {

            $task = Task::findOrFail($request->task_id);

            // Safety check
            if ($task->archived_at || $task->project->archived_at) {
                abort(403, 'Cannot update archived task.');
            }

            // Update progress on TASK table
            $task->update([
                'progress' => $request->progress,
            ]);

            // Save remark ONLY if provided
            if ($request->filled('remark')) {
                $remark = $task->remarks()->create([
                    'remark'  => $request->remark,
                    'user_id' => auth()->id(),
                ]);

                // Save attachments if any
                if ($request->hasFile('attachments')) {
                    foreach ($request->file('attachments') as $file) {
                        $path = $file->store('task_attachments', 'public');

                        $remark->files()->create([
                            'file_path' => $path,
                            'file_name' => $file->getClientOriginalName(),
                        ]);
                    }
                }
            }
        });

        return redirect()
            ->route('tasks.index')
            ->with('success', FlashMessage::success('task_progress_updated'));
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

        $request->validate([
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

        return back()->with('success', FlashMessage::success('task_updated'));
    }
}
