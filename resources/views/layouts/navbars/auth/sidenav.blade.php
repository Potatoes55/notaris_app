<aside class="sidenav bg-white navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start d-flex flex-column" id="sidenav-main" style="height: 100vh; overflow: hidden;">
    
    <!-- HEADER: PROFILE FOTO & TEXT -->
    <div class="sidenav-header flex-shrink-0 px-3 py-3">
        <div class="d-flex align-items-center gap-3">
            <!-- FOTO -->
            <div class="rounded-circle overflow-hidden flex-shrink-0" style="width:50px; height:50px;">
                <img src="{{ auth()->user()->notaris && auth()->user()->notaris->image
                    ? (filter_var(auth()->user()->notaris->image, FILTER_VALIDATE_URL)
                        ? auth()->user()->notaris->image
                        : asset('storage/' . auth()->user()->notaris->image))
                    : asset('img/img_profile.png') }}" 
                    style="width:100%; height:100%; object-fit:cover;">
            </div>

            <!-- TEXT -->
            <div class="d-flex flex-column justify-content-center profile-text-wrapper" style="min-width:0; width:100%;">
                <h6 class="mb-0 text-sm profile-name" title="{{ auth()->user()->username }}">
                    Hi, {{ \Illuminate\Support\Str::limit(auth()->user()->username, 12, '...') }}
                </h6>
                <p class="mb-0 text-xs text-secondary profile-email" title="{{ auth()->user()->email }}">
                    {{ \Illuminate\Support\Str::limit(auth()->user()->email, 15, '...') }}
                </p>
            </div>
        </div>
    </div>

    <hr class="horizontal-dark mt-0 mb-2">

    <!-- MENU (SCROLL) -->
    <div id="sidenav-collapse-main" class="flex-grow-1" style="overflow-y: auto; overflow-x: hidden;">
        <ul class="navbar-nav">

            @php
                $isNotaris =
                    request()->routeIs('notaris.*') ||
                    request()->routeIs('akta-types.*') ||
                    request()->routeIs('akta-transactions.*') ||
                    request()->routeIs('akta-documents.*') ||
                    request()->routeIs('akta-parties.*') ||
                    request()->routeIs('akta_number.*') ||
                    request()->routeIs('akta-logs.*') ||
                    request()->routeIs('notary-legalisasi.*') ||
                    request()->routeIs('notary-waarmerking.*');

                $isPpat =
                    request()->routeIs('ppat.*') ||
                    request()->routeIs('relaas-types.*') ||
                    request()->routeIs('relaas-aktas.*') ||
                    request()->routeIs('relaas-documents.*') ||
                    request()->routeIs('relaas-parties.*') ||
                    request()->routeIs('relaas_akta.*') ||
                    request()->routeIs('relaas-logs.*') ||
                    request()->routeIs('ppat.laporan') ||
                    request()->routeIs('ppat.letters') ||
                    request()->routeIs('ppat.covernotes') ||
                    request()->routeIs('ppat.pic.*') ||
                    request()->is('pic_staff*') ||
                    request()->is('pic_documents*') ||
                    request()->is('pic_process*') ||
                    request()->is('pic_handovers*') ||
                    request()->routeIs('ppat.costs') ||
                    request()->routeIs('ppat.payments') ||
                    request()->is('notary_costs*') ||
                    request()->is('notary_payments*');

                $isProsesLain =
                    request()->routeIs('proses-lain.*') ||
                    request()->routeIs('proses-lain.transaksi') ||
                    request()->routeIs('proses-lain.progress') ||
                    request()->routeIs('proses-lain.pic.*') ||
                    request()->routeIs('proses-lain.costs') ||
                    request()->routeIs('proses-lain.payments');

                $isPic =
                    request()->routeIs('ppat.pic.*') ||
                    request()->is('pic_staff*') ||
                    request()->is('pic_documents*') ||
                    request()->is('pic_process*') ||
                    request()->is('pic_handovers*');

                $isBiaya =
                    request()->routeIs('ppat.costs') ||
                    request()->routeIs('ppat.payments') ||
                    request()->is('notary_costs*') ||
                    request()->is('notary_payments*');

                $isKonsultasi =
                    request()->routeIs('konsultasi.*') ||
                    request()->routeIs('clients.*') ||
                    request()->routeIs('consultation.*') ||
                    request()->routeIs('pic-progress.*') ||
                    request()->is('client-progress*');

                $user = auth()->user();
                $isPicUser = $user->access_code !== null && !session('access_all_menu');
            @endphp

            {{-- CONDITION 1: USER HAS ACCESS CODE & NOT ACCESS ALL MENU --}}
            @if ($user->access_code !== null && !session('access_all_menu'))
                
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}" class="nav-link {{ request()->is('dashboard*') ? 'active' : '' }}">
                        <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="ni ni-tv-2 text-primary text-sm opacity-10"></i>
                        </div>
                        <span class="nav-link-text ms-1">Dashboard</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('settings') }}" class="nav-link {{ request()->is('settings*') ? 'active' : '' }}">
                        <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="fas fa-lock me-1 text-dark text-sm opacity-10"></i>
                        </div>
                        <span class="nav-link-text ms-1">Fitur Notaris/PPAT</span>
                    </a>
                </li>

                <!-- HEADER SUB-MENU: MENU -->
                <li class="nav-item mt-3 d-flex align-items-center">
                    <div class="ps-4">
                        <i class="fa-solid fa-bars" style="color: #f4645f;"></i>
                    </div>
                    <h6 class="ms-2 text-uppercase text-xs font-weight-bolder opacity-6 mb-1">Menu</h6>
                </li>

                <li class="nav-item">
                    <a href="{{ $isPicUser ? route('consultation.index') : route('konsultasi.index') }}"
                        class="nav-link {{ $isKonsultasi ? 'active' : '' }}">
                        <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="fa-solid fa-headset text-dark text-sm opacity-10"></i>
                        </div>
                        <span class="nav-link-text ms-1">Konsultasi</span>
                    </a>
                </li>

                <!-- HEADER SUB-MENU: BACK OFFICE -->
                <li class="nav-item mt-3 d-flex align-items-center">
                    <div class="ps-4">
                        <i class="fa-solid fa-gears" style="color: #f4645f;"></i>
                    </div>
                    <h6 class="ms-2 text-uppercase text-xs font-weight-bolder opacity-6 mb-1">Back Office</h6>
                </li>
                
                <li class="nav-item">
                    <a href="{{ route('warkah.selectClient') }}" class="nav-link {{ request()->is('warkah*') ? 'active' : '' }}">
                        <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="bi bi-folder-fill text-dark text-sm opacity-10 pb-0"></i>
                        </div>
                        <span class="nav-link-text ms-1 mt-2">Warkah</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="{{ route('laporan-akta.index') }}" class="nav-link {{ request()->is('laporan-akta*') ? 'active' : '' }}">
                        <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="fa-solid fa-chart-bar text-dark text-sm opacity-10 pb-0"></i>
                        </div>
                        <span class="nav-link-text ms-1 mt-2">Laporan Akta</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="{{ route('notary-letters.index') }}" class="nav-link {{ request()->is('notary-letters*') ? 'active' : '' }}">
                        <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="fa-solid fa-envelope-open-text text-dark text-sm opacity-10 pb-0"></i>
                        </div>
                        <span class="nav-link-text ms-1 mt-2">Surat Keluar</span>
                    </a>
                </li>
                
                <!-- DROPDOWN MENU: PIC -->
                <li class="nav-item">
                <a data-bs-toggle="collapse" href="#collapsePic" role="button" aria-expanded="false" aria-controls="collapsePic" class="nav-link">
                    <div class="d-flex align-items-center justify-content-between w-100">
                        <div class="d-flex align-items-center">
                            <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                                <i class="fa-solid fa-user-tie text-dark text-sm opacity-10 pb-0"></i>
                            </div>
                            <span class="nav-link-text text-sm">PIC</span>
                        </div>
                        </div>
                </a>

                    <div class="collapse {{ request()->is('pic_staff*') || request()->is('pic_documents*') || request()->is('pic_process*') || request()->is('pic_handovers*') ? 'show' : '' }}" id="collapsePic">
                        <ul class="nav nav-collapse mb-0 pb-0 d-flex flex-column">
                            <li class="w-100">
                                <a href="{{ route('pic_staff.index') }}" class="nav-link {{ request()->is('pic_staff*') ? 'active' : '' }}">
                                    <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                                        <i class="fa-solid fa-users-gear text-dark text-sm opacity-10 pb-0"></i>
                                    </div>
                                    <span class="nav-link-text ms-1 mt-2">Staff</span>
                                </a>
                            </li>
                            <li class="w-100">
                                <a href="{{ route('pic_documents.index') }}" class="nav-link {{ request()->is('pic_documents*') ? 'active' : '' }}">
                                    <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                                        <i class="fa-solid fa-file-lines text-dark text-sm opacity-10 pb-0"></i>
                                    </div>
                                    <span class="nav-link-text ms-1 mt-2">Dokumen</span>
                                </a>
                            </li>
                            <li class="w-100">
                                <a href="{{ route('pic_process.index') }}" class="nav-link {{ request()->is('pic_process*') ? 'active' : '' }}">
                                    <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                                        <i class="fa-solid fa-gears text-dark text-sm opacity-10 pb-0"></i>
                                    </div>
                                    <span class="nav-link-text ms-1 mt-2">Proses Pengurusan</span>
                                </a>
                            </li>
                            <li class="w-100">
                                <a href="{{ route('pic_handovers.index') }}" class="nav-link {{ request()->is('pic_handovers*') ? 'active' : '' }}">
                                    <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                                        <i class="fa-solid fa-envelope-open-text text-dark text-sm opacity-10 pb-0"></i>
                                    </div>
                                    <span class="nav-link-text ms-1 mt-2">Surat Terima Dokumen</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li class="nav-item">
                    <a href="{{ route('report-progress.index') }}" class="nav-link {{ request()->is('report-progress*') ? 'active' : '' }}">
                        <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="fa-solid fa-envelope-open-text text-dark text-sm opacity-10 pb-0"></i>
                        </div>
                        <span class="nav-link-text ms-1 mt-2">Laporan Pengurusan</span>
                    </a>
                </li>

                <!-- HEADER SUB-MENU: PROSES LAIN -->     
                <li class="nav-item">
                    <a href="{{ $isPicUser
                        ? route('proses-lain.transaksi')
                        : route('proses-lain.index') }}"
                    class="nav-link {{ $isProsesLain ? 'active' : '' }}">
                        <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="fa-solid fa-gears text-dark text-sm opacity-10"></i>
                        </div>
                        <span class="nav-link-text ms-1">Proses Lain</span>
                    </a>
                </li>
                
            @endif

            {{-- CONDITION 2: USER ACCESS CODE IS NULL --}}
            @if (is_null($user->access_code))
                <li class="nav-item">
                    <a href="{{ route('proses-lain.index') }}"
                    class="nav-link {{ $isProsesLain ? 'active' : '' }}">
                        <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="fa-solid fa-gears text-dark text-sm opacity-10"></i>
                        </div>
                        <span class="nav-link-text ms-1">Proses Lain</span>
                    </a>
                </li>

            {{-- CONDITION 3: ACCESS ALL MENU --}}
            @elseif (session('access_all_menu'))
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}" class="nav-link {{ request()->is('dashboard*') ? 'active' : '' }}">
                        <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="ni ni-tv-2 text-primary text-sm opacity-10"></i>
                        </div>
                        <span class="nav-link-text ms-1">Dashboard</span>
                    </a>
                </li>
                
                <li class="nav-item mt-3 d-flex align-items-center">
                    <div class="ps-4">
                        <i class="fa-solid fa-bars" style="color: #f4645f;"></i>
                    </div>
                    <h6 class="ms-2 text-uppercase text-xs font-weight-bolder opacity-6 mb-1">Menu</h6>
                </li>

                <li class="nav-item">
                    <a href="{{ route('profile') }}" class="nav-link {{ request()->is('profile*') ? 'active' : '' }}">
                        <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="ni ni-single-02 text-dark text-sm opacity-10"></i>
                        </div>
                        <span class="nav-link-text ms-1">Profile</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('subscriptions') }}" class="nav-link {{ request()->is('subscriptions*') ? 'active' : '' }}">
                        <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="bi bi-calendar-event-fill text-dark text-sm opacity-10 pb-0"></i>
                        </div>
                        <span class="nav-link-text ms-1 mt-2">Subscriptions</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('konsultasi.index') }}"
                        class="nav-link {{ $isKonsultasi ? 'active' : '' }}">
                        <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="fa-solid fa-headset text-dark text-sm opacity-10"></i>
                        </div>
                        <span class="nav-link-text ms-1">Konsultasi</span>
                    </a>
                </li>

                <!-- BACK OFFICE -->
                <li class="nav-item mt-3 d-flex align-items-center">
                    <div class="ps-4">
                        <i class="fa-solid fa-gears" style="color: #f4645f;"></i>
                    </div>
                    <h6 class="ms-2 text-uppercase text-xs font-weight-bolder opacity-6 mb-1">Back Office</h6>
                </li>
                <li class="nav-item">
                    <a href="{{ route('warkah.selectClient') }}"
                        class="nav-link {{ request()->is('warkah*') ? 'active' : '' }}">
                        <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="bi bi-folder-fill text-dark text-sm opacity-10 pb-0"></i>
                        </div>
                        <span class="nav-link-text ms-1 mt-2">Warkah</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('notaris.index') }}"
                        class="nav-link {{ $isNotaris ? 'active' : '' }}">
                        <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="fa-solid fa-handshake text-dark text-sm opacity-10"></i>
                        </div>
                        <span class="nav-link-text ms-1">Notaris</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('ppat.index') }}"
                        class="nav-link {{ $isPpat ? 'active' : '' }}">
                        <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="fa-solid fa-scroll text-dark text-sm opacity-10"></i>
                        </div>
                        <span class="nav-link-text ms-1">PPAT</span>
                    </a>
                </li>

                <!-- LAPORAN & BACKUP -->
                <li class="nav-item">
                    <a href="{{ route('report-payment.index') }}"
                        class="nav-link {{ request()->is('report-payment*') ? 'active' : '' }}">
                        <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="fa-solid fa-envelope-open-text text-dark text-sm opacity-10 pb-0"></i>
                        </div>
                        <span class="nav-link-text ms-1 mt-2">Laporan Pembayaran</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('report-progress.index') }}"
                        class="nav-link {{ request()->is('report-progress*') ? 'active' : '' }}">
                        <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="fa-solid fa-envelope-open-text text-dark text-sm opacity-10 pb-0"></i>
                        </div>
                        <span class="nav-link-text ms-1 mt-2">Laporan Pengurusan</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('backup-restore.index') }}"
                        class="nav-link {{ request()->is('backup-restore*') ? 'active' : '' }}">
                        <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="fa-solid fa-envelope-open-text text-dark text-sm opacity-10 pb-0"></i>
                        </div>
                        <span class="nav-link-text ms-1 mt-2">Backup & Restore Data</span>
                    </a>
                </li>

                <!-- PROSES LAIN -->
                <li class="nav-item">
                    <a href="{{ route('proses-lain.index') }}"
                    class="nav-link {{ $isProsesLain ? 'active' : '' }}">
                        <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="fa-solid fa-gears text-dark text-sm opacity-10"></i>
                        </div>
                        <span class="nav-link-text ms-1">Proses Lain</span>
                    </a>
                </li>

            @endif
        </ul>
    </div>
</aside>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const sidebarMenu = document.getElementById("sidenav-collapse-main");

        if (!sidebarMenu) return;

        sidebarMenu.style.visibility = "hidden";

        requestAnimationFrame(() => {
            requestAnimationFrame(() => {
                const savedScroll = sessionStorage.getItem("sidebarScroll");

                if (savedScroll !== null) {
                    sidebarMenu.scrollTop = parseInt(savedScroll, 10);
                }

                sidebarMenu.style.visibility = "visible";
            });
        });

        sidebarMenu.addEventListener("scroll", function () {
            sessionStorage.setItem("sidebarScroll", sidebarMenu.scrollTop);
        });
    });
</script>