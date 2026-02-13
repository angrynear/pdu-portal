@extends('layouts.app')

@section('title', 'Edit Personnel')

@section('content')
<x-page-wrapper
    title="Edit Personnel">

    <x-slot name="actions">
        <a href="{{ route('personnel.index') }}"
            class="btn btn-sm btn-secondary">
            ← Back to Personnel
        </a>
    </x-slot>

    <form method="POST" id="editPersonnelForm" action="{{ route('personnel.update', $user->id) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row g-3">

            {{-- Photo --}}
            <div class="col-md-12 text-center">
                <label class="form-label d-block">Profile Picture</label>

                <img id="photoPreview"
                    src="{{ isset($user) && $user->photo
                ? asset('storage/' . $user->photo)
                : asset('images/default-avatar.png') }}"
                    class="rounded-circle border mb-3"
                    style="width: 200px; height: 200px; object-fit: cover;">

                <div>
                    <button type="button"
                        class="btn btn-outline-primary btn-sm"
                        onclick="document.getElementById('photoInput').click()">
                        Upload profile picture
                    </button>

                    <input type="file"
                        name="photo"
                        id="photoInput"
                        class="d-none"
                        accept="image/*"
                        onchange="previewPhoto(event)">
                </div>
            </div>

            {{-- Name --}}
            <div class="col-md-6">
                <label class="form-label">Full Name <span class="text-danger">*</span></label>
                <input type="text" name="name"
                    class="form-control @error('name') is-invalid @enderror"
                    value="{{ old('name', $user->name) }}" required>
                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            {{-- Email --}}
            <div class="col-md-6">
                <label class="form-label">Email <span class="text-danger">*</span></label>
                <input type="email" name="email"
                    class="form-control @error('email') is-invalid @enderror"
                    value="{{ old('email', $user->email) }}" required>
                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            {{-- Contact Number --}}
            <div class="col-md-6">
                <label class="form-label">Contact Number</label>
                <input type="text" name="contact_number"
                    class="form-control"
                    value="{{ old('contact_number', $user->contact_number) }}">
            </div>

            {{-- Designation --}}
            <div class="col-md-6">
                <label class="form-label">Designation</label>
                <input type="text" name="designation"
                    class="form-control"
                    value="{{ old('designation', $user->designation) }}">
            </div>

            {{-- Profession --}}
            <div class="col-md-6">
                <label class="form-label">Profession</label>
                <input type="text" name="profession"
                    class="form-control"
                    value="{{ old('profession', $user->profession) }}">
            </div>

            {{-- Employment Status --}}
            <div class="col-md-6">
                <label class="form-label">Employment Status</label>
                <select name="employment_status" class="form-select">
                    <option value="">Select status</option>
                    <option value="Permanent" {{ $user->employment_status === 'Permanent' ? 'selected' : '' }}>Permanent</option>
                    <option value="Contractual" {{ $user->employment_status === 'Contractual' ? 'selected' : '' }}>Contractual</option>
                    <option value="Job Order" {{ $user->employment_status === 'Job Order' ? 'selected' : '' }}>Job Order</option>
                </select>
            </div>

            {{-- Employment Started --}}
            <div class="col-md-6">
                <label class="form-label">Employment Started</label>
                <input type="date" name="employment_started"
                    class="form-control"
                    value="{{ old('employment_started', $user->employment_started) }}">
            </div>

            {{-- Role (ADMIN ONLY) --}}
            @if(auth()->user()->isAdmin())
            <div class="col-md-6">
                <label class="form-label">Role <span class="text-danger">*</span></label>
                <select name="role" class="form-select" required>
                    <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="user" {{ $user->role === 'user' ? 'selected' : '' }}>User</option>
                </select>
            </div>
            @endif

            @if(auth()->user()->isAdmin())

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Temporary Password</label>
                    <input type="password"
                        name="password"
                        class="form-control @error('password') is-invalid @enderror"
                        placeholder="Optional">
                    @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Confirm Temporary Password</label>
                    <input type="password"
                        name="password_confirmation"
                        class="form-control"
                        placeholder="Optional">
                </div>
            </div>
            @endif

        </div>

        <div class="mt-4 d-flex justify-content-end gap-2">
            <a href="{{ route('personnel.index') }}" class="btn btn-secondary">
                Cancel
            </a>
            <button type="submit" id="editPersonnelBtn" class="btn btn-primary">
                Update
            </button>
        </div>

    </form>

</x-page-wrapper>

{{-- PROFILE PHOTO LIVE PREVIEW --}}
<script>
    function previewPhoto(event) {
        const input = event.target;
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('photoPreview').src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>

{{-- Edit Personnel Modal Script for Protect...--}}
<script>
    document.addEventListener('DOMContentLoaded', function() {

        const taskForm = document.querySelector('#editPersonnelForm');
        const submitBtn = document.getElementById('editPersonnelBtn');

        taskForm.addEventListener('submit', function() {
            submitBtn.disabled = true;
            submitBtn.innerText = "Updating...";
        });

    });
</script>

@endsection