@php
$fieldLabels = [
    'task_type' => 'Task Type',
    'assigned_user_id' => 'Assigned Personnel',
    'start_date' => 'Start Date',
    'due_date' => 'Due Date',
    'progress' => 'Progress',
    'remark' => 'Remark',
    'files' => 'Attachments',
];
@endphp

@foreach($changes as $field => $values)

@php
    $label = $fieldLabels[$field] ?? ucwords(str_replace('_', ' ', $field));

    $old = $values['old'] ?? null;
    $new = $values['new'] ?? null;

    if ($field === 'progress') {
        $old = $old !== null ? $old . '%' : null;
        $new = $new !== null ? $new . '%' : null;
    }

    if (in_array($field, ['start_date','due_date'])) {
        $old = $old ? \Carbon\Carbon::parse($old)->format('M. d, Y') : null;
        $new = $new ? \Carbon\Carbon::parse($new)->format('M. d, Y') : null;
    }

    if ($field === 'assigned_user_id') {
        $oldUser = \App\Models\User::find($old);
        $newUser = \App\Models\User::find($new);

        $old = $oldUser?->name ?? '—';
        $new = $newUser?->name ?? '—';
    }
@endphp

<div class="mb-1">
    <strong>{{ $label }}:</strong>

    <span class="text-danger">
        {{ $old ?? '—' }}
    </span>

    <i class="bi bi-arrow-right mx-2 text-muted"></i>

    <span class="text-success">
        {{ $new ?? '—' }}
    </span>
</div>

@endforeach
