@extends('layouts.app')

@section('title', 'Activity Logs')

@section('content')

<x-page-wrapper title="Activity Logs">

    <x-slot name="actions">

        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 w-100">


            {{-- ========================= --}}
            {{-- LEFT: FILTERS (ONE LINE) --}}
            {{-- ========================= --}}
            @if($scope === 'projects')

            <form method="GET"
                action="{{ route('logs.index') }}"
                class="d-flex flex-wrap align-items-center gap-2 ms-auto">

                <input type="hidden" name="scope" value="projects">

                {{-- 🔍 SEARCH --}}
                <div class="position-relative search-wrapper">

                    <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 small text-muted"></i>

                    <input type="text"
                        name="search"
                        id="projectLogSearch"
                        value="{{ request('search') }}"
                        class="form-control form-control-sm ps-5 pe-5 shadow-sm"
                        placeholder="Search project..."
                        autocomplete="off">

                    <button type="button"
                        id="clearProjectLogSearch"
                        class="btn btn-sm position-absolute top-50 end-0 translate-middle-y me-2 d-none border-0 bg-transparent text-muted">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>

                {{-- 👤 USER FILTER --}}
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

                {{-- 🏷 ACTION FILTER --}}
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

                {{-- 📅 DATE FROM --}}
                <input type="date"
                    name="date_from"
                    value="{{ request('date_from') }}"
                    class="form-control form-control-sm shadow-sm w-auto"
                    onchange="this.form.submit()">

                {{-- 📅 DATE TO --}}
                <input type="date"
                    name="date_to"
                    value="{{ request('date_to') }}"
                    class="form-control form-control-sm shadow-sm w-auto"
                    onchange="this.form.submit()">

                {{-- 🔄 RESET --}}
                <a href="{{ route('logs.index', ['scope' => 'projects']) }}"
                    class="btn btn-sm btn-outline-secondary px-3">
                    Reset
                </a>

            </form>
            @endif

            @if($scope === 'tasks')

            <form method="GET"
                action="{{ route('logs.index') }}"
                class="d-flex align-items-center flex-wrap gap-2">

                <input type="hidden" name="scope" value="tasks">

                {{-- 👤 USER FILTER --}}
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

                {{-- 🏷 ACTION FILTER --}}
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

                {{-- 📝 TASK TYPE FILTER --}}
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

                {{-- 📅 DATE FROM --}}
                <input type="date"
                    name="date_from"
                    value="{{ request('date_from') }}"
                    class="form-control form-control-sm shadow-sm w-auto"
                    onchange="this.form.submit()">

                {{-- 📅 DATE TO --}}
                <input type="date"
                    name="date_to"
                    value="{{ request('date_to') }}"
                    class="form-control form-control-sm shadow-sm w-auto"
                    onchange="this.form.submit()">

                {{-- 🔄 RESET --}}
                <a href="{{ route('logs.index', ['scope' => 'tasks']) }}"
                    class="btn btn-sm btn-outline-secondary px-3">
                    Reset
                </a>

            </form>

            @endif

            {{-- ========================= --}}
            {{-- RIGHT: SCOPE PILLS --}}
            {{-- ========================= --}}
            <div class="d-none d-md-block">
                <div class="btn-group scope-toggle">

                    @foreach ([
                    'projects' => 'Projects',
                    'tasks' => 'Tasks'
                    ] as $key => $label)

                    <a href="{{ route('logs.index', ['scope' => $key]) }}"
                        class="btn btn-sm {{ $scope === $key ? 'btn-dark active-scope' : 'btn-outline-secondary' }}">
                        {{ $label }}
                    </a>

                    @endforeach

                </div>
            </div>

            {{-- MOBILE DROPDOWN --}}
            <div class="d-md-none">
                <select class="form-select form-select-sm shadow-sm"
                    onchange="if(this.value) window.location.href=this.value">

                    @foreach ([
                    'projects' => 'Projects',
                    'tasks' => 'Tasks'
                    ] as $key => $label)

                    <option value="{{ route('logs.index', ['scope' => $key]) }}"
                        {{ $scope === $key ? 'selected' : '' }}>
                        {{ $label }}
                    </option>

                    @endforeach
                </select>
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