@extends('layouts.app')

@section('title', 'Archived Tasks')

@section('content')
<x-page-wrapper title="Archived Tasks">

    <x-slot name="actions">
        <a href="{{ route('tasks.index') }}"
           class="btn btn-sm btn-secondary">
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
                        <th class="text-center" style="width: 50px;">No.</th>
                        <th style="width: 130px;">Task</th>
                        <th style="width: 210px;">Project Title</th>
                        <th style="width: 150px;">Assigned Personnel</th>
                        <th style="width: 130px;">Timeline</th>
                        <th class="text-center" style="width: 80px;">Progress</th>
                        <th class="text-center" style="width: 140px;">Archived At</th>
                        <th style="width: 100px;" class="text-center">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($tasks as $task)
                    <tr class="text-muted">

                        <td class="text-center">
                            {{ $tasks->firstItem() + $loop->index }}
                        </td>

                        <td>{{ $task->task_type }}</td>

                        <td>{{ $task->project->name }}</td>

                        <td>{{ $task->assignedUser->name ?? '—' }}</td>

                        <td class="small">
                            <div>
                                <strong>Start:</strong>
                                {{ $task->start_date?->format('M. j, Y') ?? '—' }}
                            </div>
                            <div>
                                <strong>Due:</strong>
                                {{ $task->due_date?->format('M. j, Y') ?? '—' }}
                            </div>
                        </td>

                        <td class="text-center">
                            <x-progress-bar :value="$task->progress" />
                        </td>

                        <td class="text-center">
                            {{ $task->archived_at?->format('F d, Y') }}
                        </td>

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
                                <span class="text-muted small">
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

                <div class="fw-bold mb-2">
                    {{ $task->task_type }}
                </div>

                <div class="small mb-1">
                    <strong>Project:</strong>
                    {{ $task->project->name }}
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
