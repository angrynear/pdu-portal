{{-- ================= ADD PERSONAL TASK MODAL ================= --}}
<div class="modal fade" id="addPersonalTaskModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content border-0 shadow">

            <form action="{{ route('tasks.store') }}" method="POST">
                @csrf

                <input type="hidden" name="form_context" value="add_personal_task">
                <input type="hidden" name="project_id" value="">
                <input type="hidden" name="task_type_select" value="Custom">
                <input type="hidden" name="assigned_user_id" value="{{ auth()->id() }}">

                {{-- HEADER --}}
                <div class="modal-header border-0 pb-2">
                    <h5 class="modal-title fw-semibold mb-0">
                        Add Personal Task
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <hr class="mt-2 mb-0">

                <div class="modal-body pt-4">

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

                <div class="modal-footer border-0 pt-3">
                    <button type="button"
                            class="btn btn-light"
                            data-bs-dismiss="modal">
                        Cancel
                    </button>

                    <button type="submit"
                            class="btn btn-primary px-4">
                        Create
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>