{{-- ================= SYSTEM SNAPSHOT ================= --}}
<div class="mb-4">

    {{-- Projects --}}
    <div class="row g-4 mb-4">

        <div class="col-12 col-sm-6 col-xl-3">
            <a href="{{ route('projects.index') }}"
                class="stat-tile tile-blue d-flex justify-content-between text-decoration-none text-light">
                <div>
                    <div class="stat-label">Total Projects</div>
                    <div class="stat-number">{{ $totalProjects }}</div>
                </div>
                <i class="bi bi-folder2-open stat-icon"></i>
            </a>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <a href="{{ route('projects.index', ['filter' => 'completed']) }}"
                class="stat-tile tile-green d-flex justify-content-between text-decoration-none text-light">
                <div>
                    <div class="stat-label">Completed Projects</div>
                    <div class="stat-number">{{ $completedProjects }}</div>
                </div>
                <i class="bi bi-check-circle stat-icon"></i>
            </a>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <a href="{{ route('projects.index', ['filter' => 'ongoing']) }}"
                class="stat-tile tile-orange d-flex justify-content-between text-decoration-none text-light">
                <div>
                    <div class="stat-label">Ongoing Projects</div>
                    <div class="stat-number">{{ $ongoingProjects }}</div>
                </div>
                <i class="bi bi-hourglass-split stat-icon"></i>
            </a>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <a href="{{ route('projects.index', ['filter' => 'overdue']) }}"
                class="stat-tile tile-red d-flex justify-content-between text-decoration-none text-light">
                <div>
                    <div class="stat-label">Overdue Projects</div>
                    <div class="stat-number">{{ $overdueProjectsCount }}</div>
                </div>
                <i class="bi bi-exclamation-triangle stat-icon"></i>
            </a>
        </div>

    </div>

    {{-- Tasks --}}
    <div class="row g-4 mb-4">

        <div class="col-12 col-sm-6 col-xl-3">
            <a href="{{ route('tasks.index') }}"
                class="stat-tile tile-blue d-flex justify-content-between text-decoration-none text-light">
                <div>
                    <div class="stat-label">Total Tasks</div>
                    <div class="stat-number">{{ $totalTasks }}</div>
                </div>
                <i class="bi bi-folder2-open stat-icon"></i>
            </a>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <a href="{{ route('tasks.index', ['filter' => 'completed']) }}"
                class="stat-tile tile-green d-flex justify-content-between text-decoration-none text-light">
                <div>
                    <div class="stat-label">Completed Tasks</div>
                    <div class="stat-number">{{ $completedTasks }}</div>
                </div>
                <i class="bi bi-check-circle stat-icon"></i>
            </a>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <a href="{{ route('tasks.index', ['filter' => 'ongoing']) }}"
                class="stat-tile tile-orange d-flex justify-content-between text-decoration-none text-light">
                <div>
                    <div class="stat-label">Ongoing Tasks</div>
                    <div class="stat-number">{{ $ongoingTasks }}</div>
                </div>
                <i class="bi bi-hourglass-split stat-icon"></i>
            </a>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <a href="{{ route('tasks.index', ['filter' => 'overdue']) }}"
                class="stat-tile tile-red d-flex justify-content-between text-decoration-none text-light">
                <div>
                    <div class="stat-label">Overdue Tasks</div>
                    <div class="stat-number">{{ $overdueTasksCount }}</div>
                </div>
                <i class="bi bi-exclamation-triangle stat-icon"></i>
            </a>
        </div>

    </div>

</div>