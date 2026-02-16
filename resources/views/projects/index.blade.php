@extends('layouts.app')

@section('content')

@php
$isAdmin = auth()->user()->isAdmin();
$isMyPage = request()->routeIs('projects.my');

$pageTitle = $isAdmin
? ($isMyPage ? 'My Projects' : 'Manage Projects')
: 'My Projects';
@endphp

@section('title', $pageTitle)

<x-page-wrapper :title="$pageTitle">

    {{-- Page Actions --}}
    <x-slot name="actions">
        @php
        $isMyPage = request()->routeIs('projects.my');
        @endphp

        @if(auth()->user()->isAdmin() && !$isMyPage)

        <a href="{{ route('projects.create') }}"
            class="btn btn-sm btn-success">
            + Add Project
        </a>

        @endif

    </x-slot>

    {{-- ================= DESKTOP TABLE ================= --}}
    <div class="d-none d-lg-block">
        <div class="table-responsive">
            <table class="table table-sm align-middle table-projects w-100">
                <thead class="table-light">
                    <tr>
                        <th class="text-center text-nowrap">No.</th>
                        <th>Project Name</th>
                        <th>Location</th>
                        <th>Source of Fund</th>
                        <th class="text-nowrap text-center">Timeline</th>
                        <th class="text-center text-nowrap">Task</th>
                        <th class="text-center text-nowrap">Progress</th>
                        <th class="text-center text-nowrap">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($projects as $project)
                    <tr>
                        {{-- No. --}}
                        <td class="text-center">
                            {{ $projects->firstItem() + $loop->index }}
                        </td>

                        {{-- Project Name --}}
                        <td>
                            {{ $project->name }}
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

                        {{-- Timeline --}}
                        <td>
                            <div class="small">
                                <div>
                                    <strong>Start Date:</strong>
                                    {{ $project->start_date?->format('M. j, Y') ?? '—' }}
                                </div>
                                <div>
                                    <strong>Due Date:</strong>
                                    <x-due-date
                                        :dueDate="$project->due_date"
                                        :progress="$project->progress" />
                                </div>
                            </div>
                        </td>

                        {{-- Task Summary --}}
                        <td class="text-center">
                            <div class="small text-center">
                                <div>
                                    {{ $project->completed_tasks_count }} / {{ $project->tasks->count() }}
                                </div>
                            </div>
                        </td>

                        {{-- Progress (Computed) --}}
                        <td class="text-center">
                            <x-progress-bar :value="$project->progress" />
                        </td>

                        {{-- Actions --}}
                        @php
                        $isAdmin = auth()->user()->isAdmin();
                        $isArchived = !is_null($project->archived_at);
                        @endphp

                        <td class="text-center">
                            <div class="d-flex flex-wrap justify-content-center gap-1">

                                {{-- VIEW --}}
                                <a href="{{ route('projects.show', [
            'project' => $project->id,
            'from' => request()->routeIs('projects.my') ? 'my' : 'manage'
        ]) }}"
                                    class="btn btn-sm btn-secondary">
                                    View
                                </a>

                                @if($isAdmin && !$isArchived)

                                <a href="{{ route('projects.edit', $project->id) }}"
                                    class="btn btn-sm btn-primary">
                                    Edit
                                </a>

                                <button type="button"
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

                                @elseif($isAdmin && $isArchived)

                                <span class="text-muted small">
                                    Archived
                                </span>

                                @endif

                            </div>
                        </td>

                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted">
                            No projects found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="mt-3">
                {{ $projects->links() }}
            </div>

        </div>

    </div>

    {{-- ================= MOBILE CARDS ================= --}}
    <div class="d-lg-none">

        @forelse ($projects as $project)

        <div class="card mb-3 shadow-sm border-0">
            <div class="card-body">

                {{-- Header --}}
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div class="fw-semibold">
                        {{ $project->name }}
                    </div>

                    <span class="badge bg-light text-dark">
                        {{ $projects->firstItem() + $loop->index }}
                    </span>
                </div>

                {{-- Location --}}
                <div class="small text-muted mb-2">
                    📍 {{ $project->location }}
                </div>

                {{-- Source of Fund --}}
                <div class="small mb-2">
                    <div><strong>Source:</strong> {{ $project->source_of_fund }}</div>
                    <div><strong>Year:</strong> {{ $project->funding_year }}</div>
                    <div><strong>Amount:</strong> PHP {{ number_format($project->amount, 2) }}</div>
                </div>

                {{-- Task and Timeline --}}
                <div class="small mb-2">
                    <div><strong>Task:</strong> {{ $project->completed_tasks_count }} / {{ $project->tasks->count() }}</div>
                    <div><strong>Start:</strong> {{ $project->start_date?->format('M j, Y') ?? '—' }}</div>
                    <div>
                        <strong>Due:</strong>
                        <x-due-date
                            :dueDate="$project->due_date"
                            :progress="$project->progress" />
                    </div>
                </div>

                {{-- Progress --}}
                <div class="mb-3">
                    <x-progress-bar :value="$project->progress" />
                </div>

                {{-- Actions --}}
                <div class="d-flex gap-2 flex-wrap">

                    {{-- View --}}
                    <a href="{{ route('projects.show', [
                    'project' => $project->id,
                    'from' => request()->routeIs('projects.my') ? 'my' : 'manage'
                ]) }}"
                        class="btn btn-sm btn-secondary flex-fill">
                        View
                    </a>

                    @php
                    $isAdmin = auth()->user()->role === 'admin';
                    $isArchived = !is_null($project->archived_at);
                    @endphp

                    @if($isAdmin && !$isArchived)

                    <a href="{{ route('projects.edit', $project->id) }}"
                        class="btn btn-sm btn-primary flex-fill">
                        Edit
                    </a>

                    <button type="button"
                        class="btn btn-sm btn-danger flex-fill"
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

                    @endif

                </div>

            </div>
        </div>

        @empty
        <div class="text-center text-muted py-4">
            No projects found.
        </div>
        @endforelse

        {{-- Pagination --}}
        <div class="mt-3">
            {{ $projects->links() }}
        </div>

    </div>

</x-page-wrapper>
@endsection