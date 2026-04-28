<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="icon" type="image/png" href="{{ asset('img/favicon.png') }}">
    <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('img/favicon.png') }}">

    <title>@yield('title', 'Notaris App')</title>
    
    {{-- Fonts & Icons --}}
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
    <link href="{{ asset('assets/css/nucleo-icons.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/nucleo-svg.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    {{-- CSS Dasar Argon --}}
    <link id="pagestyle" href="{{ asset('assets/css/argon-dashboard.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    {{-- CSS UTAMA (Vite) - Berisi app.css yang lu kasih tadi --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

<style>
    /* 1. Paksa Modal & Layar Hitam ke angka tertinggi (di atas segalanya) */
    .modal-backdrop { 
        z-index: 10000 !important; 
        background-color: #000 !important;
        opacity: 0.5 !important;
    }
    .modal { 
        z-index: 10001 !important; 
    }

    /* 2. Sidebar tetap nampil tapi di bawah angka 10000 */
    body.modal-open #sidenav-main { 
        z-index: 9999 !important; /* Satu angka di bawah backdrop */
        opacity: 1 !important; 
        visibility: visible !important;
        pointer-events: none; 
    }

    /* 3. Pastikan konten utama tidak mengunci layer modal */
    body.modal-open .main-content {
        z-index: auto !important;
    }
</style>
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
            {{-- Latar Oranye --}}
            <div class="min-height-300 bg-primary position-absolute w-100"></div>
            
            {{-- SIDEBAR --}}
            @include('layouts.navbars.auth.sidenav')

            {{-- KONTEN UTAMA --}}
            <main class="main-content border-radius-lg">
                @yield('content')
            </main>
        @endif
    @endauth

    {{-- CORE JS --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="{{ asset('assets/js/core/popper.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('assets/js/plugins/perfect-scrollbar.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/smooth-scrollbar.min.js') }}"></script>
    
    {{-- PLUGIN & DASHBOARD --}}
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script async defer src="https://buttons.github.io/buttons.js"></script>
    <script src="{{ asset('assets/js/argon-dashboard.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            // Fix: Bersihkan sisa modal saat ditutup agar tidak nge-hang
            $(document).on('hidden.bs.modal', '.modal', function () {
                $('.modal-backdrop').remove();
                $('body').removeClass('modal-open').css('padding-right', '');
            });

            // Toggle Sidebar Mobile
            $('#iconNavbarSidenav').on('click', function() {
                $('body').toggleClass('g-sidenav-pinned');
            });
        });
    </script>

    @stack('js')

    {{-- TEMPAT MODAL (Slot Krusial untuk QR Code) --}}
    @stack('modal_luar')

</body>
</html>