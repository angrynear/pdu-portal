{{-- ================= ADD PERSONAL TASK MODAL ================= --}}
<div class="modal fade" id="addPersonalTaskModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">

        <form method="POST"
            action="{{ route('tasks.store') }}"
            class="modal-content border-0 shadow-sm">
            @csrf

            <input type="hidden" name="form_context" value="add_personal_task">
            <input type="hidden" name="project_id" value="">
            <input type="hidden" name="task_type_select" value="Custom">
            <input type="hidden" name="assigned_user_id" value="{{ auth()->id() }}">

            {{-- HEADER --}}
            <div class="modal-header border-0 pb-0">
                <div>
                    <h5 class="modal-title fw-semibold mb-0">
                        Add Personal Task
                    </h5>
                </div>

                <button type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body pt-3">

                <div class="mb-3">
                    <label class="form-label fw-semibold small">
                        Task Name
                    </label>
                    <input type="text"
                        name="custom_task_name"
                        class="form-control"
                        required>
                </div>

                <div class="row g-3">
                    <div class="col-6">
                        <label class="form-label fw-semibold small">
                            Start Date
                        </label>
                        <input type="date"
                            name="start_date"
                            class="form-control">
                    </div>

                    <div class="col-6">
                        <label class="form-label fw-semibold small">
                            Due Date
                        </label>
                        <input type="date"
                            name="due_date"
                            class="form-control">
                    </div>
                </div>

            </div>

            <hr class="mb-0">

            <div class="modal-footer border-0 pt-0 flex-column flex-sm-row gap-2">

                <button type="button"
                    class="btn btn-light w-100 w-sm-auto"
                    data-bs-dismiss="modal">
                    Cancel
                </button>

                <button type="submit"
                    id="createPersonalTaskBtn"
                    class="btn btn-primary w-100 w-sm-auto">
                    <i class="bi bi-list-task me-1"></i>
                    Create Task
                </button>
            </div>

        </form>
    </div>
</div>

{{-- Assign Task Modal Script for Protect...--}}
<script>
    document.addEventListener('DOMContentLoaded', function() {

        const modal = document.getElementById('addPersonalTaskModal');
        const form = modal?.querySelector('form');
        const submitBtn = document.getElementById('createPersonalTaskBtn');

        if (form && submitBtn) {
            form.addEventListener('submit', function() {
                submitBtn.disabled = true;
                submitBtn.innerHTML = `
                <span class="spinner-border spinner-border-sm me-1"></span>
                Creating Task...
            `;
            });
        }

    });
</script>