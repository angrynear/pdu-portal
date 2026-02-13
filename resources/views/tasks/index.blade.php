@extends('layouts.app')

@section('title', 'Manage Tasks')

@section('content')
<x-page-wrapper title="Tasks List">

    <div class="table-responsive">
        <table class="table align-middle table-sm">
            <thead class="table-light">
                <tr>
                    <th class="text-center" style="width: 50px;">No.</th>
                    <th style="width: 120px;">Task</th>
                    <th style="width: 180px;">Project Title</th>
                    <th style="width: 130px;">Assigned Personnel</th>
                    <th style="width: 100px;">Timeline</th>
                    <th class="text-center" style="width: 70px;">Progress</th>
                    <th class="text-center" style="width: 130px;">Remarks</th>
                    <th style="width: 130px;" class="text-center">Actions</th>
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
                            <strong>Due Date: </strong>{{ $task->due_date?->format('M. j, Y') ?? '—' }}
                        </div>
                    </td>

                    {{-- Progress --}}
                    <td class="text-center align-middle">

                        <div class="progress" style="height: 6px;">
                            <div
                                class="progress-bar
                                {{ $task->progress == 100 ? 'bg-success' : 'bg-primary' }}"
                                style="width: {{ $task->progress }}%">
                            </div>
                        </div>
                        <div class="small {{ $task->progress == 100 ? 'text-success fw-semibold' : 'text-muted' }}">
                            {{ $task->progress }}%
                        </div>
                    </td>

                    <td class="align-items-center">
                        @php
                        $remark = $task->latestRemark->remark ?? null;
                        @endphp

                        @if($remark)

                        <div>

                            {{-- Short preview --}}
                            <span id="preview-{{ $task->id }}">
                                {{ Str::limit($remark, 30) }}
                            </span>

                            {{-- Full remark --}}
                            <span id="full-{{ $task->id }}" class="d-none text-dark">
                                {{ $remark }}
                            </span>

                        </div>

                        @if(strlen($remark) > 30)
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

                    {{-- Actions --}}
                    <td class="text-center">
                        {{-- View --}}
                        <a href="{{ route('tasks.show', ['task' => $task->id, 'from' => 'tasks']) }}"
                            class="btn btn-sm btn-secondary">
                            View
                        </a>

                        @if (is_null($task->archived_at) && is_null($task->project->archived_at))

                        @if (!$task->start_date || !$task->due_date)

                        {{-- Set Date --}}
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

                        {{-- Update --}}
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
                        @endif

                        {{-- Archive Task --}}
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

    {{-- UPDATE TASK MODAL --}}
    <div class="modal fade" id="updateTaskProgressModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <form method="POST"
                action="{{ route('tasks.updateProgress') }}"
                enctype="multipart/form-data"
                class="modal-content">
                @csrf
                @method('PATCH')

                <input type="hidden" name="task_id" id="task_id">

                <div class="modal-header">
                    <h5 class="modal-title">Update Task Progress</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    {{-- Progress --}}
                    <div class="mb-3">
                        <label class="form-label">
                            Progress: <strong><span id="progressValue">0</span>%</strong>
                        </label>

                        <input type="range"
                            name="progress"
                            id="task_progress"
                            class="form-range"
                            min="0"
                            max="100"
                            step="1"
                            value="0">
                    </div>

                    {{-- Dates --}}
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">
                                Start Date <span class="text-muted">(optional change)</span>
                            </label>
                            <input type="date"
                                name="start_date"
                                id="update_start_date"
                                class="form-control">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">
                                Due Date <span class="text-muted">(optional change)</span>
                            </label>
                            <input type="date"
                                name="due_date"
                                id="update_due_date"
                                class="form-control">
                        </div>
                    </div>

                    {{-- Remarks --}}
                    <div class="mb-3">
                        <label class="form-label">
                            Remarks <span class="text-muted">(optional)</span>
                        </label>

                        <textarea name="remark"
                            id="remarkField"
                            class="form-control"
                            rows="3"
                            placeholder="Add remarks if necessary…"></textarea>
                    </div>

                    {{-- File Upload --}}
                    <div class="mb-3">
                        <label class="form-label">Attachment(s)</label>
                        <input type="file"
                            name="attachments[]"
                            class="form-control"
                            multiple>
                    </div>
                </div>

                <div class="modal-footer">

                    <small class="text-muted me-auto">
                        Only changes will be recorded.
                    </small>

                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Cancel
                    </button>
                    <button type="submit" id="updateProgressBtn" class="btn btn-primary">
                        Save Update
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- SET TASK DATE MODAL --}}
    <div class="modal fade" id="setTaskDateModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <form method="POST"
                action="{{ route('tasks.setDates') }}"
                class="modal-content">
                @csrf
                @method('PATCH')

                <input type="hidden" name="task_id" id="set_date_task_id">

                <div class="modal-header">
                    <h5 class="modal-title">Set Task Dates</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <div class="mb-3">
                        <label class="form-label">Start Date</label>
                        <input type="date"
                            name="start_date"
                            id="set_start_date"
                            class="form-control"
                            required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Due Date</label>
                        <input type="date"
                            name="due_date"
                            id="set_due_date"
                            class="form-control"
                            required>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">
                        Cancel
                    </button>
                    <button type="submit" id="setDateBtn" class="btn btn-warning">
                        Set Dates
                    </button>
                </div>
            </form>
        </div>
    </div>

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

    {{-- Set Date and Update Progress Modal Script --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const updateModal = document.getElementById('updateTaskProgressModal');
            const setDateModal = document.getElementById('setTaskDateModal');

            // ================================
            // UPDATE MODAL
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
                    document.getElementById('task_progress').value = progress;
                    document.getElementById('progressValue').innerText = progress;

                    const startInput = document.getElementById('update_start_date');
                    const dueInput = document.getElementById('update_due_date');

                    startInput.value = startDate ?? '';
                    dueInput.value = dueDate ?? '';

                    // Apply restriction
                    startInput.min = projectStart;
                    startInput.max = projectDue;

                    dueInput.min = projectStart;
                    dueInput.max = projectDue;

                    // Live slider display
                    const slider = document.getElementById('task_progress');
                    slider.oninput = function() {
                        document.getElementById('progressValue').innerText = this.value;
                    };
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

        });
    </script>

{{-- Script for protecting forms from multiple submissions --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            function protectForm(formSelector, buttonId, loadingText) {
                const form = document.querySelector(formSelector);
                const button = document.getElementById(buttonId);

                if (form && button) {
                    form.addEventListener('submit', function() {
                        button.disabled = true;
                        button.innerText = loadingText;
                    });
                }
            }

            // Edit Task
            protectForm('#editTaskModal form', 'editTaskBtn', 'Saving...');

            // Set Date
            protectForm('#setTaskDateModal form', 'setDateBtn', 'Saving...');

            // Update Progress
            protectForm('#updateTaskProgressModal form', 'updateProgressBtn', 'Saving...');

        });
    </script>

    @endpush

</x-page-wrapper>
@endsection