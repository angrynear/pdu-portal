{{-- ============================= --}}
{{-- 2️⃣ OVERDUE SUMMARY (2-COLUMN) --}}
{{-- ============================= --}}
<div class="row mb-4">

    {{-- ========================= --}}
    {{-- LEFT: OVERDUE PROJECTS --}}
    {{-- ========================= --}}
    <div class="col-12 col-lg-6 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body overflow-auto dashboard-scroll">

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-semibold mb-0">
                        <i class="bi bi-exclamation-triangle stat-icon text-danger"></i>
                        Overdue Projects
                    </h6>

                    <a href="{{ route('projects.index', ['filter' => 'overdue']) }}"
                        class="small text-decoration-none text-muted">
                        View All →
                    </a>
                </div>
                <hr class="mt-2 mb-3">

                @forelse($overdueProjects as $project)

                <div class="d-flex flex-column flex-sm-row 
            justify-content-between align-items-start 
            gap-2 mb-3">

                    {{-- Project Info --}}
                    <div>
                        {{-- Project Name --}}
                        <div class="fw-semibold">
                            <a href="{{ route('projects.show', $project->id) }}"
                                class="text-decoration-none text-dark">
                                {{ $project->name }}
                            </a>
                        </div>

                        {{-- Project Location --}}
                        <div class="small text-muted">
                            {{ $project->location }}
                        </div>
                    </div>

                    {{-- Due Info --}}
                    <div class="text-start text-sm-end mt-1 mt-sm-0">
                        <div class="small text-danger fw-semibold">
                            {{ optional($project->due_date)->format('M d, Y') }}
                        </div>
                        <div class="small text-muted">
                            {{ $project->due_date?->diffForHumans() }}
                        </div>
                    </div>

                </div>

                {{-- Progress Bar --}}
                <x-progress-bar :value="$project->progress" />

                @if(!$loop->last)
                <hr class="my-3">
                @endif

                @empty
                <div class="text-muted small">
                    No overdue projects.
                </div>
                @endforelse

            </div>
        </div>
    </div>

    {{-- ========================= --}}
    {{-- RIGHT: OVERDUE TASKS --}}
    {{-- ========================= --}}
    <div class="col-12 col-lg-6 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body overflow-auto dashboard-scroll">

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-semibold mb-0">
                        <i class="bi bi-exclamation-triangle stat-icon text-danger"></i>
                        Overdue Tasks
                    </h6>

                    <a href="{{ route('tasks.index', ['filter' => 'overdue']) }}"
                        class="small text-decoration-none text-muted">
                        View All →
                    </a>
                </div>

                @forelse($overdueTasks as $task)

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

                        {{-- Assigned User--}}
                        <div class="small text-muted">
                            {{ $task->assignedUser->name ?? '—' }}
                        </div>
                    </div>

                    {{-- Due Info --}}
                    <div class="text-start text-sm-end mt-1 mt-sm-0">
                        <div class="small text-danger fw-semibold">
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
                    No overdue tasks.
                </div>
                @endforelse

            </div>
        </div>
    </div>

</div>