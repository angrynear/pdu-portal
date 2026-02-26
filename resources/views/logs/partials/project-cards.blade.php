@php
    $subSectorLabels = [
        'basic_education' => 'Basic Education',
        'higher_education' => 'Higher Education',
        'madaris_education' => 'Madaris Education',
        'technical_education' => 'Technical Education',
        'others' => 'Others',
    ];
@endphp

<div class="row g-4">

@forelse($data as $log)

<div class="col-12 col-lg-6">
    <div class="card shadow-sm border-0 h-100">
        <div class="card-body d-flex flex-column">

            {{-- ================= HEADER ================= --}}
            <div class="d-flex justify-content-between align-items-start mb-2">

                <div>

                    {{-- Project --}}
                    @if($log->project)
                        <a href="{{ route('projects.show', ['project' => $log->project_id,'from' => 'logs','scope' => request('scope')]) }}"
                        class="fw-semibold text-decoration-none text-dark">
                            {{ $log->project->name }}
                        </a>
                    @else
                        <div class="fw-semibold text-muted">
                            Deleted Project
                        </div>
                    @endif

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
                        data-bs-target="#projectChanges-{{ $log->id }}">
                    View Changes
                </button>

                <div class="collapse mt-2 small"
                     id="projectChanges-{{ $log->id }}">

                    @include('logs.partials.project-log-changes', [
                        'changes' => $log->changes,
                        'subSectorLabels' => $subSectorLabels
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