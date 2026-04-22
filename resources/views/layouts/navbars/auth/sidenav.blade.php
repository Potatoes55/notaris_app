<aside
    class="sidenav bg-white navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-4 h-100"
    id="sidenav-main" style="overflow-y:auto; height: 100vh">
  
    <div class="sidenav-header">
       
        <i class="fas fa-times p-1 cursor-pointer text-secondary opacity-5 position-absolute end-0 top-0 d-none d-xl-none"
            aria-hidden="true" id="iconSidenav"></i>
        <div class="d-flex  justify-content-center mt-3 gap-2 align-items-center">
            <a class="navbar-brand m-0 p-1" href="{{ route('dashboard') }}" target="_blank">
                <div class="d-flex align-items-center justify-content-center rounded-circle"
                    style="width: 60px; height: 60px;">
                    <div class=" d-flex align-items-center justify-content-center rounded-circle"
                        style="width: 60px; height: 60px;">
                        <img src="{{ auth()->user()->notaris && auth()->user()->notaris->image
                            ? (filter_var(auth()->user()->notaris->image, FILTER_VALIDATE_URL)
                                ? auth()->user()->notaris->image
                                : asset('storage/' . auth()->user()->notaris->image))
                            : asset('img/img_profile.png') }}"
                            alt="main_logo"
                            style="width: 60px; height: 60px; object-fit:contain; object-position: center">
                    </div>
                    
                </div>
            </a>
           
            <div>
                <h6 class="mb-0">Hi, {{ auth()->user()->username }}</h6>
                <p class="mb-0">{{ auth()->user()->email }}</p>
            </div>
        </div>
    </div>
    <hr class="horizontal-dark mt-0">
    {{-- <div class="collapse navbar-collapse" style="height: 100% !important; overflow-y: auto !important;"> --}}
    <div class="collapse navbar-collapse  overflow-hidden">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->is('dashboard*') ? 'active' : '' }}">
                    <div
                        class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="ni ni-tv-2 text-primary text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Dashboard</span>
                </a>
            </li>
            @php
                $user = auth()->user();
            @endphp
            @if ($user->access_code !== null)
                <li class="nav-item mt-3 d-flex align-items-center">
                    <div class="ps-4">
                        <i class="fa-solid fa-bars" style="color: #f4645f;"></i>
                    </div>
                    <h6 class=" ms-2 text-uppercase text-xs font-weight-bolder opacity-6 mb-1">Menu</h6>
                </li>

                <li class="nav-item">
                    <a href="{{ route('settings') }}" class="nav-link {{ request()->is('settings*') ? 'active' : '' }}">
                        <div
                            class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="ni ni-single-02 text-dark text-sm opacity-10"></i>
                        </div>
                        <span class="nav-link-text ms-1">Setting</span>
                    </a>
                </li>
            @endif

            {{-- @php
                $accessCode = auth()->user()->access_code;
            @endphp
            @if ($accessCode === null && session('access_all_menu')) --}}


            @if (is_null($user->access_code))
                <li class="nav-item mt-3 d-flex align-items-center">
                    <div class="ps-4">
                        <i class="fa-solid fa-envelope-open-text text-dark text-sm opacity-10 pb-0"></i>
                    </div>
                    <h6 class=" ms-2 text-uppercase text-xs font-weight-bolder opacity-6 mb-1">Proses Lain</h6>
                </li>

                <li class="nav-item">
                    <a href="{{ route('proses-lain-transaksi.index') }}"
                        class="nav-link {{ request()->is('proses-lain-transaksi*') ? 'active' : '' }}">
                        <div
                            class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="fa-solid fa-envelope-open-text text-dark text-sm opacity-10 pb-0"></i>
                        </div>
                        <span class="nav-link-text ms-1 mt-2">Transaksi</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('proses-lain-pic.index') }}"
                        class="nav-link {{ request()->is('proses-lain-pic*') ? 'active' : '' }}">
                        <div
                            class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="fa-solid fa-envelope-open-text text-dark text-sm opacity-10 pb-0"></i>
                        </div>
                        <span class="nav-link-text ms-1 mt-2">PIC</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('proses-lain-progress.index') }}"
                        class="nav-link {{ request()->is('proses-lain-progress*') ? 'active' : '' }}">
                        <div
                            class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="fa-solid fa-envelope-open-text text-dark text-sm opacity-10 pb-0"></i>
                        </div>
                        <span class="nav-link-text ms-1 mt-2">Progress</span>
                    </a>
                </li>
            @elseif (session('access_all_menu'))
                <li class="nav-item">
                    <a href="{{ route('profile') }}" class="nav-link {{ request()->is('profile*') ? 'active' : '' }}">
                        <div
                            class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="ni ni-single-02 text-dark text-sm opacity-10"></i>
                        </div>
                        <span class="nav-link-text ms-1">Profile</span>
                    </a>

                    <a href="{{ route('subscriptions') }}"
                        class="nav-link {{ request()->is('subscriptions*') ? 'active' : '' }}">
                        <div
                            class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="bi bi-calendar-event-fill text-dark text-sm opacity-10 pb-0"></i>
                        </div>
                        <span class="nav-link-text ms-1 mt-2">Subscriptions</span>
                    </a>

                    <a href="{{ route('documents.index') }}"
                        class="nav-link {{ request()->is('documents*') ? 'active' : '' }}">
                        <div
                            class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="bi bi-folder-fill text-dark text-sm opacity-10 pb-0"></i>
                        </div>
                        <span class="nav-link-text ms-1 mt-2">Jenis Warkah</span>
                    </a>
                </li>
                <li class="nav-item mt-3 d-flex align-items-center">
                    <div class="ps-4">
                        <i class="fa-solid fa-headset" style="color: #f4645f;"></i>
                    </div>
                    <h6 class=" ms-2 text-uppercase text-xs font-weight-bolder opacity-6 mb-1">CS</h6>
                </li>
                <li class="nav-item">
                    <a href="{{ route('clients.index') }}"
                        class="nav-link {{ request()->is('clients*') ? 'active' : '' }}">
                        <div
                            class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="bi bi-person-add text-dark text-sm opacity-10 pb-0"></i>
                        </div>
                        <span class="nav-link-text ms-1 mt-2">Klien</span>

                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('consultation.index') }}"
                        class="nav-link {{ request()->is('consultation*') ? 'active' : '' }}">
                        <div
                            class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="bi bi-headset text-dark text-sm opacity-10 pb-0"></i>
                        </div>
                        <span class="nav-link-text ms-1 mt-2">Konsultasi Klien</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('pic-progress.indexProcess') }}"
                        class="nav-link {{ request()->is('client-progress*') ? 'active' : '' }}">
                        <div
                            class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="fa-solid fa-calendar-days text-dark text-sm opacity-10 pb-0"></i>
                        </div>
                        <span class="nav-link-text ms-1 mt-2">Proses Pengurusan</span>
                    </a>
                </li>

                <li class="nav-item mt-3 d-flex align-items-center">
                    <div class="ps-4">
                        <i class="fa-solid fa-gears " style="color: #f4645f;"></i>
                    </div>
                    <h6 class=" ms-2 text-uppercase text-xs font-weight-bolder opacity-6 mb-1">Back Office</h6>
                </li>
                <li class="nav-item">
                    <a href="{{ route('warkah.selectClient') }}"
                        class="nav-link {{ request()->is('warkah*') ? 'active' : '' }}">
                        <div
                            class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="bi bi-folder-fill text-dark text-sm opacity-10 pb-0"></i>
                        </div>
                        <span class="nav-link-text ms-1 mt-2">Warkah</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#tablePartijAkta" data-bs-toggle="collapse" class="mb-0">
                        <div class="d-flex align-items-center justify-content-between px-4 py-2">
                            <!-- Kiri -->
                            <div class="d-flex align-items-center">
                                <div
                                    class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                                    <i class="fa-solid fa-handshake text-dark text-sm opacity-10 pb-0"></i>
                                </div>
                                <span class="nav-link-text text-sm">Notaris</span>
                            </div>
                            <!-- Kanan -->
                            <i class="bi bi-caret-down-fill"></i>
                        </div>
                    </a>
                    <div class="collapse {{ request()->is('akta-*') ? 'show' : '' }}" id="tablePartijAkta">
                        <ul class="nav nav-collapse mb-0 pb-0 d-flex flex-column  justity-content-between px-3">
                            <li class="p-0">
                                <a href="{{ route('akta-types.index') }}"
                                    class="nav-link {{ request()->is('akta-types*') ? 'active' : '' }}">
                                    <div
                                        class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                                        <i class="fa-solid fa-layer-group text-dark text-sm opacity-10 pb-0"></i>
                                    </div>
                                    <span class="nav-link-text ms-1 mt-2">Jenis Akta</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('akta-transactions.selectClient') }}"
                                    class="nav-link {{ request()->is('akta-transactions*') ? 'active' : '' }}">
                                    <div
                                        class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                                        <i class="fa-solid fa-exchange-alt text-dark text-sm opacity-10 pb-0"></i>
                                    </div>
                                    <span class="nav-link-text ms-1 mt-2">Transaksi Akta</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('akta-documents.index') }}"
                                    class="nav-link {{ request()->is('akta-documents*') ? 'active' : '' }}">
                                    <div
                                        class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                                        <i class="fa-solid fa-file-contract text-dark text-sm opacity-10 pb-0"></i>
                                    </div>
                                    <span class="nav-link-text ms-1 mt-2">Dokumen Akta</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('akta-parties.index') }}"
                                    class="nav-link {{ request()->is('akta-parties*') ? 'active' : '' }}">
                                    <div
                                        class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                                        <i class="fa-solid fa-user-group text-dark text-sm opacity-10 pb-0"></i>
                                    </div>
                                    <span class="nav-link-text ms-1 mt-2">Pihak Akta</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('akta_number.index') }}"
                                    class="nav-link {{ request()->is('akta-number*') ? 'active' : '' }}">
                                    <div
                                        class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                                        <i class="fa-solid fa-hashtag text-dark text-sm opacity-10 pb-0"></i>
                                    </div>
                                    <span class="nav-link-text ms-1 mt-2">Penomoran Akta</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('akta-logs.index') }}"
                                    class="nav-link {{ request()->is('akta-logs*') ? 'active' : '' }}">
                                    <div
                                        class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                                        <i class="fa-solid fa-clock-rotate-left text-dark text-sm opacity-10 pb-0"></i>
                                    </div>
                                    <span class="nav-link-text ms-1 mt-2">Logs Akta</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item">
                    <a data-bs-toggle="collapse" href="#collapseRelaasAkta" role="button" aria-expanded="false"
                        aria-controls="collapseRelaasAkta">
                        <div class="d-flex align-items-center justify-content-between px-4 py-2">
                            <!-- Kiri -->
                            <div class="d-flex align-items-center">
                                <div
                                    class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                                    <i class="fa-solid fa-scroll text-dark text-sm opacity-10 pb-0"></i>
                                </div>
                                <span class="nav-link-text text-sm">PPAT</span>
                            </div>
                            <!-- Kanan -->
                            <i class="bi bi-caret-down-fill"></i>
                        </div>
                    </a>

                    <div class="collapse {{ request()->is('relaas-*') ? 'show' : '' }}" id="collapseRelaasAkta">
                        <ul class="nav nav-collapse mb-0 pb-0 d-flex flex-column  justity-content-between px-3">
                            <li class="p-0">
                                <a href="{{ route('relaas-types.index') }}"
                                    class="nav-link {{ request()->is('relaas-types*') ? 'active' : '' }}">
                                    <div
                                        class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                                        <i class="fa-solid fa-layer-group text-dark text-sm opacity-10 pb-0"></i>
                                    </div>
                                    <span class="nav-link-text ms-1 mt-2">Jenis Akta</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('relaas-aktas.selectClient') }}"
                                    class="nav-link {{ request()->is('relaas-aktas*') ? 'active' : '' }}">
                                    <div
                                        class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                                        <i class="fa-solid fa-exchange-alt text-dark text-sm opacity-10 pb-0"></i>
                                    </div>
                                    <span class="nav-link-text ms-1 mt-2">Transaksi Akta</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('relaas-documents.index') }}"
                                    class="nav-link {{ request()->is('relaas-document*') ? 'active' : '' }}">
                                    <div
                                        class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                                        <i class="fa-solid fa-file-contract text-dark text-sm opacity-10 pb-0"></i>
                                    </div>
                                    <span class="nav-link-text ms-1 mt-2">Dokumen Akta</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('relaas-parties.index') }}"
                                    class="nav-link {{ request()->is('relaas-parties*') ? 'active' : '' }}">
                                    <div
                                        class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                                        <i class="fa-solid fa-user-group text-dark text-sm opacity-10 pb-0"></i>
                                    </div>
                                    <span class="nav-link-text ms-1 mt-2">Pihak Akta</span>
                                </a>
                            </li>

                            <li>
                                <a href="{{ route('relaas_akta.indexNumber') }}"
                                    class="nav-link {{ request()->is('relaas-number*') ? 'active' : '' }}">
                                    <div
                                        class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                                        <i class="fa-solid fa-hashtag text-dark text-sm opacity-10 pb-0"></i>
                                    </div>
                                    <span class="nav-link-text ms-1 mt-2">Penomoran Akta</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('relaas-logs.index') }}"
                                    class="nav-link {{ request()->is('relaas-logs*') ? 'active' : '' }}">
                                    <div
                                        class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                                        <i class="fa-solid fa-clock-rotate-left text-dark text-sm opacity-10 pb-0"></i>
                                    </div>
                                    <span class="nav-link-text ms-1 mt-2">Logs Akta</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item">
                    <a href="{{ route('notary-legalisasi.index') }}"
                        class="nav-link {{ request()->is('notary-legalisasi*') ? 'active' : '' }}">
                        <div
                            class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="fa-solid fa-stamp text-dark text-sm opacity-10 pb-0"></i>
                        </div>
                        <span class="nav-link-text ms-1 mt-2">Legalisasi</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('notary-waarmerking.index') }}"
                        class="nav-link {{ request()->is('notary-waarmerking*') ? 'active' : '' }}">
                        <div
                            class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="fa-solid fa-file-signature text-dark text-sm opacity-10 pb-0"></i>
                        </div>
                        <span class="nav-link-text ms-1 mt-2">Waarmarking</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('laporan-akta.index') }}"
                        class="nav-link {{ request()->is('laporan-akta*') ? 'active' : '' }}">
                        <div
                            class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="fa-solid fa-chart-bar text-dark text-sm opacity-10 pb-0"></i>
                        </div>
                        <span class="nav-link-text ms-1 mt-2">Laporan Akta</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('notary-letters.index') }}"
                        class="nav-link {{ request()->is('notary-letters*') ? 'active' : '' }}">
                        <div
                            class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="fa-solid fa-envelope-open-text text-dark text-sm opacity-10 pb-0"></i>
                        </div>
                        <span class="nav-link-text ms-1 mt-2">Surat Keluar</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a data-bs-toggle="collapse" href="#collapsePic" role="button" aria-expanded="false"
                        aria-controls="collapsePic">
                        <div class="d-flex align-items-center justify-content-between px-4 py-2">
                            <!-- Kiri -->
                            <div class="d-flex align-items-center">
                                <div
                                    class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                                    <i class="fa-solid fa-user-tie text-dark text-sm opacity-10 pb-0"></i>
                                </div>
                                <span class="nav-link-text text-sm">PIC</span>
                            </div>
                            <!-- Kanan -->
                            <i class="bi bi-caret-down-fill"></i>
                        </div>
                    </a>

                    <div class="collapse {{ request()->is('pic_staff*') || request()->is('pic_documents*') || request()->is('pic_process*') || request()->is('pic_handovers*') ? 'show' : '' }}"
                        id="collapsePic">
                        <ul class="nav nav-collapse mb-0 pb-0 d-flex flex-column  justity-content-between px-3">
                            <li>
                                <a href="{{ route('pic_staff.index') }}"
                                    class="nav-link {{ request()->is('pic_staff*') ? 'active' : '' }}">
                                    <div
                                        class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                                        <i class="fa-solid fa-users-gear text-dark text-sm opacity-10 pb-0"></i>
                                    </div>
                                    <span class="nav-link-text ms-1 mt-2">Staff</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('pic_documents.index') }}"
                                    class="nav-link {{ request()->is('pic_documents*') ? 'active' : '' }}">
                                    <div
                                        class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                                        <i class="fa-solid fa-file-lines text-dark text-sm opacity-10 pb-0"></i>
                                    </div>
                                    <span class="nav-link-text ms-1 mt-2">Dokumen</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('pic_process.index') }}"
                                    class="nav-link {{ request()->is('pic_process*') ? 'active' : '' }}">
                                    <div
                                        class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                                        <i class="fa-solid fa-gears text-dark text-sm opacity-10 pb-0"></i>
                                    </div>
                                    <span class="nav-link-text ms-1 mt-2">Proses Pengurusan</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('pic_handovers.index') }}"
                                    class="nav-link {{ request()->is('pic_handovers*') ? 'active' : '' }}">
                                    <div
                                        class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                                        <i
                                            class="fa-solid fa-envelope-open-text text-dark text-sm opacity-10 pb-0"></i>
                                    </div>
                                    <span class="nav-link-text ms-1 mt-2">Surat Terima Dokumen</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item">
                    <a data-bs-toggle="collapse" href="#collapseBiaya" role="button" aria-expanded="false"
                        aria-controls="collapseBiaya">
                        <div class="d-flex align-items-center justify-content-between px-4 py-2">
                            <!-- Kiri -->
                            <div class="d-flex align-items-center">
                                <div
                                    class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                                    <i class="fa-solid fa-sack-dollar text-dark text-sm opacity-10 pb-0"></i>
                                </div>
                                <span class="nav-link-text text-sm">Biaya</span>
                            </div>
                            <!-- Kanan -->
                            <i class="bi bi-caret-down-fill"></i>
                        </div>
                    </a>

                    <div class="collapse {{ request()->is('notary_costs*') || request()->is('notary_payments*') ? 'show' : '' }}"
                        id="collapseBiaya">
                        <ul class="nav nav-collapse mb-0 pb-0 d-flex flex-column  justity-content-between px-3">
                            <li>
                                <a href="{{ route('notary_costs.index') }}"
                                    class="nav-link {{ request()->is('notary_costs*') ? 'active' : '' }}">
                                    <div
                                        class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                                        <i
                                            class="fa-solid fa-file-invoice-dollar text-dark text-sm opacity-10 pb-0"></i>
                                    </div>
                                    <span class="nav-link-text ms-1 mt-2">Total Biaya</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('notary_payments.index') }}"
                                    class="nav-link {{ request()->is('notary_payments*') ? 'active' : '' }}">
                                    <div
                                        class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                                        <i class="fa-solid fa-credit-card text-dark text-sm opacity-10 pb-0"></i>
                                    </div>
                                    <span class="nav-link-text ms-1 mt-2">Pembayaran</span>
                                </a>
                            </li>
                        </ul>
                    </div>

                </li>
                <li class="nav-item">
                    <a href="{{ route('report-payment.index') }}"
                        class="nav-link {{ request()->is('report-payment*') ? 'active' : '' }}">
                        <div
                            class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="fa-solid fa-envelope-open-text text-dark text-sm opacity-10 pb-0"></i>
                        </div>
                        <span class="nav-link-text ms-1 mt-2">Laporan Pembayaran</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('report-progress.index') }}"
                        class="nav-link {{ request()->is('report-progress*') ? 'active' : '' }}">
                        <div
                            class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="fa-solid fa-envelope-open-text text-dark text-sm opacity-10 pb-0"></i>
                        </div>
                        <span class="nav-link-text ms-1 mt-2">Laporan Pengurusan</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('backup-restore.index') }}"
                        class="nav-link {{ request()->is('backup-restore*') ? 'active' : '' }}">
                        <div
                            class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="fa-solid fa-envelope-open-text text-dark text-sm opacity-10 pb-0"></i>
                        </div>
                        <span class="nav-link-text ms-1 mt-2">Backup & Restore Data</span>
                    </a>
                </li>
                <li class="nav-item mt-3 d-flex align-items-center">
                    <div class="ps-4">
                        <i class="fa-solid fa-gears" style="color: #f4645f;"></i>
                    </div>
                    <h6 class=" ms-2 text-uppercase text-xs font-weight-bolder opacity-6 mb-1">Proses Lain</h6>
                </li>
                <li class="nav-item">
                    <a href="{{ route('proses-lain-transaksi.index') }}"
                        class="nav-link {{ request()->is('proses-lain-transaksi*') ? 'active' : '' }}">
                        <div
                            class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="fa-solid fa-envelope-open-text text-dark text-sm opacity-10 pb-0"></i>
                        </div>
                        <span class="nav-link-text ms-1 mt-2">Transaksi</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('proses-lain-pic.index') }}"
                        class="nav-link {{ request()->is('proses-lain-pic*') ? 'active' : '' }}">
                        <div
                            class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="fa-solid fa-envelope-open-text text-dark text-sm opacity-10 pb-0"></i>
                        </div>
                        <span class="nav-link-text ms-1 mt-2">PIC</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('proses-lain-progress.index') }}"
                        class="nav-link {{ request()->is('proses-lain-progress*') ? 'active' : '' }}">
                        <div
                            class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="fa-solid fa-envelope-open-text text-dark text-sm opacity-10 pb-0"></i>
                        </div>
                        <span class="nav-link-text ms-1 mt-2">Progress</span>
                    </a>
                </li>
            @endif
        </ul>
    </div>


</aside>

 
