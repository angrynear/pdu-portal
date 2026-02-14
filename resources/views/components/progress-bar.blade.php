@props(['value'])

@php
    $progress = (int) $value;

    if ($progress === 0) {
        $textColor = 'text-secondary';
        $barColor  = 'bg-secondary';
    } elseif ($progress === 100) {
        $textColor = 'text-success';
        $barColor  = 'bg-success';
    } elseif ($progress >= 70) {
        $textColor = 'text-primary';
        $barColor  = 'bg-primary';
    } elseif ($progress >= 31) {
        $textColor = 'text-warning';
        $barColor  = 'bg-warning';
    } else {
        $textColor = 'text-danger';
        $barColor  = 'bg-danger';
    }
@endphp

<div>
    <span class="{{ $textColor }} fw-semibold">
        {{ $progress }}%
    </span>

    <div class="progress mt-1" style="height:6px;">
        <div class="progress-bar {{ $barColor }}"
             role="progressbar"
             style="width: {{ $progress }}%">
        </div>
    </div>
</div>
