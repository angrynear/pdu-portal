{{-- ================= EDIT TASK MODAL ================= --}}
<div class="modal fade" id="editTaskModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">

        <form id="editTaskForm"
            method="POST"
            action="{{ route('tasks.update') }}"
            class="modal-content border-0 shadow">
            @csrf
            @method('PUT')

            <input type="hidden" name="form_context" value="edit_task">
            <input type="hidden" name="task_id" id="edit_task_id">

            {{-- HEADER --}}
            <div class="modal-header border-0 pb-2">
                <div>
                    <h5 class="modal-title fw-semibold mb-0">Edit Task</h5>
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

                    {{-- TASK INFO --}}

                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Task Type</label>
                        <select name="task_type_select"
                            id="editTaskTypeSelect"
                            class="form-select"
                            required>
                            <option value="">— Select Task Type —</option>
                            @foreach (['Perspective','Architectural','Structural','Mechanical','Electrical','Plumbing','Custom'] as $type)
                            <option value="{{ $type }}">{{ $type }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6 d-none" id="editCustomTaskWrapper">
                        <label class="form-label fw-semibold small">Custom Task Name</label>
                        <input type="text"
                            name="custom_task_name"
                            id="edit_custom_task_name"
                            class="form-control">
                    </div>

                    {{-- ASSIGNMENT --}}

                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Assigned To</label>
                        <select name="assigned_user_id"
                            id="edit_assigned_user"
                            class="form-select"
                            required>
                            @foreach ($users as $user)
                            <option value="{{ $user->id }}">
                                {{ $user->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- TIMELINE --}}

                    <div class="col-md-3">
                        <label class="form-label fw-semibold small">Start Date</label>
                        <input type="date"
                            name="start_date"
                            id="edit_start_date"
                            class="form-control">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-semibold small">Due Date</label>
                        <input type="date"
                            name="due_date"
                            id="edit_due_date"
                            class="form-control">
                    </div>

                </div>
            </div>

            <hr class="mb-0">

            <div class="modal-footer border-0 pt-3">
                <button type="button"
                    class="btn btn-light"
                    data-bs-dismiss="modal">
                    Cancel
                </button>

                <button type="submit"
                    id="editTaskBtn"
                    class="btn btn-primary px-4">
                    Save Changes
                </button>
            </div>

        </form>
    </div>
</div>


<script>
    document.addEventListener('DOMContentLoaded', function() {

        const form = document.getElementById('editTaskForm');
        const button = document.getElementById('editTaskBtn');

        if (form && button) {
            form.addEventListener('submit', function() {
                button.disabled = true;
                button.innerText = "Updating...";
            });
        }

    });
</script>