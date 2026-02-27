@extends('layouts.app')

@section('title', 'Activity Logs')

@section('content')

<x-page-wrapper title="Activity Logs">

    <x-slot name="actions">
        {{-- ================= DESKTOP ================= --}}
        <form method="GET" action="{{ route('logs.index') }}">
            <input type="hidden" name="scope" value="{{ $scope }}">
            <div id="desktopLogFiltersWrapper">
                @include('logs.partials.filters.desktop')
            </div>
        </form>

        {{-- ================= MOBILE ================= --}}
        <form method="GET" action="{{ route('logs.index') }}">
            <input type="hidden" name="scope" value="{{ $scope }}">
            <div id="mobileLogFiltersWrapper">
                @include('logs.partials.filters.mobile')
            </div>
        </form>
    </x-slot>

    {{-- ================= DYNAMIC PARTIAL RENDER ================= --}}

    <div id="logCardsWrapper">
        @if($scope === 'projects')
        @include('logs.partials.project-cards', ['data' => $data])
        @else
        @include('logs.partials.task-cards', ['data' => $data])
        @endif
    </div>

</x-page-wrapper>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {

        let debounceTimer;

        function fetchLogs(form) {

            const params = new URLSearchParams(new FormData(form));

            fetch(`{{ route('logs.index') }}?${params}`, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {

                    if (data.desktopFilters)
                        document.getElementById('desktopLogFiltersWrapper').innerHTML = data.desktopFilters;

                    if (data.mobileFilters)
                        document.getElementById('mobileLogFiltersWrapper').innerHTML = data.mobileFilters;

                    if (data.cards)
                        document.getElementById('logCardsWrapper').innerHTML = data.cards;

                    attachDesktopListeners();
                    attachSearchListeners();
                });
        }

        function attachDesktopListeners() {

            const desktopWrapper = document.getElementById('desktopLogFiltersWrapper');
            if (!desktopWrapper) return;

            const form = desktopWrapper.closest('form');

            desktopWrapper.querySelectorAll('select, input[type="date"]')
                .forEach(el => {
                    el.addEventListener('change', () => fetchLogs(form));
                });
        }

        function attachSearchListeners() {

            const desktopWrapper = document.getElementById('desktopLogFiltersWrapper');
            if (!desktopWrapper) return;

            const form = desktopWrapper.closest('form');
            const searchInput = desktopWrapper.querySelector('input[name="search"]');
            const clearBtn = desktopWrapper.querySelector('.log-search-clear');

            if (!searchInput) return;

            function toggleClear() {
                if (searchInput.value.length > 0) {
                    clearBtn.classList.remove('d-none');
                } else {
                    clearBtn.classList.add('d-none');
                }
            }

            toggleClear();

            searchInput.addEventListener('input', function() {

                toggleClear();

                clearTimeout(debounceTimer);

                debounceTimer = setTimeout(() => {
                    fetchLogs(form);
                }, 400);

            });

            if (clearBtn) {
                clearBtn.addEventListener('click', function() {
                    searchInput.value = '';
                    toggleClear();
                    fetchLogs(form);
                });
            }
        }
        // Mobile submit
        const mobileWrapper = document.getElementById('mobileLogFiltersWrapper');
        if (mobileWrapper) {

            const mobileForm = mobileWrapper.closest('form');

            mobileForm.addEventListener('submit', function(e) {
                e.preventDefault();
                fetchLogs(mobileForm);
            });
        }

        attachDesktopListeners();
        attachSearchListeners();

    });
</script>
@endpush

@endsection