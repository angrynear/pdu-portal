<aside class="col-auto sidebar p-2">

    {{-- MAIN --}}
    <h6 class="text-uppercase text-muted">Main</h6>
    <ul class="nav flex-column">
        <li class="nav-item">
            <a href="{{ route('dashboard') }}"
                class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2 me-2"></i>
                Dashboard
            </a>
        </li>
    </ul>

    {{-- PROJECT --}}
    <h6 class="text-uppercase text-muted">Project</h6>
    <ul class="nav flex-column">
        <li class="nav-item">
            <a href="{{ route('projects.index') }}"
                class="nav-link {{ request()->routeIs('projects.*') && !request()->routeIs('projects.archived') ? 'active' : '' }}">
                <i class="bi bi-kanban me-2"></i>
                Manage Projects
            </a>
        </li>
    </ul>

    {{-- TASK --}}
    <h6 class="text-uppercase text-muted">Task</h6>
    <ul class="nav flex-column">
        <li class="nav-item">
            <a href="{{ route('tasks.index') }}"
                class="nav-link {{ request()->routeIs('tasks.*') && !request()->routeIs('tasks.archived') ? 'active' : '' }}">
                <i class="bi bi-list-check me-2"></i>
                Manage Tasks
            </a>
        </li>
    </ul>

    {{-- ADMIN ONLY --}}
    @if(auth()->user()->isAdmin())

    {{-- PERSONNEL --}}
    <h6 class="text-uppercase text-muted">Personnel</h6>
    <ul class="nav flex-column">
        <li class="nav-item">
            <a href="{{ route('personnel.index') }}"
                class="nav-link {{ request()->routeIs('personnel.index', 'personnel.create', 'personnel.edit') ? 'active' : '' }}">
                <i class="bi bi-people me-2"></i>
                Manage Personnel
            </a>
        </li>
    </ul>

    {{-- CONTENT --}}
    <h6 class="text-uppercase text-muted">Content</h6>
    <ul class="nav flex-column">
        <li class="nav-item">
            <a href="#"
                class="nav-link {{ request()->routeIs('content.*') ? 'active' : '' }}">
                <i class="bi bi-images me-2"></i>
                Slideshow Manager
            </a>
        </li>
    </ul>

    {{-- ARCHIVES --}}
    <h6 class="text-uppercase text-muted">Archives</h6>
    <ul class="nav flex-column">
        <li class="nav-item">
            <a href="{{ route('projects.archived') }}"
                class="nav-link {{ request()->routeIs('projects.archived') ? 'active' : '' }}">
                <i class="bi bi-archive me-2"></i>
                Archived Projects
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('tasks.archived') }}"
                class="nav-link {{ request()->routeIs('tasks.archived') ? 'active' : '' }}">
                <i class="bi bi-archive me-2"></i>
                Archived Tasks
            </a>

        </li>
        <li class="nav-item">
            <a href="{{ route('personnel.archived') }}"
                class="nav-link {{ request()->routeIs('personnel.archived') ? 'active' : '' }}">
                <i class="bi bi-person-x me-2"></i>
                Deactivated Personnel
            </a>
        </li>
    </ul>

    @endif

    {{-- LOGS --}}
    <h6 class="text-uppercase text-muted">Logs</h6>
    <ul class="nav flex-column">
        <li class="nav-item">
            <a href="{{ route('logs.projects') }}"
                class="nav-link {{ request()->routeIs('logs.projects') ? 'active' : '' }}">
                <i class="bi bi-journal-text me-2"></i>
                Project Activity Logs
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('logs.tasks') }}"
                class="nav-link {{ request()->routeIs('logs.tasks') ? 'active' : '' }}">
                <i class="bi bi-journal-check me-2"></i>
                Task Activity Logs
            </a>
        </li>
    </ul>

    {{-- ACCOUNT --}}
    <h6 class="text-uppercase text-muted">Account</h6>
    <ul class="nav flex-column">

        {{-- My Profile --}}
        <li class="nav-item">
            <a href="{{ route('profile.show') }}"
                class="nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                <i class="bi bi-person-circle me-2"></i>
                My Profile
            </a>
        </li>

        {{-- Logout --}}
        <li class="nav-item">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="nav-link btn btn-link text-start text-danger w-100">
                    <i class="bi bi-box-arrow-right me-2"></i>
                    Logout
                </button>
            </form>
        </li>
    </ul>

</aside>