@extends('layouts.app')

@section('title', 'Archived Tasks')

@section('content')
<x-page-wrapper
    title="Archived Task"
    alert="Archived module is under development."
    alertType="warning">

    <x-slot name="actions">
        <a href="{{ route('tasks.index') }}"
            class="btn btn-sm btn-secondary">
            ← Back to Tasks
        </a>
    </x-slot>
</x-page-wrapper>
@endsection