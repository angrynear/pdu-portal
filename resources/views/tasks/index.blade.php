@extends('layouts.app')

@section('content')

@php
$isAdmin = auth()->user()->isAdmin();
$isMyPage = request()->routeIs('tasks.my');

$pageTitle = $isAdmin
? ($isMyPage ? 'My Tasks' : 'Manage Tasks')
: 'My Tasks';
@endphp

@section('title', $pageTitle)

<x-page-wrapper :title="$pageTitle">

    <x-slot name="actions">

        @php
        $status = request('filter', 'all');
        $type = request('type');
        $personnel = request('personnel');
        $scope = request('scope', 'all');

        $statusLabels = [
        'all' => 'All Status',
        'not_started' => 'Not Started',
        'ongoing' => 'Ongoing',
        'completed' => 'Completed',
        'overdue' => 'Overdue',
        ];
        @endphp

        <form method="GET"
            action="{{ route('tasks.index') }}"
            class="w-100">

            <input type="hidden" name="scope" value="{{ $scope }}">

            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">

                {{-- LEFT SIDE FILTERS --}}
                <div class="d-flex align-items-center gap-2 flex-wrap ms-auto">

                    {{-- PERSONNEL (ADMIN ONLY, ALL SCOPE ONLY) --}}
                    @if(auth()->user()->isAdmin() && $scope === 'all')
                    <select name="personnel"
                        class="form-select form-select-sm shadow-sm w-auto"
                        onchange="this.form.submit()">

                        <option value="">
                            All Personnel ({{ array_sum($personnelCounts ?? []) }})
                        </option>

                        @foreach($personnelList as $id => $name)
                        @php $count = $personnelCounts[$id] ?? 0; @endphp
                        @if($count > 0 || $personnel == $id)
                        <option value="{{ $id }}"
                            {{ $personnel == $id ? 'selected' : '' }}>
                            {{ $name }} ({{ $count }})
                        </option>
                        @endif
                        @endforeach
                    </select>
                    @endif

                    {{-- STATUS --}}
                    <select name="filter"
                        class="form-select form-select-sm shadow-sm w-auto"
                        onchange="this.form.submit()">

                        @foreach($statusLabels as $key => $label)
                        @php
                        $count = $statusCounts[$key] ?? 0;
                        @endphp

                        @if($count > 0 || $status === $key)
                        <option value="{{ $key }}"
                            {{ $status === $key ? 'selected' : '' }}>
                            {{ $label }} ({{ $count }})
                        </option>
                        @endif
                        @endforeach

                    </select>

                    {{-- TYPE --}}
                    <select name="type"
                        class="form-select form-select-sm shadow-sm w-auto"
                        onchange="this.form.submit()">

                        <option value="">
                            All Types ({{ $totalTasksCount ?? 0 }})
                        </option>

                        @foreach($taskTypes as $taskType => $count)
                        <option value="{{ $taskType }}"
                            {{ $type === $taskType ? 'selected' : '' }}>
                            {{ $taskType }} ({{ $count }})
                        </option>
                        @endforeach
                    </select>

                    {{-- RESET --}}
                    <a href="{{ route('tasks.index', ['scope' => $scope]) }}"
                        class="btn btn-sm btn-outline-secondary px-3">
                        Reset
                    </a>

                </div>

                {{-- RIGHT: SCOPE TOGGLE --}}
                @if(auth()->user()->isAdmin())
                <div class="btn-group scope-toggle">
                    <a href="{{ route('tasks.index', ['scope' => 'all']) }}"
                        class="btn btn-sm {{ $scope === 'all' ? 'btn-dark active-scope' : 'btn-outline-secondary' }}">
                        All Tasks
                    </a>

                    <a href="{{ route('tasks.index', ['scope' => 'my']) }}"
                        class="btn btn-sm {{ $scope === 'my' ? 'btn-dark active-scope' : 'btn-outline-secondary' }}">
                        My Tasks
                    </a>
                </div>
                @endif

            </div>

        </form>

    </x-slot>

    {{-- ================= TASK CARDS ================= --}}
    <div class="project-list">

        @forelse($tasks as $task)

        @php
        $isAssignedUser = auth()->id() === $task->assigned_user_id;
        $remark = $task->latest_remark;
        @endphp

        {{-- ================= DESKTOP ================= --}}
        <div class="d-none d-md-block">
            <div class="card project-card {{ $task->status_border_class }} shadow-sm border-0 mb-3">
                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-start">

                        <div>
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <span class="text-muted fw-semibold">
                                    #{{ $tasks->firstItem() + $loop->index }}
                                </span>

                                <div class="fw-semibold">
                                    {{ $task->task_type }}
                                </div>
                            </div>

                            <div class="small text-muted mt-1">
                                <a href="{{ route('projects.show', ['project' => $task->project_id,'from' => 'tasks','scope' => request('scope')]) }}"
                                    class="text-decoration-none text-muted fw-semibold link-hover">
                                    {{ $task->project->name }}
                                </a>
                            </div>

                            <div class="small text-muted mt-1">
                                <i class="bi bi-person me-1"></i>
                                {{ $task->assignedUser->name ?? '—' }}
                            </div>
                        </div>

                        <div class="d-flex align-items-center gap-2">

                            <span class="badge rounded-pill {{ $task->status_badge_class }}">
                                <i class="bi 
                    {{ $task->status === 'completed' ? 'bi-check-circle-fill' :
                       ($task->status === 'overdue' ? 'bi-exclamation-triangle-fill' :
                       ($task->status === 'not_started' ? 'bi-dash-circle-fill' :
                       'bi-arrow-repeat')) }} 
                    me-1">
                                </i>
                                {{ $task->status_label }}
                            </span>

                            <a href="{{ route('tasks.show', [ 'task'  => $task->id, 'from'  => 'tasks', 'scope' => request('scope') ]) }}"
                                class="btn btn-sm btn-light p-2">
                                <i class="bi bi-eye-fill"></i>
                            </a>

                            @if($isAdmin && !$task->assigned_user_id)
                            <button class="btn btn-sm btn-light p-2"
                                data-bs-toggle="modal"
                                data-bs-target="#assignTaskModal"
                                data-task-id="{{ $task->id }}">
                                <i class="bi bi-person-plus-fill"></i>
                            </button>
                            @endif

                            @if($task->assigned_user_id && (!$task->start_date || !$task->due_date))
                            @if($isAdmin || $isAssignedUser)
                            <button class="btn btn-sm btn-light p-2"
                                data-bs-toggle="modal"
                                data-bs-target="#setTaskDateModal"
                                data-task-id="{{ $task->id }}"
                                data-project-start="{{ $task->project->start_date->format('Y-m-d') }}"
                                data-project-due="{{ $task->project->due_date->format('Y-m-d') }}">
                                <i class="bi bi-calendar-plus"></i>
                            </button>
                            @endif
                            @endif

                            @if($task->assigned_user_id && $task->start_date && $task->due_date)
                            @if($isAdmin || $isAssignedUser)
                            <button class="btn btn-sm btn-light p-2"
                                data-bs-toggle="modal"
                                data-bs-target="#updateTaskProgressModal"
                                data-task-id="{{ $task->id }}"
                                data-progress="{{ $task->progress }}"
                                data-start-date="{{ $task->start_date?->format('Y-m-d') }}"
                                data-due-date="{{ $task->due_date?->format('Y-m-d') }}"
                                data-project-start="{{ $task->project->start_date->format('Y-m-d') }}"
                                data-project-due="{{ $task->project->due_date->format('Y-m-d') }}">
                                <i class="bi bi-arrow-repeat"></i>
                            </button>
                            @endif
                            @endif

                            @if($isAdmin)
                            <button class="btn btn-sm btn-light p-2"
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

                    <div class="project-meta mt-1">
                        <div class="d-flex flex-wrap align-items-center gap-4">

                            <div class="meta-item small text-muted">
                                <i class="bi bi-calendar3 me-1"></i>
                                {{ $task->start_date?->format('M. d, Y') ?? '—' }}
                                <span class="mx-1">→</span>
                                <x-due-date :dueDate="$task->due_date" :progress="$task->progress" />
                            </div>

                            @if($task->latest_remark)
                            <div class="meta-item small text-muted mt-1">
                                <i class="bi bi-chat-left-text me-1"></i>
                                {{ Str::limit($task->latest_remark, 100) }}
                            </div>
                            @endif

                        </div>
                    </div>

                    <div class="mt-3">
                        <x-progress-bar :value="$task->progress" />
                    </div>

                </div>
            </div>
        </div>

        {{-- ================= MOBILE ================= --}}
        <div class="d-md-none">
            <div class="card project-card {{ $task->status_border_class }} shadow-sm border-0 mb-3">
                <div class="card-body">

                    <div class="fw-semibold mb-1">
                        #{{ $tasks->firstItem() + $loop->index }}
                        {{ $task->task_type }}
                    </div>

                    <div class="small text-muted">
                        <a href="{{ route('projects.show', ['project' => $task->project_id,'from' => 'tasks','scope' => request('scope')]) }}"
                            class="text-decoration-none text-muted fw-semibold link-hover">
                            {{ $task->project->name }}
                        </a>
                    </div>

                    <div class="small text-muted mb-2">
                        <i class="bi bi-person me-1"></i>
                        {{ $task->assignedUser->name ?? '—' }}
                    </div>

                    <div class="small text-muted mb-2">
                        <i class="bi bi-calendar3 me-1"></i>
                        {{ $task->start_date?->format('M. d, Y') ?? '—' }}
                        →
                        <x-due-date :dueDate="$task->due_date" :progress="$task->progress" />
                    </div>

                    @if($task->latest_remark)
                    <div class="small text-muted mb-2">
                        <i class="bi bi-chat-left-text me-1"></i>
                        {{ Str::limit($task->latest_remark, 120) }}
                    </div>
                    @endif

                    <div class="mt-2">
                        <x-progress-bar :value="$task->progress" />
                    </div>

                    <div class="mt-3">
                        <div class="mb-2">
                            <span class="badge rounded-pill {{ $task->status_badge_class }}">
                                {{ $task->status_label }}
                            </span>
                        </div>

                        <div class="d-flex gap-2">

                            <a href="{{ route('tasks.show', [ 'task'  => $task->id, 'from'  => 'tasks', 'scope' => request('scope') ]) }}"
                                class="btn btn-sm btn-light p-2">
                                <i class="bi bi-eye-fill"></i>
                            </a>

                            @if($isAdmin && !$task->assigned_user_id)
                            <button class="btn btn-sm btn-light flex-fill"
                                data-bs-toggle="modal"
                                data-bs-target="#assignTaskModal"
                                data-task-id="{{ $task->id }}">
                                <i class="bi bi-person-plus-fill"></i>
                            </button>
                            @endif

                            @if($task->assigned_user_id && (!$task->start_date || !$task->due_date))
                            @if($isAdmin || $isAssignedUser)
                            <button class="btn btn-sm btn-light flex-fill"
                                data-bs-toggle="modal"
                                data-bs-target="#setTaskDateModal"
                                data-task-id="{{ $task->id }}"
                                data-project-start="{{ $task->project->start_date->format('Y-m-d') }}"
                                data-project-due="{{ $task->project->due_date->format('Y-m-d') }}">
                                <i class="bi bi-calendar-plus"></i>
                            </button>
                            @endif
                            @endif

                            @if($task->assigned_user_id && $task->start_date && $task->due_date)
                            @if($isAdmin || $isAssignedUser)
                            <button class="btn btn-sm btn-light flex-fill"
                                data-bs-toggle="modal"
                                data-bs-target="#updateTaskProgressModal"
                                data-task-id="{{ $task->id }}"
                                data-progress="{{ $task->progress }}"
                                data-start-date="{{ $task->start_date?->format('Y-m-d') }}"
                                data-due-date="{{ $task->due_date?->format('Y-m-d') }}"
                                data-project-start="{{ $task->project->start_date->format('Y-m-d') }}"
                                data-project-due="{{ $task->project->due_date->format('Y-m-d') }}">
                                <i class="bi bi-arrow-repeat"></i>
                            </button>
                            @endif
                            @endif

                            @if($isAdmin)
                            <button class="btn btn-sm btn-light flex-fill"
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
        </div>

        @empty
        <div class="text-center text-muted py-0">
            No tasks found.
        </div>
        @endforelse

        <div class="mt-4">
            {{ $tasks->links() }}
        </div>

    </div>

    @include('tasks.partials.update-task-modal')
    @include('tasks.partials.assign-task-modal')
    @include('tasks.partials.set-task-date-modal')

    @push('scripts')

    {{-- View Remarks Script --}}
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

    <script>
        function toggleRemarkMobile(id) {
            const preview = document.getElementById('preview-mobile-' + id);
            const full = document.getElementById('full-mobile-' + id);
            const button = document.getElementById('btn-mobile-' + id);

            if (!preview || !full || !button) return;

            if (full.classList.contains('d-none')) {
                preview.classList.add('d-none');
                full.classList.remove('d-none');
                button.innerText = 'Hide';
            } else {
                preview.classList.remove('d-none');
                full.classList.add('d-none');
                button.innerText = 'View Full';
            }
        }
    </script>


    {{-- Task Modals Script --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const updateModal = document.getElementById('updateTaskProgressModal');
            const setDateModal = document.getElementById('setTaskDateModal');
            const assignModal = document.getElementById('assignTaskModal');

            // ================================
            // UPDATE PROGRESS MODAL
            // ================================
            if (updateModal) {
                updateModal.addEventListener('show.bs.modal', function(event) {

                    const button = event.relatedTarget;

                    const taskId = button.getAttribute('data-task-id');
                    const progress = button.getAttribute('data-progress');
                    const startDate = button.getAttribute('data-start-date');
                    const dueDate = button.getAttribute('data-due-date');
                    const projectStart = button.getAttribute('data-project-start');
                    const projectDue = button.getAttribute('data-project-due');

                    document.getElementById('task_id').value = taskId;

                    const slider = document.getElementById('task_progress');
                    const progressText = document.getElementById('progressValue');

                    slider.value = progress;
                    progressText.innerText = progress;

                    slider.oninput = function() {
                        progressText.innerText = this.value;
                    };

                    const startInput = document.getElementById('update_start_date');
                    const dueInput = document.getElementById('update_due_date');

                    startInput.value = startDate ?? '';
                    dueInput.value = dueDate ?? '';

                    // Restrict dates within project schedule
                    startInput.min = projectStart;
                    startInput.max = projectDue;

                    dueInput.min = projectStart;
                    dueInput.max = projectDue;
                });
            }

            // ================================
            // SET DATE MODAL
            // ================================
            if (setDateModal) {
                setDateModal.addEventListener('show.bs.modal', function(event) {

                    const button = event.relatedTarget;

                    const taskId = button.getAttribute('data-task-id');
                    const projectStart = button.getAttribute('data-project-start');
                    const projectDue = button.getAttribute('data-project-due');

                    const startInput = document.getElementById('set_start_date');
                    const dueInput = document.getElementById('set_due_date');

                    document.getElementById('set_date_task_id').value = taskId;

                    startInput.value = '';
                    dueInput.value = '';

                    startInput.min = projectStart;
                    startInput.max = projectDue;

                    dueInput.min = projectStart;
                    dueInput.max = projectDue;
                });
            }

            // ================================
            // ASSIGN TASK MODAL
            // ================================
            if (assignModal) {
                assignModal.addEventListener('show.bs.modal', function(event) {

                    const button = event.relatedTarget;
                    const taskId = button.getAttribute('data-task-id');

                    document.getElementById('assign_task_id').value = taskId;
                });
            }

        });
    </script>

    @endpush

</x-page-wrapper>
@endsection