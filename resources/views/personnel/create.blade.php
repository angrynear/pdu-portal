@extends('layouts.app')

@section('title', 'Add Personnel')

@section('content')
<x-page-wrapper
    title="Add Personnel">

    <x-slot name="actions">
        <a href="{{ route('personnel.index') }}"
            class="btn btn-sm btn-secondary">
            ← Back to Personnel
        </a>
    </x-slot>

    <form method="POST" action="{{ route('personnel.store') }}" enctype="multipart/form-data">
        @csrf

        <div class="row g-3">

            {{-- Photo --}}
            <div class="col-md-12 text-center">
                <label class="form-label d-block">Profile Picture</label>

                <img id="photoPreview"
                    src="{{ isset($user) && $user->photo
                ? asset('storage/' . $user->photo)
                : asset('images/default-avatar.png') }}"
                    class="border mb-2"
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
                    value="{{ old('name') }}" required>
                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            {{-- Email --}}
            <div class="col-md-6">
                <label class="form-label">Email <span class="text-danger">*</span></label>
                <input type="email" name="email"
                    class="form-control @error('email') is-invalid @enderror"
                    value="{{ old('email') }}" required>
                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            {{-- Contact Number --}}
            <div class="col-md-6">
                <label class="form-label">Contact Number</label>
                <input type="text" name="contact_number"
                    class="form-control @error('contact_number') is-invalid @enderror"
                    value="{{ old('contact_number') }}">
                @error('contact_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            {{-- Role --}}
            <div class="col-md-6">
                <label class="form-label">Role <span class="text-danger">*</span></label>
                <select name="role"
                    class="form-select @error('role') is-invalid @enderror" required>
                    <option value="">Select role</option>
                    <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="user" {{ old('role') === 'user' ? 'selected' : '' }}>User</option>
                </select>
                @error('role') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            {{-- Designation --}}
            <div class="col-md-6">
                <label class="form-label">Designation</label>
                <input type="text" name="designation"
                    class="form-control"
                    value="{{ old('designation') }}">
            </div>

            {{-- Profession --}}
            <div class="col-md-6">
                <label class="form-label">Profession</label>
                <input type="text" name="profession"
                    class="form-control"
                    value="{{ old('profession') }}">
            </div>

            {{-- Employment Status --}}
            <div class="col-md-6">
                <label class="form-label">Employment Status</label>
                <select name="employment_status" class="form-select">
                    <option value="">Select status</option>
                    <option value="Permanent">Permanent</option>
                    <option value="Contractual">Contractual</option>
                    <option value="Job Order">Job Order</option>
                </select>
            </div>

            {{-- Employment Started --}}
            <div class="col-md-6">
                <label class="form-label">Employment Started</label>
                <input type="date" name="employment_started"
                    class="form-control"
                    value="{{ old('employment_started') }}">
            </div>

            {{-- Temporary Password --}}
            <div class="col-md-6">
                <label class="form-label">Temporary Password <span class="text-danger">*</span></label>
                <input type="password" name="password"
                    class="form-control @error('password') is-invalid @enderror"
                    required>
                @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            {{-- Confirm Temporary Password --}}
            <div class="col-md-6">
                <label class="form-label">Confirm Temporary Password <span class="text-danger">*</span></label>
                <input type="password" name="password_confirmation"
                    class="form-control"
                    required>
            </div>

        </div>

        <div class="mt-4 d-flex justify-content-end gap-2">
            <a href="{{ route('personnel.index') }}" class="btn btn-secondary">
                Cancel
            </a>
            <button type="submit" class="btn btn-success">
                Create Personnel
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

@endsection