<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use App\Models\User;
use App\Support\FlashMessage;
use App\Models\ProjectActivityLog;


class ProjectController extends Controller
{
    /**
     * Display a listing of active projects.
     */
    public function index()
    {
        $projects = Project::whereNull('archived_at')
            ->withCount([
                'tasks as total_tasks_count' => function ($query) {
                    $query->whereNull('archived_at');
                },
                'tasks as not_started_tasks_count' => function ($query) {
                    $query->where('progress', 0)
                        ->whereNull('archived_at');
                },
                'tasks as ongoing_tasks_count' => function ($query) {
                    $query->whereBetween('progress', [1, 99])
                        ->whereNull('archived_at');
                },
                'tasks as completed_tasks_count' => function ($query) {
                    $query->where('progress', 100)
                        ->whereNull('archived_at');
                },
            ])
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('projects.index', compact('projects'));
    }

    /**
     * Show the form for creating a new project.
     */
    public function create()
    {
        return view('projects.create');
    }

    /**
     * Store a newly created project.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'location' => ['required', 'string', 'max:255'],

            'sub_sector' => [
                'required',
                'in:basic_education,higher_education,madaris_education,technical_education,others'
            ],

            'source_of_fund' => [
                'required',
                'in:GAAB,QRF,TDIF,SDF,CF,SB,BEFF,ODA,LOCAL,For Approval'
            ],

            'funding_year' => [
                'required',
                function ($attribute, $value, $fail) {
                    if ($value !== 'For Approval' && !is_numeric($value)) {
                        $fail('Funding year must be a valid year or For Approval.');
                    }
                }
            ],

            'amount' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
            'start_date' => ['required', 'date'],
            'due_date' => ['required', 'date', 'after_or_equal:start_date'],
        ]);

        $project = Project::create($validated);

        Project::create($validated);

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

    /**
     * Display the specified project (overview page).
     */
    public function show(Project $project)
    {

        $project->load([
            'tasks.assignedUser', // optional, if relation exists
        ])->loadCount([
            'tasks as total_tasks_count',
            'tasks as completed_tasks_count' => function ($q) {
                $q->where('progress', 100);
            },
        ]);

        // Get active personnel only
        $users = User::where('account_status', 'active')
            ->orderBy('name')
            ->get();

        return view('projects.show', compact('project', 'users'));
    }

    /**
     * Show the form for editing the specified project.
     */
    public function edit(Project $project)
    {
        return view('projects.edit', compact('project'));
    }

    /**
     * Update the specified project.
     */
    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'location' => ['required', 'string', 'max:255'],
            'sub_sector' => [
                'required',
                'in:basic_education,higher_education,madaris_education,technical_education,others'
            ],
            'source_of_fund' => [
                'required',
                'in:GAAB,QRF,TDIF,SDF,CF,SB,BEFF,ODA,LOCAL,For Approval'
            ],
            'funding_year' => [
                'required',
                function ($attribute, $value, $fail) {
                    if ($value !== 'For Approval' && !is_numeric($value)) {
                        $fail('Funding year must be a valid year or For Approval.');
                    }
                }
            ],
            'amount' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
            'start_date' => ['required', 'date'],
            'due_date' => ['required', 'date', 'after_or_equal:start_date'],
        ]);

        // 🔎 Capture original values
        $original = $project->getOriginal();

        // 🧠 Detect changes
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

        // ✅ Update project
        $project->update($validated);

        // ✅ Save activity log
        ProjectActivityLog::create([
            'project_id' => $project->id,
            'user_id' => auth()->id(),
            'action' => 'updated',
            'description' => 'Project details updated',
            'changes' => !empty($changes) ? $changes : null,
        ]);

        return redirect()
            ->route('projects.index')
            ->with('success', FlashMessage::success('project_updated'));
    }

    /**
     * Archive the specified project.
     */
    public function archive(Project $project)
    {
        // Archive ALL tasks under the project
        $project->tasks()->update([
            'archived_at' => now(),
        ]);

        // Archive the project itself
        $project->update([
            'archived_at' => now(),
        ]);

        ProjectActivityLog::create([
            'project_id' => $project->id,
            'user_id' => auth()->id(),
            'action' => 'archived',
            'description' => 'Project archived',
        ]);


        return redirect()
            ->route('projects.index')
            ->with('success', FlashMessage::success('project_archived'));
    }
    /**
     * Display archived projects.
     */
    public function archived()
    {
        $projects = Project::whereNotNull('archived_at')
            ->latest('archived_at')
            ->paginate(10)
            ->withQueryString();

        return view('archives.projects', compact('projects'));
    }

    /**
     * Restore an archived project.
     */
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
        $logs = ProjectActivityLog::with(['project', 'user'])
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('logs.projects', compact('logs'));
    }
}
