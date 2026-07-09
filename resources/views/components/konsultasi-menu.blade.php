<div class="card mb-4 card-menu-notaris">
    <div class="card-body">
        <div class="d-flex flex-wrap gap-2 menu-ppat">

            {{-- Klien --}}
            <a href="{{ route('clients.index') }}"
                class="btn {{ request()->routeIs('clients.*') ? 'btn-primary active-menu' : 'btn-outline-secondary' }}">
                <i class="fa-solid fa-user-plus me-2"></i>
                Klien
            </a>

            {{-- Konsultasi --}}
            <a href="{{ route('consultation.index') }}"
                class="btn {{ request()->routeIs('consultation.*') ? 'btn-primary active-menu' : 'btn-outline-secondary' }}">
                <i class="fa-solid fa-headset me-2"></i>
                Konsultasi Klien
            </a>

            {{-- Proses Pengurusan --}}
            <a href="{{ route('pic-progress.indexProcess') }}"
                class="btn {{ request()->routeIs('pic-progress.*') || request()->is('client-progress*') ? 'btn-primary active-menu' : 'btn-outline-secondary' }}">
                <i class="fa-solid fa-calendar-days me-2"></i>
                Proses Pengurusan
            </a>

        </div>
    </div>
</div>