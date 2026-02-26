@extends('layouts.app')

@section('content')

@php
$isAdmin = auth()->user()->isAdmin();
$scope = request('scope', 'all');

$pageTitle = $isAdmin
? 'Manage Projects'
: 'My Projects';
@endphp

@section('title', $pageTitle)

<x-page-wrapper :title="$pageTitle">

    <x-slot name="actions">

        @php
        $status = request('filter', 'all');
        $search = request('search');
        $scope = request('scope', 'all');

        $statusChips = [
        'all' => 'All Status',
        'ongoing' => 'Ongoing',
        'completed' => 'Completed',
        'overdue' => 'Overdue',
        ];
        @endphp

        <form method="GET"
            action="{{ route('projects.index') }}"
            class="w-100">

            <input type="hidden" name="scope" value="{{ $scope }}">

            {{-- ================= DESKTOP ================= --}}
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
                        class="form-select form-select-sm shadow-sm w-auto"
                        onchange="this.form.submit()">

                        <option value="">All Sub-Sectors</option>

                        @foreach($subSectors as $value => $label)

                        @php
                        $count = $subSectorCounts[$value] ?? 0;
                        @endphp

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
                        class="form-select form-select-sm shadow-sm w-auto"
                        onchange="this.form.submit()">

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

            {{-- ================= MOBILE ================= --}}
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

                    {{-- Filter Toggle Button --}}
                    <button class="btn btn-outline-secondary w-100 mb-2"
                        type="button"
                        data-bs-toggle="collapse"
                        data-bs-target="#mobileProjectFilters">
                        <i class="bi bi-funnel me-1"></i>
                        Filters
                    </button>

                    {{-- Collapsible Filter Panel --}}
                    <div class="collapse {{ ($search || request('sub_sector') || $status !== 'all') ? 'show' : '' }}"
                        id="mobileProjectFilters">

                        <div class="card card-body shadow-sm border-0">

                            {{-- Sub-Sector --}}
                            <div class="mb-3">
                                <label class="form-label small text-muted">Sub-Sector</label>
                                <select name="sub_sector" class="form-select form-select-sm">
                                    <option value="">All Sub-Sectors</option>

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

        </form>

    </x-slot>

    {{-- ================= PROJECT CARDS ================= --}}
    <div id="projectListWrapper">
        @include('projects.partials.project-list')
    </div>

    {{-- ADD --}}
    @if(auth()->user()->isAdmin())
    <a href="{{ route('projects.create', ['scope' => $scope]) }}"
        class="btn btn-success rounded-circle shadow mobile-fab">
        <i class="bi bi-plus-lg"></i>
    </a>
    @endif

    @push('scripts')

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const searchInputs = document.querySelectorAll('[data-project-search]');
            const clearButtons = document.querySelectorAll('.search-clear-btn');
            const wrapper = document.getElementById('projectListWrapper');
            const statusSelect = document.querySelector('select[name="filter"]');

            let debounceTimer;

            function fetchProjects(searchValue = '') {

                const filter = statusSelect ? statusSelect.value : 'all';
                const scope = "{{ request('scope','all') }}";

                const params = new URLSearchParams({
                    search: searchValue,
                    filter: filter,
                    scope: scope
                });

                fetch(`{{ route('projects.index') }}?${params}`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.text())
                    .then(html => {
                        if (wrapper) wrapper.innerHTML = html;
                    });
            }

            function toggleClearButton(input, button) {
                if (input.value.length > 0) {
                    button.classList.remove('d-none');
                } else {
                    button.classList.add('d-none');
                }
            }

            // SEARCH INPUT LOGIC
            searchInputs.forEach((input, index) => {

                const clearBtn = clearButtons[index];

                // Toggle X on load
                toggleClearButton(input, clearBtn);

                input.addEventListener('input', function() {

                    const value = this.value;

                    toggleClearButton(this, clearBtn);

                    clearTimeout(debounceTimer);

                    debounceTimer = setTimeout(() => {
                        fetchProjects(value);
                    }, 300);

                });

                // CLEAR BUTTON CLICK
                clearBtn.addEventListener('click', function() {
                    input.value = '';
                    toggleClearButton(input, clearBtn);
                    fetchProjects('');
                });

            });

            // STATUS FILTER
            if (statusSelect) {
                statusSelect.addEventListener('change', function() {
                    fetchProjects();
                });
            }

        });
    </script>

    @endpush

</x-page-wrapper>
@endsection