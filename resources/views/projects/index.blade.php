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
            <table class="table table-card align-middle">

                <thead class="table-light">
                    <tr>
                        <th style="width:60px;" class="text-center">No.</th>
                        <th style="width:300px;">Project Name and Location</th>
                        <th style="width:220px;">Source of Fund</th>
                        <th style="width:220px;">Timeline</th>
                        <th style="width:150px;" class="text-center">Progress</th>
                        <th style="width:170px;" class="text-center">Action</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($projects as $project)
                    <tr>

                        {{-- NO --}}
                        <td class="text-center fw-semibold">
                            {{ $projects->firstItem() + $loop->index }}
                        </td>

                        {{-- PROJECT NAME + LOCATION --}}
                        <td>
                            <div class="fw-semibold">
                                {{ $project->name }}
                            </div>
                            <div class="small text-muted">
                                {{ $project->location ?? '—' }}
                            </div>
                        </td>

                        {{-- FUNDING SOURCE + YEAR --}}
                        <td>
                            <div class="fw-semibold">
                                {{ $project->source_of_fund ?? '—' }}
                                {{ $project->funding_year ?? '' }}
                            </div>
                            <div class="small text-muted">
                                P {{ number_format($project->amount, 2) }}
                            </div>
                        </td>

                        {{-- TIMELINE --}}
                        <td>
                            <div class="small">
                                <strong>Start:</strong>
                                {{ $project->start_date?->format('M. d, Y') ?? '—' }}
                            </div>
                            <div class="small text-muted">
                                <strong>Due:</strong>
                                <x-due-date
                                    :dueDate="$project->due_date"
                                    :progress="$project->progress" />
                            </div>
                        </td>

                        {{-- PROGRESS --}}
                        <td class="text-center">
                            <x-progress-bar :value="$project->progress" />
                        </td>

                        {{-- ACTIONS --}}
                        <td class="text-center">

                            <div class="d-flex justify-content-center gap-1 flex-wrap">

                                <a href="{{ route('projects.show', $project->id) }}"
                                    class="btn btn-sm btn-primary">
                                    View
                                </a>

                                <a href="{{ route('projects.edit', $project->id) }}"
                                    class="btn btn-sm btn-secondary">
                                    Edit
                                </a>

                                <button class="btn btn-sm btn-danger"
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

                            </div>

                        </td>

                    </tr>

                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
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