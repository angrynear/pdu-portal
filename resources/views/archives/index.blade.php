@extends('layouts.app')

@section('title', 'Archives')

@section('content')
<x-page-wrapper title="Archives">

<x-slot name="actions">

    {{-- ========================= --}}
    {{-- DESKTOP: SEGMENTED TOGGLE --}}
    {{-- ========================= --}}
    <div class="d-none d-md-block">
        <div class="btn-group scope-toggle">

            @foreach ([
                'projects' => 'Projects',
                'tasks' => 'Tasks',
                'slides' => 'Slides',
                'personnel' => 'Personnel'
            ] as $key => $label)

                <a href="{{ route('archives.index', ['scope' => $key]) }}"
                   class="btn btn-sm d-flex align-items-center gap-2
                          {{ $scope === $key ? 'btn-dark active-scope' : 'btn-outline-secondary' }}">

                    <span>{{ $label }}</span>

                    <span class="badge rounded-pill
                        {{ $scope === $key ? 'bg-light text-dark' : 'bg-secondary' }}">
                        {{ $counts[$key] }}
                    </span>

                </a>

            @endforeach

        </div>
    </div>


    {{-- ========================= --}}
    {{-- MOBILE: DROPDOWN SELECTOR --}}
    {{-- ========================= --}}
    <div class="d-md-none">

        <select class="form-select form-select-sm shadow-sm"
                onchange="if(this.value) window.location.href=this.value">

            @foreach ([
                'projects' => 'Projects',
                'tasks' => 'Tasks',
                'slides' => 'Slides',
                'personnel' => 'Personnel'
            ] as $key => $label)

                <option value="{{ route('archives.index', ['scope' => $key]) }}"
                    {{ $scope === $key ? 'selected' : '' }}>

                    {{ $label }} ({{ $counts[$key] }})

                </option>

            @endforeach

        </select>

    </div>

</x-slot>

    @if ($data->isEmpty())
    <div class="text-center text-muted py-0">
        No archived records found.
    </div>
    @else

    @include('archives.partials.' . $scope, ['data' => $data])

    @endif

</x-page-wrapper>
@endsection