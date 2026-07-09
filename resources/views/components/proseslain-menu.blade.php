<div class="card mb-4 card-menu-notaris">
    <div class="card-body">
        <div class="d-flex flex-wrap gap-2 menu-ppat">

            {{-- Transaksi --}}
            <a href="{{ route('proses-lain.transaksi') }}"
                class="btn {{ request()->routeIs('proses-lain.transaksi') ? 'btn-primary active-menu' : 'btn-outline-secondary' }}">
                <i class="fa-solid fa-right-left me-2"></i>
                Transaksi
            </a>

            {{-- PIC Proses Lain --}}
            <a href="{{ route('proses-lain-pic.index') }}"
                class="btn {{ request()->is('proses-lain-pic*') ? 'btn-primary active-menu' : 'btn-outline-secondary' }}">
                <i class="fa-solid fa-user-check me-2"></i>
                PIC
            </a>

            {{-- Progress --}}
            <a href="{{ route('proses-lain.progress') }}"
                class="btn {{ request()->routeIs('proses-lain.progress') ? 'btn-primary active-menu' : 'btn-outline-secondary' }}">
                <i class="fa-solid fa-bars-progress me-2"></i>
                Log
            </a>

            {{-- Dropdown PIC --}}
            <div class="dropdown">
                <button
                    class="btn {{ request()->routeIs('proses-lain.pic.*') ? 'btn-primary active-menu' : 'btn-outline-secondary' }} dropdown-toggle"
                    type="button"
                    data-bs-toggle="dropdown">

                    <i class="fa-solid fa-user-tie me-2"></i>
                    Master PIC
                </button>

                <ul class="dropdown-menu">
                    <li>
                        <a class="dropdown-item" href="{{ route('proses-lain.pic.staff') }}">
                            <i class="fa-solid fa-users-gear me-2"></i>
                            Staff
                        </a>
                    </li>

                    <li>
                        <a class="dropdown-item" href="{{ route('proses-lain.pic.documents') }}">
                            <i class="fa-solid fa-file-lines me-2"></i>
                            Dokumen
                        </a>
                    </li>

                    <li>
                        <a class="dropdown-item" href="{{ route('proses-lain.pic.process') }}">
                            <i class="fa-solid fa-gears me-2"></i>
                            Proses Pengurusan
                        </a>
                    </li>

                    <li>
                        <a class="dropdown-item" href="{{ route('proses-lain.pic.handovers') }}">
                            <i class="fa-solid fa-envelope-open-text me-2"></i>
                            Surat Terima Dokumen
                        </a>
                    </li>
                </ul>
            </div>

            {{-- Biaya --}}
            <div class="dropdown">
                <button
                    class="btn {{ request()->routeIs(['proses-lain.biaya.total', 'proses-lain.biaya.payments']) ? 'btn-primary active-menu' : 'btn-outline-secondary' }} dropdown-toggle"
                    type="button"
                    data-bs-toggle="dropdown">

                    <i class="fa-solid fa-sack-dollar me-2"></i>
                    Biaya
                </button>

                <ul class="dropdown-menu">
                    <li>
                        <a class="dropdown-item" href="{{ route('proses-lain.biaya.total') }}">
                            <i class="fa-solid fa-file-invoice-dollar me-2"></i>
                            Total Biaya
                        </a>
                    </li>

                    <li>
                        <a class="dropdown-item" href="{{ route('proses-lain.biaya.payments') }}">
                            <i class="fa-solid fa-credit-card me-2"></i>
                            Pembayaran
                        </a>
                    </li>
                </ul>
            </div>

        </div>
    </div>
</div>