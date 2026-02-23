<div class="modal fade" id="setTaskDateModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">

        <form method="POST"
            action="{{ route('tasks.setDates') }}"
            class="modal-content border-0 shadow">

            @csrf
            @method('PATCH')

            <input type="hidden" name="task_id" id="set_date_task_id">

            {{-- HEADER --}}
            <div class="modal-header border-0 pb-0">
                <div>
                    <h6 class="modal-title fw-semibold mb-1">
                        Set Task Dates
                    </h6>
                    <div class="small text-muted">
                        Dates must be within the project timeline.
                    </div>
                </div>

                <button type="button"
                    class="btn-close"
                    data-bs-dismiss="modal">
                </button>
            </div>

            {{-- BODY --}}
            <div class="modal-body pt-3">

                <div class="p-3 rounded-3 bg-light">

                    {{-- Start Date --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-uppercase text-muted">
                            Start Date
                        </label>

                        <input type="date"
                            name="start_date"
                            id="set_start_date"
                            class="form-control"
                            required>
                    </div>

                    {{-- Due Date --}}
                    <div>
                        <label class="form-label fw-semibold small text-uppercase text-muted">
                            Due Date
                        </label>

                        <input type="date"
                            name="due_date"
                            id="set_due_date"
                            class="form-control"
                            required>
                    </div>

                </div>

            </div>

            {{-- FOOTER --}}
            <div class="modal-footer border-0 pt-0 flex-column flex-sm-row gap-2">

                <button type="button"
                    class="btn btn-outline-secondary w-100 w-sm-auto"
                    data-bs-dismiss="modal">
                    Cancel
                </button>

                <button type="submit"
                    id="setDateBtn"
                    class="btn btn-primary w-100 w-sm-auto">
                    Set Dates
                </button>

            </div>

        </form>

    </div>
</div>



{{-- Set Task Date Modal Script for Protect...--}}
<script>
document.addEventListener('DOMContentLoaded', function() {

    const taskForm = document.querySelector('#setTaskDateModal form');
    const submitBtn = document.getElementById('setDateBtn');

    if (taskForm && submitBtn) {
        taskForm.addEventListener('submit', function() {
            submitBtn.disabled = true;
            submitBtn.innerText = "Setting Dates...";
        });
    }

});
</script>
