@extends('layouts.app')

@section('title', 'Project Activity Log')

@section('content')

<x-page-wrapper title="Project Activity Logs">

    @php
        $subSectorLabels = [
            'basic_education' => 'Basic Education',
            'higher_education' => 'Higher Education',
            'madaris_education' => 'Madaris Education',
            'technical_education' => 'Technical Education',
            'others' => 'Others',
        ];
    @endphp

    {{-- ===================================================== --}}
    {{-- DESKTOP TABLE VIEW --}}
    {{-- ===================================================== --}}
    <div class="d-none d-lg-block">
        <div class="table-responsive">
            <table class="table table-sm align-middle">
                <thead class="table-light">
                    <tr>
                        <th style="width: 180px;">Date</th>
                        <th style="width: 180px;">User</th>
                        <th style="width: 300px;">Project</th>
                        <th style="width: 100px;">Action</th>
                        <th style="width: 300px;">Description</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($logs as $log)
                    <tr>

                        <td>{{ $log->created_at->format('F j, Y h:i A') }}</td>

                        <td>{{ $log->user->name ?? 'System' }}</td>

                        <td>
                            <a href="{{ route('projects.show', $log->project_id) }}?from=project_logs"
                               class="text-decoration-none text-dark fw-semibold">
                                {{ $log->project->name ?? '—' }}
                            </a>
                        </td>

                        <td>
                            <span class="badge bg-secondary text-uppercase">
                                {{ $log->action }}
                            </span>
                        </td>

                        <td>
                            <div>{{ $log->description }}</div>

                            @if(!empty($log->changes))
                                <button class="btn btn-link btn-sm p-0 mt-1"
                                        type="button"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#changes-{{ $log->id }}">
                                    View Changes
                                </button>

                                <div class="collapse mt-2 small"
                                     id="changes-{{ $log->id }}">
                                    @include('logs.partials.project-log-changes', [
                                        'changes' => $log->changes,
                                        'subSectorLabels' => $subSectorLabels
                                    ])
                                </div>
                            @endif
                        </td>

                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted">
                            No activity logs found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>


    {{-- ===================================================== --}}
    {{-- MOBILE CARD VIEW --}}
    {{-- ===================================================== --}}
    <div class="d-lg-none">

        @forelse($logs as $log)

        <div class="card shadow-sm border-0 mb-3">
            <div class="card-body">

                <div class="small text-muted mb-1">
                    {{ $log->created_at->format('F j, Y h:i A') }}
                </div>

                <div class="fw-bold mb-1">
                    {{ $log->project->name ?? '—' }}
                </div>

                <div class="small mb-1">
                    <strong>User:</strong> {{ $log->user->name ?? 'System' }}
                </div>

                <div class="small mb-1">
                    <span class="badge bg-secondary text-uppercase">
                        {{ $log->action }}
                    </span>
                </div>

                <div class="small mb-2">
                    {{ $log->description }}
                </div>

                @if(!empty($log->changes))
                    <button class="btn btn-link btn-sm p-0"
                            type="button"
                            data-bs-toggle="collapse"
                            data-bs-target="#mobile-changes-{{ $log->id }}">
                        View Changes
                    </button>

                    <div class="collapse mt-2 small"
                         id="mobile-changes-{{ $log->id }}">
                        @include('logs.partials.project-log-changes', [
                            'changes' => $log->changes,
                            'subSectorLabels' => $subSectorLabels
                        ])
                    </div>
                @endif

            </div>
        </div>

        @empty
            <div class="text-center text-muted py-4">
                No activity logs found.
            </div>
        @endforelse

    </div>

    <div class="mt-3">
        {{ $logs->links() }}
    </div>

</x-page-wrapper>

@endsection
