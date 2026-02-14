@extends('layouts.app')

@section('title', 'Project Details')

@section('content')
<x-page-wrapper title="Project Details">

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

        case 'manage':
        default:
        $backUrl = route('projects.index');
        $label = auth()->user()->isAdmin() ? 'Manage Projects' : 'My Projects';
        break;
        }
        @endphp

        <a href="{{ $backUrl }}" class="btn btn-sm btn-secondary">
            ← Back to {{ $label }}
        </a>
    </x-slot>

    @php
    $progress = $project->progress ?? 0;

    $progressClass = match (true) {
    $progress == 100 => 'bg-primary',
    $progress >= 50 => 'bg-success',
    $progress > 0 => 'bg-warning',
    default => 'bg-secondary',
    };
    @endphp

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-start mb-3">
        <div>
            <h5 class="mb-1">{{ $project->name }}</h5>
            <div class="text-muted small">
                {{ $project->location }}
            </div>
        </div>

        {{-- Status --}}
        @php
        $statusClasses = [
        'Not Started' => 'bg-secondary',
        'Ongoing' => 'bg-success',
        'Completed' => 'bg-primary',
        ];
        @endphp

        <span class="badge {{ $statusClasses[$project->status] ?? 'bg-secondary' }}">
            {{ $project->status }}
        </span>
    </div>

    <hr>

    {{-- INFO GRID --}}
    <div class="row mb-3">
        <div class="col-md-4 mb-2">
            <div class="fw-semibold">Source of Fund</div>
            <div>{{ $project->source_of_fund }}</div>
        </div>

        <div class="col-md-4 mb-2">
            <div class="fw-semibold">Funding Year</div>
            <div>{{ $project->funding_year }}</div>
        </div>

        <div class="col-md-4 mb-2">
            <div class="fw-semibold">Amount</div>
            <div>₱ {{ number_format($project->amount, 2) }}</div>
        </div>

        <div class="col-md-4 mb-2">
            <div class="fw-semibold">Sub-Sector</div>
            <div>{{ ucwords(str_replace('_', ' ', $project->sub_sector)) ?? '—' }}</div>
        </div>

        <div class="col-md-4 mb-2">
            <div class="fw-semibold">Date Started</div>
            <div>{{ $project->start_date?->format('F j, Y') ?? '—' }}</div>
        </div>

        <div class="col-md-4 mb-2">
            <div class="fw-semibold">Target Completion</div>
            <div>{{ $project->due_date?->format('F j, Y') ?? '—' }}</div>
        </div>

        <div class="col-md-4 mb-2">
            <div class="fw-semibold">Task</div>
            <div> Completed: {{ $project->completed_tasks_count ?? 0 }}
                /
                Total: {{ $project->total_tasks_count ?? 0 }}
            </div>

        </div>

        <div class="col-md-4 mb-2">
            <div class="fw-semibold mb-1">Progress</div>

            <x-progress-bar :value="$project->progress" />
        </div>

        {{-- DESCRIPTION --}}
        <div>
            <div class="fw-semibold mb-1">Description</div>
            <div>
                {{ $project->description ?: 'No description provided.' }}
            </div>
        </div>

        {{-- PROJECT TASKS --}}
        <div class="mt-4">

            {{-- ADD TASK BUTTON --}}
            @if(auth()->user()->isAdmin())
            <div class="mt-4 text-end">
                @if (is_null($project->archived_at))
                <button class="btn btn-sm btn-success mb-2"
                    data-bs-toggle="modal"
                    data-bs-target="#addTaskModal">
                    + Add Task
                </button>
                @else
                <button class="btn btn-sm btn-secondary mb-2" disabled
                    title="This project is archived and cannot be modified">
                    Project Archived
                </button>
                @endif
            </div>
            @endif

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" style="width:50px;">No.</th>
                            <th style="width:150px;">Task Name</th>
                            <th style="width:150px;">Assigned To</th>
                            <th style="width:100px;">Start Date</th>
                            <th style="width:100px;">Due Date</th>
                            <th class="text-center" style="width:80px;">Progress</th>
                            <th class="text-center" style="width:200px;">Remarks</th>
                            <th class="text-center" style="width:120px;">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($project->tasks as $task)
                        <tr>

                            {{-- No. --}}
                            <td class="text-center">
                                {{ $loop->iteration }}
                            </td>

                            {{-- Task Type --}}
                            <td>
                                {{ ucfirst($task->task_type) }}
                            </td>

                            {{-- Assigned User --}}
                            <td>
                                {{ $task->assignedUser->name ?? '—' }}
                            </td>

                            {{-- Start Date --}}
                            <td>
                                {{ $task->start_date?->format('F d, Y') ?? '—' }}
                            </td>

                            {{-- Due Date --}}
                            <td>
                                {{ $task->due_date?->format('F d, Y') ?? '—' }}
                            </td>

                            {{-- P  rogress --}}
                            <td class="text-center">
                                <x-progress-bar :value="$task->progress" />
                            </td>

                            {{-- Remarks --}}
                            <td>

                                @php
                                $remark = $task->latestRemarkLog->changes['remark']['new'] ?? null;
                                @endphp

                                @if($remark)

                                <div>
                                    <span id="preview-{{ $task->id }}">
                                        {{ Str::limit($remark, 35) }}
                                    </span>

                                    <span id="full-{{ $task->id }}" class="d-none text-dark">
                                        {{ $remark }}
                                    </span>
                                </div>

                                @if(strlen($remark) > 60)
                                <button type="button"
                                    class="btn btn-link btn-sm p-0"
                                    onclick="toggleRemark({{ $task->id }})"
                                    id="btn-{{ $task->id }}">
                                    View Full Remarks
                                </button>
                                @endif

                                @else
                                <span>—</span>
                                @endif

                            </td>

                            <td class="text-center">

                                @php
                                $isAdmin = auth()->user()->isAdmin();
                                $isArchived = !is_null($project->archived_at) || !is_null($task->archived_at);
                                @endphp

                                {{-- VIEW (Everyone, even archived) --}}
                                <a href="{{ route('tasks.show', $task->id) }}?from=project"
                                    class="btn btn-sm btn-secondary">
                                    View
                                </a>

                                @if(!$isArchived && $isAdmin)

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
                            <td colspan="8" class="text-center text-muted">
                                No tasks added to this project yet.
                            </td>
                        </tr>
                        @endforelse

                    </tbody>
                </table>
            </div>
        </div>

        {{-- ADD TASK MODAL --}}
        @include('projects.partials.add-task-modal')

        {{-- EDIT TASK MODAL --}}
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