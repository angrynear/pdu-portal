@extends('layouts.app')

@section('content')

@php
$isAdmin = auth()->user()->isAdmin();
$isMyPage = request()->routeIs('tasks.my');

$pageTitle = $isAdmin
? ($isMyPage ? 'My Tasks' : 'Manage Tasks')
: 'My Tasks';
@endphp

@section('title', $pageTitle)

<x-page-wrapper :title="$pageTitle">

    <x-slot name="actions">

        @php
        $status = request('filter', 'all');
        $type = request('type');
        $personnel = request('personnel');
        $scope = request('scope', 'all');

        $statusLabels = [
        'all' => 'All Status',
        'not_started' => 'Not Started',
        'ongoing' => 'Ongoing',
        'completed' => 'Completed',
        'overdue' => 'Overdue',
        ];
        @endphp

        <form method="GET"
            action="{{ route('tasks.index') }}"
            class="d-flex flex-column w-100 w-lg-auto">

            <input type="hidden" name="scope" value="{{ $scope }}">

            <div id="desktopFiltersWrapper">
                @include('tasks.partials.filters.desktop')
            </div>

        </form>

        <form method="GET"
            action="{{ route('tasks.index') }}"
            class="d-flex flex-column w-100 w-lg-auto">

            <input type="hidden" name="scope" value="{{ $scope }}">
            <div id="mobileFiltersWrapper">
                @include('tasks.partials.filters.mobile')
            </div>

        </form>

    </x-slot>

    {{-- ================= TASK CARDS ================= --}}
    <div id="taskListWrapper">
        @include('tasks.partials.task-list')
    </div>

    {{-- Floating Add Button --}}
    <button type="button"
        data-bs-toggle="modal"
        data-bs-target="#addPersonalTaskModal"
        class="btn btn-success rounded-circle shadow mobile-fab">
        <i class="bi bi-plus-lg fs-5"></i>
    </button>

    @include('tasks.partials.update-task-modal')
    @include('tasks.partials.assign-task-modal')
    @include('tasks.partials.set-task-date-modal')
    @include('tasks.partials.add-personal-task-modal')

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const mobileWrapper = document.getElementById('mobileFiltersWrapper');
            if (!mobileWrapper) return;

            const form = mobileWrapper.closest('form');

            // 🔥 STOP normal form submission
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                fetchTasks();
            });

            function fetchTasks() {

                const personnel = mobileWrapper.querySelector('select[name="personnel"]');
                const status = mobileWrapper.querySelector('select[name="filter"]');
                const type = mobileWrapper.querySelector('select[name="type"]');

                const params = new URLSearchParams({
                    personnel: personnel ? personnel.value : '',
                    filter: status ? status.value : 'all',
                    type: type ? type.value : '',
                    scope: "{{ request('scope','all') }}"
                });

                fetch(`{{ route('tasks.index') }}?${params}`, {
                        method: 'GET',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {

                        document.getElementById('mobileFiltersWrapper').innerHTML = data.mobileFilters;
                        document.getElementById('taskListWrapper').innerHTML = data.tasks;

                    })
                    .catch(err => console.error(err));
            }

        });
    </script>

    {{-- View Remarks Script --}}
    <script>
        function toggleRemark(id) {
            const preview = document.getElementById('preview-' + id);
            const full = document.getElementById('full-' + id);
            const button = document.getElementById('btn-' + id);

            if (full.classList.contains('d-none')) {
                preview.classList.add('d-none');
                full.classList.remove('d-none');
                button.innerText = 'Hide Remarks';
            } else {
                preview.classList.remove('d-none');
                full.classList.add('d-none');
                button.innerText = 'View Full Remarks';
            }
        }
    </script>

    <script>
        function toggleRemarkMobile(id) {
            const preview = document.getElementById('preview-mobile-' + id);
            const full = document.getElementById('full-mobile-' + id);
            const button = document.getElementById('btn-mobile-' + id);

            if (!preview || !full || !button) return;

            if (full.classList.contains('d-none')) {
                preview.classList.add('d-none');
                full.classList.remove('d-none');
                button.innerText = 'Hide';
            } else {
                preview.classList.remove('d-none');
                full.classList.add('d-none');
                button.innerText = 'View Full';
            }
        }
    </script>

    {{-- Task Modals Script --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const updateModal = document.getElementById('updateTaskProgressModal');
            const setDateModal = document.getElementById('setTaskDateModal');
            const assignModal = document.getElementById('assignTaskModal');

            // ================================
            // UPDATE PROGRESS MODAL
            // ================================
            if (updateModal) {
                updateModal.addEventListener('show.bs.modal', function(event) {

                    const button = event.relatedTarget;

                    const taskId = button.getAttribute('data-task-id');
                    const progress = button.getAttribute('data-progress');
                    const startDate = button.getAttribute('data-start-date');
                    const dueDate = button.getAttribute('data-due-date');
                    const projectStart = button.getAttribute('data-project-start');
                    const projectDue = button.getAttribute('data-project-due');

                    document.getElementById('task_id').value = taskId;

                    const slider = document.getElementById('task_progress');
                    const progressText = document.getElementById('progressValue');

                    slider.value = progress;
                    progressText.innerText = progress;

                    slider.oninput = function() {
                        progressText.innerText = this.value;
                    };

                    const startInput = document.getElementById('update_start_date');
                    const dueInput = document.getElementById('update_due_date');

                    startInput.value = startDate ?? '';
                    dueInput.value = dueDate ?? '';

                    // Restrict dates within project schedule
                    startInput.min = projectStart;
                    startInput.max = projectDue;

                    dueInput.min = projectStart;
                    dueInput.max = projectDue;
                });
            }

            // ================================
            // SET DATE MODAL
            // ================================
            if (setDateModal) {
                setDateModal.addEventListener('show.bs.modal', function(event) {

                    const button = event.relatedTarget;

                    const taskId = button.getAttribute('data-task-id');
                    const projectStart = button.getAttribute('data-project-start');
                    const projectDue = button.getAttribute('data-project-due');

                    const startInput = document.getElementById('set_start_date');
                    const dueInput = document.getElementById('set_due_date');

                    document.getElementById('set_date_task_id').value = taskId;

                    startInput.value = '';
                    dueInput.value = '';

                    startInput.min = projectStart;
                    startInput.max = projectDue;

                    dueInput.min = projectStart;
                    dueInput.max = projectDue;
                });
            }

            // ================================
            // ASSIGN TASK MODAL
            // ================================
            if (assignModal) {
                assignModal.addEventListener('show.bs.modal', function(event) {

                    const button = event.relatedTarget;
                    const taskId = button.getAttribute('data-task-id');

                    document.getElementById('assign_task_id').value = taskId;
                });
            }

        });
    </script>

    @endpush

</x-page-wrapper>
@endsection