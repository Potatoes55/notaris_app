<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="icon" type="image/png" href="{{ asset('img/favicon.png') }}">
    <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('img/favicon.png') }}">

    <title>@yield('title', 'Notaris App')</title>
    
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
    <link href="{{ asset('assets/css/nucleo-icons.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/nucleo-svg.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link id="pagestyle" href="{{ asset('assets/css/argon-dashboard.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="g-sidenav-show bg-light">
    @guest
        @yield('content')
    @endguest

    @php $publicRoutes = ['akta.qr.show']; @endphp

    @auth
        @if (in_array(request()->route()->getName(), $publicRoutes))
            @yield('content')
        @else
            <div class="min-height-300 bg-primary position-absolute w-100"></div>
            @include('layouts.navbars.auth.sidenav')
            <main class="main-content border-radius-lg">
                @yield('content')
            </main>
        @endif
    @endauth

    {{-- 1. SCRIPT CEGAH ERROR PERFECT SCROLLBAR --}}
    <script>
        (function() {
            if (!document.querySelector('.main-content')) {
                var main = document.createElement('div');
                main.className = 'main-content';
                main.style.display = 'none';
                document.body.appendChild(main);
            }
            if (!document.querySelector('.sidenav')) {
                var side = document.createElement('nav');
                side.className = 'sidenav';
                side.style.display = 'none';
                document.body.appendChild(side);
            }
        })();
    </script>

    {{-- 2. CORE JS --}}
    <script src="{{ asset('assets/js/core/popper.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/perfect-scrollbar.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/smooth-scrollbar.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    {{-- 3. PLUGIN & DASHBOARD --}}
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script async defer src="https://buttons.github.io/buttons.js"></script>
    <script src="{{ asset('assets/js/argon-dashboard.js') }}"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            // Scrollbar Init
            var win = navigator.platform.indexOf('Win') > -1;
            if (win && document.querySelector('#sidenav-scrollbar')) {
                Scrollbar.init(document.querySelector('#sidenav-scrollbar'), { damping: '0.5' });
            }
            
            // Select2 & AOS
            $('.select2').select2({
                placeholder: "Pilih...",
                allowClear: true,
                theme: 'bootstrap-5',
                width: '100%'
            });
            AOS.init();
        });

        // 4. SCRIPT TOGGLE BURGER (LICIN VERSION)
        document.addEventListener('DOMContentLoaded', function() {
            var iconNavbarSidenav = document.getElementById('iconNavbarSidenav');
            if (iconNavbarSidenav) {
                ['click', 'touchstart'].forEach(evt => {
                    iconNavbarSidenav.addEventListener(evt, function(e) {
                        if(evt === 'touchstart') e.preventDefault();
                        document.body.classList.toggle('g-sidenav-pinned');
                    });
                });
            }
        });
    </script>

    @stack('js')

    {{-- TEMPAT MODAL (Slot buat Backup/Restore biar fokus) --}}
    @stack('modal_luar')
<script>
    // Mematikan animasi saat user menarik (resize) jendela browser
    let resizeTimer;
    window.addEventListener("resize", () => {
        document.body.classList.add("resizing");
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(() => {
            document.body.classList.remove("resizing");
        }, 50); // Cepat kembali siaga setelah berhenti resize
    });

    // Mencegah delay klik pada burger menu
    document.addEventListener('DOMContentLoaded', function() {
        const icon = document.getElementById('iconNavbarSidenav');
        if (icon) {
            icon.addEventListener('click', function() {
                document.body.classList.remove("resizing");
                // Biarkan CSS yang menangani transisi smooth-nya
            });
        }
    });
</script>

</body>
</html>