{{-- Add Task Modal --}}
<div class="modal fade" id="addTaskModal" tabindex="-1" aria-hidden="true">

    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <form action="{{ route('tasks.store') }}" method="POST">
                @csrf
                
                <input type="hidden" name="form_context" value="add_task">

                <input type="hidden" name="project_id" value="{{ $project->id }}">

                <div class="modal-header">
                    <h6 class="modal-title">Add Task</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">

                        {{-- Task Type --}}
                        <div class="col-md-6">
                            <label class="form-label">Task Type</label>
                            <select name="task_type_select"
                                id="taskTypeSelect"
                                class="form-select"
                                required>
                                <option value="">— Select Task Type —</option>
                                @foreach (['Perspective','Architectural','Structural','Mechanical','Electrical','Plumbing','Custom'] as $type)
                                <option value="{{ $type }}">{{ $type }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Custom Task Name --}}
                        <div class="col-md-6 d-none" id="customTaskWrapper">
                            <label class="form-label">Custom Task Name</label>
                            <input type="text"
                                name="custom_task_name"
                                class="form-control"
                                placeholder="Enter custom task name">
                        </div>

                        {{-- Assigned User --}}
                        <div class="col-md-6">
                            <label class="form-label">Assign To</label>
                            <select name="assigned_user_id"
                                class="form-select"
                                required>
                                <option value="">— Select Personnel —</option>
                                @foreach ($users as $user)
                                <option value="{{ $user->id }}">
                                    {{ $user->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Start Date --}}
                        <div class="col-md-3">
                            <label class="form-label">Start Date</label>
                            <input type="date"
                                name="start_date"
                                class="form-control @error('start_date') is-invalid @enderror"
                                value="{{ old('start_date') }}">

                            @error('start_date')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror

                        </div>

                        {{-- Due Date --}}
                        <div class="col-md-3">
                            <label class="form-label">Due Date</label>
                            <input type="date"
                                name="due_date"
                                class="form-control @error('due_date') is-invalid @enderror"
                                value="{{ old('due_date') }}">

                            @error('due_date')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror

                        </div>

                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-success">
                        Create Task
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>