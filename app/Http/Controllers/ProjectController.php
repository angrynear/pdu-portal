<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use App\Models\User;
use App\Support\FlashMessage;
use App\Models\ProjectActivityLog;
use App\Models\TaskActivityLog;
use Illuminate\Support\Facades\DB;


class ProjectController extends Controller
{
    /**
     * Display a listing of active projects.
     */
    public function index(Request $request)
    {
        $filter = $request->get('filter', 'all');

        $query = Project::whereNull('archived_at')
            ->withCount([

                // Total active tasks
                'tasks as total_tasks_count' => function ($q) {
                    $q->whereNull('archived_at');
                },

                // Completed tasks
                'tasks as completed_tasks_count' => function ($q) {
                    $q->where('progress', 100)
                        ->whereNull('archived_at');
                },

                // Started tasks (progress > 0)
                'tasks as started_tasks_count' => function ($q) {
                    $q->where('progress', '>', 0)
                        ->whereNull('archived_at');
                },

            ]);

        /*
    |--------------------------------------------------------------------------
    | FILTER LOGIC
    |--------------------------------------------------------------------------
    */

        if ($filter === 'not_started') {

            $query->whereDoesntHave('tasks', function ($q) {
                $q->whereNull('archived_at')
                    ->where('progress', '>', 0);
            });
        } elseif ($filter === 'ongoing') {

            $query->whereDate('due_date', '>=', now())
                ->whereHas('tasks', function ($q) {
                    $q->whereNull('archived_at')
                        ->where('progress', '>', 0);
                })
                ->whereHas('tasks', function ($q) {
                    $q->whereNull('archived_at')
                        ->where('progress', '<', 100);
                });
        } elseif ($filter === 'completed') {

            $query->whereHas('tasks', function ($q) {
                $q->whereNull('archived_at');
            })
                ->whereDoesntHave('tasks', function ($q) {
                    $q->whereNull('archived_at')
                        ->where('progress', '<', 100);
                });
        } elseif ($filter === 'overdue') {

            $query->whereDate('due_date', '<', now())
                ->whereHas('tasks', function ($q) {
                    $q->whereNull('archived_at')
                        ->where('progress', '<', 100);
                });
        }

        /*
    |--------------------------------------------------------------------------
    | NON-ADMIN RESTRICTION
    |--------------------------------------------------------------------------
    */

        if (!auth()->user()->isAdmin()) {
            $query->whereHas('tasks', function ($q) {
                $q->where('assigned_user_id', auth()->id());
            });
        }

        $projects = $query->latest()
            ->paginate(10)
            ->withQueryString();

        return view('projects.index', compact('projects', 'filter'));
    }

    public function create()
    {
        return view('projects.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:projects,name' // prevents duplicate names
            ],
            'location' => ['required', 'string', 'max:255'],
            'sub_sector' => [
                'required',
                'in:basic_education,higher_education,madaris_education,technical_education,others'
            ],
            'source_of_fund' => [
                'required',
                'in:GAAB,QRF,TDIF,SDF,CF,SB,BEFF,ODA,LOCAL,FOR APPROVAL'
            ],
            'funding_year' => [
                'required',
                function ($attribute, $value, $fail) {
                    if ($value !== 'FOR APPROVAL' && !is_numeric($value)) {
                        $fail('Funding year must be a valid year or FOR APPROVAL.');
                    }
                }
            ],
            'amount' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
            'start_date' => ['required', 'date'],
            'due_date' => ['required', 'date', 'after_or_equal:start_date'],
        ]);

        $project = Project::create($validated);

        ProjectActivityLog::create([
            'project_id' => $project->id,
            'user_id' => auth()->id(),
            'action' => 'created',
            'description' => 'Project created',
        ]);

        return redirect()
            ->route('projects.index')
            ->with('success', FlashMessage::success('project_created'));
    }


    public function show(Project $project)
    {
        // SECURITY: Normal users can only access related projects
        if (!auth()->user()->isAdmin()) {

            $hasAssignedTask = $project->tasks()
                ->where('assigned_user_id', auth()->id())
                ->exists();

            if (!$hasAssignedTask) {
                abort(403);
            }
        }

        $project->load([
            'tasks.assignedUser',
        ])->loadCount([
            'tasks as total_tasks_count',
            'tasks as completed_tasks_count' => function ($q) {
                $q->where('progress', 100);
            },
        ]);

        $users = User::where('account_status', 'active')
            ->orderBy('name')
            ->get();

        return view('projects.show', compact('project', 'users'));
    }

    public function edit(Project $project)
    {
        // SECURITY: Only admins can edit projects
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        return view('projects.edit', compact('project'));
    }

    public function update(Request $request, Project $project)
    {
        // SECURITY: Only admins can update projects
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'location' => ['required', 'string', 'max:255'],
            'sub_sector' => [
                'required',
                'in:basic_education,higher_education,madaris_education,technical_education,others'
            ],
            'source_of_fund' => [
                'required',
                'in:GAAB,QRF,TDIF,SDF,CF,SB,BEFF,ODA,LOCAL,FOR APPROVAL'
            ],
            'funding_year' => [
                'required',
                function ($attribute, $value, $fail) {
                    if ($value !== 'FOR APPROVAL' && !is_numeric($value)) {
                        $fail('Funding year must be a valid year or FOR APPROVAL.');
                    }
                }
            ],
            'amount' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
            'start_date' => ['required', 'date'],
            'due_date' => ['required', 'date', 'after_or_equal:start_date'],
        ]);

        $original = $project->getOriginal();
        $changes = [];

        foreach ($validated as $field => $newValue) {

            $oldValue = $original[$field] ?? null;

            if (in_array($field, ['start_date', 'due_date'])) {
                $oldValue = $oldValue ? \Carbon\Carbon::parse($oldValue)->format('Y-m-d') : null;
                $newValue = $newValue ? \Carbon\Carbon::parse($newValue)->format('Y-m-d') : null;
            }

            if ((string) $oldValue !== (string) $newValue) {
                $changes[$field] = [
                    'old' => $oldValue,
                    'new' => $newValue,
                ];
            }
        }

        // PHASE 6.3 ADDITION
        if (empty($changes)) {
            return redirect()
                ->route('projects.index')
                ->with('warning', FlashMessage::warning('project_updated'));
        }

        //  Only update if changes exist
        $project->update($validated);

        ProjectActivityLog::create([
            'project_id' => $project->id,
            'user_id' => auth()->id(),
            'action' => 'updated',
            'description' => 'Project details updated',
            'changes' => $changes,
        ]);

        return redirect()
            ->route('projects.index')
            ->with('success', FlashMessage::success('project_updated'));
    }

    public function archive(Project $project)
    {
        // SECURITY: Only admins can archive projects
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        if ($project->archived_at) {
            return back();
        }

        DB::transaction(function () use ($project) {

            // Archive project
            $project->update([
                'archived_at' => now(),
            ]);

            ProjectActivityLog::create([
                'project_id' => $project->id,
                'user_id' => auth()->id(),
                'action' => 'archived',
                'description' => 'Project archived',
            ]);

            // Archive each task individually
            foreach ($project->tasks as $task) {

                if (!$task->archived_at) {

                    $task->update([
                        'archived_at' => now(),
                    ]);

                    TaskActivityLog::create([
                        'task_id' => $task->id,
                        'user_id' => auth()->id(),
                        'action'  => 'archived',
                        'description' => 'Task archived due to project archive',
                    ]);
                }
            }
        });

        return back()->with('success', FlashMessage::success('project_archived'));
    }

    public function archived()
    {
        $projects = Project::whereNotNull('archived_at')
            ->latest('archived_at')
            ->paginate(10)
            ->withQueryString();

        return view('archives.projects', compact('projects'));
    }

    public function restore($id)
    {
        $project = Project::whereNotNull('archived_at')
            ->findOrFail($id);

        $project->update([
            'archived_at' => null,
        ]);

        ProjectActivityLog::create([
            'project_id' => $project->id,
            'user_id' => auth()->id(),
            'action' => 'restored',
            'description' => 'Project restored',
        ]);


        return redirect()
            ->route('projects.archived')
            ->with('success', FlashMessage::success('project_restored'));
    }

    public function activityLogs()
    {
        $query = ProjectActivityLog::with(['project', 'user'])
            ->latest();

        if (!auth()->user()->isAdmin()) {
            $query->whereHas('project.tasks', function ($q) {
                $q->where('assigned_user_id', auth()->id());
            });
        }

        $logs = $query->paginate(20)->withQueryString();

        return view('logs.projects', compact('logs'));
    }

    public function myProjects()
    {
        $userId = auth()->id();

        $projects = Project::whereNull('archived_at')
            ->whereHas('tasks', function ($q) use ($userId) {
                $q->where('assigned_user_id', $userId);
            })
            ->withCount([
                'tasks as total_tasks_count' => function ($q) {
                    $q->whereNull('archived_at');
                },
                'tasks as completed_tasks_count' => function ($q) {
                    $q->where('progress', 100)
                        ->whereNull('archived_at');
                }
            ])
            ->latest()
            ->paginate(10);

        return view('projects.index', compact('projects'));
    }
}
