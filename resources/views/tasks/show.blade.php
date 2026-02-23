@extends('layouts.app')

@section('title', 'Task Details')

@section('content')
<x-page-wrapper title="Task Details">

    {{-- ================= BACK BUTTON ================= --}}
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

        <a href="{{ $backUrl }}" class="btn btn-sm btn-outline-secondary">
            ← Back to {{ $label }}
        </a>
    </x-slot>

    {{-- ================= TASK HEADER CARD ================= --}}
    <div class="card task-card {{ $task->status_border_class }} shadow-sm border-0 mb-4">
        <div class="card-body">

            {{-- TOP --}}
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start gap-3">

                <div class="flex-grow-1">

                    <div class="fw-semibold fs-5">
                        {{ ucfirst($task->task_type) }}
                    </div>

                    <div class="small text-muted mt-1">
                        <i class="bi bi-folder me-1"></i>
                        {{ $task->project->name ?? '—' }}
                    </div>

                </div>

                {{-- STATUS BADGE --}}
                <span class="badge rounded-pill {{ $task->status_badge_class }}">
                    <i class="bi 
                    {{ $task->status === 'completed' ? 'bi-check-circle-fill' :
                       ($task->status === 'overdue' ? 'bi-exclamation-triangle-fill' :
                       ($task->status === 'not_started' ? 'bi-dash-circle-fill' :
                       'bi-arrow-repeat')) }} 
                    me-1">
                    </i>
                    {{ $task->status_label }}
                </span>

            </div>

            {{-- META STRIP --}}
            <div class="mt-3">

                <div class="d-flex flex-column flex-md-row flex-wrap align-items-start align-items-md-center gap-3 gap-md-4">

                    <div class="small text-muted">
                        <i class="bi bi-person me-1"></i>
                        {{ $task->assignedUser->name ?? 'Deactivated User' }}
                    </div>

                    <div class="small text-muted">
                        <i class="bi bi-calendar3 me-1"></i>
                        {{ $task->start_date?->format('M. d, Y') ?? '—' }}
                        <span class="mx-1">→</span>
                        <x-due-date
                            :dueDate="$task->due_date"
                            :progress="$task->progress" />
                    </div>

                </div>
            </div>

            {{-- PROGRESS --}}
            <div class="mt-3">
                <x-progress-bar :value="$task->progress" />
            </div>

        </div>
    </div>


    {{-- ================= ACTIVITY SECTION ================= --}}
    <div class="mt-4">

        <h6 class="text-muted mb-3">Task Activity</h6>

        @forelse($activityLogs as $log)

        <div class="card shadow-sm border-0 mb-3">
            <div class="card-body">

                {{-- HEADER --}}
                <div class="d-flex justify-content-between align-items-center">
                    <strong>{{ $log->user->name ?? 'System' }}</strong>
                    <small class="text-muted">
                        {{ $log->created_at->diffForHumans() }}
                    </small>
                </div>

                {{-- CHANGES --}}
                <div class="mt-2">

                    {{-- PROGRESS CHANGE --}}
                    @if(isset($log->changes['progress']))
                    @php
                    $old = $log->changes['progress']['old'] ?? null;
                    $new = $log->changes['progress']['new'] ?? null;
                    @endphp

                    <div class="small mb-2">
                        <strong>Progress:</strong>
                        @if(!is_null($old))
                        <span class="text-danger">{{ $old }}%</span>
                        <i class="bi bi-arrow-right mx-1 text-muted"></i>
                        @endif
                        <span class="text-success fw-semibold">
                            {{ $new }}%
                        </span>

                        <div class="mt-1">
                            <x-progress-bar :value="$new" />
                        </div>
                    </div>
                    @endif

                    {{-- REMARK --}}
                    @if(isset($log->changes['remark']))
                    <div class="small mb-2">
                        <strong>Remarks:</strong><br>
                        <i class="bi bi-chat-left-text me-1"></i>
                        {{ $log->changes['remark']['new'] }}
                    </div>
                    @endif

                    {{-- OTHER CHANGES --}}
                    @foreach($log->changes ?? [] as $field => $values)

                    @if(!in_array($field, ['progress','remark','files']))

                    @php
                    $old = $values['old'] ?? null;
                    $new = $values['new'] ?? null;
                    $label = ucwords(str_replace('_', ' ', $field));

                    if (in_array($field, ['start_date','due_date'])) {
                    $old = $old ? \Carbon\Carbon::parse($old)->format('M d, Y') : '—';
                    $new = $new ? \Carbon\Carbon::parse($new)->format('M d, Y') : '—';
                    }

                    if ($field === 'assigned_user_id') {
                    $old = $changedUsers[$old] ?? '—';
                    $new = $changedUsers[$new] ?? '—';
                    $label = 'Assigned To';
                    }
                    @endphp

                    <div class="small mb-2">
                        <strong>{{ $label }}:</strong>
                        <span class="text-danger">{{ $old ?? '—' }}</span>
                        <i class="bi bi-arrow-right mx-1 text-muted"></i>
                        <span class="text-success">{{ $new ?? '—' }}</span>
                    </div>

                    @endif

                    @endforeach

                    {{-- FILES --}}
                    @if($log->files->count())
                    <div class="small mt-2">
                        <strong>Files:</strong><br>
                        @foreach($log->files as $file)
                        <a href="{{ asset('storage/' . $file->file_path) }}"
                            target="_blank"
                            class="d-block text-decoration-none">
                            <i class="bi bi-paperclip me-1"></i>
                            {{ $file->original_name }}
                        </a>
                        @endforeach
                    </div>
                    @endif

                </div>

            </div>
        </div>

        @empty
        <div class="text-muted">No activity recorded.</div>
        @endforelse

        <div class="mt-3">
            {{ $activityLogs->withQueryString()->links() }}
        </div>

    </div>

</x-page-wrapper>
@endsection