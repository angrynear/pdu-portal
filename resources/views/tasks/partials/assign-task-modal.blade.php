{{-- ================= ASSIGN TASK MODAL ================= --}}
<div class="modal fade" id="assignTaskModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-md">

        <form method="POST"
            action="{{ route('tasks.assign') }}"
            class="modal-content border-0 shadow-sm">
            @csrf
            @method('PATCH')

            <input type="hidden" name="task_id" id="assign_task_id">

            {{-- HEADER --}}
            <div class="modal-header border-0 pb-0">
                <div>
                    <h5 class="modal-title fw-semibold">
                        Assign Task
                    </h5>
                    <div class="small text-muted">
                        Select personnel responsible for this task.
                    </div>
                </div>

                <button type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"></button>
            </div>

            {{-- BODY --}}
            <div class="modal-body pt-3">

                <div class="mb-3">
                    <label class="form-label fw-semibold">
                        Personnel
                    </label>

                    <select name="assigned_user_id"
                        class="form-select"
                        required>
                        <option value="">— Select Personnel —</option>

                        @foreach ($users->whereNull('archived_at') as $user)
                        <option value="{{ $user->id }}">
                            {{ $user->name }}
                        </option>
                        @endforeach
                    </select>

                    <div class="form-text">
                        Only active personnel are shown.
                    </div>
                </div>

            </div>

            {{-- FOOTER --}}
            <div class="modal-footer border-0 pt-0 flex-column flex-sm-row gap-2">

                <button type="button"
                    class="btn btn-light w-100 w-sm-auto"
                    data-bs-dismiss="modal">
                    Cancel
                </button>

                <button type="submit"
                    id="assignTaskBtn"
                    class="btn btn-primary w-100 w-sm-auto">
                    <i class="bi bi-person-check me-1"></i>
                    Assign Task
                </button>

            </div>

        </form>
    </div>
</div>



{{-- Assign Task Modal Script for Protect...--}}
<script>
    document.addEventListener('DOMContentLoaded', function() {

        const modal = document.getElementById('assignTaskModal');
        const form = modal?.querySelector('form');
        const submitBtn = document.getElementById('assignTaskBtn');

        if (form && submitBtn) {
            form.addEventListener('submit', function() {
                submitBtn.disabled = true;
                submitBtn.innerHTML = `
                <span class="spinner-border spinner-border-sm me-1"></span>
                Assigning...
            `;
            });
        }

    });
</script>