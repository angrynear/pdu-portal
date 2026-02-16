@extends('layouts.app')

@section('title', 'Archived Projects')

@section('content')
<x-page-wrapper title="Archived Projects">

    <x-slot name="actions">
        <a href="{{ route('projects.index') }}"
            class="btn btn-sm btn-secondary">
            ← Back to Projects
        </a>
    </x-slot>

    @if ($projects->isEmpty())
    <div class="text-center text-muted py-0">
        No archived projects found.
    </div>
    @else

    {{-- ===================================================== --}}
    {{-- DESKTOP TABLE VIEW --}}
    {{-- ===================================================== --}}
    <div class="d-none d-lg-block">
        <div class="table-responsive">
            <table class="table table-sm align-middle table-projects">
                <thead class="table-light">
                    <tr>
                        <th class="text-center" style="width: 60px;">No.</th>
                        <th style="width: 320px;">Project</th>
                        <th style="width: 220px;">Source of Fund</th>
                        <th style="width: 220px;">Timeline</th>
                        <th class="text-center" style="width: 150px;">Progress</th>
                        <th class="text-center" style="width: 160px;">Archived At</th>
                        <th class="text-center" style="width: 140px;">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($projects as $project)
                    <tr class="text-muted">

                        {{-- NO --}}
                        <td class="text-center fw-semibold">
                            {{ $projects->firstItem() + $loop->index }}
                        </td>

                        {{-- PROJECT NAME + LOCATION --}}
                        <td>
                            <div class="fw-semibold">
                                {{ $project->name }}
                            </div>
                            <div class="small text-muted">
                                {{ $project->location ?? '—' }}
                            </div>
                        </td>

                        {{-- SOURCE + YEAR + AMOUNT --}}
                        <td>
                            <div class="fw-semibold">
                                {{ $project->source_of_fund ?? '—' }}
                                {{ $project->funding_year ?? '' }}
                            </div>
                            <div class="small text-muted">
                                PHP {{ number_format($project->amount, 2) }}
                            </div>
                        </td>

                        {{-- TIMELINE --}}
                        <td>
                            <div class="small">
                                <strong>Start:</strong>
                                {{ $project->start_date?->format('M. d, Y') ?? '—' }}
                            </div>
                            <div class="small text-muted">
                                <strong>Due:</strong>
                                {{ $project->due_date?->format('M. d, Y') ?? '—' }}
                            </div>
                        </td>

                        {{-- PROGRESS --}}
                        <td class="text-center">
                            <x-progress-bar :value="$project->progress" />
                        </td>

                        {{-- ARCHIVED AT --}}
                        <td class="text-center">
                            {{ $project->archived_at?->format('F d, Y') }}
                        </td>

                        {{-- ACTIONS --}}
                        <td class="text-center">
                            <button
                                type="button"
                                class="btn btn-sm btn-success"
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
                        </td>

                    </tr>

                    @endforeach
                </tbody>
            </table>

            <div class="mt-3">
                {{ $projects->links() }}
            </div>
        </div>
    </div>


    {{-- ===================================================== --}}
    {{-- MOBILE CARD VIEW --}}
    {{-- ===================================================== --}}
    <div class="d-lg-none">

        @foreach ($projects as $project)

        <div class="card shadow-sm border-0 mb-3 text-muted">
            <div class="card-body">

                <div class="fw-bold">
                    {{ $project->name }}
                </div>
                <div class="small text-muted mb-2">
                    {{ $project->location ?? '—' }}
                </div>

                <div class="small mb-1">
                    <strong>Funding:</strong>
                    {{ $project->source_of_fund }} {{ $project->funding_year }}
                </div>
                <div class="small text-muted mb-1">
                    PHP {{ number_format($project->amount, 2) }}
                </div>

                <div class="small mb-2">
                    <strong>Timeline:</strong><br>
                    Start: {{ $project->start_date?->format('M. j, Y') ?? '—' }}<br>
                    Due: {{ $project->due_date?->format('M. j, Y') ?? '—' }}
                </div>

                <div class="small mb-2">
                    <strong>Progress:</strong>
                    <x-progress-bar :value="$project->progress" />
                </div>

                <div class="small mb-3">
                    <strong>Archived:</strong>
                    {{ $project->archived_at?->format('F d, Y') }}
                </div>

                <div class="d-grid">
                    <button
                        type="button"
                        class="btn btn-sm btn-success"
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

        @endforeach

        <div class="mt-3">
            {{ $projects->links() }}
        </div>

    </div>

    @endif

</x-page-wrapper>
@endsection