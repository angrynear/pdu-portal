<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'EFS - Planning and Design Unit Portal')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Ibarra+Real+Nova:wght@500;600;700&display=swap" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>

<body class="login-body">

    @if (!request()->routeIs('login'))
    @include('layouts.public-navbar')
    @endif

    @yield('content')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

<script>
    document.addEventListener("DOMContentLoaded", function() {

        const navbar = document.querySelector(".navbar");
        const collapse = document.getElementById("publicNavbar");

        let isAnimating = false;

        collapse.addEventListener('show.bs.collapse', function() {
            isAnimating = true;
        });

        collapse.addEventListener('shown.bs.collapse', function() {
            isAnimating = false;
        });

        collapse.addEventListener('hide.bs.collapse', function() {
            isAnimating = true;
        });

        collapse.addEventListener('hidden.bs.collapse', function() {
            isAnimating = false;
        });

        window.addEventListener("scroll", function() {

            if (isAnimating) return;

            if (window.scrollY > 50) {
                navbar.classList.add("scrolled");
            } else {
                navbar.classList.remove("scrolled");
            }

        });

    });
</script>

</html>