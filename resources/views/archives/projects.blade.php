@extends('layouts.app')

@section('title', 'Archived Projects')

@section('content')
<x-page-wrapper
    title="Archived Project"
    alert="Archived module is under development."
    alertType="warning">

    <x-slot name="actions">
        <a href="{{ route('projects.index') }}"
            class="btn btn-sm btn-secondary">
            ← Back to Projects
        </a>
    </x-slot>
</x-page-wrapper>
@endsection