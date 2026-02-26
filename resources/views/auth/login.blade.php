@extends('layouts.public')

@section('title', 'EFS – Planning & Design Unit Portal')

@section('content')

<div class="login-wrapper">

    <div class="login-card">

        {{-- Header --}}
        <div class="text-center mb-4">
            <img src="{{ asset('images/mbhte-logo-compact.png') }}"
                alt="MBHTE Logo"
                class="login-logo">

            <h4 class="login-title mt-3">
                Planning & Design Unit Portal
            </h4>

            <p class="login-subtitle">
                Secure Internal Access
            </p>
        </div>

        {{-- FORM --}}
        <form method="POST" action="{{ route('login') }}">
            @csrf

            {{-- EMAIL --}}
            <div class="mb-3">
                <label class="form-label">Email Address</label>

                <input type="email"
                    name="email"
                    value="{{ old('email') }}"
                    class="form-control login-input @error('email') is-invalid @enderror"
                    required autofocus>

                @error('email')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>

            {{-- PASSWORD --}}
            <div class="mb-4 position-relative">
                <label class="form-label">Password</label>

                <div class="position-relative">

                    <input type="password"
                        name="password"
                        id="passwordInput"
                        class="form-control login-input pe-5 @error('password') is-invalid @enderror"
                        required>

                    <span class="password-toggle" onclick="togglePassword()">
                        <i class="bi bi-eye" id="toggleIcon"></i>
                    </span>

                    @error('password')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror

                </div>
            </div>

            <button type="submit"
                id="loginButton"
                class="btn login-btn w-100 d-flex align-items-center justify-content-center gap-2">

                <span id="loginButtonText">
                    Login
                </span>

                <span id="loginSpinner"
                    class="spinner-border spinner-border-sm d-none"
                    role="status"
                    aria-hidden="true">
                </span>

            </button>

            <div class="text-center mt-4">
                <a href="{{ url('/') }}" class="login-back-link">
                    ← Back to Homepage
                </a>
            </div>

        </form>

        {{-- SECURITY NOTICE --}}
        <div class="login-security mt-4 text-center">
            <i class="bi bi-shield-lock"></i>
            Authorized personnel only.
        </div>

    </div>

</div>

{{-- SYSTEM FOOTER --}}
<div class="login-footer text-center">
    <small>
        EFS Portal v1.0.0 |
        © {{ date('Y') }} MBHTE – BARMM
    </small>
</div>

<script>
    function togglePassword() {
        const input = document.getElementById("passwordInput");
        const icon = document.getElementById("toggleIcon");

        if (input.type === "password") {
            input.type = "text";
            icon.classList.replace("bi-eye", "bi-eye-slash");
        } else {
            input.type = "password";
            icon.classList.replace("bi-eye-slash", "bi-eye");
        }
    }
</script>

<script>
    document.querySelector("form").addEventListener("submit", function() {

        const button = document.getElementById("loginButton");
        const text = document.getElementById("loginButtonText");
        const spinner = document.getElementById("loginSpinner");

        button.disabled = true;
        text.textContent = "Logging in...";
        spinner.classList.remove("d-none");

    });
</script>

@endsection