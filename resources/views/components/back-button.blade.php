@php
    $returnUrl = request('return');
@endphp

<a href="{{ $returnUrl ?? ($fallback ? route($fallback) : url()->previous()) }}"
   class="btn btn-sm btn-secondary">
    ← {{ $label }}
</a>
