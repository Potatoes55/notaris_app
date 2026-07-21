<div class="card mb-4 card-menu-notaris">
    <div class="card-body">
        <div class="d-flex flex-wrap gap-2 menu-ppat">

            {{-- Menu PPAT --}}
            <div class="dropdown">
                <button class="btn {{ request()->routeIs(['relaas-types.*', 'relaas-aktas.*', 'relaas-documents.*', 'relaas-parties.*', 'relaas_akta.*', 'relaas-logs.*']) ? 'btn-primary active-menu' : 'btn-outline-secondary' }} dropdown-toggle"
                    type="button" data-bs-toggle="dropdown">
                    <i class="fa-solid fa-scroll me-2"></i>
                    Menu PPAT
                </button>

                <ul class="dropdown-menu">
                    <li>
                        <a class="dropdown-item" href="{{ route('relaas-types.index') }}">
                            <i class="fa-solid fa-layer-group me-2"></i> Jenis Akta
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('relaas-aktas.selectClient') }}">
                            <i class="fa-solid fa-exchange-alt me-2"></i> Transaksi Akta
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('relaas_akta.indexNumber') }}">
                            <i class="fa-solid fa-hashtag me-2"></i> Penomoran
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('relaas-parties.index') }}">
                            <i class="fa-solid fa-user-group me-2"></i> Pihak Akta
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('relaas-documents.index') }}">
                            <i class="fa-solid fa-file-contract me-2"></i> Dokumen
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('relaas-logs.index') }}">
                            <i class="fa-solid fa-clock-rotate-left me-2"></i> Log
                        </a>
                    </li>
                </ul>
            </div>

            {{-- Laporan --}}
            <a href="{{ route('ppat.laporan') }}"
                class="btn {{ request()->routeIs('ppat.laporan') ? 'btn-primary active-menu' : 'btn-outline-secondary' }}">
                <i class="fa-solid fa-chart-column me-2"></i> Laporan PPAT
            </a>

            {{-- Surat Masuk PPAT --}}
            <a href="{{ route('ppat.letters.incoming.index') }}"
            class="btn {{ request()->routeIs('ppat.letters.incoming.*') ? 'btn-primary active-menu' : 'btn-outline-secondary' }}">
                <i class="fa-solid fa-envelope me-2"></i> Surat Masuk
            </a>

            {{-- Surat Keluar PPAT --}}
            <a href="{{ route('ppat.letters') }}"
            class="btn {{ request()->routeIs('ppat.letters*') && !request()->routeIs('ppat.letters.incoming.*') ? 'btn-primary active-menu' : 'btn-outline-secondary' }}">
                <i class="fa-solid fa-envelope-open-text me-2"></i> Surat Keluar PPAT
            </a>

            {{-- Covernote --}}
            <a href="{{ route('ppat.covernotes') }}"
                class="btn {{ request()->routeIs('ppat.covernotes') ? 'btn-primary active-menu' : 'btn-outline-secondary' }}">
                <i class="fa-solid fa-file-signature me-2"></i> Covernote PPAT
            </a>

            {{-- PIC --}}
            <div class="dropdown">
                <button class="btn {{ request()->routeIs('ppat.pic.*') ? 'btn-primary active-menu' : 'btn-outline-secondary' }} dropdown-toggle"
                    type="button" data-bs-toggle="dropdown">
                    <i class="fa-solid fa-user-tie me-2"></i> PIC
                </button>

                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ route('ppat.pic.staff') }}"><i class="fa-solid fa-users-gear me-2"></i> Staff</a></li>
                    <li><a class="dropdown-item" href="{{ route('ppat.pic.documents') }}"><i class="fa-solid fa-file-lines me-2"></i> Dokumen</a></li>
                    <li><a class="dropdown-item" href="{{ route('ppat.pic.process') }}"><i class="fa-solid fa-gears me-2"></i> Proses Pengurusan</a></li>
                    <li><a class="dropdown-item" href="{{ route('ppat.pic.handovers') }}"><i class="fa-solid fa-envelope-open-text me-2"></i> Surat Terima Dokumen</a></li>
                </ul>
            </div>

            {{-- Biaya --}}
            <div class="dropdown">
                <button class="btn {{ request()->routeIs(['ppat.costs', 'ppat.payments']) ? 'btn-primary active-menu' : 'btn-outline-secondary' }} dropdown-toggle"
                    type="button" data-bs-toggle="dropdown">
                    <i class="fa-solid fa-sack-dollar me-2"></i> Biaya
                </button>

                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ route('ppat.costs') }}"><i class="fa-solid fa-file-invoice-dollar me-2"></i> Total Biaya</a></li>
                    <li><a class="dropdown-item" href="{{ route('ppat.payments') }}"><i class="fa-solid fa-credit-card me-2"></i> Pembayaran</a></li>
                </ul>
            </div>

        </div>
    </div>
</div>