@extends('layouts.app')

@section('title', 'Deactivated Personnel')

@section('content')
<x-page-wrapper
    title="Deactivated Personnel">

    <x-slot name="actions">
        <a href="{{ route('personnel.index') }}"
            class="btn btn-sm btn-secondary">
            ← Back to Personnel
        </a>
    </x-slot>

    @if ($users->isEmpty())
    <div class="text-center text-muted">
        No deactivated user found.
    </div>
    @else
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
                    <th class="text-center" style="width: 90px;">Deactivated At</th>
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
                            <span class="badge bg-warning text-dark me-1">
                                Ongoing: {{ $user->ongoing_tasks_count }}
                            </span><br />
                            <span class="badge bg-primary">
                                Total: {{ $user->total_tasks_count }}
                            </span>
                        </div>
                    </td>

                    {{-- Deactivated At --}}
                    <td class="text-center">
                        {{ $user->deactivated_at?->format('F d, Y') }}
                    </td>

                    {{-- Actions --}}
                    <td class="text-center">

                        {{-- Reactivate User --}}
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
    @endif

</x-page-wrapper>

@endsection