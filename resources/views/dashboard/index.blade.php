@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

<x-page-wrapper title="Dashboard">

    {{-- ========================= --}}
    {{-- Scope Toggle (Admin Only) --}}
    {{-- ========================= --}}
    <x-slot name="actions">
        @if(auth()->user()->isAdmin())
        <div class="btn-group scope-toggle">

            <a href="{{ route('dashboard', ['scope' => 'all']) }}"
                class="btn btn-sm {{ $scope === 'all' ? 'btn-dark' : 'btn-outline-secondary' }}">
                System Dashboard
            </a>

            <a href="{{ route('dashboard', ['scope' => 'my']) }}"
                class="btn btn-sm {{ $scope === 'my' ? 'btn-dark' : 'btn-outline-secondary' }}">
                My Dashboard
            </a>

        </div>
        @endif
    </x-slot>


    {{-- ============================================= --}}
    {{-- SYSTEM DASHBOARD --}}
    {{-- ============================================= --}}
    @if($scope === 'all')

    @include('dashboard.partials.system')

    @else

    @include('dashboard.partials.my')

    @endif

</x-page-wrapper>

@endsection