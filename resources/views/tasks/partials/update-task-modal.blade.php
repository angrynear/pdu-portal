<div class="modal fade" id="updateTaskProgressModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-fullscreen-sm-down">

        <form method="POST"
              action="{{ route('tasks.updateProgress') }}"
              enctype="multipart/form-data"
              class="modal-content border-0 shadow">
            @csrf
            @method('PATCH')

            <input type="hidden" name="task_id" id="task_id">

            {{-- HEADER --}}
            <div class="modal-header border-0 pb-0">
                <div>
                    <h6 class="modal-title fw-semibold mb-1">
                        Update Task Progress
                    </h6>
                    <div class="small text-muted">
                        Adjust progress, timeline, or add remarks.
                    </div>
                </div>

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"></button>
            </div>

            {{-- BODY --}}
            <div class="modal-body pt-3">

                {{-- ================= PROGRESS SECTION ================= --}}
                <div class="p-3 rounded-3 bg-light mb-4">

                    <label class="form-label fw-semibold d-flex justify-content-between align-items-center mb-3">
                        <span class="small text-uppercase text-muted">Progress</span>
                        <span class="fw-bold text-primary">
                            <span id="progressValue">0</span>%
                        </span>
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

                {{-- ================= TIMELINE SECTION ================= --}}
                <div class="p-3 rounded-3 bg-light mb-4">

                    <div class="small text-uppercase text-muted fw-semibold mb-3">
                        Timeline Adjustment (Optional)
                    </div>

                    <div class="row g-3">

                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-semibold">
                                Start Date
                            </label>

                            <input type="date"
                                   name="start_date"
                                   id="update_start_date"
                                   class="form-control">
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-semibold">
                                Due Date
                            </label>

                            <input type="date"
                                   name="due_date"
                                   id="update_due_date"
                                   class="form-control">
                        </div>

                    </div>

                </div>

                {{-- ================= REMARKS ================= --}}
                <div class="mb-4">

                    <label class="form-label fw-semibold small text-uppercase text-muted">
                        Remarks (Optional)
                    </label>

                    <textarea name="remark"
                              id="remarkField"
                              class="form-control"
                              rows="3"
                              placeholder="Add remarks if necessary…"></textarea>

                </div>

                {{-- ================= ATTACHMENTS ================= --}}
                <div>

                    <label class="form-label fw-semibold small text-uppercase text-muted">
                        Attachment(s)
                    </label>

                    <input type="file"
                           name="attachments[]"
                           class="form-control"
                           multiple>

                    <div class="form-text">
                        Max 5MB per file.
                    </div>

                </div>

            </div>

            {{-- FOOTER --}}
            <div class="modal-footer border-0 pt-0 flex-column flex-sm-row gap-2">

                <small class="text-muted me-sm-auto text-center text-sm-start">
                    Only changes will be recorded in activity history.
                </small>

                <button type="button"
                        class="btn btn-outline-secondary w-100 w-sm-auto"
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

    // Sync slider value
    if (slider && progressText) {
        slider.addEventListener('input', function () {
            progressText.innerText = this.value;
        });
    }

    // Prevent double submit
    if (taskForm && submitBtn) {
        taskForm.addEventListener('submit', function () {
            submitBtn.disabled = true;
            submitBtn.innerText = "Updating...";
        });
    }

});
</script>

