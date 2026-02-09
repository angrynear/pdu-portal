@extends('layouts.app')

@section('title', 'Project Details')

@section('content')
<x-page-wrapper title="Project Details">

    <x-slot name="actions">
        <a href="{{ route('projects.index') }}" class="btn btn-sm btn-secondary">
            ← Back to Project
        </a>
    </x-slot>

    @php
    $progress = $project->progress ?? 0;

    $progressClass = match (true) {
    $progress == 100 => 'bg-primary',
    $progress >= 50 => 'bg-success',
    $progress > 0 => 'bg-warning',
    default => 'bg-secondary',
    };
    @endphp

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-start mb-3">
        <div>
            <h5 class="mb-1">{{ $project->name }}</h5>
            <div class="text-muted small">
                {{ $project->location }}
            </div>
        </div>

        {{-- Status --}}
        @php
        $statusClasses = [
        'Not Started' => 'bg-secondary',
        'Ongoing' => 'bg-success',
        'Completed' => 'bg-primary',
        ];
        @endphp

        <span class="badge {{ $statusClasses[$project->status] ?? 'bg-secondary' }}">
            {{ $project->status }}
        </span>
    </div>

    <hr>

    {{-- INFO GRID --}}
    <div class="row mb-3 small">
        <div class="col-md-4 mb-2">
            <div class="fw-semibold">Source of Fund</div>
            <div>{{ $project->source_of_fund }}</div>
        </div>

        <div class="col-md-4 mb-2">
            <div class="fw-semibold">Funding Year</div>
            <div>{{ $project->funding_year }}</div>
        </div>

        <div class="col-md-4 mb-2">
            <div class="fw-semibold">Amount</div>
            <div>₱ {{ number_format($project->amount, 2) }}</div>
        </div>

        <div class="col-md-4 mb-2">
            <div class="fw-semibold">Date Started</div>
            <div>{{ $project->start_date?->format('M d, Y') ?? '—' }}</div>
        </div>

        <div class="col-md-4 mb-2">
            <div class="fw-semibold">Target Completion</div>
            <div>{{ $project->due_date?->format('M d, Y') ?? '—' }}</div>
        </div>

        <div class="col-md-4 mb-2">
            <div class="fw-semibold mb-1">Progress</div>

            <div class="progress" style="height: 6px;">
                <div class="progress-bar {{ $progressClass }}"
                    role="progressbar"
                    style="width: {{ $progress }}%;"
                    aria-valuenow="{{ $progress }}"
                    aria-valuemin="0"
                    aria-valuemax="100">
                </div>
            </div>

            <div class="small text-muted mt-1">
                {{ $progress }}%
            </div>
        </div>

        <div class="col-md-4 mb-2">
            <div class="fw-semibold">Task</div>
            <div> Completed: {{ $project->completed_tasks_count ?? 0 }}
                /
                Total: {{ $project->total_tasks_count ?? 0 }}
            </div>

        </div>

        {{-- DESCRIPTION --}}
        <div>
            <div class="fw-semibold mb-1">Description</div>
            <div>
                {{ $project->description ?: 'No description provided.' }}
            </div>
        </div>

</x-page-wrapper>
@endsection