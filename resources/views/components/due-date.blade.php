@props(['dueDate', 'progress' => null])

@php
    $statusClass = '';
    $statusIcon = '';
    $relativeText = null;
    $daysDiff = null;

    $displayDate = $dueDate instanceof \Carbon\Carbon
        ? $dueDate->copy()->startOfDay()
        : ($dueDate ? \Carbon\Carbon::parse($dueDate)->startOfDay() : null);

    $today = now()->startOfDay();

    // ================================
    // COMPLETED (Highest Priority)
    // ================================
    if ($progress === 100) {

        $statusClass = 'text-success fw-semibold';
        $statusIcon = 'bi-check-circle-fill';
        $relativeText = 'Completed';

    } elseif (!$displayDate) {

        $statusClass = 'text-muted';

    } else {

        $daysDiff = (int) $today->diffInDays($displayDate, false);

        // ================================
        // OVERDUE
        // ================================
        if ($daysDiff < 0) {

            $statusClass = 'text-danger fw-semibold';
            $statusIcon = 'bi-exclamation-circle-fill';

            $overdueDays = abs($daysDiff);
            $relativeText = 'Overdue by ' . $overdueDays . ' day' . ($overdueDays === 1 ? '' : 's');

        }
        // ================================
        // DUE TODAY
        // ================================
        elseif ($daysDiff === 0) {

            $statusClass = 'text-warning fw-semibold';
            $statusIcon = 'bi-clock-fill';
            $relativeText = 'Due today';

        }
        // ================================
        // DUE TOMORROW
        // ================================
        elseif ($daysDiff === 1) {

            $statusClass = 'text-warning fw-semibold';
            $statusIcon = 'bi-clock-fill';
            $relativeText = 'Due tomorrow';

        }
        // ================================
        // DUE WITHIN 7 DAYS
        // ================================
        elseif ($daysDiff <= 7) {

            $statusClass = 'text-warning fw-semibold';
            $statusIcon = 'bi-clock-fill';
            $relativeText = 'Due in ' . $daysDiff . ' days';

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

    @if($relativeText)
        <div class="ps-4">
            {{ $relativeText }}
        </div>
    @endif
@else
    <span>—</span>
@endif

