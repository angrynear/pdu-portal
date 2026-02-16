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

    {{-- ============================= --}}
    {{-- DESKTOP TABLE VIEW --}}
    {{-- ============================= --}}
    <div class="d-none d-lg-block">
        <div class="table-responsive">
            <table class="table align-middle table-sm">
                <thead class="table-light">
                    <tr>
                        <th class="text-center" style="width: 50px;">No.</th>
                        <th style="width: 130px;">Task</th>
                        <th style="width: 150px;">Project Title</th>
                        <th style="width: 120px;">Assigned Personnel</th>
                        <th style="width: 120px;">Timeline</th>
                        <th class="text-center" style="width: 90px;">Progress</th>
                        <th class="text-center" style="width: 120px;">Remarks</th>
                        <th style="width: 120px;" class="text-center">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($tasks as $task)
                    <tr>
                        <td class="text-center">
                            {{ $tasks->firstItem() + $loop->index }}
                        </td>

                        {{-- Task --}}
                        <td>{{ $task->task_type }}</td>

                        {{-- Project --}}
                        <td>
                            <a href="{{ route('projects.show', $task->project_id) }}?from=tasks"
                                class="text-decoration-none text-dark fw-semibold link-hover">
                                {{ $task->project->name }}
                            </a>
                        </td>

                        {{-- Assigned --}}
                        <td>{{ $task->assignedUser->name ?? '—' }}</td>

                        {{-- Timeline --}}
                        <td class="small">
                            <div>
                                <strong>Start Date:</strong>
                                {{ $task->start_date?->format('M. j, Y') ?? '—' }}
                            </div>
                            <div>
                                <strong>Due Date: </strong>
                                <x-due-date
                                    :dueDate="$task->due_date"
                                    :progress="$task->progress" />
                            </div>
                        </td>

                        {{-- Progress --}}
                        <td class="text-center align-middle">
                            <x-progress-bar :value="$task->progress" />
                        </td>

                        <td class="small align-middle">

                            @php
                            $remark = $task->latest_remark;
                            @endphp


                            @if($remark)

                            <div>
                                <span id="preview-{{ $task->id }}">
                                    {{ Str::limit($remark, 60) }}
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
                                View Full
                            </button>
                            @endif

                            @else
                            <span class="text-muted">—</span>
                            @endif

                        </td>

                        {{-- Actions --}}
                        <td class="text-center">

                            {{-- Always can View --}}
                            <a href="{{ route('tasks.show', [
        'task' => $task->id,
        'from' => request()->routeIs('tasks.my') ? 'my' : 'manage'
    ]) }}"
                                class="btn btn-sm btn-secondary">
                                View
                            </a>

                            @if (is_null($task->archived_at) && is_null($task->project->archived_at))

                            @php
                            $isAdmin = auth()->user()->isAdmin();
                            $isAssignedUser = auth()->id() === $task->assigned_user_id;
                            @endphp

                            {{-- ========================= --}}
                            {{-- ASSIGN (ADMIN ONLY) --}}
                            {{-- ========================= --}}
                            @if($isAdmin && !$task->assigned_user_id)
                            <button
                                class="btn btn-sm btn-info"
                                data-bs-toggle="modal"
                                data-bs-target="#assignTaskModal"
                                data-task-id="{{ $task->id }}">
                                Assign
                            </button>
                            @endif


                            {{-- ========================= --}}
                            {{-- SET DATE --}}
                            {{-- ========================= --}}
                            @if($task->assigned_user_id && (!$task->start_date || !$task->due_date))

                            @if($isAdmin || $isAssignedUser)
                            <button
                                class="btn btn-sm btn-warning"
                                data-bs-toggle="modal"
                                data-bs-target="#setTaskDateModal"
                                data-task-id="{{ $task->id }}"
                                data-project-start="{{ $task->project->start_date->format('Y-m-d') }}"
                                data-project-due="{{ $task->project->due_date->format('Y-m-d') }}">
                                Set Date
                            </button>
                            @else
                            <button class="btn btn-sm btn-warning" disabled>
                                Set Date
                            </button>
                            @endif

                            @endif


                            {{-- ========================= --}}
                            {{-- UPDATE PROGRESS --}}
                            {{-- ========================= --}}
                            @if($task->assigned_user_id && $task->start_date && $task->due_date)

                            @if($isAdmin || $isAssignedUser)
                            <button
                                class="btn btn-sm btn-primary ms-1"
                                data-bs-toggle="modal"
                                data-bs-target="#updateTaskProgressModal"
                                data-task-id="{{ $task->id }}"
                                data-progress="{{ $task->progress }}"
                                data-start-date="{{ $task->start_date?->format('Y-m-d') }}"
                                data-due-date="{{ $task->due_date?->format('Y-m-d') }}"
                                data-project-start="{{ $task->project->start_date->format('Y-m-d') }}"
                                data-project-due="{{ $task->project->due_date->format('Y-m-d') }}">
                                Update
                            </button>
                            @else
                            <button class="btn btn-sm btn-primary ms-1" disabled>
                                Update
                            </button>
                            @endif

                            @endif


                            {{-- ========================= --}}
                            {{-- ARCHIVE (ADMIN ONLY) --}}
                            {{-- ========================= --}}
                            @if($isAdmin)
                            <button
                                type="button"
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
                            @endif

                            @else
                            <span class="d-block text-muted small mt-1">
                                {{ $task->project->archived_at ? 'Project Archived' : 'Task Archived' }}
                            </span>
                            @endif
                        </td>

                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted">
                            No tasks found.
                        </td>
                    </tr>
                    @endforelse

                </tbody>
            </table>

            <div class="mt-3">
                {{ $tasks->links() }}
            </div>
        </div>
    </div>

    {{-- ============================= --}}
    {{-- MOBILE CARD VIEW --}}
    {{-- ============================= --}}
    <div class="d-lg-none">

        @forelse ($tasks as $task)

        @php
        $isAdmin = auth()->user()->isAdmin();
        $isAssignedUser = auth()->id() === $task->assigned_user_id;
        $remark = $task->latestRemarkLog->changes['remark']['new'] ?? null;
        @endphp

        <div class="card shadow-sm mb-3 border-0">
            <div class="card-body">

                {{-- Task Title --}}
                <h6 class="fw-bold mb-1">
                    {{ $task->task_type }}
                </h6>

                {{-- Project --}}
                <div class="small mb-2">
                    Project:
                    <a href="{{ route('projects.show', $task->project_id) }}?from=tasks"
                        class="text-decoration-none text-dark fw-semibold link-hover">
                        {{ $task->project->name }}
                    </a>
                </div>

                {{-- Assigned --}}
                <div class="small mb-1">
                    Assigned:
                    {{ $task->assignedUser->name ?? '—' }}
                </div>

                {{-- Timeline --}}
                <div class="small mb-2">
                    <div>
                        Start:
                        {{ $task->start_date?->format('M. j, Y') ?? '—' }}
                    </div>
                    <div>
                        Due:
                        <x-due-date
                            :dueDate="$task->due_date"
                            :progress="$task->progress" />
                    </div>
                </div>

                {{-- Remarks --}}
                <div class="small mb-2">

                    <div class="fw-semibold">Remarks</div>

                    @php
                    $remark = $task->latest_remark;
                    @endphp

                    @if($remark)

                    <span id="preview-mobile-{{ $task->id }}">
                        {{ Str::limit($remark, 80) }}
                    </span>

                    <span id="full-mobile-{{ $task->id }}" class="d-none">
                        {{ $remark }}
                    </span>

                    @if(strlen($remark) > 80)
                    <button type="button"
                        class="btn btn-link btn-sm p-0"
                        onclick="toggleRemarkMobile({{ $task->id }})"
                        id="btn-mobile-{{ $task->id }}">
                        View Full
                    </button>
                    @endif

                    @else
                    <span class="text-muted">—</span>
                    @endif

                </div>

                {{-- Progress --}}
                <div class="mb-2">
                    <x-progress-bar :value="$task->progress" />
                </div>

                {{-- Actions --}}
                <div class="d-flex gap-2 flex-wrap">

                    {{-- VIEW --}}
                    <a href="{{ route('tasks.show', [
                    'task' => $task->id,
                    'from' => request()->routeIs('tasks.my') ? 'my' : 'manage'
                ]) }}"
                        class="btn btn-sm btn-secondary flex-fill">
                        View
                    </a>

                    @if (is_null($task->archived_at) && is_null($task->project->archived_at))

                    {{-- ASSIGN --}}
                    @if($isAdmin && !$task->assigned_user_id)
                    <button
                        class="btn btn-sm btn-info flex-fill"
                        data-bs-toggle="modal"
                        data-bs-target="#assignTaskModal"
                        data-task-id="{{ $task->id }}">
                        Assign
                    </button>
                    @endif

                    {{-- SET DATE --}}
                    @if($task->assigned_user_id && (!$task->start_date || !$task->due_date))

                    @if($isAdmin || $isAssignedUser)
                    <button
                        class="btn btn-sm btn-warning flex-fill"
                        data-bs-toggle="modal"
                        data-bs-target="#setTaskDateModal"
                        data-task-id="{{ $task->id }}"
                        data-project-start="{{ $task->project->start_date->format('Y-m-d') }}"
                        data-project-due="{{ $task->project->due_date->format('Y-m-d') }}">
                        Set Date
                    </button>
                    @else
                    <button class="btn btn-sm btn-warning flex-fill" disabled>
                        Set Date
                    </button>
                    @endif

                    @endif

                    {{-- UPDATE --}}
                    @if($task->assigned_user_id && $task->start_date && $task->due_date)

                    @if($isAdmin || $isAssignedUser)
                    <button
                        class="btn btn-sm btn-primary flex-fill"
                        data-bs-toggle="modal"
                        data-bs-target="#updateTaskProgressModal"
                        data-task-id="{{ $task->id }}"
                        data-progress="{{ $task->progress }}"
                        data-start-date="{{ $task->start_date?->format('Y-m-d') }}"
                        data-due-date="{{ $task->due_date?->format('Y-m-d') }}"
                        data-project-start="{{ $task->project->start_date->format('Y-m-d') }}"
                        data-project-due="{{ $task->project->due_date->format('Y-m-d') }}">
                        Update
                    </button>
                    @else
                    <button class="btn btn-sm btn-primary flex-fill" disabled>
                        Update
                    </button>
                    @endif

                    @endif

                    {{-- ARCHIVE --}}
                    @if($isAdmin)
                    <button
                        type="button"
                        class="btn btn-sm btn-danger flex-fill"
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
                    @endif

                    @else
                    <div class="text-muted small">
                        {{ $task->project->archived_at ? 'Project Archived' : 'Task Archived' }}
                    </div>
                    @endif

                </div>

            </div>
        </div>

        @empty
        <div class="text-center text-muted py-4">
            No tasks found.
        </div>
        @endforelse

        <div class="mt-3">
            {{ $tasks->links() }}
        </div>

    </div>

    </div>

    {{-- UPDATE TASK MODAL --}}
    @include('tasks.partials.update-task-modal')

    {{-- ASSIGN TASK MODAL --}}
    @include('tasks.partials.assign-task-modal')

    {{-- SET TASK DATE MODAL --}}
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