@extends('layouts.app')

@section('title', 'Task Details')

@section('content')
<x-page-wrapper title="Task Details">

    {{-- ============================= --}}
    {{-- BACK BUTTON --}}
    {{-- ============================= --}}
    <x-slot name="actions">

        @php
        switch ($from) {
            case 'project':
                $backUrl = route('projects.show', $task->project_id);
                $label = 'Project';
                break;

            case 'task_logs':
                $backUrl = route('logs.tasks');
                $label = 'Task Logs';
                break;

            case 'my':
                $backUrl = route('tasks.my');
                $label = 'My Tasks';
                break;

            case 'manage':
            default:
                $backUrl = route('tasks.index');
                $label = auth()->user()->isAdmin() ? 'Manage Tasks' : 'My Tasks';
                break;
        }
        @endphp

        <a href="{{ $backUrl }}" class="btn btn-sm btn-secondary">
            ← Back to {{ $label }}
        </a>
    </x-slot>


    {{-- ============================= --}}
    {{-- HEADER --}}
    {{-- ============================= --}}
    @php
        $progress = $task->progress ?? 0;
        $status = $progress == 100 ? 'Completed' : ($progress > 0 ? 'Ongoing' : 'Not Started');

        $statusClasses = [
            'Not Started' => 'bg-secondary',
            'Ongoing' => 'bg-warning text-dark',
            'Completed' => 'bg-success',
        ];
    @endphp

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3 gap-2">

        <div>
            <h5 class="mb-1">{{ ucfirst($task->task_type) }}</h5>
            <div class="text-muted small">
                Project: {{ $task->project->name ?? '—' }}
            </div>
        </div>

    </div>

    <hr>

    {{-- ============================= --}}
    {{-- INFO GRID --}}
    {{-- ============================= --}}
    <div class="row mb-4">

        <div class="col-12 col-md-4 mb-3">
            <div class="fw-semibold">Assigned To</div>
            <div>
                {{ $task->assignedUser->name ?? 'Deactivated User' }}
            </div>
        </div>

        <div class="col-12 col-md-4 mb-3">
            <div class="fw-semibold">Start Date</div>
            <div>{{ $task->start_date?->format('F j, Y') ?? '—' }}</div>
        </div>

        <div class="col-12 col-md-4 mb-3">
            <div class="fw-semibold">Due Date</div>
            <div>
                <x-due-date
                    :dueDate="$task->due_date"
                    :progress="$task->progress" />
            </div>
        </div>

        <div class="col-12 col-md-4 mb-3">
            <div class="fw-semibold mb-1">Progress</div>
            <x-progress-bar :value="$task->progress" />
        </div>

    </div>

    {{-- ============================= --}}
    {{-- ACTIVITY LOGS --}}
    {{-- ============================= --}}
    <h6 class="text-muted mb-3">Task Activity</h6>

    <div class="ms-1">

    @forelse($activityLogs as $log)

        <div class="card shadow-sm border-0 mb-3">
            <div class="card-body">

                <div class="d-flex justify-content-between">
                    <strong>{{ $log->user->name ?? 'System' }}</strong>
                    <small class="text-muted">
                        {{ $log->created_at->diffForHumans() }}
                    </small>
                </div>

                {{-- PROGRESS CHANGE --}}
                @if(isset($log->changes['progress']))

                    @php
                        $old = $log->changes['progress']['old'] ?? null;
                        $new = $log->changes['progress']['new'] ?? null;
                    @endphp

                    <div class="mt-2 small">
                        <strong>Progress:</strong>

                        @if(!is_null($old))
                            <span class="text-danger">{{ $old }}%</span>
                            <i class="bi bi-arrow-right mx-1 text-muted"></i>
                        @endif

                        <span class="text-success fw-semibold">
                            {{ $new }}%
                        </span>

                        <x-progress-bar :value="$new" />
                    </div>

                @endif

                {{-- REMARKS --}}
                @if(isset($log->changes['remark']))
                    <div class="mt-2 small">
                        <strong>Remarks:</strong>
                        {{ $log->changes['remark']['new'] }}
                    </div>
                @endif

                {{-- FILES --}}
                @if($log->files->count())
                    <div class="mt-2 small">
                        <strong>Files:</strong><br>
                        @foreach($log->files as $file)
                            <a href="{{ asset('storage/' . $file->file_path) }}"
                               target="_blank">
                                {{ $file->original_name }}
                            </a><br>
                        @endforeach
                    </div>
                @endif

                {{-- OTHER STRUCTURAL CHANGES --}}
                @foreach($log->changes ?? [] as $field => $values)

                    @if(!in_array($field, ['progress','remark','files']))

                        @php
                            $old = $values['old'] ?? null;
                            $new = $values['new'] ?? null;
                            $label = ucwords(str_replace('_', ' ', $field));

                            if (in_array($field, ['start_date','due_date'])) {
                                $old = $old ? \Carbon\Carbon::parse($old)->format('F d, Y') : '—';
                                $new = $new ? \Carbon\Carbon::parse($new)->format('F d, Y') : '—';
                            }

                            if ($field === 'assigned_user_id') {
                                $old = $changedUsers[$old] ?? '—';
                                $new = $changedUsers[$new] ?? '—';
                                $label = 'Assigned To';
                            }
                        @endphp

                        <div class="mt-2 small">
                            <strong>{{ $label }}:</strong>

                            <span class="text-danger">{{ $old ?? '—' }}</span>
                            <i class="bi bi-arrow-right mx-1 text-muted"></i>
                            <span class="text-success">{{ $new ?? '—' }}</span>
                        </div>

                    @endif

                @endforeach

            </div>
        </div>

    @empty
        <div class="text-muted">No activity recorded.</div>
    @endforelse

    </div>

    <div class="mt-3">
        {{ $activityLogs->withQueryString()->links() }}
    </div>

</x-page-wrapper>
@endsection
