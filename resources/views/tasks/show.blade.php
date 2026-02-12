@extends('layouts.app')

@section('title', 'Task Details')

@section('content')
<x-page-wrapper title="Task Details">

    <x-slot name="actions">

        @if($from === 'project')
        <a href="{{ route('projects.show', $task->project->id) }}"
            class="btn btn-secondary btn-sm">
            ← Back to Project
        </a>
        @elseif($from === 'tasks')
        <a href="{{ route('tasks.index') }}"
            class="btn btn-secondary btn-sm">
            ← Back to Task List
        </a>
        @else
        <a href="{{ url()->previous() }}"
            class="btn btn-secondary btn-sm">
            ← Back
        </a>
        @endif

    </x-slot>

    <div class="card mb-4">
        <div class="card-body">

            <h5 class="fw-bold">{{ $task->task_type }}</h5>

            <div class="mb-1">
                <strong>Project:</strong>
                <a href="{{ route('projects.show', $task->project->id) }}"
                    class="text-decoration-none text-dark fw-semibold link-hover">
                    {{ $task->project->name ?? '-' }}
                </a>
            </div>

            <div class="mb-1">
                <strong>Assigned To:</strong>
                {{ $task->assignedUser->name }}
            </div>

            <div class="mb-1">
                <strong>Start Date:</strong>
                {{ optional($task->start_date)->format('F d, Y') }}
            </div>

            <div class="mb-1">
                <strong>Due Date:</strong>
                {{ optional($task->due_date)->format('F d, Y') }}
            </div>

            <div class="mb-0">
                <strong>Progress:</strong> {{ $task->progress }}%
            </div>

        </div>
    </div>

    <h6 class="text-uppercase text-muted mb-3">Update History</h6>

    @foreach($remarks as $remark)
    <div class="card mb-3">
        <div class="card-body">

            <div class="d-flex justify-content-between">
                <strong>{{ $remark->user->name }}</strong>
                <small class="text-muted">
                    {{ $remark->created_at->diffForHumans() }}
                </small>
            </div>

            {{-- Progress --}}
            <div class="mt-2 mb-1">
                @if(!is_null($remark->progress))
                <div>
                    <strong>Progress:</strong> {{ $remark->progress }}%
                </div>
                @endif
            </div>

            {{-- Remarks --}}
            @if(filled($remark->remark))
            <div class="mb-1">
                <strong>Remarks: </strong>
                {{ $remark->remark }}
            </div>
            @endif

            {{-- Files --}}
            @if($remark->files->count())
            <div class="mb-0">
                <strong>Files: </strong>

                @foreach($remark->files as $file)
                <a href="{{ asset('storage/' . $file->file_path) }}"
                    target="_blank">
                    {{ $file->original_name }}
                </a><br>
                @endforeach
            </div>
            @endif

        </div>
    </div>
    @endforeach

    <div class="mt-3">
        {{ $remarks->withQueryString()->links() }}
    </div>

</x-page-wrapper>
@endsection