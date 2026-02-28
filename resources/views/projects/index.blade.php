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
        'not_started' => 'Not Started',
        ];
        @endphp

        <form method="GET"
            action="{{ route('projects.index') }}"
            class="w-100">

            <input type="hidden" name="scope" value="{{ $scope }}">

            {{-- ================= DESKTOP ================= --}}
            <div id="desktopFiltersWrapper">
                @include('projects.partials.filters.desktop')
            </div>

        </form>

        <form method="GET"
            action="{{ route('projects.index') }}"
            class="w-100"
            id="mobileFilterForm">

            <input type="hidden" name="scope" value="{{ $scope }}">

            {{-- ================= MOBILE ================= --}}
            <div id="mobileFiltersWrapper">
                @include('projects.partials.filters.mobile')
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

            let debounceTimer;

            function fetchProjects() {

                const desktopWrapper = document.getElementById('desktopFiltersWrapper');
                if (!desktopWrapper) return;

                const searchInput = desktopWrapper.querySelector('[data-project-search]');
                const subSectorSelect = desktopWrapper.querySelector('select[name="sub_sector"]');
                const statusSelect = desktopWrapper.querySelector('select[name="filter"]');

                const params = new URLSearchParams({
                    search: searchInput ? searchInput.value : '',
                    sub_sector: subSectorSelect ? subSectorSelect.value : '',
                    filter: statusSelect ? statusSelect.value : 'all',
                    scope: "{{ request('scope','all') }}"
                });

                fetch(`{{ route('projects.index') }}?${params}`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {

                        document.getElementById('desktopFiltersWrapper').innerHTML = data.desktopFilters;
                        document.getElementById('mobileFiltersWrapper').innerHTML = data.mobileFilters;
                        document.getElementById('projectListWrapper').innerHTML = data.projects;

                        attachListeners(); // re-bind after DOM replace
                    });
            }

            function attachListeners() {

                const desktopWrapper = document.getElementById('desktopFiltersWrapper');
                if (!desktopWrapper) return;

                const searchInput = desktopWrapper.querySelector('[data-project-search]');
                const clearBtn = desktopWrapper.querySelector('.search-clear-btn');
                const subSectorSelect = desktopWrapper.querySelector('select[name="sub_sector"]');
                const statusSelect = desktopWrapper.querySelector('select[name="filter"]');

                // SEARCH
                if (searchInput) {

                    toggleClear(searchInput, clearBtn);

                    searchInput.addEventListener('input', function() {

                        toggleClear(searchInput, clearBtn);

                        clearTimeout(debounceTimer);
                        debounceTimer = setTimeout(fetchProjects, 300);
                    });
                }

                if (clearBtn) {
                    clearBtn.addEventListener('click', function() {
                        searchInput.value = '';
                        toggleClear(searchInput, clearBtn);
                        fetchProjects();
                    });
                }

                // SUB-SECTOR
                if (subSectorSelect) {
                    subSectorSelect.addEventListener('change', fetchProjects);
                }

                // STATUS
                if (statusSelect) {
                    statusSelect.addEventListener('change', fetchProjects);
                }
            }

            function toggleClear(input, button) {
                if (!input || !button) return;

                if (input.value.length > 0) {
                    button.classList.remove('d-none');
                } else {
                    button.classList.add('d-none');
                }
            }

            attachListeners();

        });
    </script>
    @endpush

</x-page-wrapper>
@endsection