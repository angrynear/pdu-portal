<div class="modal fade" id="updateTaskProgressModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-fullscreen-sm-down">
        <form method="POST"
              action="{{ route('tasks.updateProgress') }}"
              enctype="multipart/form-data"
              class="modal-content">
            @csrf
            @method('PATCH')

            <input type="hidden" name="task_id" id="task_id">

            {{-- HEADER --}}
            <div class="modal-header">
                <h5 class="modal-title">Update Task Progress</h5>
                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"></button>
            </div>

            {{-- BODY --}}
            <div class="modal-body">

                {{-- Progress --}}
                <div class="mb-4">
                    <label class="form-label fw-semibold">
                        Progress:
                        <strong>
                            <span id="progressValue">0</span>%
                        </strong>
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
                <div class="row g-3 mb-4">

                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold">
                            Start Date
                            <span class="text-muted small">(optional)</span>
                        </label>

                        <input type="date"
                               name="start_date"
                               id="update_start_date"
                               class="form-control">
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold">
                            Due Date
                            <span class="text-muted small">(optional)</span>
                        </label>

                        <input type="date"
                               name="due_date"
                               id="update_due_date"
                               class="form-control">
                    </div>

                </div>

                {{-- Remarks --}}
                <div class="mb-4">
                    <label class="form-label fw-semibold">
                        Remarks
                        <span class="text-muted small">(optional)</span>
                    </label>

                    <textarea name="remark"
                              id="remarkField"
                              class="form-control"
                              rows="3"
                              placeholder="Add remarks if necessary…"></textarea>
                </div>

                {{-- Attachments --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">
                        Attachment(s)
                    </label>

                    <input type="file"
                           name="attachments[]"
                           class="form-control"
                           multiple>
                </div>

            </div>

            {{-- FOOTER --}}
            <div class="modal-footer flex-column flex-sm-row gap-2">

                <small class="text-muted me-sm-auto text-center text-sm-start">
                    Only changes will be recorded.
                </small>

                <button type="button"
                        class="btn btn-secondary w-100 w-sm-auto"
                        data-bs-dismiss="modal">
                    Cancel
                </button>

                <button type="submit"
                        id="updateProgressBtn"
                        class="btn btn-primary w-100 w-sm-auto">
                    Save Update
                </button>

            </div>

        </form>
    </div>
</div>


{{-- Update Task Modal Script for Protect...--}}
<script>
document.addEventListener('DOMContentLoaded', function () {

    const taskForm = document.querySelector('#updateTaskProgressModal form');
    const submitBtn = document.getElementById('updateProgressBtn');
    const slider = document.getElementById('task_progress');
    const progressText = document.getElementById('progressValue');

    if (slider && progressText) {
        slider.addEventListener('input', function () {
            progressText.innerText = this.value;
        });
    }

    if (taskForm && submitBtn) {
        taskForm.addEventListener('submit', function () {
            submitBtn.disabled = true;
            submitBtn.innerText = "Updating...";
        });
    }

});
</script>
