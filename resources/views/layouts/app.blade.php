<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="apple-touch-icon" sizes="76x76" href="/img/apple-icon.png">
    <link rel="icon" type="image/png" href="/img/favicon.png">

    <title>@yield('title', 'Notaris App')</title>
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
    <!-- Nucleo Icons -->
    <link href="{{ asset('assets/css/nucleo-icons.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/nucleo-svg.css') }}" rel="stylesheet" />
    <!-- Font Awesome Icons -->
    <link href="{{ asset('assets/css/nucleo-svg.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- CSS Files -->
    <link id="pagestyle" href="{{ asset('assets/css/argon-dashboard.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="g-sidenav-show bg-light">
    @guest
        @yield('content')
    @endguest
    @php
        $publicRoutes = ['akta.qr.show'];
    @endphp

    @auth
        @if (in_array(request()->route()->getName(), $publicRoutes))
            @yield('content')
        @else
            {{-- DASHBOARD NORMAL --}}
            <div class="min-height-300 bg-primary position-absolute w-100"></div>

            @include('layouts.navbars.auth.sidenav')

            <main class="main-content border-radius-lg">
                @yield('content')
            </main>
        @endif
    @endauth

<script>
    // Fix buat cegah error PerfectScrollbar sebelum file argon-dashboard.js jalan
    if (!document.querySelector('.main-content')) {
        let dummy = document.createElement('div');
        dummy.className = 'main-content';
        dummy.style.display = 'none';
        document.body.appendChild(dummy);
    }
</script>

    <script src="{{ asset('assets/js/core/popper.min.js') }}"></script>
    {{-- <script src="{{ asset('assets/js/core/bootstrap.min.js') }}"></script> --}}
    <script src="{{ asset('assets/js/plugins/perfect-scrollbar.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/smooth-scrollbar.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        var win = navigator.platform.indexOf('Win') > -1;
        if (win && document.querySelector('#sidenav-scrollbar')) {
            var options = {
                damping: '0.5'
            }
            Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
        }
    </script>
    <!-- Github buttons -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script async defer src="https://buttons.github.io/buttons.js"></script>


<script src="{{ asset('assets/js/argon-dashboard.js') }}"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            // otomatis apply Select2 ke semua select yang punya class .select2
            $('.select2').select2({
                placeholder: "Pilih Klien",
                allowClear: true,
                theme: 'bootstrap-5',
                width: '100%'
            });
        });
    </script>
    <script>
        AOS.init();
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var iconNavbarSidenav = document.getElementById('iconNavbarSidenav');
            var body = document.getElementsByTagName('body')[0];
            var className = 'g-sidenav-pinned';

            if (iconNavbarSidenav) {
                iconNavbarSidenav.addEventListener("click", function() {
                    // Gunakan toggle agar lebih ringkas
                    body.classList.toggle(className);
                });
            }
        });
    </script>
    
    @stack('js')
</body>

</html>
