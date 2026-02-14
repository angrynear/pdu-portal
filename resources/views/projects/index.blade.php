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

    <div class="table-responsive">
        <table class="table table-sm align-middle table-projects">
            <thead class="table-light">
                <tr>
                    <th class="text-center" style="width: 50px;">No.</th>
                    <th style="width: 280px;">Project Name</th>
                    <th style="width: 180px;">Location</th>
                    <th style="width: 180px;">Source of Fund</th>
                    <th class="text-center" style="width: 150px;">Timeline</th>
                    <th class="text-center" style="width: 50px;">Task</th>
                    <th class="text-center" style="width: 100px;">Progress</th>
                    <th class="text-center" style="width: 150px;">Actions</th>
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
                    <td class="text-center">
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
                    <td class="text-center">

                        {{-- VIEW (Everyone Allowed) --}}
                        <a href="{{ route('projects.show', [
        'project' => $project->id,
        'from' => request()->routeIs('projects.my') ? 'my' : 'manage'
    ]) }}"
                            class="btn btn-sm btn-secondary">
                            View
                        </a>

                        @php
                        $isAdmin = auth()->user()->role === 'admin';
                        $isArchived = !is_null($project->archived_at);
                        @endphp

                        {{-- ADMIN ONLY CONTROLS --}}
                        @if($isAdmin)

                        @if(!$isArchived)

                        {{-- EDIT --}}
                        <a href="{{ route('projects.edit', $project->id) }}"
                            class="btn btn-sm btn-primary">
                            Edit
                        </a>

                        {{-- ARCHIVE --}}
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

                        @else
                        <span class="text-muted small">
                            Archived
                        </span>
                        @endif

                        @endif

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

        <div class="mt-3">
            {{ $projects->links() }}
        </div>

    </div>

</x-page-wrapper>
@endsection