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

        $statusLabels = [
        'all' => 'All Status',
        'completed' => 'Completed',
        'ongoing' => 'Ongoing',
        'overdue' => 'Overdue',
        'not_started' => 'Not Started',
        ];
        @endphp

        <form method="GET"
            action="{{ route('projects.index') }}"
            class="w-100">

            <input type="hidden" name="scope" value="{{ $scope }}">

            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">




                {{-- LEFT SIDE: Filters --}}
                <div class="d-flex align-items-center gap-2 flex-wrap ms-auto">

                    {{-- SEARCH --}}
                    <div class="position-relative search-wrapper">

                        <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 small text-muted"></i>

                        <input type="text"
                            name="search"
                            id="projectSearch"
                            value="{{ $search }}"
                            class="form-control form-control-sm ps-5 pe-5 shadow-sm"
                            placeholder="Search project..."
                            autocomplete="off">

                        <button type="button"
                            id="clearSearch"
                            class="btn btn-sm position-absolute top-50 end-0 translate-middle-y me-2 d-none border-0 bg-transparent text-muted">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>

                    {{-- STATUS --}}
                    <select name="filter"
                        class="form-select form-select-sm shadow-sm w-auto"
                        onchange="this.form.submit()">

                        @foreach($statusLabels as $key => $label)
                        @php
                        $count = $statusCounts[$key] ?? 0;
                        @endphp

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
                        class="btn btn-sm btn-outline-secondary px-3">
                        Reset
                    </a>

                    {{-- ADD --}}
                    @if(auth()->user()->isAdmin())
                    <a href="{{ route('projects.create', ['scope' => request('scope')]) }}"
                        class="btn btn-sm btn-success px-3 shadow-sm">
                        <i class="bi bi-plus-lg"></i>
                    </a>
                    @endif

                    {{-- RIGHT SIDE: Scope Toggle --}}
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

        </form>

    </x-slot>

    {{-- ================= PROJECT CARDS ================= --}}
    <div id="projectListWrapper">
        @include('projects.partials.project-list')
    </div>

    @push('scripts')

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const searchInput = document.getElementById('projectSearch');
            const clearBtn = document.getElementById('clearSearch');
            const wrapper = document.getElementById('projectListWrapper');
            const statusSelect = document.querySelector('select[name="filter"]');

            let debounceTimer;

            function fetchProjects() {

                const search = searchInput ? searchInput.value : '';
                const filter = statusSelect ? statusSelect.value : 'all';

                const scope = "{{ request('scope','all') }}";

                const params = new URLSearchParams({
                    search: search,
                    filter: filter,
                    scope: scope
                });

                fetch(`{{ route(request()->routeIs('projects.my') ? 'projects.my' : 'projects.index') }}?${params}`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.text())
                    .then(html => {
                        if (wrapper) wrapper.innerHTML = html;
                    });
            }

            function toggleClearButton() {
                if (!searchInput || !clearBtn) return;

                if (searchInput.value.length > 0) {
                    clearBtn.classList.remove('d-none');
                } else {
                    clearBtn.classList.add('d-none');
                }
            }

            // ======================
            // LIVE SEARCH (Debounced)
            // ======================
            if (searchInput) {
                searchInput.addEventListener('input', function() {

                    toggleClearButton();

                    clearTimeout(debounceTimer);

                    debounceTimer = setTimeout(() => {
                        fetchProjects();
                    }, 300);
                });

                // Show X on page load if needed
                toggleClearButton();
            }

            // ======================
            // STATUS CHANGE
            // ======================
            if (statusSelect) {
                statusSelect.addEventListener('change', function() {
                    fetchProjects();
                });
            }

            // ======================
            // CLEAR BUTTON
            // ======================
            if (clearBtn) {
                clearBtn.addEventListener('click', function() {
                    searchInput.value = '';
                    toggleClearButton();
                    fetchProjects();
                });
            }

        });
    </script>

    @endpush

</x-page-wrapper>
@endsection