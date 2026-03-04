    <div class="project-list">

        @forelse($tasks as $task)

        @php
        $user = auth()->user();

        $isAdmin = $user->isAdmin();
        $isPersonalTask = !$task->project_id;
        $isAssignedUser = $user->id === $task->assigned_user_id || $user->id === $task->created_by;

        $remark = $task->latest_remark;
        @endphp

        {{-- ================= DESKTOP ================= --}}
        <div class="d-none d-md-block">
            <div class="card project-card {{ $task->status_border_class }} shadow-sm border-0 mb-3">
                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-start">

                        <div>
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <span class="text-muted fw-semibold">
                                    #{{ $tasks->firstItem() + $loop->index }}
                                </span>

                                <div class="fw-semibold">
                                    {{ $task->task_type }}
                                </div>
                            </div>

                            <div class="small text-muted mt-1">
                                @if($task->project)
                                <a href="{{ route('projects.show', ['project' => $task->project_id,'from' => 'tasks','scope' => request('scope')]) }}"
                                    class="text-decoration-none text-muted fw-semibold link-hover">
                                    <i class="bi bi-building"></i>
                                    {{ $task->project->name }}
                                </a>
                                @else
                                <span class="badge bg-secondary">Personal Custom Task</span>
                                @endif
                            </div>

                            <div class="small text-muted mt-1">
                                <i class="bi bi-person me-1"></i>
                                {{ $task->assignedUser->name ?? '—' }}
                            </div>
                        </div>

                        <div class="d-flex align-items-center gap-2">

                            <span class="badge rounded-pill {{ $task->status_badge_class }}">
                                <i class="bi 
                    {{ $task->status === 'completed' ? 'bi-check-circle-fill' :
                       ($task->status === 'overdue' ? 'bi-exclamation-triangle-fill' :
                       ($task->status === 'not_started' ? 'bi-dash-circle-fill' :
                       'bi-arrow-repeat')) }} 
                    me-1">
                                </i>
                                {{ $task->status_label }}
                            </span>

                            {{-- View Task--}}
                            <a href="{{ route('tasks.show', [ 'task'  => $task->id, 'from'  => 'tasks', 'scope' => request('scope') ]) }}"
                                class="btn btn-sm btn-light p-2 flex-fill">
                                <i class="bi bi-eye-fill"></i>
                            </a>

                            {{-- Assign Task--}}
                            @if($isAdmin && !$task->assigned_user_id)
                            <button class="btn btn-sm btn-light p-2"
                                data-bs-toggle="modal"
                                data-bs-target="#assignTaskModal"
                                data-task-id="{{ $task->id }}">
                                <i class="bi bi-person-plus-fill"></i>
                            </button>
                            @endif

                            {{-- Set Task Date--}}
                            @if($task->assigned_user_id && (!$task->start_date || !$task->due_date))
                            @if($isAdmin || $isAssignedUser)
                            <button class="btn btn-sm btn-light p-2"
                                data-bs-toggle="modal"
                                data-bs-target="#setTaskDateModal"
                                data-task-id="{{ $task->id }}"
                                data-project-start="{{ $task->project?->start_date?->format('Y-m-d') }}"
                                data-project-due="{{ $task->project?->due_date?->format('Y-m-d') }}">
                                <i class="bi bi-calendar-plus"></i>
                            </button>
                            @endif
                            @endif

                            {{-- Update--}}
                            @if($task->assigned_user_id && $task->start_date && $task->due_date)
                            @if($isAdmin || $isAssignedUser)
                            <button class="btn btn-sm btn-light p-2"
                                data-bs-toggle="modal"
                                data-bs-target="#updateTaskProgressModal"
                                data-task-id="{{ $task->id }}"
                                data-task-type="{{ $task->task_type }}"
                                data-personal="{{ $task->project_id ? '0' : '1' }}"
                                data-progress="{{ $task->progress }}"
                                data-start-date="{{ $task->start_date?->format('Y-m-d') }}"
                                data-due-date="{{ $task->due_date?->format('Y-m-d') }}"
                                data-project-start="{{ $task->project?->start_date?->format('Y-m-d') }}"
                                data-project-due="{{ $task->project?->due_date?->format('Y-m-d') }}">
                                <i class="bi bi-arrow-repeat"></i>
                            </button>
                            @endif
                            @endif

                            {{-- Archive --}}
                            @if($isAdmin || ($isPersonalTask && $isAssignedUser))
                            <button type="button"
                                class="btn btn-sm btn-light p-2 flex-fill"
                                data-bs-toggle="modal"
                                data-bs-target="#confirmActionModal"
                                data-action="{{ route('tasks.archive', $task->id) }}"
                                data-method="PATCH"
                                data-title="Archive Task"
                                data-message="Are you sure you want to archive this task?"
                                data-confirm-text="Archive"
                                data-confirm-class="btn-danger">
                                <i class="bi bi-archive-fill text-danger"></i>
                            </button>
                            @endif

                        </div>
                    </div>

                    <div class="project-meta mt-1">
                        <div class="d-flex flex-wrap align-items-center gap-4">

                            <div class="meta-item small text-muted">
                                <i class="bi bi-calendar3 me-1"></i>
                                {{ $task->start_date?->format('M. d, Y') ?? '—' }}
                                <span class="mx-1">→</span>
                                <x-due-date :dueDate="$task->due_date" :progress="$task->progress" />
                            </div>

                            @if($task->latest_remark)
                            <div class="meta-item small text-muted mt-1">
                                <i class="bi bi-chat-left-text me-1"></i>
                                {{ Str::limit($task->latest_remark, 100) }}
                            </div>
                            @endif

                        </div>
                    </div>

                    <div class="mt-3">
                        <x-progress-bar :value="$task->progress" />
                    </div>

                </div>
            </div>
        </div>

        {{-- ================= MOBILE ================= --}}
        <div class="d-md-none">
            <div class="card project-card {{ $task->status_border_class }} shadow-sm border-0 mb-3">
                <div class="card-body">

                    <div class="fw-semibold mb-1">
                        #{{ $tasks->firstItem() + $loop->index }}
                        {{ $task->task_type }}
                    </div>

                    <div class="small text-muted">
                        @if($task->project)
                        <a href="{{ route('projects.show', ['project' => $task->project_id,'from' => 'tasks','scope' => request('scope')]) }}"
                            class="text-decoration-none text-muted fw-semibold link-hover">
                            <i class="bi bi-building"></i>
                            {{ $task->project->name }}
                        </a>
                        @else
                        <span class="badge bg-secondary">Personal Custom Task</span>
                        @endif
                    </div>

                    <div class="small text-muted mb-2">
                        <i class="bi bi-person me-1"></i>
                        {{ $task->assignedUser->name ?? '—' }}
                    </div>

                    <div class="small text-muted mb-2">
                        <i class="bi bi-calendar3 me-1"></i>
                        {{ $task->start_date?->format('M. d, Y') ?? '—' }}
                        →
                        <x-due-date :dueDate="$task->due_date" :progress="$task->progress" />
                    </div>

                    @if($task->latest_remark)
                    <div class="small text-muted mb-2">
                        <i class="bi bi-chat-left-text me-1"></i>
                        {{ Str::limit($task->latest_remark, 120) }}
                    </div>
                    @endif

                    <div class="mt-2">
                        <x-progress-bar :value="$task->progress" />
                    </div>

                    <div class="mt-3">
                        <div class="mb-2">
                            <span class="badge rounded-pill {{ $task->status_badge_class }}">
                                {{ $task->status_label }}
                            </span>
                        </div>

                        <div class="d-flex gap-2">

                            {{-- View --}}
                            <a href="{{ route('tasks.show', [ 'task'  => $task->id, 'from'  => 'tasks', 'scope' => request('scope') ]) }}"
                                class="btn btn-sm btn-light p-2 flex-fill">
                                <i class="bi bi-eye-fill"></i>
                            </a>

                            {{-- Assign Task--}}
                            @if($isAdmin && !$task->assigned_user_id)
                            <button class="btn btn-sm btn-light flex-fill"
                                data-bs-toggle="modal"
                                data-bs-target="#assignTaskModal"
                                data-task-id="{{ $task->id }}">
                                <i class="bi bi-person-plus-fill"></i>
                            </button>
                            @endif

                            {{-- Set Date--}}
                            @if($task->assigned_user_id && (!$task->start_date || !$task->due_date))
                            @if($isAdmin || $isAssignedUser)
                            <button class="btn btn-sm btn-light p-2 flex-fill"
                                data-bs-toggle="modal"
                                data-bs-target="#setTaskDateModal"
                                data-task-id="{{ $task->id }}"
                                data-project-start="{{ $task->project?->start_date?->format('Y-m-d') }}"
                                data-project-due="{{ $task->project?->due_date?->format('Y-m-d') }}">
                                <i class="bi bi-calendar-plus"></i>
                            </button>
                            @endif
                            @endif

                            {{-- Update --}}
                            @if($task->assigned_user_id && $task->start_date && $task->due_date)
                            @if($isAdmin || $isAssignedUser)
                            <button class="btn btn-sm btn-light flex-fill"
                                data-bs-toggle="modal"
                                data-bs-target="#updateTaskProgressModal"
                                data-task-id="{{ $task->id }}"
                                data-task-type="{{ $task->task_type }}"
                                data-personal="{{ $task->project_id ? '0' : '1' }}"
                                data-progress="{{ $task->progress }}"
                                data-start-date="{{ $task->start_date?->format('Y-m-d') }}"
                                data-due-date="{{ $task->due_date?->format('Y-m-d') }}"
                                data-project-start="{{ $task->project?->start_date?->format('Y-m-d') }}"
                                data-project-due="{{ $task->project?->due_date?->format('Y-m-d') }}">
                                <i class="bi bi-arrow-repeat"></i>
                            </button>
                            @endif
                            @endif

                            {{-- Archive --}}
                            @if($isAdmin || ($isPersonalTask && $isAssignedUser))
                            <button type="button"
                                class="btn btn-sm btn-light flex-fill"
                                data-bs-toggle="modal"
                                data-bs-target="#confirmActionModal"
                                data-action="{{ route('tasks.archive', $task->id) }}"
                                data-method="PATCH"
                                data-title="Archive Task"
                                data-message="Are you sure you want to archive this task?"
                                data-confirm-text="Archive"
                                data-confirm-class="btn-danger">
                                <i class="bi bi-archive-fill text-danger"></i>
                            </button>
                            @endif

                        </div>
                    </div>

                </div>
            </div>
        </div>

        @empty
        <div class="text-center text-muted py-0">
            No tasks found.
        </div>
        @endforelse

        <div class="mt-4">
            {{ $tasks->links() }}
        </div>

    </div>