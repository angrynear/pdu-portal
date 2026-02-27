{{-- ================= DESKTOP FILTERS ================= --}}

<div class="d-none d-lg-flex justify-content-end">
    <div class="d-flex align-items-center flex-wrap gap-3">

        {{-- SEARCH --}}
        <div class="position-relative search-wrapper">
            <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 small text-muted"></i>

            <input type="text"
                name="search"
                value="{{ request('search') }}"
                class="form-control form-control-sm ps-5 pe-5 shadow-sm"
                placeholder="Search project..."
                autocomplete="off">

            <button type="button"
                class="btn btn-sm position-absolute top-50 end-0 translate-middle-y me-2 d-none border-0 bg-transparent text-muted log-search-clear">
                <i class="bi bi-x-lg small"></i>
            </button>
        </div>

        {{-- USER --}}
        <select name="user" class="form-select form-select-sm shadow-sm w-auto">
            <option value="">All Users</option>
            @foreach($users as $user)
            <option value="{{ $user->id }}"
                {{ request('user') == $user->id ? 'selected' : '' }}>
                {{ $user->name }}
            </option>
            @endforeach
        </select>

        {{-- ACTION --}}
        <select name="action" class="form-select form-select-sm shadow-sm w-auto">
            <option value="">All Actions</option>
            @foreach($availableActions as $action)
            <option value="{{ $action }}"
                {{ request('action') == $action ? 'selected' : '' }}>
                {{ ucfirst($action) }}
            </option>
            @endforeach
        </select>

        @if($scope === 'tasks')
        {{-- TASK TYPE --}}
        <select name="task_type" class="form-select form-select-sm shadow-sm w-auto">
            <option value="">All Task Types</option>
            @foreach($taskTypes as $type)
            <option value="{{ $type }}"
                {{ request('task_type') == $type ? 'selected' : '' }}>
                {{ $type }}
            </option>
            @endforeach
        </select>
        @endif

        {{-- DATE RANGE --}}
        <input type="date"
            name="date_from"
            value="{{ request('date_from') }}"
            class="form-control form-control-sm shadow-sm w-auto">

        <input type="date"
            name="date_to"
            value="{{ request('date_to') }}"
            class="form-control form-control-sm shadow-sm w-auto">

        <a href="{{ route('logs.index', ['scope' => $scope]) }}"
            class="btn btn-sm btn-outline-secondary">
            Reset
        </a>

        {{-- SCOPE PILLS --}}
        <div class="btn-group scope-toggle">
            @foreach (['projects' => 'Projects','tasks' => 'Tasks'] as $key => $label)
            <a href="{{ route('logs.index', ['scope' => $key]) }}"
                class="btn btn-sm {{ $scope === $key ? 'btn-dark active-scope' : 'btn-outline-secondary' }}">
                {{ $label }}
            </a>
            @endforeach
        </div>

    </div>
</div>