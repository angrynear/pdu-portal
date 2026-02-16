@extends('layouts.app')

@section('title', 'Project Details')

@section('content')
<x-page-wrapper title="Project Details">

    {{-- ============================= --}}
    {{-- BACK BUTTON --}}
    {{-- ============================= --}}
    <x-slot name="actions">
        @php
        $from = request('from');

        switch ($from) {
        case 'tasks':
        $backUrl = route('tasks.index');
        $label = 'Tasks';
        break;
        case 'task_logs':
        $backUrl = route('logs.tasks');
        $label = 'Task Logs';
        break;
        case 'project_logs':
        $backUrl = route('logs.projects');
        $label = 'Project Logs';
        break;
        case 'my':
        $backUrl = route('projects.my');
        $label = 'My Projects';
        break;
        default:
        $backUrl = route('projects.index');
        $label = auth()->user()->isAdmin() ? 'Manage Projects' : 'My Projects';
        }
        @endphp

        <a href="{{ $backUrl }}" class="btn btn-sm btn-secondary">
            ← Back to {{ $label }}
        </a>
    </x-slot>

    {{-- ============================= --}}
    {{-- PROJECT HEADER --}}
    {{-- ============================= --}}
    <div class="d-flex justify-content-between align-items-start mb-3">
        <div>
            <h5 class="mb-1">{{ $project->name }}</h5>
            <div class="text-muted small">
                {{ $project->location }}
            </div>
        </div>
    </div>

    <hr>

    {{-- ============================= --}}
    {{-- INFO GRID --}}
    {{-- ============================= --}}
    <div class="row mb-4">

        <div class="col-md-4 mb-3">
            <div class="fw-semibold">Source of Fund</div>
            <div>{{ $project->source_of_fund }}</div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="fw-semibold">Funding Year</div>
            <div>{{ $project->funding_year }}</div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="fw-semibold">Amount</div>
            <div>₱ {{ number_format($project->amount, 2) }}</div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="fw-semibold">Sub-sector</div>
            <div>{{ ucwords(str_replace('_', ' ', $project->sub_sector)) }}</div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="fw-semibold">Start Date</div>
            <div>{{ $project->start_date?->format('F j, Y') ?? '—' }}</div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="fw-semibold">Due Date</div>
            <div>{{ $project->due_date?->format('F j, Y') ?? '—' }}</div>
        </div>

        <div class="col-md-6 mb-3">
            <div class="fw-semibold mb-1">Progress</div>
            <x-progress-bar :value="$project->progress" />
        </div>

        <div class="col-12">
            <div class="fw-semibold mb-1">Description</div>
            <div>{{ $project->description ?: 'No description provided.' }}</div>
        </div>

    </div> {{-- ✅ PROPERLY CLOSED ROW --}}

    {{-- ============================= --}}
    {{-- PROJECT TASKS SECTION --}}
    {{-- ============================= --}}
    <div class="mt-4">

        @if(auth()->user()->isAdmin() && is_null($project->archived_at))
        <div class="text-end mb-3">
            <button class="btn btn-sm btn-success"
                data-bs-toggle="modal"
                data-bs-target="#addTaskModal">
                + Add Task
            </button>
        </div>
        @endif

        {{-- ================= DESKTOP TABLE ================= --}}
        <div class="table-responsive d-none d-md-block">
            <table class="table align-middle">
                <thead class="table-light">
                    <tr>
                        <th>No.</th>
                        <th>Task</th>
                        <th>Assigned</th>
                        <th>Start</th>
                        <th>Due</th>
                        <th>Progress</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($project->tasks as $task)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ ucfirst($task->task_type) }}</td>
                        <td>{{ $task->assignedUser->name ?? '—' }}</td>
                        <td>{{ $task->start_date?->format('M d, Y') ?? '—' }}</td>
                        <td>{{ $task->due_date?->format('M d, Y') ?? '—' }}</td>
                        <td><x-progress-bar :value="$task->progress" /></td>
                        <td>

                            @php
                            $isAdmin = auth()->user()->isAdmin();
                            $isArchived = !is_null($project->archived_at) || !is_null($task->archived_at);
                            @endphp

                            {{-- VIEW --}}
                            <a href="{{ route('tasks.show', $task->id) }}?from=project"
                                class="btn btn-sm btn-secondary">
                                View
                            </a>

                            @if($isAdmin && !$isArchived)

                            {{-- EDIT --}}
                            <button
                                class="btn btn-sm btn-primary ms-1"
                                data-bs-toggle="modal"
                                data-bs-target="#editTaskModal"
                                data-task-id="{{ $task->id }}"
                                data-task-type="{{ $task->task_type }}"
                                data-assigned="{{ $task->assigned_user_id }}"
                                data-start="{{ optional($task->start_date)->format('Y-m-d') }}"
                                data-due="{{ optional($task->due_date)->format('Y-m-d') }}"
                                data-project-start="{{ $task->project->start_date->format('Y-m-d') }}"
                                data-project-due="{{ $task->project->due_date->format('Y-m-d') }}">
                                Edit
                            </button>

                            {{-- ARCHIVE --}}
                            <button
                                class="btn btn-sm btn-danger ms-1"
                                data-bs-toggle="modal"
                                data-bs-target="#confirmActionModal"
                                data-action="{{ route('tasks.archive', $task->id) }}"
                                data-method="PATCH"
                                data-title="Archive Task"
                                data-message="Are you sure you want to archive this task?"
                                data-confirm-text="Archive"
                                data-confirm-class="btn-danger">
                                Archive
                            </button>

                            @elseif($isArchived)

                            <div class="small text-muted mt-1">
                                {{ $project->archived_at ? 'Project Archived' : 'Task Archived' }}
                            </div>

                            @endif

                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted">
                            No tasks yet.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- ================= MOBILE CARDS ================= --}}
        <div class="d-md-none">
            @forelse($project->tasks as $task)
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body">

                    <div class="fw-semibold mb-1">
                        {{ ucfirst($task->task_type) }}
                    </div>

                    <div class="small mb-2">
                        <div>Assigned: {{ $task->assignedUser->name ?? '—' }}</div>
                        <div>Start: {{ $task->start_date?->format('M d, Y') ?? '—' }}</div>
                        <div>Due: {{ $task->due_date?->format('M d, Y') ?? '—' }}</div>
                    </div>

                    <div class="mb-2">
                        <x-progress-bar :value="$task->progress" />
                    </div>

                    {{-- ACTIONS --}}
                    @php
                    $isAdmin = auth()->user()->isAdmin();
                    $isArchived = !is_null($project->archived_at) || !is_null($task->archived_at);
                    @endphp

                    <div class="d-grid gap-2">

                        {{-- VIEW --}}
                        <a href="{{ route('tasks.show', $task->id) }}?from=project"
                            class="btn btn-sm btn-secondary">
                            View Task
                        </a>

                        @if($isAdmin && !$isArchived)

                        {{-- EDIT --}}
                        <button
                            class="btn btn-sm btn-primary"
                            data-bs-toggle="modal"
                            data-bs-target="#editTaskModal"
                            data-task-id="{{ $task->id }}"
                            data-task-type="{{ $task->task_type }}"
                            data-assigned="{{ $task->assigned_user_id }}"
                            data-start="{{ optional($task->start_date)->format('Y-m-d') }}"
                            data-due="{{ optional($task->due_date)->format('Y-m-d') }}"
                            data-project-start="{{ $task->project->start_date->format('Y-m-d') }}"
                            data-project-due="{{ $task->project->due_date->format('Y-m-d') }}">
                            Edit Task
                        </button>

                        {{-- ARCHIVE --}}
                        <button
                            class="btn btn-sm btn-danger"
                            data-bs-toggle="modal"
                            data-bs-target="#confirmActionModal"
                            data-action="{{ route('tasks.archive', $task->id) }}"
                            data-method="PATCH"
                            data-title="Archive Task"
                            data-message="Are you sure you want to archive this task?"
                            data-confirm-text="Archive"
                            data-confirm-class="btn-danger">
                            Archive Task
                        </button>

                        @elseif($isArchived)

                        <div class="small text-muted text-center">
                            {{ $project->archived_at ? 'Project Archived' : 'Task Archived' }}
                        </div>

                        @endif

                    </div>

                </div>
            </div>
            @empty
            <div class="text-muted small">
                No tasks yet.
            </div>
            @endforelse
        </div>

    </div>

    {{-- MODALS --}}
    @include('projects.partials.add-task-modal')
    @include('projects.partials.edit-task-modal')

</x-page-wrapper>

@push('scripts')

{{-- View full Remarks Script --}}
<script>
    function toggleRemark(id) {
        const preview = document.getElementById('preview-' + id);
        const full = document.getElementById('full-' + id);
        const button = document.getElementById('btn-' + id);

        if (full.classList.contains('d-none')) {
            preview.classList.add('d-none');
            full.classList.remove('d-none');
            button.innerText = 'Hide Remarks';
        } else {
            preview.classList.remove('d-none');
            full.classList.add('d-none');
            button.innerText = 'View Full Remarks';
        }
    }
</script>

{{-- Add Task Modal Script --}}
@if ($errors->any() && session('form_context') === 'add_task')
<script>
    window.addEventListener('load', function() {
        const modalEl = document.getElementById('addTaskModal');
        if (modalEl) {
            const modal = new bootstrap.Modal(modalEl);
            modal.show();
        }
    });
</script>
@endif

{{-- Edit Task Modal Script --}}
@if ($errors->any() && session('form_context') === 'edit_task')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        new bootstrap.Modal(
            document.getElementById('editTaskModal')
        ).show();
    });
</script>
@endif


@endpush

@endsection