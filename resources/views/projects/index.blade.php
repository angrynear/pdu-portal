@extends('layouts.app')

@section('title', 'Manage Projects')

@section('content')
<x-page-wrapper title="Projects List">

    {{-- Page Actions --}}
    <x-slot name="actions">
        <a href="{{ route('projects.create') }}" class="btn btn-sm btn-success">
            <i class="bi bi-plus-circle me-1"></i>
            Add Project
        </a>
    </x-slot>

    <div class="table-responsive">
        <table class="table align-middle table-projects">
            <thead class="table-light">
                <tr>
                    <th class="text-center" style="width: 50px;">No.</th>
                    <th style="width: 250px;">Project Name</th>
                    <th style="width: 120px;">Sub-sector</th>
                    <th style="width: 180px;">Location</th>
                    <th style="width: 200px;">Source of Fund</th>
                    <th style="width: 160px;">Timeline</th>
                    <th class="text-center" style="width: 80px;">Task</th>
                    <th class="text-center" style="width: 100px;">Status</th>
                    <th class="text-center" style="width: 100px;">Progress</th>
                    <th class="text-center" style="width: 180px;">Actions</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($projects as $project)
                <tr>
                    {{-- No. --}}
                    <td class="text-center">
                        {{ $loop->iteration }}
                    </td>

                    {{-- Project Name --}}
                    <td>
                        {{ $project->name }}
                    </td>

                    {{-- Sub-sector --}}
                    <td>
                        {{ ucwords(str_replace('_', ' ', $project->sub_sector)) }}
                    </td>

                    {{-- Location --}}
                    <td>
                        {{ $project->location }}
                    </td>

                    {{-- Source of Fund --}}
                    <td>
                        <div class="small">
                            <div>
                                <strong>Source:</strong>
                                {{ $project->source_of_fund }}
                            </div>
                            <div>
                                <strong>Funding Year:</strong>
                                {{ $project->funding_year }}
                            </div>
                            <div>
                                <strong>Amount:</strong>
                                PHP {{ number_format($project->amount, 2) }}
                            </div>
                        </div>
                    </td>

                    {{-- Date --}}
                    <td>
                        <div class="small">
                            <div>
                                <strong>Start Date:</strong>
                                {{ $project->start_date?->format('M. j, Y') ?? '—' }}
                            </div>
                            <div>
                                <strong>Due Date:</strong>
                                {{ $project->due_date?->format('M. j, Y') ?? '—' }}
                            </div>
                        </div>
                    </td>

                    {{-- Task Summary --}}
                    <td>
                        <div class="small text-center">
                            <div>
                                {{ $project->completed_tasks_count }} / {{ $project->tasks->count() }}
                            </div>
                        </div>
                    </td>

                    {{-- Status (Computed) --}}
                    <td class="text-center">
                        @if ($project->status === 'Completed')
                        <span class="badge bg-success">Completed</span>
                        @elseif ($project->status === 'Ongoing')
                        <span class="badge bg-warning text-dark">Ongoing</span>
                        @else
                        <span class="badge bg-secondary">Not Started</span>
                        @endif
                    </td>

                    {{-- Progress (Computed) --}}
                    <td class="text-center">
                        <div class="progress" style="height: 18px;">
                            <div
                                class="progress-bar
                                    {{ $project->progress == 100 ? 'bg-success' : 'bg-primary' }}"
                                role="progressbar"
                                style="width: {{ $project->progress }}%;"
                                aria-valuenow="{{ $project->progress }}"
                                aria-valuemin="0"
                                aria-valuemax="100">
                                {{ $project->progress }}%
                            </div>
                        </div>
                    </td>

                    {{-- Actions --}}
                    <td class="text-center">
                        <a href="{{ route('projects.show', $project->id) }}"
                            class="btn btn-sm btn-secondary">
                            View
                        </a>

                        <a href="{{ route('projects.edit', $project->id) }}"
                            class="btn btn-sm btn-primary">
                            Edit
                        </a>

                        <button
                            type="button"
                            class="btn btn-sm btn-danger"
                            data-bs-toggle="modal"
                            data-bs-target="#confirmActionModal"
                            data-action="{{ route('projects.archive', $project->id) }}"
                            data-method="PATCH"
                            data-title="Archive Project"
                            data-message="Are you sure you want to archive this project?"
                            data-confirm-text="Archive"
                            data-confirm-class="btn-danger">
                            Archive
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" class="text-center text-muted">
                        No projects found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</x-page-wrapper>
@endsection