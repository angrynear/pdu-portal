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

{{-- Update Task Modal Script for Protect...--}}
<script>
    document.addEventListener('DOMContentLoaded', function() {

        const taskForm = document.querySelector('#updateTaskProgressModal form');
        const submitBtn = document.getElementById('updateProgressBtn');

        taskForm.addEventListener('submit', function() {
            submitBtn.disabled = true;
            submitBtn.innerText = "Updating...";
        });

    });
</script>