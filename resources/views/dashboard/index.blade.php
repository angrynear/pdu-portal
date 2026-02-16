@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

<x-page-wrapper title="Dashboard">


    @if(auth()->user()->isAdmin())

    {{-- ============================= --}}
    {{-- 1️⃣ SYSTEM SNAPSHOT --}}
    {{-- ============================= --}}
    <div class="mb-3 mb-md-4">

        {{-- ================= Projects Row ================= --}}
        <div class="row g-4 mb-4">

            {{-- Total Projects --}}
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="stat-tile tile-blue d-flex justify-content-between">
                    <div>
                        <div class="stat-label">Total Projects</div>
                        <div class="stat-number">{{ $totalProjects }}</div>
                    </div>
                    <i class="bi bi-folder2-open stat-icon"></i>
                </div>
            </div>

            {{-- Completed Projects --}}
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="stat-tile tile-green d-flex justify-content-between">
                    <div>
                        <div class="stat-label">Completed Projects</div>
                        <div class="stat-number">{{ $completedProjects }}</div>
                    </div>
                    <i class="bi bi-check-circle stat-icon"></i>
                </div>
            </div>

            {{-- Ongoing Projects --}}
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="stat-tile tile-orange d-flex justify-content-between">
                    <div>
                        <div class="stat-label">Ongoing Projects</div>
                        <div class="stat-number">{{ $ongoingProjects }}</div>
                    </div>
                    <i class="bi bi-hourglass-split stat-icon"></i>
                </div>
            </div>

            {{-- Overdue Projects --}}
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="stat-tile tile-red d-flex justify-content-between">
                    <div>
                        <div class="stat-label">Overdue Projects</div>
                        <div class="stat-number">{{ $overdueProjectsCount }}</div>
                    </div>
                    <i class="bi bi-exclamation-triangle stat-icon"></i>
                </div>
            </div>

        </div>

        {{-- ================= Tasks Row ================= --}}
        <div class="row g-3 g-md-4">


            {{-- Total Tasks --}}
            <div class="col-md-4">
                <div class="stat-tile tile-blue d-flex justify-content-between">
                    <div>
                        <div class="stat-label">Total Tasks</div>
                        <div class="stat-number">{{ $totalTasks }}</div>
                    </div>
                    <i class="bi bi-folder2-open stat-icon"></i>
                </div>
            </div>

            {{-- Ongoing Tasks --}}
            <div class="col-md-4">
                <div class="stat-tile tile-orange d-flex justify-content-between">
                    <div>
                        <div class="stat-label">Ongoing Tasks</div>
                        <div class="stat-number">{{ $ongoingTasks }}</div>
                    </div>
                    <i class="bi bi-hourglass-split stat-icon"></i>
                </div>
            </div>

            {{-- Overdue Tasks --}}
            <div class="col-md-4">
                <div class="stat-tile tile-red d-flex justify-content-between">
                    <div>
                        <div class="stat-label">Overdue Tasks</div>
                        <div class="stat-number">{{ $overdueTasksCount }}</div>
                    </div>
                    <i class="bi bi-exclamation-triangle stat-icon"></i>
                </div>
            </div>

        </div>

    </div>

    {{-- ============================= --}}
    {{-- PANEL TEMPLATE --}}
    {{-- ============================= --}}
    @php
    function panelHeader($title) {
    return '
    <div class="d-flex justify-content-between align-items-center mb-2">
        <h6 class="mb-0">'.$title.'</h6>
    </div>
    <hr class="mt-2 mb-3">';
    }
    @endphp

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

                        <a href="{{ route('projects.index') }}"
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

                        <a href="{{ route('tasks.index') }}"
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

                        <a href="{{ route('projects.index') }}"
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
                            <div class="small fw-semibold text-warning">
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

                        <a href="{{ route('tasks.index') }}"
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
                        No upcoming tasks.
                    </div>
                    @endforelse

                </div>
            </div>
        </div>

    </div>

    {{-- ============================= --}}
    {{-- 4️⃣ WORKLOAD DISTRIBUTION --}}
    {{-- ============================= --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body overflow-auto dashboard-scroll">

            <h6 class="fw-semibold mb-4">Workload Distribution</h6>

            @php
            $maxTasks = $workload->max('active_tasks_count') ?: 1;
            @endphp

            @forelse($workload as $person)

            <div class="mb-3 mb-md-4">

                {{-- Name + Count --}}
                <div class="d-flex justify-content-between mb-2">
                    <div class="fw-semibold">
                        {{ $person->name }}
                    </div>
                    <div class="small text-muted">
                        {{ $person->active_tasks_count }}
                    </div>
                </div>

                {{-- Progress Bar --}}
                @php
                $percentage = ($person->active_tasks_count / $maxTasks) * 100;

                // Color Logic
                if ($percentage >= 80) {
                $barColor = 'bg-danger';
                } elseif ($percentage >= 50) {
                $barColor = 'bg-warning';
                } else {
                $barColor = 'bg-success';
                }
                @endphp

                <div class="progress rounded-pill" style="height:8px;">
                    <div class="progress-bar {{ $barColor }}"
                        style="width: {{ $percentage }}%">
                    </div>
                </div>

            </div>

            @empty
            <div class="text-muted small">
                No workload data.
            </div>
            @endforelse

        </div>
    </div>

    @else

    {{-- ============================= --}}
    {{-- 1️⃣ USER TASK SNAPSHOT --}}
    {{-- ============================= --}}
    <div class="mb-3 mb-md-4">

        <div class="row g-3 g-md-4">

            {{-- My Total Tasks --}}
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="stat-tile tile-blue d-flex justify-content-between">
                    <div>
                        <div class="stat-label">My Total Tasks</div>
                        <div class="stat-number">
                            {{ $userTotalTasks }}
                        </div>
                    </div>
                    <i class="bi bi-folder2-open stat-icon"></i>
                </div>
            </div>

            {{-- My Ongoing Tasks --}}
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="stat-tile tile-orange d-flex justify-content-between">
                    <div>
                        <div class="stat-label">My Ongoing Tasks</div>
                        <div class="stat-number">
                            {{ $userOngoingTasks }}
                        </div>
                    </div>
                    <i class="bi bi-hourglass-split stat-icon"></i>
                </div>
            </div>

            {{-- My Completed Tasks --}}
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="stat-tile tile-green d-flex justify-content-between">
                    <div>
                        <div class="stat-label">My Completed Tasks</div>
                        <div class="stat-number">
                            {{ $userCompletedTasks }}
                        </div>
                    </div>
                    <i class="bi bi-check-circle stat-icon"></i>
                </div>
            </div>

            {{-- My Overdue Tasks --}}
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="stat-tile tile-red d-flex justify-content-between">
                    <div>
                        <div class="stat-label">My Overdue Tasks</div>
                        <div class="stat-number">
                            {{ $userOverdueTasksCount }}
                        </div>
                    </div>
                    <i class="bi bi-exclamation-triangle stat-icon"></i>
                </div>
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

                        <a href="{{ route('tasks.index') }}"
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

                        <a href="{{ route('tasks.index') }}"
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

            <h6 class="fw-semibold mb-4">
                <i class="bi bi-building-gear stat-icon text-primary"></i>
                My Assigned Projects
            </h6>

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

    @endif

</x-page-wrapper>

@endsection