@extends('layouts.app')

@section('title', 'Edit Personnel')

@section('content')
<x-page-wrapper title="Edit Personnel">

    <x-slot name="actions">
        <a href="{{ route('personnel.index') }}"
           class="btn btn-sm btn-outline-secondary">
            ← Back to Personnel
        </a>
    </x-slot>

    <form method="POST"
          id="editPersonnelForm"
          action="{{ route('personnel.update', $user->id) }}"
          enctype="multipart/form-data">

        @csrf
        @method('PUT')

        <div class="row g-3">

            {{-- PHOTO --}}
            <div class="col-12 text-center mb-2">

                <label class="form-label fw-semibold d-block">
                    Profile Picture
                </label>

                <img id="photoPreview"
                     src="{{ $user->photo
                        ? asset('storage/' . $user->photo)
                        : asset('images/default-avatar.png') }}"
                     class="rounded-circle border mb-3"
                     style="width: 250px; height: 250px; object-fit: cover;">

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
                       value="{{ old('name', $user->name) }}"
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
                       value="{{ old('email', $user->email) }}"
                       required>
                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            {{-- CONTACT --}}
            <div class="col-12 col-md-6">
                <label class="form-label fw-semibold">Contact Number</label>
                <input type="text"
                       name="contact_number"
                       class="form-control"
                       value="{{ old('contact_number', $user->contact_number) }}">
            </div>

            {{-- DESIGNATION --}}
            <div class="col-12 col-md-6">
                <label class="form-label fw-semibold">Designation</label>
                <input type="text"
                       name="designation"
                       class="form-control"
                       value="{{ old('designation', $user->designation) }}">
            </div>

            {{-- PROFESSION --}}
            <div class="col-12 col-md-6">
                <label class="form-label fw-semibold">Profession</label>
                <input type="text"
                       name="profession"
                       class="form-control"
                       value="{{ old('profession', $user->profession) }}">
            </div>

            {{-- EMPLOYMENT STATUS --}}
            <div class="col-12 col-md-6">
                <label class="form-label fw-semibold">Employment Status</label>
                <select name="employment_status"
                        class="form-select">
                    <option value="">Select status</option>
                    <option value="Permanent" {{ $user->employment_status === 'Permanent' ? 'selected' : '' }}>Permanent</option>
                    <option value="Contractual" {{ $user->employment_status === 'Contractual' ? 'selected' : '' }}>Contractual</option>
                    <option value="Job Order" {{ $user->employment_status === 'Job Order' ? 'selected' : '' }}>Job Order</option>
                </select>
            </div>

            {{-- EMPLOYMENT STARTED --}}
            <div class="col-12 col-md-6">
                <label class="form-label fw-semibold">Employment Started</label>
                <input type="date"
                       name="employment_started"
                       class="form-control"
                       value="{{ old('employment_started', $user->employment_started) }}">
            </div>

            {{-- ROLE (ADMIN ONLY) --}}
            @if(auth()->user()->isAdmin())
                <div class="col-12 col-md-6">
                    <label class="form-label fw-semibold">
                        Role <span class="text-danger">*</span>
                    </label>
                    <select name="role"
                            class="form-select"
                            required>
                        <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="user" {{ $user->role === 'user' ? 'selected' : '' }}>User</option>
                    </select>
                </div>

                {{-- PASSWORD --}}
                <div class="col-12 col-md-6">
                    <label class="form-label fw-semibold">
                        Temporary Password
                    </label>
                    <input type="password"
                           name="password"
                           class="form-control @error('password') is-invalid @enderror"
                           placeholder="Optional">
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- CONFIRM PASSWORD --}}
                <div class="col-12 col-md-6">
                    <label class="form-label fw-semibold">
                        Confirm Temporary Password
                    </label>
                    <input type="password"
                           name="password_confirmation"
                           class="form-control"
                           placeholder="Optional">
                </div>
            @endif

        </div>

        {{-- FOOTER --}}
        <div class="mt-4 d-flex justify-content-end gap-2 flex-wrap">
            <a href="{{ route('personnel.index') }}"
               class="btn btn-light">
                Cancel
            </a>

            <button type="submit"
                    id="editPersonnelBtn"
                    class="btn btn-primary px-4">
                Update
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
document.addEventListener('DOMContentLoaded', function () {

    const form = document.getElementById('editPersonnelForm');
    const submitBtn = document.getElementById('editPersonnelBtn');

    if (form && submitBtn) {
        form.addEventListener('submit', function () {
            submitBtn.disabled = true;
            submitBtn.innerText = "Updating...";
        });
    }

});
</script>

@endsection
