<div class="row g-4">

    @foreach ($data as $project)

    @php
    $total = $project->total_tasks_count ?? 0;
    $completed = $project->completed_tasks_count ?? 0;

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

    <div class="col-12 col-lg-6">
        <div class="card project-card shadow-sm border-0 h-100 text-muted">
            <div class="card-body d-flex flex-column">

                {{-- HEADER --}}
                <div class="d-flex justify-content-between align-items-start">

                    <div>
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <span class="text-muted fw-semibold">
                                #{{ $data->firstItem() + $loop->index }}
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

                    <span class="badge rounded-pill bg-secondary">
                        <i class="bi bi-archive-fill me-1"></i>
                        Archived
                    </span>

                </div>

                {{-- META --}}
                <div class="project-meta mt-3">

                    <div class="d-flex flex-column flex-md-row align-items-md-center gap-2 gap-md-3">

                        {{-- FUNDING META PILL --}}
                        <div>
                            <span class="meta-pill">
                                {{ $displayText }} • ₱{{ number_format($project->amount, 2) }}
                            </span>
                        </div>

                        {{-- TIMELINE --}}
                        <div class="small text-muted">
                            <i class="bi bi-calendar3 me-1"></i>
                            {{ $project->start_date?->format('M. d, Y') ?? '—' }}
                            <span class="mx-1">→</span>
                            <x-due-date :dueDate="$project->due_date" :progress="$project->progress" />
                        </div>

                    </div>

                </div>

                {{-- PROGRESS --}}
                <div class="mt-3">
                    <x-progress-bar :value="$project->progress" />
                </div>

                {{-- ARCHIVED DATE --}}
                <div class="small text-muted mt-3">
                    <i class="bi bi-clock-history me-1"></i>
                    Archived {{ $project->archived_at?->format('F d, Y') }}
                </div>

                {{-- FOOTER --}}
                <div class="mt-auto pt-3">
                    <button
                        type="button"
                        class="btn btn-sm btn-success w-100"
                        data-bs-toggle="modal"
                        data-bs-target="#confirmActionModal"
                        data-action="{{ route('projects.restore', $project->id) }}"
                        data-method="PATCH"
                        data-title="Restore Project"
                        data-message="Are you sure you want to restore this project? Tasks under this project will remain archived."
                        data-confirm-text="Restore"
                        data-confirm-class="btn-success">
                        Restore
                    </button>
                </div>

            </div>
        </div>
    </div>

    @endforeach

</div>

<div class="mt-4">
    {{ $data->links() }}
</div>