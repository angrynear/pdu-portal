@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
<x-page-wrapper title="My Profile">

    <x-slot name="actions">
        <a href="{{ route('profile.edit') }}"
           class="btn btn-sm btn-primary">
            <i class="bi bi-pencil-square me-1"></i>
            Edit Profile
        </a>
    </x-slot>

    <div class="row g-4">

        {{-- ===================================================== --}}
        {{-- PROFILE SUMMARY --}}
        {{-- ===================================================== --}}
        <div class="col-12 col-lg-4 text-center">

            <img
                src="{{ $user->photo
                        ? asset('storage/' . $user->photo)
                        : asset('images/default-avatar.png') }}"
                class="rounded-circle border mb-3"
                style="width: 180px; height: 180px; object-fit: cover;"
                alt="Profile Photo">

            <h5 class="mb-1">{{ $user->name }}</h5>
            <div class="text-muted small mb-2">
                {{ ucfirst($user->role) }}
            </div>

            @if($user->account_status === 'active')
                <span class="badge bg-success">Active</span>
            @else
                <span class="badge bg-secondary">Inactive</span>
            @endif

        </div>


        {{-- ===================================================== --}}
        {{-- PROFILE DETAILS --}}
        {{-- ===================================================== --}}
        <div class="col-12 col-lg-8">

            {{-- ACCOUNT INFORMATION --}}
            <div class="mb-4">
                <h6 class="text-uppercase text-muted mb-3">
                    Account Information
                </h6>

                <div class="row g-2">

                    <div class="col-12 col-md-6">
                        <div class="small text-muted">Email</div>
                        <div class="fw-semibold">{{ $user->email }}</div>
                    </div>

                    <div class="col-12 col-md-6">
                        <div class="small text-muted">Role</div>
                        <div class="fw-semibold">
                            {{ ucfirst($user->role) }}
                        </div>
                    </div>

                    <div class="col-12 col-md-6">
                        <div class="small text-muted">Member Since</div>
                        <div class="fw-semibold">
                            {{ $user->created_at->format('F d, Y') }}
                        </div>
                    </div>

                </div>
            </div>


            {{-- PERSONNEL INFORMATION --}}
            <div>
                <h6 class="text-uppercase text-muted mb-3">
                    Personnel Information
                </h6>

                <div class="row g-3">

                    <div class="col-12 col-md-6">
                        <div class="small text-muted">Profession</div>
                        <div class="fw-semibold">
                            {{ $user->profession ?? '—' }}
                        </div>
                    </div>

                    <div class="col-12 col-md-6">
                        <div class="small text-muted">Designation</div>
                        <div class="fw-semibold">
                            {{ $user->designation ?? '—' }}
                        </div>
                    </div>

                    <div class="col-12 col-md-6">
                        <div class="small text-muted">Contact Number</div>
                        <div class="fw-semibold">
                            {{ $user->contact_number ?? '—' }}
                        </div>
                    </div>

                    <div class="col-12 col-md-6">
                        <div class="small text-muted">Employment Status</div>
                        <div class="fw-semibold">
                            {{ $user->employment_status ?? '—' }}
                        </div>
                    </div>

                    <div class="col-12 col-md-6">
                        <div class="small text-muted">Employment Started</div>
                        <div class="fw-semibold">
                            {{ $user->employment_started
                                ? \Carbon\Carbon::parse($user->employment_started)->format('F d, Y')
                                : '—' }}
                        </div>
                    </div>

                </div>
            </div>

        </div>

    </div>

</x-page-wrapper>
@endsection
