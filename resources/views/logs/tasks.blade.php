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
                    <th style="width:230px;">Project</th>
                    <th style="width:100px;">Task</th>
                    <th style="width:70px;">Action</th>
                    <th style="width:300px;">Description</th>
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

                        <div>
                            {{ $log->description }}
                        </div>

                        @if(!empty($log->changes))

                        <button class="btn btn-link btn-sm p-0 mt-1 text-decoration-none"
                            type="button"
                            data-bs-toggle="collapse"
                            data-bs-target="#taskChanges-{{ $log->id }}">
                            View Details
                        </button>

                        <div class="collapse mt-2 small"
                            id="taskChanges-{{ $log->id }}">

                            @php
                            $fieldLabels = [
                            'task_type' => 'Task Type',
                            'assigned_user_id' => 'Assigned Personnel',
                            'start_date' => 'Start Date',
                            'due_date' => 'Due Date',
                            'progress' => 'Progress',
                            'remark' => 'Remark',
                            'files' => 'Attachments',
                            ];
                            @endphp

                            @foreach($log->changes as $field => $values)

                            @php
                            $label = $fieldLabels[$field] ?? ucwords(str_replace('_', ' ', $field));

                            $old = $values['old'] ?? null;
                            $new = $values['new'] ?? null;

                            // Format progress
                            if ($field === 'progress') {
                            $old = $old !== null ? $old . '%' : null;
                            $new = $new !== null ? $new . '%' : null;
                            }

                            // Format dates
                            if (in_array($field, ['start_date','due_date'])) {
                            $old = $old ? \Carbon\Carbon::parse($old)->format('M. d, Y') : null;
                            $new = $new ? \Carbon\Carbon::parse($new)->format('M. d, Y') : null;
                            }

                            // Convert assigned user ID to name
                            if ($field === 'assigned_user_id') {
                            $oldUser = \App\Models\User::find($old);
                            $newUser = \App\Models\User::find($new);

                            $old = $oldUser?->name ?? '—';
                            $new = $newUser?->name ?? '—';
                            }
                            @endphp

                            <div class="small">
                                <strong>{{ $label }}:</strong>

                                <span class="text-danger">
                                    {{ $old ?? '—' }}
                                </span>

                                <i class="bi bi-arrow-right mx-2 text-muted"></i>

                                <span class="text-success">
                                    {{ $new ?? '—' }}
                                </span>
                            </div>

                            @endforeach

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

    <div class="mt-3">
        {{ $logs->links() }}
    </div>

</x-page-wrapper>
@endsection