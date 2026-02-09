<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    /**
     * Display a listing of active projects.
     */
    public function index()
    {
        $projects = Project::active()
            ->withCount([
                'tasks as total_tasks_count',
                'tasks as not_started_tasks_count' => function ($query) {
                    $query->where('progress', 0);
                },
                'tasks as ongoing_tasks_count' => function ($query) {
                    $query->whereBetween('progress', [1, 99]);
                },
                'tasks as completed_tasks_count' => function ($query) {
                    $query->where('progress', 100);
                },
            ])
            ->latest()
            ->get();

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

        Project::create($validated);

        return redirect()
            ->route('projects.index')
            ->with('success', 'Project has been created successfully.');
    }

    /**
     * Display the specified project (overview page).
     */
    public function show(Project $project)
    {
        return view('projects.show', compact('project'));
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

        $project->update($validated);

        return redirect()
            ->route('projects.index')
            ->with('success', 'Project has been updated successfully.');
    }

    /**
     * Archive the specified project.
     */
    public function archive(Project $project)
    {
        $project->archive();

        return redirect()
            ->route('projects.index')
            ->with('success', 'Project archived successfully.');
    }

    /**
     * Display archived projects.
     */
    public function archived()
    {
        $projects = Project::archived()
            ->latest()
            ->get();

        return view('archives.projects', compact('projects'));
    }

    /**
     * Restore an archived project.
     */
    public function restore(Project $project)
    {
        $project->restore();

        return redirect()
            ->route('projects.archived')
            ->with('success', 'Project restored successfully.');
    }
}
