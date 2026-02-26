@extends('layouts.public')

@section('content')

{{-- HERO (NO CONTAINER HERE) --}}
@if($slides->count())

<section class="hero-section">

    {{-- SLIDESHOW IMAGE --}}
    <div id="heroCarousel"
        class="carousel slide carousel-fade"
        data-bs-ride="carousel">

        <div class="carousel-inner">

            @foreach($slides as $index => $slide)

            <div class="carousel-item {{ $index === 0 ? 'active' : '' }} hero-slide">

                <img src="{{ asset('storage/' . $slide->image_path) }}"
                    class="d-block w-100 hero-bg">

                <div class="hero-overlay">

                    <div class="hero-bottom-content container text-center">

                        <h1 class="hero-main-title">
                            {{ $slide->title ?? 'Planning & Design Unit Portal' }}
                        </h1>

                        @if($slide->description)
                        <p class="hero-subtitle">
                            {{ \Illuminate\Support\Str::limit($slide->description, 200) }}
                        </p>
                        @endif

                    </div>

                </div>

            </div>

            @endforeach

        </div>

        {{-- Indicators --}}
        <div class="carousel-indicators">
            @foreach($slides as $index => $slide)
            <button type="button"
                data-bs-target="#heroCarousel"
                data-bs-slide-to="{{ $index }}"
                class="{{ $index === 0 ? 'active' : '' }}">
            </button>
            @endforeach
        </div>

        <button class="carousel-control-prev" type="button"
            data-bs-target="#heroCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
        </button>

        <button class="carousel-control-next" type="button"
            data-bs-target="#heroCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
        </button>

    </div>

</section>

@endif

{{-- ABOUT SECTION --}}
<section class="container py-0">

    <h2 class="section-title">About the Portal</h2>

    <p class="section-text">
        The Planning and Design Unit Portal exists as a secure internal platform
        for monitoring and managing projects aligned with MBHTE’s mission
        and development goals in the Bangsamoro Autonomous Region.
    </p>

</section>


{{-- ================= FEATURES ================= --}}
<section class="container pb-5">

    <h2 class="section-title mb-4">Key Features</h2>

    <div class="row g-4">

        <div class="col-md-6 col-lg-3">
            <div class="feature-card text-center p-4">
                <i class="bi bi-clipboard-check fs-2 text-success"></i>
                <h6 class="mt-3">Project Monitoring</h6>
                <p>Track progress of education facilities projects.</p>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="feature-card text-center p-4">
                <i class="bi bi-list-task fs-2 text-success"></i>
                <h6 class="mt-3">Task Tracking</h6>
                <p>Manage tasks and deadlines efficiently.</p>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="feature-card text-center p-4">
                <i class="bi bi-people fs-2 text-success"></i>
                <h6 class="mt-3">Personnel Management</h6>
                <p>Oversee team members and assignments.</p>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="feature-card text-center p-4">
                <i class="bi bi-clock-history fs-2 text-success"></i>
                <h6 class="mt-3">Activity Logs</h6>
                <p>Maintain transparency with system logs.</p>
            </div>
        </div>

    </div>

</section>

{{-- ================= FOOTER ================= --}}
<footer class="public-footer mt-5">

    <div class="container py-5">

        <div class="row g-4">

            {{-- LEFT --}}
            <div class="col-md-4">
                <h6 class="footer-title">EFS – Planning & Design Unit</h6>
                <p class="footer-text">
                    A secure internal platform for monitoring and managing
                    education facilities projects under MBHTE – BARMM.
                </p>
            </div>

            {{-- CENTER --}}
            <div class="col-md-4">
                <h6 class="footer-title">Quick Links</h6>
                <ul class="list-unstyled footer-links">
                    <li><a href="https://bangsamoro.gov.ph/" target="_blank">BARMM Official Website</a></li>
                    <li><a href="https://mbhte.bangsamoro.gov.ph/" target="_blank">MBHTE Official Website</a></li>
                    <li><a href="{{ route('login') }}">Login Portal</a></li>
                </ul>
            </div>

            {{-- RIGHT --}}
            <div class="col-md-4">
                <h6 class="footer-title">Contact Information</h6>
                <p class="footer-text mb-1">
                    Ministry of Basic, Higher and Technical Education
                </p>
                <p class="footer-text">
                    Bangsamoro Autonomous Region in Muslim Mindanao
                </p>
            </div>

        </div>

    </div>

    {{-- Bottom Bar --}}
    <div class="footer-bottom text-center py-3">
        <small>
            © {{ date('Y') }} MBHTE – BARMM. All rights reserved.
        </small>
    </div>

</footer>

@endsection