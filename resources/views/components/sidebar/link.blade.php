@props(['route', 'icon', 'active' => false])

<li class="nav-item">
    <a href="{{ route($route) }}"
        class="nav-link {{ $active ? 'active' : '' }}">
        <i class="bi {{ $icon }} me-2"></i>
        <span>{{ $slot }}</span>
    </a>
</li>
