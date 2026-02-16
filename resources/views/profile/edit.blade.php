@extends('layouts.app')

@section('title', 'Edit My Profile')

@section('content')
<x-page-wrapper title="Edit My Profile">

    <form action="{{ route('profile.update') }}"
          id="editProfileForm"
          method="POST"
          enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row g-4">

            {{-- ===================================================== --}}
            {{-- PROFILE PHOTO --}}
            {{-- ===================================================== --}}
            <div class="col-12 col-lg-4 text-center">

                <img
                    src="{{ $user->photo
                            ? asset('storage/' . $user->photo)
                            : asset('images/default-avatar.png') }}"
                    class="rounded-circle border mb-3"
                    style="width: 160px; height: 160px; object-fit: cover;"
                    alt="Profile Photo"
                    id="photoPreview">

                <div class="mt-2">
                    <label class="btn btn-sm btn-secondary">
                        Change Profile Picture
                        <input type="file"
                               name="photo"
                               class="d-none"
                               accept="image/*"
                               onchange="previewPhoto(event)">
                    </label>
                </div>

            </div>


            {{-- ===================================================== --}}
            {{-- PROFILE FORM --}}
            {{-- ===================================================== --}}
            <div class="col-12 col-lg-8">

                {{-- PROFILE INFORMATION --}}
                <h6 class="text-uppercase text-muted mb-3">
                    Profile Information
                </h6>

                <div class="row g-3">

                    <div class="col-12 col-md-6">
                        <label class="form-label">Full Name</label>
                        <input type="text"
                               name="name"
                               class="form-control"
                               value="{{ old('name', $user->name) }}"
                               required>
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email"
                               name="email"
                               class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email', $user->email) }}"
                               required>
                        @error('email')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label">Profession</label>
                        <input type="text"
                               name="profession"
                               class="form-control"
                               value="{{ old('profession', $user->profession) }}">
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label">Designation</label>
                        <input type="text"
                               name="designation"
                               class="form-control"
                               value="{{ old('designation', $user->designation) }}">
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label">Contact Number</label>
                        <input type="text"
                               name="contact_number"
                               class="form-control"
                               value="{{ old('contact_number', $user->contact_number) }}">
                    </div>

                </div>

                <hr class="my-4">

                {{-- CHANGE PASSWORD --}}
                <h6 class="text-uppercase text-muted mb-3">
                    Change Password
                </h6>

                <div class="row g-3">

                    <div class="col-12 col-md-4">
                        <label class="form-label">Current Password</label>
                        <input type="password"
                               name="current_password"
                               class="form-control @error('current_password') is-invalid @enderror">
                        @error('current_password')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-4">
                        <label class="form-label">New Password</label>
                        <input type="password"
                               name="password"
                               class="form-control @error('password') is-invalid @enderror">
                        @error('password')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-4">
                        <label class="form-label">Confirm New Password</label>
                        <input type="password"
                               name="password_confirmation"
                               class="form-control">
                    </div>

                </div>

                {{-- ACTION BUTTONS --}}
                <div class="mt-4 d-flex flex-column flex-sm-row justify-content-end gap-2">

                    <a href="{{ route('profile.show') }}"
                       class="btn btn-secondary w-100 w-sm-auto flex-fill">
                        Cancel
                    </a>

                    <button type="submit"
                            id="editProfileBtn"
                            class="btn btn-primary w-100 w-sm-auto flex-fill">
                        Save Changes
                    </button>

                </div>

            </div>
        </div>
    </form>

</x-page-wrapper>


{{-- ===================================================== --}}
{{-- SCRIPTS --}}
{{-- ===================================================== --}}
<script>
function previewPhoto(event) {
    const file = event.target.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = function() {
        document.getElementById('photoPreview').src = reader.result;
    };
    reader.readAsDataURL(file);
}

document.addEventListener('DOMContentLoaded', function () {

    const profileForm = document.getElementById('editProfileForm');
    const submitBtn = document.getElementById('editProfileBtn');

    if (profileForm && submitBtn) {
        profileForm.addEventListener('submit', function() {
            submitBtn.disabled = true;
            submitBtn.innerText = "Saving...";
        });
    }

});
</script>

@endsection
