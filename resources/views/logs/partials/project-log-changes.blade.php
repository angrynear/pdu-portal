@foreach($changes as $field => $values)

@php
    $old = $values['old'];
    $new = $values['new'];

    if ($field === 'sub_sector') {
        $old = $subSectorLabels[$old] ?? $old;
        $new = $subSectorLabels[$new] ?? $new;
    }

    if ($field === 'amount') {
        $old = number_format((float)$old, 2);
        $new = number_format((float)$new, 2);
    }

    if (in_array($field, ['start_date', 'due_date'])) {
        $old = \Carbon\Carbon::parse($old)->format('F j, Y');
        $new = \Carbon\Carbon::parse($new)->format('F j, Y');
    }

    $label = ucwords(str_replace('_', ' ', $field));
@endphp

<div class="mb-1">
    <strong>{{ $label }}:</strong>
    <span class="text-danger">{{ $old }}</span>
    →
    <span class="text-success">{{ $new }}</span>
</div>

@endforeach
