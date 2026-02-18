{{-- ================= ADD TASK MODAL ================= --}}
<div class="modal fade" id="addTaskModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow">

            <form id="addTaskForm" action="{{ route('tasks.store') }}" method="POST">
                @csrf

                <input type="hidden" name="form_context" value="add_task">
                <input type="hidden" name="project_id" value="{{ $project->id }}">

                {{-- HEADER --}}
                <div class="modal-header border-0 pb-2">
                    <div>
                        <h5 class="modal-title fw-semibold mb-0">Add Task</h5>
                        <small class="text-muted">
                            Project Date Range:
                            {{ $project->start_date?->format('M. d, Y') }}
                            →
                            {{ $project->due_date?->format('M. d, Y') }}
                        </small>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <hr class="mt-2 mb-0">

                <div class="modal-body pt-4">

                    <div class="row g-4">

                        {{-- TASK INFORMATION --}}

                        {{-- Task Type --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Task Type</label>
                            <select name="task_type_select"
                                id="taskTypeSelect"
                                class="form-select @error('task_type_select') is-invalid @enderror"
                                required>
                                <option value="">— Select Task Type —</option>
                                @foreach (['Perspective','Architectural','Structural','Mechanical','Electrical','Plumbing','Custom'] as $type)
                                <option value="{{ $type }}"
                                    {{ old('task_type_select') === $type ? 'selected' : '' }}>
                                    {{ $type }}
                                </option>
                                @endforeach
                            </select>
                            @error('task_type_select')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Custom Task --}}
                        <div class="col-md-6 {{ old('task_type_select') === 'Custom' ? '' : 'd-none' }}"
                            id="customTaskWrapper">
                            <label class="form-label fw-semibold small">Custom Task Name</label>
                            <input type="text"
                                name="custom_task_name"
                                class="form-control @error('custom_task_name') is-invalid @enderror"
                                value="{{ old('custom_task_name') }}">
                            @error('custom_task_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- ASSIGNMENT --}}

                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Assign To</label>
                            <select name="assigned_user_id"
                                class="form-select @error('assigned_user_id') is-invalid @enderror"
                                required>
                                <option value="">— Select Personnel —</option>
                                @foreach ($users as $user)
                                <option value="{{ $user->id }}"
                                    {{ old('assigned_user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- TIMELINE --}}

                        <div class="col-md-3">
                            <label class="form-label fw-semibold small">Start Date</label>
                            <input type="date"
                                id="add_start_date"
                                name="start_date"
                                class="form-control @error('start_date') is-invalid @enderror"
                                value="{{ old('start_date') }}">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold small">Due Date</label>
                            <input type="date"
                                id="add_due_date"
                                name="due_date"
                                class="form-control @error('due_date') is-invalid @enderror"
                                value="{{ old('due_date') }}">
                        </div>

                    </div>
                </div>

                <hr class="mb-0">

                {{-- FOOTER --}}
                <div class="modal-footer border-0 pt-3">
                    <button type="button"
                        class="btn btn-light"
                        data-bs-dismiss="modal">
                        Cancel
                    </button>

                    <button type="submit"
                        id="createTaskBtn"
                        class="btn btn-success px-4">
                        Create Task
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        const form = document.getElementById('addTaskForm');
        const button = document.getElementById('createTaskBtn');

        if (form && button) {
            form.addEventListener('submit', function() {
                button.disabled = true;
                button.innerText = "Creating...";
            });
        }

        const startInput = document.getElementById('add_start_date');
        const dueInput = document.getElementById('add_due_date');

        if (startInput && dueInput) {
            const projectStart = "{{ optional($project->start_date)->format('Y-m-d') }}";
            const projectDue = "{{ optional($project->due_date)->format('Y-m-d') }}";

            startInput.min = projectStart;
            startInput.max = projectDue;

            dueInput.min = projectStart;
            dueInput.max = projectDue;
        }

    });
</script>