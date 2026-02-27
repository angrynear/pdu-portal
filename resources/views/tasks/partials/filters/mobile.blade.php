<div class="d-lg-none w-100 d-flex justify-content-center">
    <div class="w-100" style="max-width: 420px;">

        {{-- Scope Toggle --}}
        @if(auth()->user()->isAdmin())
        <div class="btn-group w-100 overflow-hidden shadow-sm mb-3">
            <a href="{{ route('tasks.index', ['scope' => 'all']) }}"
                class="btn flex-fill border-0 {{ $scope === 'all' ? 'btn-dark text-white' : 'btn-light text-muted' }}">
                All Tasks
            </a>

            <a href="{{ route('tasks.index', ['scope' => 'my']) }}"
                class="btn flex-fill border-0 {{ $scope === 'my' ? 'btn-dark text-white' : 'btn-light text-muted' }}">
                My Tasks
            </a>
        </div>
        @endif


        {{-- Filter Toggle Button --}}
        <button class="btn btn-outline-secondary w-100 mb-2"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#mobileTaskFilters">
            <i class="bi bi-funnel me-1"></i>
            Filters
        </button>


        {{-- Collapsible Filter Panel --}}
        <div class="collapse {{ ($status !== 'all' || $type || $personnel) ? 'show' : '' }}"
            id="mobileTaskFilters">

            <div class="card card-body shadow-sm border-0">

                {{-- ================= PERSONNEL ================= --}}
                @if(auth()->user()->isAdmin() && $scope === 'all')
                <div class="mb-3">
                    <label class="form-label small text-muted">Personnel</label>

                    <select name="personnel" class="form-select form-select-sm">

                        <option value=""
                            {{ $personnel ? '' : 'selected' }}>
                            All Personnel ({{ $statusCounts['all'] ?? 0 }})
                        </option>

                        @foreach($personnelList as $id => $name)
                            @php $count = $personnelCounts[$id] ?? 0; @endphp

                            @if($count > 0)
                                <option value="{{ $id }}"
                                    {{ $personnel == $id ? 'selected' : '' }}>
                                    {{ $name }} ({{ $count }})
                                </option>
                            @endif
                        @endforeach

                    </select>
                </div>
                @endif


                {{-- ================= STATUS ================= --}}
                <div class="mb-3">
                    <label class="form-label small text-muted">Status</label>

                    <select name="filter" class="form-select form-select-sm">

                        @foreach($statusLabels as $key => $label)
                            @php $count = $statusCounts[$key] ?? 0; @endphp

                            @if($count > 0)
                                <option value="{{ $key }}"
                                    {{ $status === $key ? 'selected' : '' }}>
                                    {{ $label }} ({{ $count }})
                                </option>
                            @endif
                        @endforeach

                    </select>
                </div>


                {{-- ================= TASK TYPE ================= --}}
                <div class="mb-3">
                    <label class="form-label small text-muted">Task Type</label>

                    <select name="type" class="form-select form-select-sm">

                        <option value=""
                            {{ $type ? '' : 'selected' }}>
                            All Types ({{ $statusCounts['all'] ?? 0 }})
                        </option>

                        @foreach($taskTypes as $taskType => $count)
                            @if($count > 0)
                                <option value="{{ $taskType }}"
                                    {{ $type === $taskType ? 'selected' : '' }}>
                                    {{ $taskType }} ({{ $count }})
                                </option>
                            @endif
                        @endforeach

                    </select>
                </div>


                {{-- ================= APPLY & RESET ================= --}}
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-dark btn-sm flex-fill">
                        Apply
                    </button>

                    <a href="{{ route('tasks.index', ['scope'=>$scope]) }}"
                        class="btn btn-outline-secondary btn-sm flex-fill">
                        Reset
                    </a>
                </div>

            </div>
        </div>

    </div>
</div>