@extends('layouts.app')

@section('title', 'Deactivated Personnel')

@section('content')
<x-page-wrapper title="Deactivated Personnel">

    <x-slot name="actions">
        <a href="{{ route('personnel.index') }}"
           class="btn btn-sm btn-secondary">
            ← Back to Personnel
        </a>
    </x-slot>

    @if ($users->isEmpty())
        <div class="text-center text-muted py-0">
            No deactivated user found.
        </div>
    @else

    {{-- ===================================================== --}}
    {{-- DESKTOP TABLE VIEW --}}
    {{-- ===================================================== --}}
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
                        <th class="text-center" style="width: 120px;">Deactivated At</th>
                        <th class="text-center" style="width: 120px;">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($users as $user)
                    <tr class="text-muted">

                        <td class="text-center">
                            {{ $users->firstItem() + $loop->index }}
                        </td>

                        <td class="text-center">
                            <img
                                src="{{ $user->photo
                                    ? asset('storage/' . $user->photo)
                                    : asset('images/default-avatar.png') }}"
                                alt="Photo"
                                class="rounded-circle border"
                                style="width: 50px; height: 50px; object-fit: cover;">
                        </td>

                        <td>{{ $user->name }}</td>

                        <td>{{ $user->designation ?? '—' }}</td>

                        <td>{{ $user->email }}</td>

                        <td>{{ $user->contact_number ?? '—' }}</td>

                        <td class="text-center">
                            <span class="badge bg-secondary">
                                {{ ucfirst($user->role) }}
                            </span>
                        </td>

                        <td class="text-center">
                            {{ $user->deactivated_at?->format('F d, Y') }}
                        </td>

                        <td class="text-center">
                            <button
                                class="btn btn-sm btn-success"
                                data-bs-toggle="modal"
                                data-bs-target="#confirmActionModal"
                                data-action="{{ route('personnel.reactivate', $user->id) }}"
                                data-method="PATCH"
                                data-title="Reactivate Personnel"
                                data-message="Are you sure you want to reactivate this personnel?"
                                data-confirm-text="Reactivate"
                                data-confirm-class="btn-success">
                                Reactivate
                            </button>
                        </td>

                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="mt-3">
                {{ $users->links() }}
            </div>
        </div>
    </div>


    {{-- ===================================================== --}}
    {{-- MOBILE CARD VIEW --}}
    {{-- ===================================================== --}}
    <div class="d-lg-none">

        @foreach($users as $user)

        <div class="card shadow-sm border-0 mb-3 text-muted">
            <div class="card-body">

                {{-- Header --}}
                <div class="d-flex align-items-center mb-2">

                    <img
                        src="{{ $user->photo
                            ? asset('storage/' . $user->photo)
                            : asset('images/default-avatar.png') }}"
                        alt="Photo"
                        class="rounded-circle border me-2"
                        style="width: 50px; height: 50px; object-fit: cover;">

                    <div>
                        <div class="fw-bold">
                            {{ $user->name }}
                        </div>
                        <div class="small">
                            {{ ucfirst($user->role) }}
                        </div>
                    </div>

                </div>

                <div class="small mb-1">
                    <strong>Designation:</strong> {{ $user->designation ?? '—' }}
                </div>

                <div class="small mb-1">
                    <strong>Email:</strong> {{ $user->email }}
                </div>

                <div class="small mb-1">
                    <strong>Contact:</strong> {{ $user->contact_number ?? '—' }}
                </div>

                <div class="small mb-3">
                    <strong>Deactivated:</strong>
                    {{ $user->deactivated_at?->format('F d, Y') }}
                </div>

                <div class="d-grid">
                    <button
                        class="btn btn-sm btn-success"
                        data-bs-toggle="modal"
                        data-bs-target="#confirmActionModal"
                        data-action="{{ route('personnel.reactivate', $user->id) }}"
                        data-method="PATCH"
                        data-title="Reactivate Personnel"
                        data-message="Are you sure you want to reactivate this personnel?"
                        data-confirm-text="Reactivate"
                        data-confirm-class="btn-success">
                        Reactivate
                    </button>
                </div>

            </div>
        </div>

        @endforeach

        <div class="mt-3">
            {{ $users->links() }}
        </div>

    </div>

    @endif

</x-page-wrapper>
@endsection
