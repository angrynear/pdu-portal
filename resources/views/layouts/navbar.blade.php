<nav class="navbar bg-white border-bottom sticky-top px-3 px-md-4 py-2">
    <div class="container-fluid p-0 d-flex align-items-center justify-content-between">

        {{-- LEFT SIDE --}}
        <div class="d-flex align-items-center gap-3">

            {{-- Mobile Sidebar Toggle --}}
            <button class="btn btn-sm btn-light d-md-none"
                type="button"
                data-bs-toggle="offcanvas"
                data-bs-target="#mobileSidebar">
                <i class="bi bi-list fs-4"></i>
            </button>

            {{-- Logo --}}
            <img
                src="{{ asset('images/mbhte-logo.png') }}"
                alt="MBHTE Logo"
                class="navbar-logo">

            {{-- Mobile Short Title --}}
            <div class="d-flex d-sm-none flex-column">
                <span class="fw-bold text-success navbar-title-mobile">
                    EFS - PDU Portal
                </span>
            </div>

            {{-- Desktop / Tablet Full Title --}}
            <div class="d-none d-sm-flex flex-column">
                <span class="fw-bold text-success navbar-title">
                    EFS - Planning and Design Unit Portal
                </span>
                <small class="text-muted">
                    Ministry of Basic, Higher and Technical Education
                </small>
            </div>

        </div>


        {{-- RIGHT SIDE --}}
        <div class="d-flex align-items-center gap-3">

            {{-- Hide welcome text on extra small --}}
            <span class="text-muted small d-none d-md-inline">
                Hi, <strong>{{ Str::before(auth()->user()->name, ' ') }}</strong>
            </span>

            {{-- Avatar --}}
            <img
                src="{{ auth()->user()->photo
                    ? asset('storage/' . auth()->user()->photo)
                    : asset('images/default-avatar.png') }}"
                alt="User Avatar"
                class="rounded-circle border navbar-avatar">

        </div>

    </div>
</nav>