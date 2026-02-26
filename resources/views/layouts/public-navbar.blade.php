<nav class="navbar navbar-expand-md bg-white border-bottom sticky-top px-3 px-md-4 py-2">
    <div class="container-fluid p-0">

        <!-- LEFT: Logo + Title -->
        <div class="d-flex align-items-center gap-3">
            <a class="d-flex align-items-center gap-3 text-decoration-none" href="#">
                <img src="{{ asset('images/mbhte-logo.png') }}"
                    alt="MBHTE Logo"
                    class="navbar-logo">

                <!-- Mobile Short Title -->
                <div class="d-flex d-sm-none flex-column">
                    <span class="fw-bold text-success navbar-title-mobile">
                        EFS - PDU Portal
                    </span>
                </div>

                <!-- Desktop Title -->
                <div class="d-none d-sm-flex flex-column">
                    <span class="fw-bold text-success navbar-title">
                        EFS - Planning and Design Unit Portal
                    </span>
                    <small class="text-muted">
                        Ministry of Basic, Higher and Technical Education
                    </small>
                </div>
            </a>
        </div>

        <!-- Hamburger -->
        <button class="navbar-toggler border-0"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#publicNavbar"
            aria-controls="publicNavbar"
            aria-expanded="false"
            aria-label="Toggle navigation">
            <i class="bi bi-list fs-2 text-success"></i>
        </button>

        <!-- COLLAPSIBLE MENU -->
        <div class="collapse navbar-collapse justify-content-end public-collapse"
            id="publicNavbar">

            <ul class="navbar-nav align-items-md-center gap-md-4 small fw-semibold">

                <li class="nav-item">
                    <a href="https://bangsamoro.gov.ph/"
                        target="_blank"
                        class="nav-link nav-link-public">
                        BARMM
                    </a>
                </li>

                <li class="nav-item">
                    <a href="https://mbhte.bangsamoro.gov.ph/"
                        target="_blank"
                        class="nav-link nav-link-public">
                        MBHTE
                    </a>
                </li>

                <li class="nav-item">
                    <a href="https://mbhte-facilityoffice.com/"
                        target="_blank"
                        class="nav-link nav-link-public">
                        EFS
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('login') }}"
                        class="btn btn-success btn-sm px-3 ms-md-3">
                        <strong>
                            Login
                        </strong>
                    </a>
                </li>

            </ul>

        </div>

    </div>
</nav>