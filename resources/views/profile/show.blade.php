@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
<x-page-wrapper title="My Profile">

    {{-- Edit Profile --}}
    <x-slot name="actions">
        <a href="{{ route('profile.edit') }}"
            class="btn btn-sm btn-primary">
            <i class="bi bi-pencil-square me-1"></i>
            Edit Profile
        </a>
    </x-slot>


    <div class="row g-4">

        {{-- LEFT: Profile Photo --}}
        <div class="col-md-4 text-center">
            <img
                src="{{ $user->photo
                        ? asset('storage/' . $user->photo)
                        : asset('images/default-avatar.png') }}"
                class="rounded-circle border mb-3"
                style="width: 200px; height: 200px; object-fit: cover;"
                alt="Profile Photo">

            <h5 class="mb-0">{{ $user->name }}</h5>
            <div class="text-muted small">{{ ucfirst($user->role) }}</div>

            <div class="mt-2">
                @if($user->account_status === 'active')
                <span class="badge bg-success">Active</span>
                @else
                <span class="badge bg-secondary">Inactive</span>
                @endif
            </div>
        </div>

        {{-- RIGHT: Profile Details --}}
        <div class="col-md-8">

            {{-- ACCOUNT INFORMATION --}}
            <h6 class="text-uppercase text-muted mb-2">Account Information</h6>
            <table class="table table-sm table-borderless mb-4">
                <tr>
                    <th width="200">Email</th>
                    <td>{{ $user->email }}</td>
                </tr>
                <tr>
                    <th>Role</th>
                    <td>{{ ucfirst($user->role) }}</td>
                </tr>
                <tr>
                    <th>Member Since</th>
                    <td>{{ $user->created_at->format('F d, Y') }}</td>
                </tr>
            </table>

            {{-- PERSONNEL INFORMATION --}}
            <h6 class="text-uppercase text-muted mb-2">Personnel Information</h6>
            <table class="table table-sm table-borderless mb-4">
                <tr>
                    <th width="200">Profession</th>
                    <td>{{ $user->profession ?? '—' }}</td>
                </tr>
                <tr>
                    <th width="200">Designation</th>
                    <td>{{ $user->designation ?? '—' }}</td>
                </tr>
                <tr>
                    <th>Contact Number</th>
                    <td>{{ $user->contact_number ?? '—' }}</td>
                </tr>
                <tr>
                    <th>Employment Status</th>
                    <td>{{ $user->employment_status ?? '—' }}</td>
                </tr>
                <tr>
                    <th>Employment Started</th>
                    <td>
                        {{ $user->employment_started
                            ? \Carbon\Carbon::parse($user->employment_started)->format('F d, Y')
                            : '—' }}
                    </td>
                </tr>
            </table>

            {{-- TASK SUMMARY
            <h6 class="text-uppercase text-muted mb-2">Task Summary</h6>
            <table class="table table-sm table-borderless">

                <tr>
                    <th>Total Tasks</th>
                    <td>
                        <span class="badge bg-primary">
                            {{ $user->total_tasks_count ?? 0 }}
            </span>
            </td>
            </tr>

            <tr>
                <th width="200">Completed Tasks</th>
                <td>
                    <span class="badge bg-success">
                        {{ $user->completed_tasks_count ?? 0 }}
                    </span>
                </td>
            </tr>

            <tr>
                <th width="200">Ongoing Tasks</th>
                <td>
                    <span class="badge bg-warning text-dark">
                        {{ $user->ongoing_tasks_count ?? 0 }}
                    </span>
                </td>
            </tr>

            </table>
            --}}
        </div>
    </div>

</x-page-wrapper>
@endsection