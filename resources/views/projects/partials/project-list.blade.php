    @php
    $isAdmin = auth()->user()?->isAdmin();
    $isMyPage = request()->routeIs('projects.my');
    @endphp


    <div class="project-list">

        @forelse($projects as $project)

        @php
        $total = $project->total_tasks_count;
        $completed = $project->completed_tasks_count;

        /* Funding Display Logic */
        $source = $project->source_of_fund;
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


        {{-- ================= DESKTOP ================= --}}
        <div class="d-none d-md-block">
            <div class="card project-card {{ $project->status_border_class }} shadow-sm border-0 mb-3">
                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-start">

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

                        <div class="d-flex align-items-center gap-2">

                            <span class="badge rounded-pill {{ $project->status_badge_class }}">
                                <i class="bi {{ $project->status_icon }} me-1"></i>
                                {{ $project->status_label }}
                            </span>

                            <a href="{{ route('projects.show', ['project' => $project->id,'from' => 'projects','scope' => request('scope')]) }}"
                                class="btn btn-sm btn-light p-2">
                                <i class="bi bi-eye-fill"></i>
                            </a>

                            @if($isAdmin)
                            <a href="{{ route('projects.edit', ['project' => $project->id,'scope' => request('scope')]) }}"
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
                                <i class="bi bi-archive-fill text-danger"></i>
                            </button>
                            @endif

                        </div>
                    </div>

                    {{-- META --}}
                    <div class="project-meta mt-3">
                        <div class="d-flex flex-wrap align-items-center gap-4">

                            <div>
                                <span class="meta-pill">
                                    {{ $displayText }} • ₱{{ number_format($project->amount, 2) }}
                                </span>
                            </div>

                            <div class="small text-muted">
                                <i class="bi bi-calendar3 me-1"></i>
                                {{ $project->start_date?->format('M. d, Y') ?? '—' }}
                                <span class="mx-1">→</span>
                                <x-due-date
                                    :dueDate="$project->due_date"
                                    :progress="$project->progress" />
                            </div>

                            <div class="small text-muted">
                                <i class="bi bi-list-check me-1"></i>
                                {{ $completed }} / {{ $total }} Tasks
                            </div>

                        </div>
                    </div>

                    <div class="mt-3">
                        <x-progress-bar :value="$project->progress" />
                    </div>

                </div>
            </div>
        </div>


        {{-- ================= MOBILE ================= --}}
        <div class="d-md-none">
            <div class="card project-card shadow-sm border-0 mb-3">
                <div class="card-body">

                    <div class="fw-semibold mb-1">
                        #{{ $projects->firstItem() + $loop->index }}
                        {{ $project->name }}
                    </div>

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

                    <div class="mt-2">
                        <x-progress-bar :value="$project->progress" />
                    </div>

                    <div class="mt-3">

                        <div class="mb-2">
                            <span class="badge rounded-pill {{ $project->status_badge_class }}">
                                <i class="bi {{ $project->status_icon }} me-1"></i>
                                {{ $project->status_label }}
                            </span>
                        </div>

                        <div class="d-flex gap-2">

                            <a href="{{ route('projects.show', ['project' => $project->id,'from' => 'projects','scope' => request('scope')]) }}"
                                class="btn btn-sm btn-light flex-fill">
                                <i class="bi bi-eye-fill"></i>
                            </a>

                            @if($isAdmin)
                            <a href="{{ route('projects.edit', ['project' => $project->id,'scope' => request('scope')]) }}"
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
            No projects found.
        </div>
        @endforelse

        <div class="mt-4">
            {{ $projects->links() }}
        </div>

    </div>