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

                    {{-- Task --}}
                    <td>
                            {{ $task->task_type }}
                    </td>

                    {{-- Project --}}
                    <td>
                            {{ $task->project->name }}
                    </td>

                    {{-- Assigned --}}
                    <td>{{ $task->assignedUser->name ?? '—' }}</td>

                    {{-- Timeline --}}
                    <td class="small">
                        <div>
                            <strong>Start Date:</strong>
                            {{ $task->start_date?->format('M. j, Y') ?? '—' }}
                        </div>
                        <div>
                            <strong>Due Date:</strong>
                            {{ $task->due_date?->format('M. j, Y') ?? '—' }}
                        </div>
                    </td>

                    {{-- Progress --}}
                    <td class="text-center">
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-secondary"
                                style="width: {{ $task->progress }}%">
                            </div>
                        </div>
                        <div class="small {{ $task->progress == 100 ? 'text-success fw-semibold' : 'text-muted' }}">
                            {{ $task->progress }}%
                        </div>
                    </td>

                    {{-- Archived At --}}
                    <td class="text-center">
                        {{ $task->archived_at?->format('F d, Y') }}
                    </td>

                    {{-- Actions --}}
                    <td class="text-center">
                        {{-- Restore Task --}}
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
    @endif

</x-page-wrapper>
@endsection