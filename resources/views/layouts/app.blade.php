<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Planning and Design Unit - Portal')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Bootstrap --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">


    {{-- Custom styles (later) --}}
    <style>
        body {
            background-color: #f5f7fa;
        }

        .navbar {
            min-height: 64px;
            z-index: 1030;
        }

        /* Sidebar Humbergur on Mobile*/
        .sidebar {
            width: 260px;
            background-color: #ffffff;
            border-right: 1px solid #dee2e6;
        }

        /* Desktop */
        @media (min-width: 768px) {
            .sidebar {
                position: sticky;
                top: 64px;
                height: calc(100vh - 64px);
            }
        }

        /* Mobile */
        @media (max-width: 767.98px) {
            .sidebar {
                height: 100vh;
            }
        }

        /*---------------*/

        .sidebar .nav-link {
            color: #212529;
            padding: 8px 12px;
            border-radius: 4px;
            font-size: 0.9rem;
        }

        .sidebar .nav-link:hover {
            background-color: #f1f3f5;
        }

        .sidebar .nav-link.active {
            background-color: #e9f5ee;
            color: #198754;
            font-weight: 600;
        }

        .sidebar h6 {
            font-size: 0.7rem;
            letter-spacing: 0.05em;
            margin-top: 20px;
            margin-bottom: 8px;
        }

        /* Collapsed Sidebar */
        body.sidebar-collapsed .sidebar {
            width: 80px !important;
        }

        body.sidebar-collapsed .sidebar .nav-link span,
        body.sidebar-collapsed .sidebar h6 {
            display: none;
        }

        body.sidebar-collapsed .sidebar .nav-link {
            text-align: center;
        }

        body.sidebar-collapsed .sidebar .nav-link i {
            margin-right: 0 !important;
        }

        /*-------------*/

        .content {
            padding: 20px;
        }

        .page-wrapper {
            background-color: #ffffff;
            border-radius: 6px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .page-header {
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }

        .page-title {
            font-size: 1.25rem;
            font-weight: 600;
        }

        .page-subtitle {
            font-size: 0.85rem;
            color: #6c757d;
        }

        .link-hover:hover {
            text-decoration: underline;
        }

        /* ==============================
        Refined Stat Tiles
        ============================== */

        .card {
            border-radius: 14px;
        }

        .card:hover {
            transform: translateY(-2px);
            transition: 0.2s ease;
        }

        .stat-tile {
            border-radius: 14px;
            padding: 22px 24px;
            color: #ffffff;
            box-shadow: 0 8px 18px rgba(0, 0, 0, 0.06);
            transition: all .2s ease;
            position: relative;
            overflow: hidden;
        }

        .stat-tile:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.08);
        }

        /* Softer Gradients */
        .tile-blue {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
        }

        .tile-green {
            background: linear-gradient(135deg, #16a34a, #15803d);
        }

        .tile-orange {
            background: linear-gradient(135deg, #f59e0b, #d97706);
        }

        .tile-red {
            background: linear-gradient(135deg, #dc2626, #b91c1c);
        }

        /* Label */
        .stat-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: .05em;
            opacity: .85;
            font-weight: 600;
        }

        /* Number */
        .stat-number {
            font-size: 2.4rem;
            font-weight: 700;
            margin-top: 8px;
        }

        /* Subtle light overlay */
        .stat-tile::after {
            content: "";
            position: absolute;
            top: -40%;
            right: -30%;
            width: 200px;
            height: 200px;
            background: rgba(255, 255, 255, 0.08);
            border-radius: 50%;
        }

        .dashboard-section {
            margin-bottom: 3rem;
        }
    </style>
</head>

<body>

    {{-- Toast Container --}}
    <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1100">

        @foreach (['success', 'error', 'warning'] as $type)
        @if (session($type))
        <div class="toast align-items-center border-0
                {{ $type === 'success' ? 'text-bg-success' : '' }}
                {{ $type === 'error' ? 'text-bg-danger' : '' }}
                {{ $type === 'warning' ? 'text-bg-warning text-dark' : '' }}"
            role="alert"
            aria-live="assertive"
            aria-atomic="true"
            data-bs-delay="2000">

            <div class="d-flex">
                <div class="toast-body">
                    {{ session($type) }}
                </div>

                <button type="button"
                    class="btn-close {{ $type === 'warning' ? '' : 'btn-close-white' }} me-2 m-auto"
                    data-bs-dismiss="toast">
                </button>
            </div>
        </div>
        @endif
        @endforeach

    </div>

    {{-- Top Navbar --}}
    @include('layouts.navbar')

    <div class="container-fluid">
        <div class="row g-0">

            {{-- Sidebar --}}
            <div class="col-md-auto">
                @include('layouts.sidebar')
            </div>

            {{-- Main Content --}}
            <main class="col content">

                {{-- Page Content --}}
                @yield('content')

            </main>
        </div>
    </div>

    {{-- Confirmation Modal --}}
    <div class="modal fade" id="confirmActionModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="confirmModalTitle"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <p id="confirmModalMessage" class="mb-0"></p>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Cancel
                    </button>

                    <form id="confirmActionForm" method="POST">
                        @csrf
                        <input type="hidden" name="_method" id="confirmActionMethod">
                        <button type="submit" class="btn" id="confirmActionButton"></button>
                    </form>
                </div>

            </div>
        </div>
    </div>

    {{-- Bootstrap JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    {{-- Confirmation Modal Script --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const modal = document.getElementById('confirmActionModal');
            const confirmForm = document.getElementById('confirmActionForm');
            const confirmBtn = document.getElementById('confirmActionButton');

            if (!modal) return;

            modal.addEventListener('show.bs.modal', function(event) {

                const button = event.relatedTarget;

                const action = button.getAttribute('data-action');
                const method = button.getAttribute('data-method');
                const title = button.getAttribute('data-title');
                const message = button.getAttribute('data-message');
                const confirmText = button.getAttribute('data-confirm-text');
                const confirmClass = button.getAttribute('data-confirm-class');

                document.getElementById('confirmModalTitle').textContent = title;
                document.getElementById('confirmModalMessage').textContent = message;

                confirmForm.action = action;
                document.getElementById('confirmActionMethod').value = method;

                confirmBtn.textContent = confirmText;
                confirmBtn.className = `btn ${confirmClass}`;
                confirmBtn.disabled = false;
            });

            // 🔥 Dynamic loading state
            if (confirmForm && confirmBtn) {
                confirmForm.addEventListener('submit', function() {

                    const originalText = confirmBtn.textContent.trim().toLowerCase();

                    let loadingText = "Processing...";

                    if (originalText.includes('archive')) {
                        loadingText = "Archiving...";
                    } else if (originalText.includes('restore')) {
                        loadingText = "Restoring...";
                    } else if (originalText.includes('deactivate')) {
                        loadingText = "Deactivating...";
                    } else if (originalText.includes('reactivate')) {
                        loadingText = "Reactivating...";
                    } else if (originalText.includes('delete')) {
                        loadingText = "Deleting...";
                    }

                    confirmBtn.disabled = true;
                    confirmBtn.textContent = loadingText;
                });
            }

        });
    </script>

    {{-- Add Task Modal Script --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const taskTypeSelect = document.getElementById('taskTypeSelect');
            const customWrapper = document.getElementById('customTaskWrapper');

            if (!taskTypeSelect) return;

            taskTypeSelect.addEventListener('change', function() {
                if (this.value === 'Custom') {
                    customWrapper.classList.remove('d-none');
                } else {
                    customWrapper.classList.add('d-none');
                }
            });
        });
    </script>

    {{-- Edit Task Modal Script --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const modal = document.getElementById('editTaskModal');
            if (!modal) return;

            modal.addEventListener('shown.bs.modal', function(event) {

                const hasOldInput = document.getElementById('has_old_input')?.value === '1';
                const button = event.relatedTarget;

                const taskId = hasOldInput ? "{{ old('task_id') }}" : button.getAttribute('data-task-id');
                const assigned = hasOldInput ? "{{ old('assigned_user_id') }}" : button.getAttribute('data-assigned');
                const startDate = hasOldInput ? "{{ old('start_date') }}" : button.getAttribute('data-start');
                const dueDate = hasOldInput ? "{{ old('due_date') }}" : button.getAttribute('data-due');
                const projectStart = button.getAttribute('data-project-start');
                const projectDue = button.getAttribute('data-project-due');

                document.getElementById('edit_task_id').value = taskId;
                document.getElementById('edit_assigned_user').value = assigned;

                const startInput = document.getElementById('edit_start_date');
                const dueInput = document.getElementById('edit_due_date');

                startInput.value = startDate ?? '';
                dueInput.value = dueDate ?? '';

                // Apply project date restriction
                startInput.min = projectStart;
                startInput.max = projectDue;

                dueInput.min = projectStart;
                dueInput.max = projectDue;

                // Task type logic (keep your existing logic)
                const taskType = hasOldInput ?
                    "{{ old('custom_task_name') ?: old('task_type_select') }}" :
                    button.getAttribute('data-task-type');

                if (typeof handleEditTaskType === 'function') {
                    handleEditTaskType(taskType);
                }

            });

        });
    </script>

    {{-- AUTO-DETECT CUSTOM VS PREDEFINED --}}
    <script>
        function handleEditTaskType(taskType) {

            const select = document.getElementById('editTaskTypeSelect');
            const customWrapper = document.getElementById('editCustomTaskWrapper');
            const customInput = document.getElementById('edit_custom_task_name');

            const predefined = [
                'Perspective', 'Architectural', 'Structural',
                'Mechanical', 'Electrical', 'Plumbing'
            ];

            if (predefined.includes(taskType)) {
                select.value = taskType;
                customWrapper.classList.add('d-none');
                customInput.value = '';
            } else {
                select.value = 'Custom';
                customWrapper.classList.remove('d-none');
                customInput.value = taskType;
            }
        }
    </script>

    {{-- TOGGLE CUSTOM FIELD ON CHANGE --}}
    <script>
        document.getElementById('editTaskTypeSelect')
            .addEventListener('change', function() {

                const wrapper = document.getElementById('editCustomTaskWrapper');
                const input = document.getElementById('edit_custom_task_name');

                if (this.value === 'Custom') {
                    wrapper.classList.remove('d-none');
                    input.focus();
                } else {
                    wrapper.classList.add('d-none');
                    input.value = '';
                }
            });
    </script>

    {{-- Toast Container Script --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toastElList = [].slice.call(document.querySelectorAll('.toast'));
            toastElList.forEach(function(toastEl) {
                const toast = new bootstrap.Toast(toastEl);
                toast.show();
            });
        });
    </script>

    {{-- Hide Sidebar Script--}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const toggleBtn = document.getElementById('sidebarToggle');

            if (!toggleBtn) return;

            // Restore state
            if (localStorage.getItem('sidebarCollapsed') === 'true') {
                document.body.classList.add('sidebar-collapsed');
                toggleBtn.innerHTML = '<i class="bi bi-chevron-right"></i>';
            }

            toggleBtn.addEventListener('click', function() {

                document.body.classList.toggle('sidebar-collapsed');

                const collapsed = document.body.classList.contains('sidebar-collapsed');

                localStorage.setItem('sidebarCollapsed', collapsed);

                toggleBtn.innerHTML = collapsed ?
                    '<i class="bi bi-chevron-right"></i>' :
                    '<i class="bi bi-chevron-left"></i>';
            });

        });
    </script>

    @stack('scripts')

</body>

</html>