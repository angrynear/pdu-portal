<div class="modal fade" id="setTaskDateModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm modal-md">
        <form method="POST"
            action="{{ route('tasks.setDates') }}"
            class="modal-content">
            @csrf
            @method('PATCH')

            <input type="hidden" name="task_id" id="set_date_task_id">

            {{-- HEADER --}}
            <div class="modal-header">
                <h5 class="modal-title">Set Task Dates</h5>
                <button type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"></button>
            </div>

            {{-- BODY --}}
            <div class="modal-body">

                <div class="mb-3">
                    <label class="form-label fw-semibold">
                        Start Date
                    </label>

                    <input type="date"
                        name="start_date"
                        id="set_start_date"
                        class="form-control"
                        required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">
                        Due Date
                    </label>

                    <input type="date"
                        name="due_date"
                        id="set_due_date"
                        class="form-control"
                        required>
                </div>

            </div>

            {{-- FOOTER --}}
            <div class="modal-footer flex-column flex-sm-row gap-2">

                <button type="button"
                    class="btn btn-secondary w-100 w-sm-auto"
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