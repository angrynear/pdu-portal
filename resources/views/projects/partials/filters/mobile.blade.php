<div class="d-lg-none w-100 d-flex justify-content-center">
    <div class="w-100" style="max-width: 420px;">

        {{-- Scope Toggle --}}
        @if(auth()->user()->isAdmin())
        <div class="btn-group w-100 overflow-hidden shadow-sm mb-3">
            <a href="{{ route('projects.index', ['scope' => 'all']) }}"
                class="btn flex-fill border-0 {{ $scope === 'all' ? 'btn-dark text-white' : 'btn-light text-muted' }}">
                All Projects
            </a>

            <a href="{{ route('projects.index', ['scope' => 'my']) }}"
                class="btn flex-fill border-0 {{ $scope === 'my' ? 'btn-dark text-white' : 'btn-light text-muted' }}">
                My Projects
            </a>
        </div>
        @endif

        {{-- Filter Toggle --}}
        <button class="btn btn-outline-secondary w-100 mb-2"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#mobileProjectFilters">
            <i class="bi bi-funnel me-1"></i>
            Filters
        </button>

        {{-- Collapsible Panel --}}
        <div class="collapse {{ ($search || request('sub_sector') || $status !== 'all') ? 'show' : '' }}"
            id="mobileProjectFilters">

            <div class="card card-body shadow-sm border-0">
                
                {{-- Search --}}
                <div class="position-relative search-wrapper mb-3">
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

                {{-- Sub-Sector --}}
                <div class="mb-3">
                    <label class="form-label small text-muted">Sub-Sector</label>
                    <select name="sub_sector" class="form-select form-select-sm">
                        <option value="">
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
                </div>

                {{-- Status --}}
                <div class="mb-3">
                    <label class="form-label small text-muted">Status</label>
                    <select name="filter" class="form-select form-select-sm">
                        @foreach($statusChips as $key => $label)
                        @php $count = $statusCounts[$key] ?? 0; @endphp
                        @if($count > 0)
                        <option value="{{ $key }}"
                            {{ $status === $key ? 'selected' : '' }}>
                            {{ $label }} ({{ $count }})
                        </option>
                        @endif
                        @endforeach
                    </select>
                </div>

                {{-- Apply & Reset --}}
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-dark btn-sm flex-fill">
                        Apply
                    </button>

                    <a href="{{ route('projects.index', ['scope'=>$scope]) }}"
                        class="btn btn-outline-secondary btn-sm flex-fill">
                        Reset
                    </a>
                </div>

            </div>
        </div>

    </div>
</div>