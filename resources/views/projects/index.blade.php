@extends('layouts.app')

@section('content')

@php
$isAdmin = auth()->user()->isAdmin();
$isMyPage = request()->routeIs('projects.my');

$pageTitle = $isAdmin
? ($isMyPage ? 'My Projects' : 'Manage Projects')
: 'My Projects';
@endphp

@section('title', $pageTitle)

<x-page-wrapper :title="$pageTitle">

    <x-slot name="actions">

        @php
        $isMyPage = request()->routeIs('projects.my');
        $filter = request('filter', 'all');
        @endphp

        {{-- ================= DESKTOP FILTERS ================= --}}
        <div class="d-none d-md-flex align-items-center gap-2 flex-wrap">

            <a href="{{ route('projects.index', ['filter' => 'all']) }}"
                class="btn btn-sm {{ $filter === 'all' ? 'btn-dark' : 'btn-outline-secondary' }}">
                All
            </a>

            <a href="{{ route('projects.index', ['filter' => 'completed']) }}"
                class="btn btn-sm {{ $filter === 'completed' ? 'btn-dark' : 'btn-outline-secondary' }}">
                Completed
            </a>

            <a href="{{ route('projects.index', ['filter' => 'ongoing']) }}"
                class="btn btn-sm {{ $filter === 'ongoing' ? 'btn-dark' : 'btn-outline-secondary' }}">
                Ongoing
            </a>

            <a href="{{ route('projects.index', ['filter' => 'overdue']) }}"
                class="btn btn-sm {{ $filter === 'overdue' ? 'btn-dark' : 'btn-outline-secondary' }}">
                Overdue
            </a>

            <a href="{{ route('projects.index', ['filter' => 'not_started']) }}"
                class="btn btn-sm {{ $filter === 'not_started' ? 'btn-dark' : 'btn-outline-secondary' }}">
                Not Started
            </a>

            {{-- + Add Project --}}
            @if(auth()->user()->isAdmin() && !$isMyPage)
            <a href="{{ route('projects.create') }}"
                class="btn btn-sm btn-success">
                <i class="bi bi-plus-lg"></i>
            </a>
            @endif

        </div>


        {{-- ================= MOBILE COLLAPSIBLE FILTER ================= --}}
        <div class="d-md-none w-100">

            @php
            $filterLabels = [
            'all' => 'All',
            'completed' => 'Completed',
            'ongoing' => 'Ongoing',
            'overdue' => 'Overdue',
            'not_started' => 'Not Started',
            ];

            $activeLabel = $filterLabels[$filter] ?? 'All';
            @endphp

            {{-- Top Row: Toggle + Add --}}
            <div class="d-flex align-items-center">

                {{-- Filter Toggle --}}
                <button class="btn btn-sm btn-outline-secondary me-2"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#mobileProjectFilters"
                    aria-expanded="false"
                    aria-controls="mobileProjectFilters">

                    <i class="bi bi-funnel me-1"></i>
                    Filters
                    <span class="fw-semibold ms-1">
                        ({{ $activeLabel }})
                    </span>
                </button>

                {{-- + Add Project --}}
                @if(auth()->user()->isAdmin() && !$isMyPage)
                <a href="{{ route('projects.create') }}"
                    class="btn btn-sm btn-success">
                    <i class="bi bi-plus-lg"></i>
                </a>
                @endif

            </div>

            {{-- Collapsible Filter List --}}
            <div class="collapse mt-3" id="mobileProjectFilters">

                <div class="d-grid gap-2">

                    @foreach($filterLabels as $key => $label)
                    <a href="{{ route('projects.index', ['filter' => $key]) }}"
                        class="btn btn-sm {{ $filter === $key ? 'btn-dark' : 'btn-outline-secondary' }}">
                        {{ $label }}
                    </a>
                    @endforeach

                </div>

            </div>

        </div>

    </x-slot>

    {{-- ================= PROJECT CARDS ================= --}}
    <div class="project-list">

        @forelse($projects as $project)

        @php
        $total = $project->total_tasks_count;
        $completed = $project->completed_tasks_count;
        $started = $project->started_tasks_count;
        $isPast = $project->due_date && $project->due_date->isPast();

        /* ================= STATUS LOGIC ================= */
        if ($total > 0 && $completed == $total) {
        $statusClass = 'status-completed';
        $statusLabel = 'Completed';
        $statusIcon = 'bi-check-circle-fill';
        $badgeClass = 'bg-success-subtle text-success';
        } elseif ($total > 0 && $completed < $total && $isPast) {
            $statusClass='status-overdue' ;
            $statusLabel='Overdue' ;
            $statusIcon='bi-exclamation-triangle-fill' ;
            $badgeClass='bg-danger-subtle text-danger' ;
            } elseif ($started==0) {
            $statusClass='status-not-started' ;
            $statusLabel='Not Started' ;
            $statusIcon='bi-dash-circle-fill' ;
            $badgeClass='bg-secondary-subtle text-secondary' ;
            } else {
            $statusClass='status-ongoing' ;
            $statusLabel='Ongoing' ;
            $statusIcon='bi-arrow-repeat' ;
            $badgeClass='bg-primary-subtle text-primary' ;
            }

            /*=================FUNDING DISPLAY=================*/
            $source=$project->source_of_fund;
            $year = $project->funding_year;

            $isSourceApproval = strtolower($source) === 'for approval';
            $isYearApproval = strtolower($year) === 'for approval';

            if ($isSourceApproval && $isYearApproval) {
            $displayText = 'FOR APPROVAL';
            } elseif (!$isSourceApproval && $isYearApproval) {
            $displayText = $source;
            } elseif ($isSourceApproval && !$isYearApproval) {
            $displayText = $year;
            } else {
            $displayText = trim($source . ' ' . $year);
            }
            @endphp


            {{-- ========================================================= --}}
            {{-- ====================== DESKTOP ========================== --}}
            {{-- ========================================================= --}}
            <div class="d-none d-md-block">

                <div class="card project-card {{ $statusClass }} shadow-sm border-0 mb-3">
                    <div class="card-body">

                        {{-- TOP ROW --}}
                        <div class="d-flex justify-content-between align-items-start">

                            {{-- LEFT --}}
                            <div>
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <span class="text-muted fw-semibold">
                                        #{{ $projects->firstItem() + $loop->index }}
                                    </span>
                                    <div class="fw-semibold">
                                        {{ $project->name }}
                                    </div>
                                </div>

                                <div class="small text-muted mt-1">
                                    <i class="bi bi-geo-alt me-1"></i>
                                    {{ $project->location ?? '—' }}
                                </div>

                                @if($project->sub_sector)
                                <div class="small text-secondary">
                                    <i class="bi bi-diagram-3 me-1"></i>
                                    {{ ucwords(str_replace('_', ' ', $project->sub_sector)) }}
                                </div>
                                @endif
                            </div>

                            {{-- RIGHT --}}
                            <div class="d-flex align-items-center gap-2">

                                <span class="badge rounded-pill {{ $badgeClass }}">
                                    <i class="bi {{ $statusIcon }} me-1"></i>
                                    {{ $statusLabel }}
                                </span>

                                <a href="{{ route('projects.show', $project->id) }}"
                                    class="btn btn-sm btn-light p-2">
                                    <i class="bi bi-eye-fill"></i>
                                </a>

                                @if(auth()->user()->isAdmin())
                                <a href="{{ route('projects.edit', $project->id) }}"
                                    class="btn btn-sm btn-light p-2">
                                    <i class="bi bi-pencil-fill"></i>
                                </a>

                                <button class="btn btn-sm btn-light p-2"
                                    data-bs-toggle="modal"
                                    data-bs-target="#confirmActionModal"
                                    data-action="{{ route('projects.archive', $project->id) }}"
                                    data-method="PATCH"
                                    data-title="Archive Project"
                                    data-message="Are you sure you want to archive this project?"
                                    data-confirm-text="Archive"
                                    data-confirm-class="btn-danger">
                                    <i class="bi bi-archive-fill"></i>
                                </button>
                                @endif

                            </div>
                        </div>

                        {{-- META --}}
                        <div class="project-meta mt-3">

                            <div class="d-flex flex-wrap align-items-center gap-4">

                                <div class="meta-item">
                                    <span class="meta-pill">
                                        {{ $displayText }} • ₱{{ number_format($project->amount, 2) }}
                                    </span>
                                </div>

                                <div class="meta-item small text-muted">
                                    <i class="bi bi-calendar3 me-1"></i>
                                    {{ $project->start_date?->format('M. d, Y') ?? '—' }}
                                    <span class="mx-1">→</span>
                                    <x-due-date
                                        :dueDate="$project->due_date"
                                        :progress="$project->progress" />
                                </div>

                                <div class="meta-item small text-muted">
                                    <i class="bi bi-list-check me-1"></i>
                                    {{ $completed }} / {{ $total }} Tasks
                                </div>

                            </div>
                        </div>

                        {{-- PROGRESS --}}
                        <div class="mt-3">
                            <x-progress-bar :value="$project->progress" />
                        </div>

                    </div>
                </div>
            </div>



            {{-- ========================================================= --}}
            {{-- ======================= MOBILE ========================== --}}
            {{-- ========================================================= --}}
            <div class="d-md-none">

                <div class="card project-card {{ $statusClass }} shadow-sm border-0 mb-3">
                    <div class="card-body">

                        {{-- TITLE --}}
                        <div class="fw-semibold mb-1">
                            #{{ $projects->firstItem() + $loop->index }}
                            {{ $project->name }}
                        </div>

                        {{-- LOCATION --}}
                        <div class="small text-muted">
                            <i class="bi bi-geo-alt me-1"></i>
                            {{ $project->location ?? '—' }}
                        </div>

                        @if($project->sub_sector)
                        <div class="small text-secondary">
                            <i class="bi bi-diagram-3 me-1"></i>
                            {{ ucwords(str_replace('_', ' ', $project->sub_sector)) }}
                        </div>
                        @endif

                        {{-- META --}}
                        <div class="mt-3">

                            <div class="meta-pill text-center mb-2">
                                {{ $displayText }} • ₱{{ number_format($project->amount, 2) }}
                            </div>

                            <div class="small text-muted mb-1">
                                <i class="bi bi-calendar3 me-1"></i>
                                {{ $project->start_date?->format('M. d, Y') ?? '—' }}
                                →
                                <x-due-date
                                    :dueDate="$project->due_date"
                                    :progress="$project->progress" />
                            </div>

                            <div class="small text-muted mb-2">
                                <i class="bi bi-list-check me-1"></i>
                                {{ $completed }} / {{ $total }} Tasks
                            </div>

                        </div>

                        {{-- PROGRESS --}}
                        <div class="mt-2">
                            <x-progress-bar :value="$project->progress" />
                        </div>

                        {{-- STATUS + ACTIONS BELOW PROGRESS --}}
                        <div class="mt-3">

                            <div class="mb-2">
                                <span class="badge rounded-pill {{ $badgeClass }}">
                                    <i class="bi {{ $statusIcon }} me-1"></i>
                                    {{ $statusLabel }}
                                </span>
                            </div>

                            <div class="d-flex gap-2">

                                <a href="{{ route('projects.show', $project->id) }}"
                                    class="btn btn-sm btn-light flex-fill">
                                    <i class="bi bi-eye-fill"></i>
                                </a>

                                @if(auth()->user()->isAdmin())
                                <a href="{{ route('projects.edit', $project->id) }}"
                                    class="btn btn-sm btn-light flex-fill">
                                    <i class="bi bi-pencil-fill"></i>
                                </a>

                                <button class="btn btn-sm btn-light flex-fill"
                                    data-bs-toggle="modal"
                                    data-bs-target="#confirmActionModal"
                                    data-action="{{ route('projects.archive', $project->id) }}"
                                    data-method="PATCH"
                                    data-title="Archive Project"
                                    data-message="Are you sure you want to archive this project?"
                                    data-confirm-text="Archive"
                                    data-confirm-class="btn-danger">
                                    <i class="bi bi-archive-fill"></i>
                                </button>
                                @endif

                            </div>

                        </div>

                    </div>
                </div>

            </div>

            @empty
            <div class="text-center text-muted py-4">
                No projects found.
            </div>
            @endforelse

            <div class="mt-4">
                {{ $projects->links() }}
            </div>

    </div>

</x-page-wrapper>
@endsection