<div class="d-none d-lg-flex flex-wrap align-items-center justify-content-between gap-3">

    {{-- LEFT SIDE (Filters) --}}
    <div class="d-flex align-items-center gap-2 flex-wrap ms-auto">

        {{-- Personnel --}}
        @if(auth()->user()->isAdmin() && $scope === 'all')
        <select name="personnel"
            class="form-select form-select-sm shadow-sm w-auto"
            onchange="this.form.submit()">

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
        @endif

        {{-- Status --}}
        <select name="filter"
            class="form-select form-select-sm shadow-sm w-auto"
            onchange="this.form.submit()">

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

        {{-- Task Type --}}
        <select name="type"
            class="form-select form-select-sm shadow-sm w-auto"
            onchange="this.form.submit()">

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

        {{-- Reset --}}
        <a href="{{ route('tasks.index', ['scope' => $scope]) }}"
            class="btn btn-sm btn-outline-secondary">
            Reset
        </a>

    </div>

    {{-- RIGHT SIDE (Scope Toggle) --}}
    @if(auth()->user()->isAdmin())
    <div class="btn-group scope-toggle">
        <a href="{{ route('tasks.index', ['scope' => 'all']) }}"
            class="btn btn-sm {{ $scope === 'all' ? 'btn-dark' : 'btn-outline-secondary' }}">
            All Tasks
        </a>

        <a href="{{ route('tasks.index', ['scope' => 'my']) }}"
            class="btn btn-sm {{ $scope === 'my' ? 'btn-dark' : 'btn-outline-secondary' }}">
            My Tasks
        </a>
    </div>
    @endif

</div>