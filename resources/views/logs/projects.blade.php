@extends('layouts.app')

@section('title', 'Project Activity Log')

@section('content')

<x-page-wrapper title="Project Activity Logs">

    <div class="table-responsive">

        @php
        $subSectorLabels = [
        'basic_education' => 'Basic Education',
        'higher_education' => 'Higher Education',
        'madaris_education' => 'Madaris Education',
        'technical_education' => 'Technical Education',
        'others' => 'Others',
        ];
        @endphp

        <table class="table table-sm align-middle">
            <thead class="table-light">
                <tr>
                    <th style="width: 180px;">Date</th>
                    <th style="width: 180px;">User</th>
                    <th style="width: 300px;">Project</th>
                    <th style="width: 100px;">Action</th>
                    <th style="width: 300px;">Description</th>
                </tr>
            </thead>

            <tbody>
                @forelse($logs as $log)
                <tr>
                    <td>{{ $log->created_at->format('F j, Y h:i A') }}</td>

                    <td>{{ $log->user->name ?? 'System' }}</td>

                    <td>
                        <a href="{{ route('projects.show', $log->project_id) }}?from=project_logs"
                            class="text-decoration-none text-dark fw-semibold link-hover">
                            {{ $log->project->name ?? '—' }}
                        </a>
                    </td>

                    <td>
                        <span class="badge bg-secondary text-uppercase">
                            {{ $log->action }}
                        </span>
                    </td>

                    <td style="width: 350px;">
                        <div>{{ $log->description }}</div>

                        @if($log->changes)
                        @php
                        $changes = $log->changes;
                        @endphp

                        @if(!empty($changes))
                        <button class="btn btn-link btn-sm p-0 mt-1"
                            type="button"
                            data-bs-toggle="collapse"
                            data-bs-target="#changes-{{ $log->id }}">
                            View Changes
                        </button>

                        <div class="collapse mt-2 small" id="changes-{{ $log->id }}">
                            <ul class="list-unstyled mb-0">

                                @foreach($changes as $field => $values)

                                @php
                                $old = $values['old'];
                                $new = $values['new'];

                                // Sub-sector formatting
                                if ($field === 'sub_sector') {
                                $old = $subSectorLabels[$old] ?? $old;
                                $new = $subSectorLabels[$new] ?? $new;
                                }

                                // Amount formatting
                                if ($field === 'amount') {
                                $old = number_format((float)$old, 2);
                                $new = number_format((float)$new, 2);
                                }

                                // Date formatting
                                if (in_array($field, ['start_date', 'due_date'])) {
                                $old = \Carbon\Carbon::parse($old)->format('F j, Y');
                                $new = \Carbon\Carbon::parse($new)->format('F j, Y');
                                }

                                // Beautify label
                                $label = ucwords(str_replace('_', ' ', $field));
                                @endphp

                                <div class="small">
                                    <strong>{{ $label }}:</strong>
                                    <span class="text-danger">{{ $old }}</span>
                                    →
                                    <span class="text-success">{{ $new }}</span>
                                </div>

                                @endforeach

                            </ul>
                        </div>
                        @endif
                        @endif
                    </td>

                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center text-muted">
                        No activity logs found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-3">
        {{ $logs->links() }}
    </div>

</x-page-wrapper>

@endsection