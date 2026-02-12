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

                    <td>
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
                        {{-- Update (future) --}}
                        <button
                            class="btn btn-sm btn-primary ms-1"
                            data-bs-toggle="modal"
                            data-bs-target="#updateTaskProgressModal"
                            data-task-id="{{ $task->id }}"
                            data-progress="{{ $task->progress }}">
                            Update
                        </button>

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
                    <button class="btn btn-primary">
                        Save Update
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

    {{-- Update Task Modal Script --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const modal = document.getElementById('updateTaskProgressModal');
            const form = modal.querySelector('form');
            const progressInput = document.getElementById('task_progress');
            const progressValue = document.getElementById('progressValue');
            const taskIdInput = document.getElementById('task_id');
            const remarkField = document.getElementById('remarkField');
            const saveButton = form.querySelector('button[type="submit"]');
            const fileInput = form.querySelector('input[type="file"]');

            let originalProgress = 0;

            // When modal opens
            modal.addEventListener('show.bs.modal', function(event) {

                const button = event.relatedTarget;

                originalProgress = parseInt(button.getAttribute('data-progress')) || 0;

                taskIdInput.value = button.getAttribute('data-task-id');
                progressInput.value = originalProgress;
                progressValue.textContent = originalProgress;

                remarkField.value = '';
                fileInput.value = '';

                saveButton.disabled = true; // disable initially
            });

            // Update progress display
            progressInput.addEventListener('input', function() {
                progressValue.textContent = this.value;
                checkChanges();
            });

            remarkField.addEventListener('input', checkChanges);
            fileInput.addEventListener('change', checkChanges);

            function checkChanges() {
                const progressChanged = parseInt(progressInput.value) !== originalProgress;
                const hasRemark = remarkField.value.trim() !== '';
                const hasFiles = fileInput.files.length > 0;

                saveButton.disabled = !(progressChanged || hasRemark || hasFiles);
            }

            // Clean empty remark before submit
            form.addEventListener('submit', function() {
                if (remarkField.value.trim() === '') {
                    remarkField.value = null;
                }
            });

        });
    </script>
    @endpush

</x-page-wrapper>
@endsection