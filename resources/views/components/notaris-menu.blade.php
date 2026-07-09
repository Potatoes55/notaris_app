<div class="card mb-4 card-menu-notaris">
    <div class="card-body">
        <div class="d-flex flex-wrap gap-2 menu-notaris">

            {{-- Menu Notaris --}}
            <div class="dropdown">
                <button class="btn {{ request()->routeIs(['akta-types.*', 'akta-transactions.*', 'akta-documents.*', 'akta-parties.*', 'akta_number.*', 'akta-logs.*']) ? 'btn-primary active-menu' : 'btn-outline-secondary' }} dropdown-toggle"
                        type="button" data-bs-toggle="dropdown">
                    <i class="fa-solid fa-scroll me-2"></i>
                    Menu Notaris
                </button>

                <ul class="dropdown-menu">
                    <li>
                        <a class="dropdown-item" href="{{ route('akta-types.index') }}">
                            <i class="fa-solid fa-layer-group me-2"></i> Jenis Akta
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('akta-transactions.selectClient') }}">
                            <i class="fa-solid fa-exchange-alt me-2"></i> Transaksi Akta
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('akta_number.index') }}">
                            <i class="fa-solid fa-hashtag me-2"></i> Penomoran
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('akta-parties.index') }}">
                            <i class="fa-solid fa-user-group me-2"></i> Pihak Akta
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('akta-documents.index') }}">
                            <i class="fa-solid fa-file-contract me-2"></i> Dokumen
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('akta-logs.index') }}">
                            <i class="fa-solid fa-clock-rotate-left me-2"></i> Log
                        </a>
                    </li>
                </ul>
            </div>

            {{-- Laporan --}}
            <a href="{{ route('notaris.laporan') }}"
               class="btn {{ request()->routeIs('notaris.laporan') ? 'btn-primary active-menu' : 'btn-outline-secondary' }}">
                <i class="fa-solid fa-chart-column me-2"></i> Laporan
            </a>

            {{-- PIC (PERBAIKAN KONDISI ACTIVE & HREF ROUTE) --}}
            <div class="dropdown">
                <button class="btn {{ request()->routeIs('notaris.pic.*') ? 'btn-primary active-menu' : 'btn-outline-secondary' }} dropdown-toggle"
                        type="button" data-bs-toggle="dropdown">
                    <i class="fa-solid fa-user-tie me-2"></i> PIC
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ route('notaris.pic.staff') }}"><i class="fa-solid fa-users-gear me-2"></i> Staff</a></li>
                    <li><a class="dropdown-item" href="{{ route('notaris.pic.documents') }}"><i class="fa-solid fa-file-lines me-2"></i> Dokumen</a></li>
                    <li><a class="dropdown-item" href="{{ route('notaris.pic.process') }}"><i class="fa-solid fa-gears me-2"></i> Proses Pengurusan</a></li>
                    <li><a class="dropdown-item" href="{{ route('notaris.pic.handovers') }}"><i class="fa-solid fa-envelope-open-text me-2"></i> Surat Terima Dokumen</a></li>
                </ul>
            </div>

            {{-- Biaya (PERBAIKAN KONDISI ACTIVE & HREF ROUTE) --}}
            <div class="dropdown">
                <button class="btn {{ request()->routeIs(['notaris.costs', 'notaris.payments']) ? 'btn-primary active-menu' : 'btn-outline-secondary' }} dropdown-toggle"
                        type="button" data-bs-toggle="dropdown">
                    <i class="fa-solid fa-sack-dollar me-2"></i> Biaya
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ route('notaris.costs') }}"><i class="fa-solid fa-file-invoice-dollar me-2"></i> Total Biaya</a></li>
                    <li><a class="dropdown-item" href="{{ route('notaris.payments') }}"><i class="fa-solid fa-credit-card me-2"></i> Pembayaran</a></li>
                </ul>
            </div>
            
            {{-- Surat Keluar --}}
            <a href="{{ route('notaris.letters') }}"
               class="btn {{ request()->routeIs('notaris.letters') ? 'btn-primary active-menu' : 'btn-outline-secondary' }}">
                <i class="fa-solid fa-envelope-open-text me-2"></i> Surat Keluar
            </a>

            {{-- Covernote --}}
            <a href="{{ route('notaris.covernotes') }}"
               class="btn {{ request()->routeIs('notaris.covernotes') ? 'btn-primary active-menu' : 'btn-outline-secondary' }}">
                <i class="fa-solid fa-file-signature me-2"></i> Covernote
            </a>

            <a href="{{ route('notary-legalisasi.index') }}" class="btn {{ request()->routeIs('notary-legalisasi.*') ? 'btn-primary active-menu' : 'btn-outline-secondary' }}">
                <i class="fa-solid fa-stamp me-2"></i> Legalisasi
            </a>
            <a href="{{ route('notary-waarmerking.index') }}" class="btn {{ request()->routeIs('notary-waarmerking.*') ? 'btn-primary active-menu' : 'btn-outline-secondary' }}">
                <i class="fa-solid fa-file-contract me-2"></i> Waarmerking
            </a>

        </div>
    </div>
</div>