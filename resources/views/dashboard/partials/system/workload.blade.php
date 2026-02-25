{{-- ============================= --}}
{{-- 4️⃣ WORKLOAD DISTRIBUTION --}}
{{-- ============================= --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body overflow-auto dashboard-scroll">

        <h6 class="fw-semibold mb-4">Workload Distribution</h6>

        @php
        $maxTasks = $workload->max('active_tasks_count') ?: 1;
        @endphp

        @forelse($workload as $person)

        <div class="mb-3 mb-md-4">

            {{-- Name + Count --}}
            <div class="d-flex justify-content-between mb-2">
                <div class="fw-semibold">
                    {{ $person->name }}
                </div>
                <div class="small text-muted">
                    {{ $person->active_tasks_count }}
                </div>
            </div>

            {{-- Progress Bar --}}
            @php
            $percentage = ($person->active_tasks_count / $maxTasks) * 100;

            // Color Logic
            if ($percentage >= 80) {
            $barColor = 'bg-danger';
            } elseif ($percentage >= 50) {
            $barColor = 'bg-warning';
            } else {
            $barColor = 'bg-success';
            }
            @endphp

            <div class="progress rounded-pill" style="height:8px;">
                <div class="progress-bar {{ $barColor }}"
                    style="width: {{ $percentage }}%">
                </div>
            </div>

        </div>

        @empty
        <div class="text-muted small">
            No workload data.
        </div>
        @endforelse

    </div>
</div>