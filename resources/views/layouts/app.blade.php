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
            background-color: #f8f9fa;
        }

        .navbar {
            min-height: 64px;
            z-index: 1030;
        }

        .sidebar {
            width: 260px;
            min-height: 100vh;
            background-color: #ffffff;
            border-right: 1px solid #dee2e6;
        }

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
    </style>
</head>

<body>

    {{-- Top Navbar --}}
    @include('layouts.navbar')

    <div class="container-fluid">
        <div class="row">
            {{-- Sidebar --}}
            @include('layouts.sidebar')

            {{-- Main Content --}}
            <main class="col content">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Confirmation Modal -->
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

    <!-- Confirmation Modal Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('confirmActionModal');

            modal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;

                const action = button.getAttribute('data-action');
                const method = button.getAttribute('data-method');
                const title = button.getAttribute('data-title');
                const message = button.getAttribute('data-message');
                const confirmText = button.getAttribute('data-confirm-text');
                const confirmClass = button.getAttribute('data-confirm-class');

                // Set modal content
                document.getElementById('confirmModalTitle').textContent = title;
                document.getElementById('confirmModalMessage').textContent = message;

                // Set form action & method
                const form = document.getElementById('confirmActionForm');
                form.action = action;
                document.getElementById('confirmActionMethod').value = method;

                // Set button text & style
                const confirmBtn = document.getElementById('confirmActionButton');
                confirmBtn.textContent = confirmText;
                confirmBtn.className = `btn ${confirmClass}`;
            });
        });
    </script>

</body>

</html>