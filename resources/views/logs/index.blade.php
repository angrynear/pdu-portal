@extends('layouts.app')

@section('title', 'Activity Logs')

@section('content')

<x-page-wrapper title="Activity Logs">

    <x-slot name="actions">

        {{-- ================= DESKTOP ================= --}}
        <div class="d-none d-lg-flex justify-content-end">

            <div class="d-flex align-items-center flex-wrap gap-3">

                {{-- ================= FILTERS ================= --}}
                @if($scope === 'projects')
                <form method="GET"
                    action="{{ route('logs.index') }}"
                    class="d-flex flex-wrap align-items-center gap-2">

                    <input type="hidden" name="scope" value="projects">

                    {{-- 🔍 SEARCH --}}
                    <div class="position-relative search-wrapper">
                        <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 small text-muted"></i>

                        <input type="text"
                            name="search"
                            value="{{ request('search') }}"
                            class="form-control form-control-sm ps-5 pe-5 shadow-sm"
                            placeholder="Search project..."
                            autocomplete="off">
                    </div>

                    {{-- USER --}}
                    <select name="user"
                        class="form-select form-select-sm shadow-sm w-auto"
                        onchange="this.form.submit()">
                        <option value="">All Users</option>
                        @foreach($users as $user)
                        <option value="{{ $user->id }}"
                            {{ request('user') == $user->id ? 'selected' : '' }}>
                            {{ $user->name }}
                        </option>
                        @endforeach
                    </select>

                    {{-- ACTION --}}
                    <select name="action"
                        class="form-select form-select-sm shadow-sm w-auto"
                        onchange="this.form.submit()">
                        <option value="">All Actions</option>
                        @foreach(['created','updated','archived','restored'] as $action)
                        <option value="{{ $action }}"
                            {{ request('action') == $action ? 'selected' : '' }}>
                            {{ ucfirst($action) }}
                        </option>
                        @endforeach
                    </select>

                    {{-- DATE RANGE --}}
                    <input type="date"
                        name="date_from"
                        value="{{ request('date_from') }}"
                        class="form-control form-control-sm shadow-sm w-auto"
                        onchange="this.form.submit()">

                    <input type="date"
                        name="date_to"
                        value="{{ request('date_to') }}"
                        class="form-control form-control-sm shadow-sm w-auto"
                        onchange="this.form.submit()">

                    <a href="{{ route('logs.index', ['scope' => 'projects']) }}"
                        class="btn btn-sm btn-outline-secondary">
                        Reset
                    </a>

                </form>
                @endif


                @if($scope === 'tasks')
                <form method="GET"
                    action="{{ route('logs.index') }}"
                    class="d-flex flex-wrap align-items-center gap-2">

                    <input type="hidden" name="scope" value="tasks">

                    <select name="user"
                        class="form-select form-select-sm shadow-sm w-auto"
                        onchange="this.form.submit()">
                        <option value="">All Users</option>
                        @foreach($users as $user)
                        <option value="{{ $user->id }}"
                            {{ request('user') == $user->id ? 'selected' : '' }}>
                            {{ $user->name }}
                        </option>
                        @endforeach
                    </select>

                    <select name="action"
                        class="form-select form-select-sm shadow-sm w-auto"
                        onchange="this.form.submit()">
                        <option value="">All Actions</option>
                        @foreach(['created','updated','archived','restored'] as $action)
                        <option value="{{ $action }}"
                            {{ request('action') == $action ? 'selected' : '' }}>
                            {{ ucfirst($action) }}
                        </option>
                        @endforeach
                    </select>

                    <select name="task_type"
                        class="form-select form-select-sm shadow-sm w-auto"
                        onchange="this.form.submit()">
                        <option value="">All Task Types</option>
                        @foreach($taskTypes as $type)
                        <option value="{{ $type }}"
                            {{ request('task_type') == $type ? 'selected' : '' }}>
                            {{ $type }}
                        </option>
                        @endforeach
                    </select>

                    <input type="date"
                        name="date_from"
                        value="{{ request('date_from') }}"
                        class="form-control form-control-sm shadow-sm w-auto"
                        onchange="this.form.submit()">

                    <input type="date"
                        name="date_to"
                        value="{{ request('date_to') }}"
                        class="form-control form-control-sm shadow-sm w-auto"
                        onchange="this.form.submit()">

                    <a href="{{ route('logs.index', ['scope' => 'tasks']) }}"
                        class="btn btn-sm btn-outline-secondary">
                        Reset
                    </a>

                </form>
                @endif


                {{-- ================= SCOPE PILLS ================= --}}
                <div class="btn-group scope-toggle">
                    @foreach ([
                    'projects' => 'Projects',
                    'tasks' => 'Tasks'
                    ] as $key => $label)

                    <a href="{{ route('logs.index', ['scope' => $key]) }}"
                        class="btn btn-sm
                    {{ $scope === $key ? 'btn-dark active-scope' : 'btn-outline-secondary' }}">
                        {{ $label }}
                    </a>

                    @endforeach
                </div>

            </div>

        </div>

        {{-- ================= MOBILE ================= --}}
        <div class="d-lg-none w-100 d-flex justify-content-center">
            <div class="w-100" style="max-width: 420px;">

                {{-- Scope --}}
                <div class="btn-group w-100 overflow-hidden shadow-sm mb-3">
                    @foreach ([
                    'projects' => 'Projects',
                    'tasks' => 'Tasks'
                    ] as $key => $label)

                    <a href="{{ route('logs.index', ['scope' => $key]) }}"
                        class="btn flex-fill border-0
                {{ $scope === $key ? 'btn-dark text-white' : 'btn-light text-muted' }}">
                        {{ $label }}
                    </a>

                    @endforeach
                </div>

                {{-- SEARCH --}}
                <div class="mb-3 position-relative">
                    <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 small text-muted"></i>

                    <input type="text"
                        name="search"
                        value="{{ request('search') }}"
                        class="form-control ps-5 shadow-sm"
                        placeholder="Search project...">
                </div>

                {{-- Filter Toggle --}}
                <button class="btn btn-outline-secondary w-100 mb-2"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#mobileLogFilters">
                    <i class="bi bi-funnel me-1"></i>
                    Filters
                </button>

                {{-- Collapsible Panel --}}
                <div class="collapse" id="mobileLogFilters">
                    <div class="card card-body shadow-sm border-0">

                        <form method="GET" action="{{ route('logs.index') }}">

                            <input type="hidden" name="scope" value="{{ $scope }}">

                            {{-- ================= PROJECT FILTERS ================= --}}
                            @if($scope === 'projects')

                            {{-- USER --}}
                            <div class="mb-3">
                                <label class="form-label small text-muted">User</label>
                                <select name="user" class="form-select shadow-sm">
                                    <option value="">All Users</option>
                                    @foreach($users as $user)
                                    <option value="{{ $user->id }}"
                                        {{ request('user') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- ACTION --}}
                            <div class="mb-3">
                                <label class="form-label small text-muted">Action</label>
                                <select name="action" class="form-select shadow-sm">
                                    <option value="">All Actions</option>
                                    @foreach(['created','updated','archived','restored'] as $action)
                                    <option value="{{ $action }}"
                                        {{ request('action') == $action ? 'selected' : '' }}>
                                        {{ ucfirst($action) }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            @endif


                            {{-- ================= TASK FILTERS ================= --}}
                            @if($scope === 'tasks')

                            {{-- USER --}}
                            <div class="mb-3">
                                <label class="form-label small text-muted">User</label>
                                <select name="user" class="form-select shadow-sm">
                                    <option value="">All Users</option>
                                    @foreach($users as $user)
                                    <option value="{{ $user->id }}"
                                        {{ request('user') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- ACTION --}}
                            <div class="mb-3">
                                <label class="form-label small text-muted">Action</label>
                                <select name="action" class="form-select shadow-sm">
                                    <option value="">All Actions</option>
                                    @foreach(['created','updated','archived','restored'] as $action)
                                    <option value="{{ $action }}"
                                        {{ request('action') == $action ? 'selected' : '' }}>
                                        {{ ucfirst($action) }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- TASK TYPE --}}
                            <div class="mb-3">
                                <label class="form-label small text-muted">Task Type</label>
                                <select name="task_type" class="form-select shadow-sm">
                                    <option value="">All Task Types</option>
                                    @foreach($taskTypes as $type)
                                    <option value="{{ $type }}"
                                        {{ request('task_type') == $type ? 'selected' : '' }}>
                                        {{ $type }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            @endif


                            {{-- DATE RANGE --}}
                            <div class="mb-3">
                                <label class="form-label small text-muted">Date From</label>
                                <input type="date"
                                    name="date_from"
                                    value="{{ request('date_from') }}"
                                    class="form-control shadow-sm">
                            </div>

                            <div class="mb-3">
                                <label class="form-label small text-muted">Date To</label>
                                <input type="date"
                                    name="date_to"
                                    value="{{ request('date_to') }}"
                                    class="form-control shadow-sm">
                            </div>


                            {{-- APPLY + RESET --}}
                            <div class="d-flex gap-2">
                                <button type="submit"
                                    class="btn btn-dark btn-sm flex-fill">
                                    Apply
                                </button>

                                <a href="{{ route('logs.index', ['scope' => $scope]) }}"
                                    class="btn btn-outline-secondary btn-sm flex-fill">
                                    Reset
                                </a>
                            </div>

                        </form>

                    </div>
                </div>

            </div>
        </div>

    </x-slot>

    {{-- ================= DYNAMIC PARTIAL RENDER ================= --}}

    @if($scope === 'projects')
    @include('logs.partials.project-cards', ['data' => $data])
    @elseif($scope === 'tasks')
    @include('logs.partials.task-cards', ['data' => $data])
    @endif

</x-page-wrapper>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {

        const searchInput = document.getElementById('projectLogSearch');
        const clearBtn = document.getElementById('clearProjectLogSearch');

        if (!searchInput) return;

        let debounceTimer;

        function toggleClearButton() {
            if (searchInput.value.length > 0) {
                clearBtn.classList.remove('d-none');
            } else {
                clearBtn.classList.add('d-none');
            }
        }

        searchInput.addEventListener('input', function() {

            toggleClearButton();

            clearTimeout(debounceTimer);

            debounceTimer = setTimeout(function() {
                searchInput.form.submit();
            }, 500);

        });

        clearBtn.addEventListener('click', function() {
            searchInput.value = '';
            toggleClearButton();
            searchInput.form.submit();
        });

        toggleClearButton();

    });
</script>
@endpush

@endsection