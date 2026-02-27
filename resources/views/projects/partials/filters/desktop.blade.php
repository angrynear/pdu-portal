<div class="d-none d-lg-flex justify-content-end">

    <div class="d-flex align-items-center flex-wrap gap-3">

        {{-- SEARCH --}}
        <div class="position-relative search-wrapper">
            <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 small text-muted"></i>

            <input type="text"
                name="search"
                value="{{ $search }}"
                data-project-search
                class="form-control form-control-sm ps-5 pe-5 shadow-sm"
                placeholder="Search project..."
                autocomplete="off">

            <button type="button"
                class="btn btn-sm position-absolute top-50 end-0 translate-middle-y me-2 d-none border-0 bg-transparent text-muted search-clear-btn">
                <i class="bi bi-x-lg small"></i>
            </button>
        </div>

        {{-- SUB-SECTOR --}}
        <select name="sub_sector"
            class="form-select form-select-sm shadow-sm w-auto">

            <option value=""
                {{ request('sub_sector') ? '' : 'selected' }}>
                All Sub-Sectors ({{ $statusCounts['all'] ?? 0 }})
            </option>

            @foreach($subSectors as $value => $label)
            @php $count = $subSectorCounts[$value] ?? 0; @endphp

            @if($count > 0)
            <option value="{{ $value }}"
                {{ request('sub_sector') === $value ? 'selected' : '' }}>
                {{ $label }} ({{ $count }})
            </option>
            @endif
            @endforeach
        </select>


        {{-- STATUS --}}
        <select name="filter"
            class="form-select form-select-sm shadow-sm w-auto">

            <option value="all"
                {{ $status === 'all' ? 'selected' : '' }}>
                All Status ({{ $statusCounts['all'] ?? 0 }})
            </option>

            @foreach($statusChips as $key => $label)
            @if($key === 'all') @continue @endif

            @php $count = $statusCounts[$key] ?? 0; @endphp

            @if($count > 0)
            <option value="{{ $key }}"
                {{ $status === $key ? 'selected' : '' }}>
                {{ $label }} ({{ $count }})
            </option>
            @endif
            @endforeach
        </select>

        {{-- RESET --}}
        <a href="{{ route('projects.index', ['scope' => $scope]) }}"
            class="btn btn-sm btn-outline-secondary">
            Reset
        </a>

        {{-- SCOPE --}}
        @if(auth()->user()->isAdmin())
        <div class="btn-group scope-toggle">
            <a href="{{ route('projects.index', ['scope' => 'all']) }}"
                class="btn btn-sm {{ $scope === 'all' ? 'btn-dark active-scope' : 'btn-outline-secondary' }}">
                All Projects
            </a>

            <a href="{{ route('projects.index', ['scope' => 'my']) }}"
                class="btn btn-sm {{ $scope === 'my' ? 'btn-dark active-scope' : 'btn-outline-secondary' }}">
                My Projects
            </a>
        </div>
        @endif

    </div>

</div>