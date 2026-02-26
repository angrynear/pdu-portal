@extends('layouts.app')

@section('title', 'Archives')

@section('content')
<x-page-wrapper title="Archives">

    <x-slot name="actions">

        {{-- DESKTOP: SEGMENTED TOGGLE --}}
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

        {{-- ================= MOBILE ================= --}}
        <div class="d-md-none w-100 d-flex justify-content-center">
            <div class="w-100" style="max-width: 420px;">

                <div class="row g-2">

                    @foreach ([
                    'projects' => 'Projects',
                    'tasks' => 'Tasks',
                    'slides' => 'Slides',
                    'personnel' => 'Personnel'
                    ] as $key => $label)

                    <div class="col-6">
                        <a href="{{ route('archives.index', ['scope' => $key]) }}"
                            class="btn w-100 d-flex justify-content-between align-items-center shadow-sm
                            {{ $scope === $key ? 'btn-dark text-white' : 'btn-light text-muted' }}">

                            <span>{{ $label }}</span>

                            <span class="badge rounded-pill
                            {{ $scope === $key ? 'bg-light text-dark' : 'bg-secondary' }}">
                                {{ $counts[$key] }}
                            </span>

                        </a>
                    </div>

                    @endforeach

                </div>

            </div>
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