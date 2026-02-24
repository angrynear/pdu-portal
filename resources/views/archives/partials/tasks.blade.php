<div class="row g-4">

    @foreach ($data as $task)

    <div class="col-12 col-lg-6">
        <div class="card project-card shadow-sm border-0 h-100 text-muted">
            <div class="card-body d-flex flex-column">

                {{-- HEADER --}}
                <div class="d-flex justify-content-between align-items-start mb-2">

                    <div>

                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <span class="text-muted fw-semibold">
                                #{{ $data->firstItem() + $loop->index }}
                            </span>

                            <div class="fw-semibold">
                                {{ $task->task_type }}
                            </div>
                        </div>

                        <div class="small text-muted">
                            {{ $task->project->name ?? '—' }}
                        </div>

                        <div class="small text-secondary mt-1">
                            <i class="bi bi-person me-1"></i>
                            {{ $task->assignedUser->name ?? '—' }}
                        </div>

                    </div>

                    <span class="badge bg-secondary rounded-pill">
                        <i class="bi bi-archive-fill me-1"></i>
                        Archived
                    </span>

                </div>

                {{-- META (Responsive Timeline) --}}
                <div class="mt-1">

                    <div class="d-flex flex-column flex-md-row align-items-md-center gap-2 gap-md-3 small text-muted">

                        <div>
                            <i class="bi bi-calendar3 me-1"></i>
                            {{ $task->start_date?->format('M. d, Y') ?? '—' }}
                            <span class="mx-1">→</span>
                            <x-due-date :dueDate="$task->due_date" :progress="$task->progress" />
                        </div>

                    </div>

                </div>

                {{-- PROGRESS --}}
                <div class="mt-3">

                    <x-progress-bar :value="$task->progress" />

                </div>

                {{-- ARCHIVED DATE --}}
                <div class="small text-muted mt-3">
                    <i class="bi bi-clock-history me-1"></i>
                    Archived {{ $task->archived_at?->format('F d, Y') }}
                </div>

                {{-- FOOTER ACTION --}}
                <div class="mt-auto pt-3">

                    @if ($task->project && is_null($task->project->archived_at))
                    <button
                        type="button"
                        class="btn btn-sm btn-success w-100"
                        data-bs-toggle="modal"
                        data-bs-target="#confirmActionModal"
                        data-action="{{ route('tasks.restore', $task->id) }}"
                        data-method="PATCH"
                        data-title="Restore Task"
                        data-message="Are you sure you want to restore this task?"
                        data-confirm-text="Restore"
                        data-confirm-class="btn-success">
                        Restore
                    </button>
                    @else
                    <div class="text-center small text-muted">
                        Project Archived
                    </div>
                    @endif

                </div>

            </div>
        </div>
    </div>

    @endforeach

</div>

<div class="mt-4">
    {{ $data->links() }}
</div>