@extends('layouts.app')

@section('title', 'Task Details')

@section('content')
<x-page-wrapper title="Task Details">

    <x-slot name="actions">
        @php
        $from = request('from');

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

    @php
    $progress = $task->progress ?? 0;
    @endphp

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-start mb-3">
        <div>
            <h5 class="mb-1">{{ ucfirst($task->task_type) }}</h5>
            <div class="text-muted small">
                Project: {{ $task->project->name ?? '—' }}
            </div>
        </div>

        {{-- Optional Status Badge (Future Ready) --}}
        @php
        $status = $progress == 100 ? 'Completed' : ($progress > 0 ? 'Ongoing' : 'Not Started');

        $statusClasses = [
        'Not Started' => 'bg-secondary',
        'Ongoing' => 'bg-success',
        'Completed' => 'bg-primary',
        ];
        @endphp

        <span class="badge {{ $statusClasses[$status] ?? 'bg-secondary' }}">
            {{ $status }}
        </span>
    </div>

    <hr>

    {{-- INFO GRID --}}
    <div class="row mb-3">

        <div class="col-md-4 mb-2">
            <div class="fw-semibold">Assigned To</div>
            <div>
                @if($task->assignedUser)
                {{ $task->assignedUser->name }}
                @else
                <span class="text-danger">Deactivated User</span>
                @endif
            </div>
        </div>

        <div class="col-md-4 mb-2">
            <div class="fw-semibold">Start Date</div>
            <div>{{ $task->start_date?->format('F j, Y') ?? '—' }}</div>
        </div>

        <div class="col-md-4 mb-2">
            <div class="fw-semibold">Due Date</div>
            <div>
                <x-due-date
                    :dueDate="$task->due_date"
                    :progress="$task->progress" />
            </div>
        </div>

        <div class="col-md-4 mb-2">
            <div class="fw-semibold mb-1">Progress</div>
            <x-progress-bar :value="$task->progress" />
        </div>

    </div>


    <h6 class="text-muted mb-3">Task Activity</h6>

    @forelse($activityLogs as $log)

    <div class="card mb-3">
        <div class="card-body">

            <div class="d-flex justify-content-between">
                <strong>{{ $log->user->name ?? 'System' }}</strong>
                <small class="text-muted">
                    {{ $log->created_at->diffForHumans() }}
                </small>
            </div>

            {{-- Progress --}}
            @if(isset($log->changes['progress']))

            @php
            $old = $log->changes['progress']['old'] ?? null;
            $new = $log->changes['progress']['new'] ?? null;

            // Determine new progress color
            if ($new == 100) {
            $barColor = 'bg-success';
            $textColor = 'text-success';
            } elseif ($new >= 70) {
            $barColor = 'bg-primary';
            $textColor = 'text-primary';
            } elseif ($new >= 31) {
            $barColor = 'bg-warning';
            $textColor = 'text-warning';
            } else {
            $barColor = 'bg-danger';
            $textColor = 'text-danger';
            }
            @endphp

            <div class="mt-1 small">

                <strong>Progress:</strong>

                @if(!is_null($old))
                <span class="text-danger">
                    {{ $old }}%
                </span>

                <i class="bi bi-arrow-right mx-1 text-muted"></i>
                @endif

                <span class="{{ $textColor }} fw-semibold">
                    {{ $new }}%
                </span>

                <div class="progress mt-1" style="height:6px;">
                    <div class="progress-bar {{ $barColor }}"
                        style="width: {{ $new }}%">
                    </div>
                </div>

            </div>

            @endif

            {{-- Remarks --}}
            @if(isset($log->changes['remark']))
            <div class="mt-1 small">
                <strong>Remarks:</strong>
                {{ $log->changes['remark']['new'] }}
            </div>
            @endif

            {{-- Files --}}
            @if($log->files->count())
            <div class="mt-1 small">
                <strong>Files: </strong>

                @foreach($log->files as $file)
                <a href="{{ asset('storage/' . $file->file_path) }}"
                    target="_blank">
                    {{ $file->original_name }}
                </a><br>
                @endforeach
            </div>
            @endif

            {{-- Other Structural Changes --}}
            @foreach($log->changes ?? [] as $field => $values)

            @if(!in_array($field, ['progress','remark','files']))

            @php
            $old = $values['old'] ?? null;
            $new = $values['new'] ?? null;
            $label = ucwords(str_replace('_', ' ', $field));

            /*
            |--------------------------------------------------------------------------
            | Special Handling
            |--------------------------------------------------------------------------
            */

            // 1️⃣ Date Formatting
            if (in_array($field, ['start_date','due_date'])) {
            $old = $old ? \Carbon\Carbon::parse($old)->format('F d, Y') : '—';
            $new = $new ? \Carbon\Carbon::parse($new)->format('F d, Y') : '—';
            }

            // 2️⃣ Assigned User ID → Convert to Name
            if ($field === 'assigned_user_id') {
            $oldUser = $old ? \App\Models\User::find($old) : null;
            $newUser = $new ? \App\Models\User::find($new) : null;

            $old = $oldUser->name ?? '—';
            $new = $newUser->name ?? '—';

            $label = 'Assigned To';
            }

            @endphp

            <div class="mt-1 small">
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

    <div class="mt-3">
        {{ $activityLogs->withQueryString()->links() }}
    </div>

</x-page-wrapper>
@endsection