{{-- ================= MOBILE FILTERS ================= --}}

<div class="d-lg-none w-100 d-flex justify-content-center">
    <div class="w-100" style="max-width: 420px;">

        {{-- Scope --}}
        <div class="btn-group w-100 overflow-hidden shadow-sm mb-3">
            @foreach (['projects' => 'Projects','tasks' => 'Tasks'] as $key => $label)
            <a href="{{ route('logs.index', ['scope' => $key]) }}"
                class="btn flex-fill border-0
                    {{ $scope === $key ? 'btn-dark text-white' : 'btn-light text-muted' }}">
                {{ $label }}
            </a>
            @endforeach
        </div>

        {{-- Toggle --}}
        <button class="btn btn-outline-secondary w-100 mb-2"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#mobileLogFilters">
            <i class="bi bi-funnel me-1"></i>
            Filters
        </button>

        <div class="collapse" id="mobileLogFilters">
            <div class="card card-body shadow-sm border-0">

                {{-- SEARCH --}}
                <div class="position-relative search-wrapper mb-3">

                    <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 small text-muted"></i>

                    <input type="text"
                        name="search"
                        value="{{ request('search') }}"
                        class="form-control form-control-sm ps-5 pe-5 shadow-sm"
                        placeholder="{{ $scope === 'projects' ? 'Search project...' : 'Search task/project...' }}"
                        autocomplete="off">

                    <button type="button"
                        class="btn btn-sm position-absolute top-50 end-0 translate-middle-y me-2 d-none border-0 bg-transparent text-muted log-search-clear">
                        <i class="bi bi-x-lg small"></i>
                    </button>

                </div>

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
                        @foreach($availableActions as $action)
                        <option value="{{ $action }}"
                            {{ request('action') == $action ? 'selected' : '' }}>
                            {{ ucfirst($action) }}
                        </option>
                        @endforeach
                    </select>
                </div>

                @if($scope === 'tasks')
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

            </div>
        </div>

    </div>
</div>