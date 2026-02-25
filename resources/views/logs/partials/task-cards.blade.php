<div class="row g-4">

    @forelse($data as $log)

    <div class="col-12 col-lg-6">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body d-flex flex-column">

                {{-- ================= HEADER ================= --}}
                <div class="d-flex justify-content-between align-items-start mb-2">

                    <div>

                        {{-- Task --}}
                        @if($log->task)
                        <a href="{{ route('tasks.show', [ 'task'  => $log->task_id, 'from'  => 'logs', 'scope' => request('scope') ]) }}"
                            class="fw-semibold text-decoration-none text-dark">
                            {{ $log->task->task_type }}
                        </a>
                        @else
                        <div class="fw-semibold text-muted">Deleted Task</div>
                        @endif

                        {{-- Project --}}
                        <div class="small text-muted">
                            @if($log->task?->project)
                            <a href="{{ route('projects.show', ['project' => $log->task->project_id,'from' => 'logs','scope' => request('scope')]) }}"
                                class="text-decoration-none text-muted">
                                {{ $log->task->project->name }}
                            </a>
                            @else
                            <span class="badge bg-secondary">Personal Custom Task</span>
                            @endif
                        </div>

                    </div>

                    {{-- Action Badge --}}
                    <span class="badge bg-secondary rounded-pill">
                        {{ ucfirst(str_replace('_', ' ', $log->action)) }}
                    </span>

                </div>

                {{-- ================= META ================= --}}
                <div class="small text-muted mb-2">
                    <i class="bi bi-person-circle me-1"></i>
                    {{ $log->user->name ?? 'System' }}
                </div>

                <div class="small text-muted mb-3">
                    <i class="bi bi-clock me-1"></i>
                    {{ $log->created_at->format('F j, Y h:i A') }}
                </div>

                {{-- ================= DESCRIPTION ================= --}}
                <div class="small mb-2">
                    {{ $log->description }}
                </div>

                {{-- ================= CHANGES ================= --}}
                @if(!empty($log->changes))
                <button class="btn btn-link btn-sm p-0"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#taskChanges-{{ $log->id }}">
                    View Details
                </button>

                <div class="collapse mt-2 small"
                    id="taskChanges-{{ $log->id }}">
                    @include('logs.partials.task-log-changes', [
                    'changes' => $log->changes
                    ])
                </div>
                @endif

            </div>
        </div>
    </div>

    @empty

    <div class="col-12">
        <div class="text-center text-muted py-0">
            No activity logs found.
        </div>
    </div>

    @endforelse

</div>

<div class="mt-4">
    {{ $data->links() }}
</div>