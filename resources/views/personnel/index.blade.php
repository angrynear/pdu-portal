@extends('layouts.app')

@section('title', 'Manage Personnel')

@section('content')
<x-page-wrapper
    title="Personnel List">

    <x-slot name="actions">
        <a href="{{ route('personnel.create') }}"
            class="btn btn-sm btn-success">
            <i class="bi bi-plus-circle me-1"></i>
            Add Personnel
        </a>
    </x-slot>

    {{-- ============================= --}}
    {{-- DESKTOP TABLE VIEW --}}
    {{-- ============================= --}}
    <div class="d-none d-lg-block">
        <div class="table-responsive">

            <table class="table table-sm align-middle table-projects">
                <thead class="table-light">
                    <tr>
                        <th class="text-center" style="width: 60px;">No.</th>
                        <th class="text-center" style="width: 80px;">Photo</th>
                        <th style="width: 200px;">Name</th>
                        <th style="width: 160px;">Designation</th>
                        <th style="width: 180px;">Email</th>
                        <th style="width: 100px;">Contact No.</th>
                        <th class="text-center" style="width: 90px;">Role</th>
                        <th class="text-center" style="width: 100px;">Tasks</th>
                        <th class="text-center" style="width: 90px;">Status</th>
                        <th class="text-center" style="width: 120px;">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($users as $user)
                    <tr>
                        {{-- No. --}}
                        <td class="text-center">
                            {{ $users->firstItem() + $loop->index }}
                        </td>

                        {{-- Photo --}}
                        <td class="text-center">
                            <img
                                src="{{ $user->photo
                                        ? asset('storage/' . $user->photo)
                                        : asset('images/default-avatar.png') }}"
                                alt="Photo"
                                class="rounded-circle border"
                                style="width: 50px; height: 50px; object-fit: cover;">
                        </td>

                        {{-- Name --}}
                        <td>{{ $user->name }}</td>

                        {{-- Designation --}}
                        <td>
                            {{ $user->designation ?? '—' }}
                        </td>

                        {{-- Email --}}
                        <td>{{ $user->email }}</td>

                        {{-- Contact Number --}}
                        <td>
                            {{ $user->contact_number ?? '—' }}
                        </td>

                        {{-- Role --}}
                        <td class="text-center">
                            <span class="badge bg-secondary">
                                {{ ucfirst($user->role) }}
                            </span>
                        </td>

                        {{-- Tasks --}}
                        <td class="text-center">
                            <div class="small">
                                <span>
                                    Ongoing: <strong>{{ $user->ongoing_tasks_count }}</strong>
                                </span><br />
                                <span>
                                    Total: <strong>{{ $user->total_tasks_count }}</strong>
                                </span>
                            </div>
                        </td>

                        {{-- Status --}}
                        <td class="text-center">
                            @if($user->account_status === 'active')
                            <span class="badge bg-success">Active</span>
                            @else
                            <span class="badge bg-secondary">Inactive</span>
                            @endif
                        </td>

                        {{-- Actions --}}
                        <td class="text-center">

                            {{-- Edit --}}
                            <a href="{{ route('personnel.edit', $user->id) }}"
                                class="btn btn-sm btn-primary">
                                Edit
                            </a>

                            {{-- Deactivate/ User --}}
                            @if($user->account_status === 'active' && auth()->id() !== $user->id)
                            <button
                                class="btn btn-sm btn-danger"
                                data-bs-toggle="modal"
                                data-bs-target="#confirmActionModal"
                                data-action="{{ route('personnel.deactivate', $user->id) }}"
                                data-method="PATCH"
                                data-title="Deactivate Personnel"
                                data-message="Are you sure you want to deactivate this personnel?"
                                data-confirm-text="Deactivate"
                                data-confirm-class="btn-danger">
                                Deactivate
                            </button>
                            @endif

                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center text-muted">
                            No personnel found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="mt-3">
                {{ $users->links() }}
            </div>

        </div>
    </div>

    {{-- ============================= --}}
    {{-- MOBILE CARD VIEW --}}
    {{-- ============================= --}}
    <div class="d-lg-none">

        @forelse($users as $user)

        <div class="card shadow-sm border-0 mb-3">
            <div class="card-body">

                {{-- Header Row --}}
                <div class="d-flex align-items-center mb-2">

                    <img
                        src="{{ $user->photo
                                    ? asset('storage/' . $user->photo)
                                    : asset('images/default-avatar.png') }}"
                        class="rounded-circle border me-2"
                        style="width: 50px; height: 50px; object-fit: cover;">

                    <div>
                        <div class="fw-bold">
                            {{ $user->name }}
                        </div>

                        <div class="small text-muted">
                            {{ $user->designation ?? '—' }}
                        </div>
                    </div>

                </div>

                {{-- Email --}}
                <div class="small mb-1">
                    <strong>Email:</strong> {{ $user->email }}
                </div>

                {{-- Contact --}}
                <div class="small mb-1">
                    <strong>Contact:</strong> {{ $user->contact_number ?? '—' }}
                </div>

                {{-- Role --}}
                <div class="small mb-1">
                    <strong>Role:</strong>
                    <span class="badge bg-secondary">
                        {{ ucfirst($user->role) }}
                    </span>
                </div>

                {{-- Tasks --}}
                <div class="small mb-2">
                    <strong>Tasks:</strong><br>
                    Ongoing: <strong>{{ $user->ongoing_tasks_count }}</strong><br>
                    Total: <strong>{{ $user->total_tasks_count }}</strong>
                </div>

                {{-- Status --}}
                <div class="small mb-3">
                    <strong>Status:</strong>
                    @if($user->account_status === 'active')
                    <span class="badge bg-success">Active</span>
                    @else
                    <span class="badge bg-secondary">Inactive</span>
                    @endif
                </div>

                {{-- Actions --}}
                <div class="d-grid d-flex gap-1">

                    {{-- Edit --}}
                    <a href="{{ route('personnel.edit', $user->id) }}"
                        class="btn btn-sm btn-primary flex-fill">
                        Edit
                    </a>

                    {{-- Deactivate --}}
                    @if($user->account_status === 'active' && auth()->id() !== $user->id)
                    <button
                        class="btn btn-sm btn-danger flex-fill"
                        data-bs-toggle="modal"
                        data-bs-target="#confirmActionModal"
                        data-action="{{ route('personnel.deactivate', $user->id) }}"
                        data-method="PATCH"
                        data-title="Deactivate Personnel"
                        data-message="Are you sure you want to deactivate this personnel?"
                        data-confirm-text="Deactivate"
                        data-confirm-class="btn-danger">
                        Deactivate
                    </button>
                    @endif

                </div>

            </div>
        </div>

        @empty
        <div class="text-center text-muted py-4">
            No personnel found.
        </div>
        @endforelse

        <div class="mt-3">
            {{ $users->links() }}
        </div>

    </div>

</x-page-wrapper>

@endsection