<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use App\Models\User;
use App\Support\FlashMessage;
use App\Models\ProjectActivityLog;
use App\Models\TaskActivityLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;



class ProjectController extends Controller
{
    /**
     * Display a listing of active projects.
     */
    public function index(Request $request)
    {
        $scope = $request->get('scope', 'all');
        $user = auth()->user();

        $baseQuery = Project::active();

        // Normal user → only assigned projects
        if (!$user->isAdmin()) {
            $baseQuery->assignedTo($user->id);
        }

        // Admin personal scope
        if ($user->isAdmin() && $scope === 'my') {
            $baseQuery->assignedTo($user->id);
        }

        return $this->buildProjectIndex($baseQuery, $request);
    }

    /**
     * Display a listing of archived projects.
     */


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
            ->route('projects.index', [
                'scope' => $request->get('scope')
                    ?? (auth()->user()->isAdmin() ? 'all' : 'my')
            ])
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
            ->route('projects.index', [
                'scope' => request('scope')
                    ?? (auth()->user()->isAdmin() ? 'all' : 'my')
            ])
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
            ->route('archives.index', ['scope' => 'projects'])
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

    private function buildProjectIndex($baseQuery, Request $request)
    {
        $subSector = $request->get('sub_sector');
        $status    = $request->get('filter', 'all');
        $search    = $request->get('search');

        $subSectors = [
            'basic_education'      => 'Basic Education',
            'higher_education'     => 'Higher Education',
            'madaris_education'    => 'Madaris Education',
            'technical_education'  => 'Technical Education',
            'others'               => 'Others',
        ];

        $statusChips = [
            'all'         => 'All Status',
            'ongoing'     => 'Ongoing',
            'completed'   => 'Completed',
            'overdue'     => 'Overdue',
            'not_started' => 'Not Started',
            'due_soon'    => 'Due Soon',
        ];

        /*
    |--------------------------------------------------------------------------
    | MAIN PROJECT QUERY (ALL FILTERS)
    |--------------------------------------------------------------------------
    */

        $query = clone $baseQuery;

        $this->applyProjectFilters($query, $request);

        $projects = $query
            ->withCount([
                'tasks as total_tasks_count',
                'tasks as completed_tasks_count' => function ($q) {
                    $q->where('progress', 100);
                }
            ])
            ->latest()
            ->paginate(10)
            ->withQueryString();

        /*
    |--------------------------------------------------------------------------
    | STATUS COUNTS (IGNORE STATUS)
    |--------------------------------------------------------------------------
    */

        $statusCounts = [];

        foreach ($statusChips as $key => $label) {

            $statusQuery = clone $baseQuery;

            $this->applyProjectFilters($statusQuery, $request, 'status');

            if ($key !== 'all') {
                $method = Str::camel($key);

                if (method_exists($statusQuery->getModel(), 'scope' . ucfirst($method))) {
                    $statusQuery->{$method}();
                }
            }

            $statusCounts[$key] = $statusQuery->count();
        }

        /*
    |--------------------------------------------------------------------------
    | SUB-SECTOR COUNTS (IGNORE SUB-SECTOR)
    |--------------------------------------------------------------------------
    */

        $subSectorQuery = clone $baseQuery;

        $this->applyProjectFilters($subSectorQuery, $request, 'sub_sector');

        $subSectorCounts = $subSectorQuery
            ->select('sub_sector', DB::raw('count(*) as total'))
            ->groupBy('sub_sector')
            ->pluck('total', 'sub_sector');

        /*
    |--------------------------------------------------------------------------
    | AJAX RESPONSE
    |--------------------------------------------------------------------------
    */

        $scope = request('scope', 'all');

        if ($request->ajax()) {
            return response()->json([
                'desktopFilters' => view('projects.partials.filters.desktop', compact(
                    'statusCounts',
                    'subSectorCounts',
                    'subSectors',
                    'statusChips',
                    'status',
                    'search',
                    'subSector',
                    'scope'
                ))->render(),

                'mobileFilters' => view('projects.partials.filters.mobile', compact(
                    'statusCounts',
                    'subSectorCounts',
                    'subSectors',
                    'statusChips',
                    'status',
                    'search',
                    'subSector',
                    'scope'
                ))->render(),

                'projects' => view('projects.partials.project-list', compact('projects'))->render(),
            ]);
        }

        /*
    |--------------------------------------------------------------------------
    | NORMAL RESPONSE
    |--------------------------------------------------------------------------
    */

        return view('projects.index', compact(
            'projects',
            'statusCounts',
            'subSectors',
            'subSectorCounts',
            'statusChips',
            'status',
            'search',
            'subSector'
        ));
    }

    private function applyProjectFilters($query, $request, $ignore = null)
    {
        $subSector = $request->get('sub_sector');
        $status    = $request->get('filter', 'all');
        $search    = $request->get('search');

        if ($ignore !== 'search' && !empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%")
                    ->orWhere('sub_sector', 'like', "%{$search}%")
                    ->orWhere('source_of_fund', 'like', "%{$search}%")
                    ->orWhere('funding_year', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($ignore !== 'sub_sector' && !empty($subSector)) {
            $query->where('sub_sector', $subSector);
        }

        if ($ignore !== 'status' && $status !== 'all') {

            $method = Str::camel($status);

            if (method_exists($query->getModel(), 'scope' . ucfirst($method))) {
                $query->{$method}();
            }
        }

        return $query;
    }
}
