<div class="modal fade" id="assignTaskModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST"
            action="{{ route('tasks.assign') }}"
            class="modal-content">
            @csrf
            @method('PATCH')

            <input type="hidden" name="task_id" id="assign_task_id">

            <div class="modal-header">
                <h5 class="modal-title">Assign Task</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <label class="form-label">Select Personnel</label>
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

            </div>

            <div class="modal-footer">
                <button type="button"
                    class="btn btn-secondary"
                    data-bs-dismiss="modal">
                    Cancel
                </button>
                <button type="submit"
                    id="assignTaskBtn"
                    class="btn btn-info">
                    Assign
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Assign Task Modal Script for Protect...--}}
<script>
    document.addEventListener('DOMContentLoaded', function() {

        const taskForm = document.querySelector('#assignTaskModal form');
        const submitBtn = document.getElementById('assignTaskBtn');

        taskForm.addEventListener('submit', function() {
            submitBtn.disabled = true;
            submitBtn.innerText = "Assigning...";
        });

    });
</script>