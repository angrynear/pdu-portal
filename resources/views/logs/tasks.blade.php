@extends('layouts.app')

@section('title', 'Task Activity Logs')

@section('content')
<x-page-wrapper title="Task Activity Logs">

    {{-- ===================================================== --}}
    {{-- DESKTOP TABLE VIEW --}}
    {{-- ===================================================== --}}
    <div class="d-none d-lg-block">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead class="table-light">
                    <tr>
                        <th style="width:160px;">Date & Time</th>
                        <th style="width:180px;">User</th>
                        <th style="width:230px;">Project</th>
                        <th style="width:150px;">Task</th>
                        <th style="width:100px;">Action</th>
                        <th style="width:300px;">Description</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($logs as $log)
                    <tr>

                        <td>
                            {{ $log->created_at->format('F j, Y h:i A') }}
                        </td>

                        <td>
                            {{ $log->user->name ?? '—' }}
                        </td>

                        <td>
                            @if($log->task?->project)
                                <a href="{{ route('projects.show', [
                                    'project' => $log->task->project->id,
                                    'from' => 'task_logs'
                                ]) }}"
                                   class="text-decoration-none text-dark fw-semibold">
                                    {{ $log->task->project->name }}
                                </a>
                            @else
                                —
                            @endif
                        </td>

                        <td>
                            @if($log->task)
                                <a href="{{ route('tasks.show', [
                                    'task' => $log->task->id,
                                    'from' => 'task_logs'
                                ]) }}"
                                   class="text-decoration-none text-dark fw-semibold">
                                    {{ $log->task->task_type }}
                                </a>
                            @else
                                —
                            @endif
                        </td>

                        <td>
                            <span class="badge bg-secondary">
                                {{ ucfirst(str_replace('_', ' ', $log->action)) }}
                            </span>
                        </td>

                        <td>
                            <div>{{ $log->description }}</div>

                            @if(!empty($log->changes))
                                <button class="btn btn-link btn-sm p-0 mt-1"
                                        type="button"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#taskChanges-{{ $log->id }}">
                                    View Details
                                </button>

                                <div class="collapse mt-2 small"
                                     id="taskChanges-{{ $log->id }}">
                                    @include('logs.partials.task-log-changes', [
                                        'changes' => $log->changes
                                    ])
                                </div>
                            @endif
                        </td>

                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted">
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
                    @if($log->task?->project)
                        {{ $log->task->project->name }}
                    @else
                        —
                    @endif
                </div>

                <div class="small mb-1">
                    <strong>User:</strong>
                    {{ $log->user->name ?? '—' }}
                </div>

                <div class="small mb-1">
                    <strong>Task:</strong>
                    {{ $log->task->task_type ?? '—' }}
                </div>

                <div class="small mb-1">
                    <span class="badge bg-secondary">
                        {{ ucfirst(str_replace('_', ' ', $log->action)) }}
                    </span>
                </div>

                <div class="small mb-2">
                    {{ $log->description }}
                </div>

                @if(!empty($log->changes))
                    <button class="btn btn-link btn-sm p-0"
                            type="button"
                            data-bs-toggle="collapse"
                            data-bs-target="#mobileTaskChanges-{{ $log->id }}">
                        View Details
                    </button>

                    <div class="collapse mt-2 small"
                         id="mobileTaskChanges-{{ $log->id }}">
                        @include('logs.partials.task-log-changes', [
                            'changes' => $log->changes
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
