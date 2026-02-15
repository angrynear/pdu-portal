@php
    $statusClass = '';
    $statusIcon = '';

    $displayDate = $dueDate instanceof \Carbon\Carbon
        ? $dueDate->copy()->startOfDay()
        : ($dueDate ? \Carbon\Carbon::parse($dueDate)->startOfDay() : null);

    $today = now()->startOfDay();

    if ($progress === 100) {
        $statusClass = 'text-success fw-semibold';
        $statusIcon = 'bi-check-circle-fill';
    } elseif (!$displayDate) {
        $statusClass = 'text-muted';
    } else {

        $daysDiff = $today->diffInDays($displayDate, false); 
        // false = signed difference

        if ($daysDiff < 0) {
            $statusClass = 'text-danger fw-semibold';
            $statusIcon = 'bi-exclamation-circle-fill';
        } elseif ($daysDiff <= 7) {
            $statusClass = 'text-warning fw-semibold';
            $statusIcon = 'bi-clock-fill';
        }
    }
@endphp

@if($displayDate)
    <span class="{{ $statusClass }}">
        @if($statusIcon)
            <i class="bi {{ $statusIcon }} me-1"></i>
        @endif
        {{ $displayDate->format('M. j, Y') }}
    </span>
@else
    <span class="text-muted">—</span>
@endif
