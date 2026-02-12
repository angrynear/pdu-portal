@extends('layouts.app')

@section('title', 'Task Activity Logs')

@section('content')
<x-page-wrapper title="Task Activity Logs">

    <div class="table-responsive">
        <table class="table align-middle">
            <thead class="table-light">
                <tr>
                    <th style="width:160px;">Date & Time</th>
                    <th style="width:180px;">User</th>
                    <th style="width:250px;">Project</th>
                    <th style="width:180px;">Task</th>
                    <th style="width:70px;">Action</th>
                    <th style="width:150px;">Description</th>
                </tr>
            </thead>

            <tbody>
                @forelse($logs as $log)
                <tr>
                    {{-- Date --}}
                    <td>
                        {{ $log->created_at->format('F j, Y h:i A') }}
                    </td>

                    {{-- User --}}
                    <td>
                        {{ $log->user->name ?? '—' }}
                    </td>

                    {{-- Project --}}
                    <td>
                        @if($log->task?->project)
                        <a href="{{ route('projects.show', ['project' => $log->task->project->id,'from' => 'task_logs']) }}"
                            class="text-decoration-none text-dark fw-semibold link-hover">
                            {{ $log->task->project->name }}
                        </a>
                        @else
                        —
                        @endif
                    </td>

                    {{-- Task --}}
                    <td>
                        @if($log->task)
                        <a href="{{ route('tasks.show', [
        'task' => $log->task->id,
        'from' => 'task_logs'
    ]) }}"
                            class="text-decoration-none text-dark fw-semibold link-hover">
                            {{ $log->task->task_type }}
                        </a>
                        @else
                        —
                        @endif
                    </td>

                    {{-- Action --}}
                    <td>
                        <span class="badge bg-secondary">
                            {{ ucfirst(str_replace('_', ' ', $log->action)) }}
                        </span>
                    </td>

                    {{-- Description --}}
                    <td>
                        {{ $log->description }}
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

    <div class="mt-3">
        {{ $logs->links() }}
    </div>

</x-page-wrapper>
@endsection