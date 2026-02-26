@extends('layouts.app')

@section('title', 'Add Personnel')

@section('content')
<x-page-wrapper title="Add Personnel">

    <x-slot name="actions">
        <a href="{{ route('personnel.index') }}"
            class="btn btn-sm btn-outline-secondary">
            ← Back to Personnel
        </a>
    </x-slot>

    <form method="POST"
        id="createPersonnelForm"
        action="{{ route('personnel.store') }}"
        enctype="multipart/form-data">

        @csrf

        <div class="row g-3">

            {{-- PHOTO --}}
            <div class="col-12 text-center mb-2">

                <label class="form-label d-block fw-semibold">
                    Profile Picture
                </label>

                <img id="photoPreview"
                    src="{{ asset('images/default-avatar.png') }}"
                    class="border rounded-circle mb-3"
                    style="width: 180px; height: 180px; object-fit: cover;">

                <div>
                    <button type="button"
                        class="btn btn-outline-primary btn-sm"
                        onclick="document.getElementById('photoInput').click()">
                        Upload Profile Picture
                    </button>

                    <input type="file"
                        name="photo"
                        id="photoInput"
                        class="d-none"
                        accept="image/*"
                        onchange="previewPhoto(event)">
                </div>

            </div>

            {{-- NAME --}}
            <div class="col-12 col-md-6">
                <label class="form-label fw-semibold">
                    Full Name <span class="text-danger">*</span>
                </label>
                <input type="text"
                    name="name"
                    class="form-control @error('name') is-invalid @enderror"
                    value="{{ old('name') }}"
                    required>
                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            {{-- EMAIL --}}
            <div class="col-12 col-md-6">
                <label class="form-label fw-semibold">
                    Email <span class="text-danger">*</span>
                </label>
                <input type="email"
                    name="email"
                    class="form-control @error('email') is-invalid @enderror"
                    value="{{ old('email') }}"
                    required>
                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            {{-- CONTACT --}}
            <div class="col-12 col-md-6">
                <label class="form-label fw-semibold">
                    Contact Number
                </label>
                <input type="text"
                    name="contact_number"
                    class="form-control @error('contact_number') is-invalid @enderror"
                    value="{{ old('contact_number') }}">
                @error('contact_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            {{-- ROLE --}}
            <div class="col-12 col-md-6">
                <label class="form-label fw-semibold">
                    Role <span class="text-danger">*</span>
                </label>
                <select name="role"
                    class="form-select @error('role') is-invalid @enderror"
                    required>
                    <option value="">Select role</option>
                    <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="user" {{ old('role') === 'user' ? 'selected' : '' }}>User</option>
                    @error('role') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </select>
            </div>

            {{-- DESIGNATION --}}
            <div class="col-12 col-md-6">
                <label class="form-label fw-semibold">Designation</label>
                <input type="text"
                    name="designation"
                    class="form-control"
                    value="{{ old('designation') }}">
            </div>

            {{-- PROFESSION --}}
            <div class="col-12 col-md-6">
                <label class="form-label fw-semibold">Profession</label>
                <input type="text"
                    name="profession"
                    class="form-control"
                    value="{{ old('profession') }}">
            </div>

            {{-- EMPLOYMENT STATUS --}}
            <div class="col-12 col-md-6">
                <label class="form-label fw-semibold">Employment Status</label>
                <select name="employment_status"
                    class="form-select">
                    <option value="">Select status</option>
                    <option value="Permanent">Permanent</option>
                    <option value="Contractual">Contractual</option>
                    <option value="Job Order">Job Order</option>
                </select>
            </div>

            {{-- EMPLOYMENT STARTED --}}
            <div class="col-12 col-md-6">
                <label class="form-label fw-semibold">Employment Started</label>
                <input type="date"
                    name="employment_started"
                    class="form-control"
                    value="{{ old('employment_started') }}">
            </div>

            {{-- PASSWORD --}}
            <div class="col-12 col-md-6">
                <label class="form-label fw-semibold">
                    Temporary Password <span class="text-danger">*</span>
                </label>
                <input type="password"
                    name="password"
                    class="form-control @error('password') is-invalid @enderror"
                    required>
                @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            {{-- CONFIRM PASSWORD --}}
            <div class="col-12 col-md-6">
                <label class="form-label fw-semibold">
                    Confirm Temporary Password <span class="text-danger">*</span>
                </label>
                <input type="password"
                    name="password_confirmation"
                    class="form-control"
                    required>
            </div>

        </div>

        {{-- FOOTER BUTTONS --}}
        <div class="mt-4 d-flex justify-content-end gap-2 flex-wrap">
            <a href="{{ route('personnel.index') }}"
                class="btn btn-light">
                Cancel
            </a>

            <button type="submit"
                id="createPersonnelBtn"
                class="btn btn-primary px-4">
                <i class="bi bi-people me-2"></i>
                Create Personnel
            </button>
        </div>

    </form>

</x-page-wrapper>

{{-- PHOTO PREVIEW --}}
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

{{-- SAFE SUBMIT SCRIPT --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {

        const form = document.getElementById('createPersonnelForm');
        const submitBtn = document.getElementById('createPersonnelBtn');

        if (form && submitBtn) {
            form.addEventListener('submit', function() {
                submitBtn.disabled = true;
                submitBtn.innerText = "Creating...";
            });
        }

    });
</script>

@endsection