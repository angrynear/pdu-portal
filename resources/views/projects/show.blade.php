@extends('layouts.app')

@section('title', 'Project Details')

@section('content')
<x-page-wrapper title="Project Details">

    {{-- ================= BACK BUTTON ================= --}}
    <x-slot name="actions">
        @php
        $from = request('from');
        $scope = request('scope');

        switch ($from) {

        // =========================
        // From Projects Index
        // =========================
        case 'projects':
        $effectiveScope = $scope ?? (auth()->user()->isAdmin() ? 'all' : 'my');
        $backUrl = route('projects.index', [ 'scope' => $effectiveScope ]);
        $label = $effectiveScope === 'my' ? 'My Projects' : 'All Projects';
        break;

        // =========================
        // From Tasks Index
        // =========================
        case 'tasks':
        $backUrl = route('tasks.index', ['scope' => $scope ?? 'all' ]);
        $label = $scope === 'my' ? 'My Tasks' : 'All Tasks';
        break;

        // =========================
        // From Logs
        // =========================
        case 'logs':
        $backUrl = route('logs.index', ['scope' => $scope ?? 'projects']);
        $label = $scope === 'tasks' ? 'Task Logs' : 'Project Logs';
        break;

        // =========================
        // Default
        // =========================
        default:
        $backUrl = route('projects.index', ['scope' => auth()->user()->isAdmin() ? 'all' : 'my']);
        $label = auth()->user()->isAdmin() ? 'All Projects' : 'My Projects';
        }
        @endphp

        <a href="{{ $backUrl }}" class="btn btn-sm btn-outline-secondary">
            ← Back to {{ $label }}
        </a>
    </x-slot>

    {{-- ================= PROJECT HEADER CARD ================= --}}
    @php
    $total = $project->tasks()->count();
    $completed = $project->tasks()->where('progress', 100)->count();

    $source = $project->source_of_fund;
    $year = $project->funding_year;

    $isSourceApproval = strtolower($source) === 'for approval';
    $isYearApproval = strtolower($year) === 'for approval';

    if ($isSourceApproval && $isYearApproval) {
    $displayText = 'FOR APPROVAL';
    } elseif (!$isSourceApproval && $isYearApproval) {
    $displayText = $source;
    } elseif ($isSourceApproval && !$isYearApproval) {
    $displayText = $year;
    } else {
    $displayText = trim($source . ' ' . $year);
    }
    @endphp

    <div class="card project-card {{ $project->status_border_class }} shadow-sm border-0 mb-4">
        <div class="card-body">

            {{-- TOP --}}
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start gap-3">

                <div class="flex-grow-1">

                    <div class="fw-semibold fs-5">
                        {{ $project->name }}
                    </div>

                    <div class="small text-muted mt-1">
                        <i class="bi bi-geo-alt me-1"></i>
                        {{ $project->location ?? '—' }}
                    </div>

                    @if($project->sub_sector)
                    <div class="small text-secondary">
                        <i class="bi bi-diagram-3 me-1"></i>
                        {{ ucwords(str_replace('_', ' ', $project->sub_sector)) }}
                    </div>
                    @endif

                </div>

                <span class="badge rounded-pill {{ $project->statusBadgeClass }}">
                    <i class="bi {{ $project->statusIcon }} me-1"></i>
                    {{ $project->statusLabel }}
                </span>

            </div>

            {{-- META STRIP --}}
            <div class="project-meta mt-3">

                <div class="d-flex flex-column flex-md-row flex-wrap align-items-start align-items-md-center gap-3 gap-md-4">

                    <div class="meta-item">
                        <span class="meta-pill">
                            {{ $displayText }} • ₱{{ number_format($project->amount, 2) }}
                        </span>
                    </div>

                    <div class="meta-item small text-muted">
                        <i class="bi bi-calendar3 me-1"></i>
                        {{ $project->start_date?->format('M. d, Y') ?? '—' }}
                        <span class="mx-1">→</span>
                        <x-due-date
                            :dueDate="$project->due_date"
                            :progress="$project->progress" />
                    </div>

                    <div class="meta-item small text-muted">
                        <i class="bi bi-list-check me-1"></i>
                        {{ $completed }} / {{ $total }} Tasks
                    </div>

                </div>

            </div>

            {{-- PROGRESS --}}
            <div class="mt-3">
                <x-progress-bar :value="$project->progress" />
            </div>

            {{-- DESCRIPTION --}}
            <div class="mt-3">
                <div class="fw-semibold mb-1">Description</div>
                <div class="text-muted">
                    {{ $project->description ?: 'No description provided.' }}
                </div>
            </div>

        </div>
    </div>

    {{-- ================= TASKS SECTION ================= --}}
    <div class="mt-4">

        @if(auth()->user()->isAdmin() && is_null($project->archived_at))
        <div class="text-end mb-3">
            <button class="btn btn-sm btn-success"
                data-bs-toggle="modal"
                data-bs-target="#addTaskModal">
                <i class="bi bi-plus-lg me-1"></i> Add Task
            </button>
        </div>
        @endif

        <div class="row">

            @forelse($project->tasks as $task)

            @php
            $taskIsPast = $task->due_date && $task->due_date->isPast();

            if ($task->progress == 100) {
            $taskStatusLabel = 'Completed';
            $taskStatusIcon = 'bi-check-circle-fill';
            $taskBadgeClass = 'bg-success-subtle text-success';
            }
            elseif ($task->progress < 100 && $taskIsPast) {
                $taskStatusLabel='Overdue' ;
                $taskStatusIcon='bi-exclamation-triangle-fill' ;
                $taskBadgeClass='bg-danger-subtle text-danger' ;
                }
                elseif ($task->progress == 0) {
                $taskStatusLabel = 'Not Started';
                $taskStatusIcon = 'bi-dash-circle-fill';
                $taskBadgeClass = 'bg-secondary-subtle text-secondary';
                }
                else {
                $taskStatusLabel = 'Ongoing';
                $taskStatusIcon = 'bi-arrow-repeat';
                $taskBadgeClass = 'bg-primary-subtle text-primary';
                }
                @endphp

                <div class="col-12 col-md-6 mb-3">

                    <div class="card {{ $task->status_border_class }} border-0 shadow-sm h-100">
                        <div class="card-body d-flex flex-column">

                            {{-- TOP SECTION --}}
                            <div class="d-flex justify-content-between align-items-start mb-2">

                                <div class="fw-semibold">
                                    {{ ucfirst($task->task_type) }}
                                </div>

                                {{-- STATUS BADGE --}}
                                <span class="badge rounded-pill {{ $taskBadgeClass }}">
                                    <i class="bi {{ $taskStatusIcon }} me-1"></i>
                                    {{ $taskStatusLabel }}
                                </span>

                            </div>

                            {{-- META --}}
                            <div class="small text-muted mb-2">
                                Assigned: {{ $task->assignedUser->name ?? '—' }}<br>
                                Timeline:
                                {{ $task->start_date?->format('M. d, Y') ?? '—' }}
                                →
                                <x-due-date
                                    :dueDate="$task->due_date"
                                    :progress="$task->progress" />
                            </div>

                            {{-- PROGRESS --}}
                            <div class="mb-3">
                                <x-progress-bar :value="$task->progress" />
                            </div>

                            {{-- ACTIONS --}}
                            <div class="mt-auto d-flex gap-2 flex-wrap">

                                {{-- View --}}
                                <a href="{{ route('tasks.show', [ 'task'  => $task->id, 'from'  => 'projects', 'scope' => request('scope') ]) }}"
                                    class="btn btn-sm btn-light">
                                    <i class="bi bi-eye-fill"></i>
                                </a>

                                @if(auth()->user()->isAdmin() && is_null($task->archived_at))

                                {{-- Edit --}}
                                <button
                                    class="btn btn-sm btn-light"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editTaskModal"
                                    data-task-id="{{ $task->id }}"
                                    data-task-type="{{ $task->task_type }}"
                                    data-assigned="{{ $task->assigned_user_id }}"
                                    data-start="{{ optional($task->start_date)->format('Y-m-d') }}"
                                    data-due="{{ optional($task->due_date)->format('Y-m-d') }}"
                                    data-project-start="{{ optional($project->start_date)->format('Y-m-d') }}"
                                    data-project-due="{{ optional($project->due_date)->format('Y-m-d') }}">
                                    <i class="bi bi-pencil-fill"></i>
                                </button>

                                {{-- Archive --}}
                                <button
                                    class="btn btn-sm btn-light"
                                    data-bs-toggle="modal"
                                    data-bs-target="#confirmActionModal"
                                    data-action="{{ route('tasks.archive', $task->id) }}"
                                    data-method="PATCH"
                                    data-title="Archive Task"
                                    data-message="Are you sure you want to archive this task?"
                                    data-confirm-text="Archive"
                                    data-confirm-class="btn-danger">
                                    <i class="bi bi-archive-fill text-danger"></i>
                                </button>

                                @endif

                            </div>

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


    @include('projects.partials.add-task-modal')
    @include('projects.partials.edit-task-modal')


    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const editModal = document.getElementById('editTaskModal');

            editModal.addEventListener('show.bs.modal', function(event) {

                const button = event.relatedTarget;

                const taskId = button.getAttribute('data-task-id');
                const taskType = button.getAttribute('data-task-type');
                const assigned = button.getAttribute('data-assigned');
                const start = button.getAttribute('data-start');
                const due = button.getAttribute('data-due');
                const projectStart = button.getAttribute('data-project-start');
                const projectDue = button.getAttribute('data-project-due');

                const startInput = document.getElementById('edit_start_date');
                const dueInput = document.getElementById('edit_due_date');

                document.getElementById('edit_task_id').value = taskId;
                document.getElementById('edit_assigned_user').value = assigned;

                startInput.value = start ?? '';
                dueInput.value = due ?? '';

                // 🔥 ENFORCE PROJECT DATE LIMITS
                startInput.min = projectStart;
                startInput.max = projectDue;

                dueInput.min = start ?? projectStart;
                dueInput.max = projectDue;

                // If user changes start date → update due min dynamically
                startInput.addEventListener('change', function() {
                    if (startInput.value) {
                        dueInput.min = startInput.value;
                    } else {
                        dueInput.min = projectStart;
                    }
                });

                const select = document.getElementById('editTaskTypeSelect');
                const customWrapper = document.getElementById('editCustomTaskWrapper');
                const customInput = document.getElementById('edit_custom_task_name');

                const predefinedTypes = [
                    'Perspective',
                    'Architectural',
                    'Structural',
                    'Mechanical',
                    'Electrical',
                    'Plumbing'
                ];

                if (predefinedTypes.includes(taskType)) {
                    select.value = taskType;
                    customWrapper.classList.add('d-none');
                    customInput.value = '';
                } else {
                    select.value = 'Custom';
                    customWrapper.classList.remove('d-none');
                    customInput.value = taskType;
                }

            });

        });
    </script>

</x-page-wrapper>
@endsection