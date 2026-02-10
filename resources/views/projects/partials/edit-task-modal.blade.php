{{-- Edit Task Modal --}}
<div class="modal fade" id="editTaskModal" tabindex="-1">

    <div class="modal-dialog modal-lg modal-dialog-centered">

        <form method="POST" action="{{ route('tasks.update') }}" class="modal-content">
            @csrf
            @method('PUT')

            <input type="hidden" name="form_context" value="edit_task">

            <input type="hidden" name="task_id" id="edit_task_id">

            <div class="modal-header">
                <h6 class="modal-title">Edit Task</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="row g-3">

                    <div class="col-md-6">
                        <label class="form-label">Task Type</label>
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

                    {{-- Custom Task Name --}}
                    <div class="col-md-6 d-none" id="editCustomTaskWrapper">
                        <label class="form-label">Custom Task Name</label>
                        <input type="text"
                            name="custom_task_name"
                            id="edit_custom_task_name"
                            class="form-control"
                            placeholder="Enter custom task name">
                    </div>

                    {{-- Assigned User --}}
                    <div class="col-md-6">
                        <label class="form-label">Assigned To</label>
                        <select name="assigned_user_id"
                            id="edit_assigned_user"
                            class="form-select" required>
                            @foreach ($users as $user)
                            <option value="{{ $user->id }}">
                                {{ $user->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Start Date</label>
                        <input type="date" name="start_date"
                            id="edit_start_date"
                            class="form-control">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Due Date</label>
                        <input type="date" name="due_date"
                            id="edit_due_date"
                            class="form-control">
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    Cancel
                </button>
                <button class="btn btn-primary">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>