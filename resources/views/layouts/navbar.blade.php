<nav class="navbar navbar-expand-lg bg-white border-bottom px-4 sticky-top">
    <div class="container-fluid p-0">

        {{-- LEFT: System Identity and Logo--}}

        <button class="btn btn-outline-secondary d-md-none me-2"
            type="button"
            data-bs-toggle="offcanvas"
            data-bs-target="#appSidebar">
            <i class="bi bi-list"></i>
        </button>

        <img
            src="{{ asset('images/mbhte-logo.png') }}"
            alt="MBHTE Logo"
            style="height: 42px; margin-right: 10px;">

        <div class="d-flex flex-column">
            <span class="fw-bold text-success" style="font-size: 1.1rem;">
                EFS - Planning and Design Unit Portal
            </span>
            <small class="text-muted">
                Ministry of Basic, Higher and Technical Education
            </small>
        </div>

        {{-- RIGHT: User Info --}}
        <div class="ms-auto d-flex align-items-center gap-3 py-1">

            <span class="text-muted small">
                Welcome, <strong>{{ auth()->user()->name }}</strong>
            </span>

            {{-- Avatar placeholder --}}
            <img
                src="{{ auth()->user()->photo
            ? asset('storage/' . auth()->user()->photo)
            : asset('images/default-avatar.png') }}"
                alt="User Avatar"
                class="rounded-circle border"
                style="width: 36px; height: 36px; object-fit: cover;">

        </div>
    </div>
</nav>