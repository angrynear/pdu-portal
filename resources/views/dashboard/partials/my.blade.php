{{-- ============================================= --}}
    {{-- MY DASHBOARD (Admin scope=my OR Normal User) --}}
    {{-- ============================================= --}}

    <div class="mb-4">

        <div class="row g-4">

            <div class="col-12 col-sm-6 col-xl-3">
                <a href="{{ route('tasks.index', ['scope' => 'my']) }}"
                    class="stat-tile tile-blue d-flex justify-content-between text-decoration-none text-light">
                    <div>
                        <div class="stat-label">My Total Tasks</div>
                        <div class="stat-number">{{ $userTotalTasks }}</div>
                    </div>
                    <i class="bi bi-folder2-open stat-icon"></i>
                </a>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <a href="{{ route('tasks.index', ['scope' => 'my', 'filter' => 'completed']) }}"
                    class="stat-tile tile-green d-flex justify-content-between text-decoration-none text-light">
                    <div>
                        <div class="stat-label">My Completed Tasks</div>
                        <div class="stat-number">{{ $userCompletedTasks }}</div>
                    </div>
                    <i class="bi bi-check-circle stat-icon"></i>
                </a>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <a href="{{ route('tasks.index', ['scope' => 'my', 'filter' => 'ongoing']) }}"
                    class="stat-tile tile-orange d-flex justify-content-between text-decoration-none text-light">
                    <div>
                        <div class="stat-label">My Ongoing Tasks</div>
                        <div class="stat-number">{{ $userOngoingTasks }}</div>
                    </div>
                    <i class="bi bi-hourglass-split stat-icon"></i>
                </a>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <a href="{{ route('tasks.index', ['scope' => 'my', 'filter' => 'overdue']) }}"
                    class="stat-tile tile-red d-flex justify-content-between text-decoration-none text-light">
                    <div>
                        <div class="stat-label">My Overdue Tasks</div>
                        <div class="stat-number">{{ $userOverdueTasksCount }}</div>
                    </div>
                    <i class="bi bi-exclamation-triangle stat-icon"></i>
                </a>
            </div>

        </div>

    </div>

    {{-- ============================= --}}
    {{-- 2️⃣ TASK STATUS SUMMARY (2-COLUMN)--}}
    {{-- ============================= --}}

    <div class="row mb-4">

        {{-- ========================= --}}
        {{-- LEFT: MY OVERDUE TASK   --}}
        {{-- ========================= --}}
        <div class="col-12 col-lg-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body overflow-auto dashboard-scroll">

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-semibold mb-0">
                            <i class="bi bi-exclamation-triangle stat-icon text-danger"></i>
                            My Overdue Tasks
                        </h6>

                        <a href="{{ route('tasks.index', ['scope' => 'my', 'filter' => 'overdue']) }}"
                            class="small text-decoration-none text-muted">
                            View All →
                        </a>
                    </div>

                    @forelse($userOverdueTasks as $task)

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
                        </div>

                        {{-- Due Date --}}
                        <div class="text-start text-sm-end mt-1 mt-sm-0">
                            <div class="small text-danger fw-semibold">
                                {{ optional($task->due_date)->format('M d, Y') }}
                            </div>
                            <div class="small text-muted">
                                {{ $task->due_date?->diffForHumans() }}
                            </div>
                        </div>

                    </div>

                    {{-- Progress--}}
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

        {{-- ========================= --}}
        {{-- RIGHT: TASK DUE SOON    --}}
        {{-- ========================= --}}
        <div class="col-12 col-lg-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body overflow-auto dashboard-scroll">

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-semibold mb-0">
                            <i class="bi bi-hourglass-split stat-icon text-primary"></i>
                            My Tasks Due Soon
                        </h6>

                        <a href="{{ route('tasks.index', ['scope' => 'my', 'filter' => 'due_soon']) }}"
                            class="small text-decoration-none text-muted">
                            View All →
                        </a>
                    </div>

                    @forelse($userDueSoonTasks as $task)

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
                        </div>

                        {{-- Due Date --}}
                        <div class="text-start text-sm-end mt-1 mt-sm-0">
                            <div class="small text-warning fw-semibold">
                                {{ optional($task->due_date)->format('M d, Y') }}
                            </div>
                            <div class="small text-muted">
                                {{ $task->due_date?->diffForHumans() }}
                            </div>
                        </div>

                    </div>

                    {{-- Progress --}}
                    <x-progress-bar :value="$task->progress" />

                    @if(!$loop->last)
                    <hr class="my-3">
                    @endif

                    @empty
                    <div class="text-muted small">
                        No upcoming tasks.
                    </div>
                    @endforelse

                </div>
            </div>
        </div>

    </div>

    {{-- ============================= --}}
    {{-- 3️⃣ MY ASSIGNED PROJECTS --}}
    {{-- ============================= --}}

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body overflow-auto dashboard-scroll">

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-semibold mb-4">
                    <i class="bi bi-building-gear stat-icon text-primary"></i>
                    My Assigned Projects
                </h6>

                <a href="{{ route('projects.index') }}"
                    class="small text-decoration-none text-muted">
                    View All →
                </a>
            </div>

            @forelse($userProjects as $project)

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

                {{-- Due Date --}}
                <div class="text-start text-sm-end mt-1 mt-sm-0">
                    <div class="small text-muted">
                        {{ optional($project->due_date)->format('M d, Y') }}
                    </div>
                    <div class="small text-muted">
                        {{ $project->due_date?->diffForHumans() }}
                    </div>
                </div>

            </div>

            {{-- Progress --}}
            <x-progress-bar :value="$project->progress" />

            @if(!$loop->last)
            <hr class="my-3">
            @endif

            @empty
            <div class="text-muted small">
                No assigned projects.
            </div>
            @endforelse

        </div>
    </div>