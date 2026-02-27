<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Planning and Design Unit - Portal')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Bootstrap --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    

    {{-- Custom styles --}}
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

</head>

<body>

    {{-- Toast Container --}}
    @include('layouts.partials.toasts')

    {{-- Top Navbar --}}
    @include('layouts.navbar')

    <div class="app-layout d-flex">

        {{-- Desktop Sidebar --}}
        <div class="d-none d-md-block sidebar-desktop">
            @include('layouts.sidebar')
        </div>

        {{-- Mobile Offcanvas Sidebar --}}
        <div class="offcanvas offcanvas-start d-md-none mobile-sidebar"
            tabindex="-1"
            id="mobileSidebar">

            <div class="offcanvas-header">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-buildings fs-5 text-success"></i>
                    <span class="fw-semibold">
                        Planning and Design Unit Portal
                    </span>
                </div>
                <button type="button"
                    class="btn-close"
                    data-bs-dismiss="offcanvas">
                </button>
            </div>

            <div class="offcanvas-body p-0">
                @include('layouts.sidebar')
            </div>
        </div>

        {{-- Main Content --}}
        <main class="app-content flex-fill">
            @yield('content')
        </main>

    </div>


    {{-- ================= CONFIRMATION MODAL (UPGRADED) ================= --}}
    <div class="modal fade" id="confirmActionModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">

                <div class="modal-body text-center p-4">

                    {{-- ICON --}}
                    <div id="confirmModalIconWrapper" class="mb-3">
                        <div class="rounded-circle d-inline-flex align-items-center justify-content-center"
                            style="width:70px; height:70px;"
                            id="confirmModalIconContainer">
                            <i class="bi fs-3" id="confirmModalIcon"></i>
                        </div>
                    </div>

                    {{-- TITLE --}}
                    <h5 class="fw-semibold mb-2" id="confirmModalTitle"></h5>

                    {{-- MESSAGE --}}
                    <p class="text-muted mb-4" id="confirmModalMessage"></p>

                    {{-- ACTIONS --}}
                    <div class="d-flex justify-content-center gap-2">

                        <button type="button"
                            class="btn btn-light px-4"
                            data-bs-dismiss="modal">
                            Cancel
                        </button>

                        <form id="confirmActionForm" method="POST">
                            @csrf
                            <input type="hidden" name="_method" id="confirmActionMethod">
                            <button type="submit"
                                class="btn px-4"
                                id="confirmActionButton">
                            </button>
                        </form>

                    </div>

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

                const iconContainer = document.getElementById('confirmModalIconContainer');
                const icon = document.getElementById('confirmModalIcon');

                // Set basic content
                document.getElementById('confirmModalTitle').textContent = title;
                document.getElementById('confirmModalMessage').textContent = message;

                confirmForm.action = action;
                document.getElementById('confirmActionMethod').value = method;

                confirmBtn.textContent = confirmText;
                confirmBtn.disabled = false;

                // Reset button class cleanly
                confirmBtn.className = 'btn px-4';
                confirmBtn.classList.add(confirmClass);

                // Reset icon styles
                iconContainer.className = 'rounded-circle d-inline-flex align-items-center justify-content-center';
                iconContainer.style.width = "70px";
                iconContainer.style.height = "70px";

                // Dynamic visual styling based on action type
                if (confirmClass.includes('danger')) {
                    iconContainer.classList.add('bg-danger-subtle');
                    icon.className = 'bi bi-exclamation-triangle-fill text-danger fs-3';
                } else if (confirmClass.includes('success')) {
                    iconContainer.classList.add('bg-success-subtle');
                    icon.className = 'bi bi-check-circle-fill text-success fs-3';
                } else {
                    iconContainer.classList.add('bg-secondary-subtle');
                    icon.className = 'bi bi-question-circle-fill text-secondary fs-3';
                }

            });

            // 🔥 Loading State (Preserved + Improved)
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
                    confirmBtn.innerHTML = `
                <span class="spinner-border spinner-border-sm me-2"></span>
                ${loadingText}
            `;
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
        document.addEventListener('shown.bs.modal', function(event) {
            if (event.target.id !== 'editTaskModal') return;

            const select = document.getElementById('editTaskTypeSelect');
            const wrapper = document.getElementById('editCustomTaskWrapper');
            const input = document.getElementById('edit_custom_task_name');

            if (!select) return;

            select.addEventListener('change', function() {
                if (this.value === 'Custom') {
                    wrapper.classList.remove('d-none');
                    input.focus();
                } else {
                    wrapper.classList.add('d-none');
                    input.value = '';
                }
            });
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const toggleBtn = document.getElementById('sidebarToggle');

            if (!toggleBtn) return;

            toggleBtn.addEventListener('click', function() {
                document.body.classList.toggle('sidebar-hidden');
                localStorage.setItem(
                    'sidebarHidden',
                    document.body.classList.contains('sidebar-hidden')
                );
            });

            // Restore state
            if (localStorage.getItem('sidebarHidden') === 'true') {
                document.body.classList.add('sidebar-hidden');
            }

        });
    </script>

    @stack('scripts')

</body>

</html>