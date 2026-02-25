{{-- ============================= --}}
{{-- 3️⃣ DUE SOON SUMMARY (2-COLUMN) --}}
{{-- ============================= --}}
<div class="row mb-4">

    {{-- ========================= --}}
    {{-- LEFT: PROJECTS DUE SOON --}}
    {{-- ========================= --}}
    <div class="col-12 col-lg-6 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body overflow-auto dashboard-scroll">

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-semibold mb-0">
                        <i class="bi bi-hourglass-split stat-icon text-primary"></i>
                        Projects Due Soon
                    </h6>

                    <a href="{{ route('projects.index', ['filter' => 'due_soon']) }}"
                        class="small text-decoration-none text-muted">
                        View All →
                    </a>
                </div>

                @forelse($dueSoonProjects as $project)

                <div class="d-flex flex-column flex-sm-row 
                justify-content-between align-items-start 
                gap-2 mb-3">

                    {{-- Project Info --}}
                    <div>
                        <div class="fw-semibold">
                            <a href="{{ route('projects.show', $project->id) }}"
                                class="text-decoration-none text-dark">
                                {{ $project->name }}
                            </a>
                        </div>

                        <div class="small text-muted">
                            {{ $project->location }}
                        </div>
                    </div>

                    {{-- Due Date --}}
                    <div class="text-start text-sm-end mt-1 mt-sm-0">
                        <div class="small fw-semibold text-warning">
                            {{ optional($project->due_date)->format('M d, Y') }}
                        </div>

                        <div class="small text-muted">
                            {{ $project->due_date?->diffForHumans() }}
                        </div>
                    </div>

                </div>

                <x-progress-bar :value="$project->progress" />

                @if(!$loop->last)
                <hr class="my-3">
                @endif

                @empty
                <div class="text-muted small">
                    No upcoming projects.
                </div>
                @endforelse

            </div>
        </div>
    </div>

    {{-- ========================= --}}
    {{-- RIGHT: TASKS DUE SOON --}}
    {{-- ========================= --}}
    <div class="col-12 col-lg-6 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body overflow-auto dashboard-scroll">

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-semibold mb-0">
                        <i class="bi bi-hourglass-split stat-icon text-primary"></i>
                        Tasks Due Soon
                    </h6>

                    <a href="{{ route('tasks.index', ['filter' => 'due_soon']) }}"
                        class="small text-decoration-none text-muted">
                        View All →
                    </a>
                </div>

                @forelse($dueSoonTasks as $task)

                <div class="d-flex flex-column flex-sm-row 
            justify-content-between align-items-start 
            gap-2 mb-3">

                    {{-- Task Info --}}
                    <div>
                        {{-- Task Name --}}
                        <div class="fw-semibold">
                            <a href="{{ route('tasks.show', $task->id) }}"
                                class="text-decoration-none text-dark">
                                {{ ucfirst($task->task_type) }}
                            </a>
                        </div>

                        {{-- Project Name --}}
                        <div class="small text-muted">
                            {{ $task->project->name ?? '—' }}
                        </div>

                        <div class="small text-muted">
                            {{ $task->assignedUser->name ?? '—' }}
                        </div>
                    </div>

                    {{-- Due Date --}}
                    <div class="text-start text-sm-end mt-1 mt-sm-0">
                        <div class="small fw-semibold text-warning">
                            {{ optional($task->due_date)->format('M d, Y') }}
                        </div>

                        <div class="small text-muted">
                            {{ $task->due_date?->diffForHumans() }}
                        </div>
                    </div>

                </div>

                {{-- Progress Bar --}}
                <x-progress-bar :value="$task->progress" />

                @if(!$loop->last)
                <hr class="my-3">
                @endif

                @empty
                <div class="text-muted small">
                    No upcoming task.
                </div>
                @endforelse

            </div>
        </div>
    </div>

</div>