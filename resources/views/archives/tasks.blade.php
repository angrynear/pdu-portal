@extends('layouts.app')

@section('title', 'Archived Tasks')

@section('content')
<x-page-wrapper title="Archived Tasks">

    <x-slot name="actions">
        <a href="{{ route('tasks.index') }}"
            class="btn btn-sm btn-outline-secondary">
            ← Back to Tasks
        </a>
    </x-slot>

    @if ($tasks->isEmpty())
    <div class="text-center text-muted py-0">
        No archived tasks found.
    </div>
    @else

    {{-- ===================================================== --}}
    {{-- DESKTOP TABLE VIEW --}}
    {{-- ===================================================== --}}
    <div class="d-none d-lg-block">
        <div class="table-responsive">
            <table class="table table-sm align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="text-center" style="width: 60px;">No.</th>
                        <th style="width: 280px;">Task</th>
                        <th style="width: 160px;">Assigned</th>
                        <th style="width: 220px;">Timeline</th>
                        <th class="text-center" style="width: 150px;">Progress</th>
                        <th class="text-center" style="width: 160px;">Archived At</th>
                        <th class="text-center" style="width: 140px;">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($tasks as $task)
                    <tr class="text-muted">

                        {{-- NO --}}
                        <td class="text-center fw-semibold">
                            {{ $tasks->firstItem() + $loop->index }}
                        </td>

                        {{-- TASK + PROJECT --}}
                        <td>
                            <div class="fw-semibold">
                                {{ $task->task_type }}
                            </div>
                            <div class="small text-muted">
                                {{ $task->project->name ?? '—' }}
                            </div>
                        </td>

                        {{-- ASSIGNED --}}
                        <td>
                            {{ $task->assignedUser->name ?? '—' }}
                        </td>

                        {{-- TIMELINE --}}
                        <td>
                            <div class="small">
                                <strong>Start:</strong>
                                {{ $task->start_date?->format('M. d, Y') ?? '—' }}
                            </div>
                            <div class="small text-muted">
                                <strong>Due:</strong>
                                {{ $task->due_date?->format('M. d, Y') ?? '—' }}
                            </div>
                        </td>

                        {{-- PROGRESS --}}
                        <td class="text-center">
                            <x-progress-bar :value="$task->progress" />
                        </td>

                        {{-- ARCHIVED AT --}}
                        <td class="text-center">
                            {{ $task->archived_at?->format('F d, Y') }}
                        </td>

                        {{-- ACTIONS --}}
                        <td class="text-center">
                            @if (is_null($task->project->archived_at))
                            <button
                                type="button"
                                class="btn btn-sm btn-success"
                                data-bs-toggle="modal"
                                data-bs-target="#confirmActionModal"
                                data-action="{{ route('tasks.restore', $task->id) }}"
                                data-method="PATCH"
                                data-title="Restore Task"
                                data-message="Are you sure you want to restore this task?"
                                data-confirm-text="Restore"
                                data-confirm-class="btn-success">
                                Restore
                            </button>
                            @else
                            <span class="small text-muted">
                                Project Archived
                            </span>
                            @endif
                        </td>

                    </tr>

                    @endforeach
                </tbody>
            </table>

            <div class="mt-3">
                {{ $tasks->links() }}
            </div>
        </div>
    </div>


    {{-- ===================================================== --}}
    {{-- MOBILE CARD VIEW --}}
    {{-- ===================================================== --}}
    <div class="d-lg-none">

        @foreach ($tasks as $task)

        <div class="card shadow-sm border-0 mb-3 text-muted">
            <div class="card-body">

                <div class="fw-bold">
                    {{ $task->task_type }}
                </div>
                <div class="small text-muted mb-2">
                    {{ $task->project->name ?? '—' }}
                </div>

                <div class="small mb-1">
                    <strong>Assigned:</strong>
                    {{ $task->assignedUser->name ?? '—' }}
                </div>

                <div class="small mb-2">
                    <strong>Timeline:</strong><br>
                    Start: {{ $task->start_date?->format('M. j, Y') ?? '—' }}<br>
                    Due: {{ $task->due_date?->format('M. j, Y') ?? '—' }}
                </div>

                <div class="small mb-2">
                    <strong>Progress:</strong>
                    <x-progress-bar :value="$task->progress" />
                </div>


                <div class="small mb-3">
                    <strong>Archived:</strong>
                    {{ $task->archived_at?->format('F d, Y') }}
                </div>

                <div class="d-grid">
                    @if (is_null($task->project->archived_at))
                    <button
                        type="button"
                        class="btn btn-sm btn-success"
                        data-bs-toggle="modal"
                        data-bs-target="#confirmActionModal"
                        data-action="{{ route('tasks.restore', $task->id) }}"
                        data-method="PATCH"
                        data-title="Restore Task"
                        data-message="Are you sure you want to restore this task?"
                        data-confirm-text="Restore"
                        data-confirm-class="btn-success">
                        Restore
                    </button>
                    @else
                    <div class="text-center small text-muted">
                        Project Archived
                    </div>
                    @endif
                </div>

            </div>
        </div>

        @endforeach

        <div class="mt-3">
            {{ $tasks->links() }}
        </div>

    </div>

    @endif

</x-page-wrapper>
@endsection