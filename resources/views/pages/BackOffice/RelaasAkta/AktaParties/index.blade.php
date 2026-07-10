@extends('layouts.app')

@section('title', 'Pihak Akta')

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'PPAT / Pihak Akta'])
    @include('components.ppat-menu')

    <div class="row mt-4 mx-4">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0">
                    <h5>Pihak Akta</h5>
                </div>
                <div class="card-body pt-1">

                    {{-- Form Pencarian --}}
                    <form method="GET" action="{{ route('relaas-parties.index') }}"
                        class="d-flex gap-2 mb-3 justify-content-end">
                        <div class="input-group" style="max-width: 400px;">
                            <input type="text" name="search" class="form-control"
                                placeholder="Cari Kode, No Akta, atau Nama Klien..." value="{{ request('search') }}">
                            <button type="submit" class="btn btn-primary mb-0">Cari</button>
                        </div>
                    </form>

                    {{-- KONDISI 1: JIKA HASIL PENCARIAN BERUPA DAFTAR TRANSAKSI (Berdasarkan Nama Klien / Parsial) --}}
                    @if (isset($transactions) && $transactions->count() > 0)
                        <div class="card mb-4 shadow-sm">
                            <div class="card-header bg-secondary text-white py-2">
                                <h6 class="mb-0 text-white text-sm">Hasil Pencarian Transaksi</h6>
                            </div>
                            <div class="table-responsive">
                                <table class="table align-items-center mb-0 table-hover">
                                    <thead>
                                        <tr class="text-xs font-weight-bold opacity-7">
                                            <th class="ps-3">#</th>
                                            <th>Kode Transaksi</th>
                                            <th>Nama Klien</th>
                                            <th>Nomor Akta</th>
                                            <th class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($transactions as $tx)
                                            <tr class="text-sm">
                                                <td class="ps-3">{{ $loop->iteration + ($transactions->currentPage() - 1) * $transactions->perPage() }}</td>
                                                <td><span class="badge bg-light text-dark font-weight-bold">{{ $tx->transaction_code }}</span></td>
                                                <td>{{ $tx->client->fullname ?? '-' }}</td>
                                                <td>{{ $tx->relaas_number ?? '-' }}</td>
                                                <td class="text-center">
                                                    {{-- Gunakan transaction_code untuk memicu pencarian spesifik tunggal --}}
                                                    <a href="{{ route('relaas-parties.index', ['search' => $tx->transaction_code]) }}" class="btn btn-xs btn-primary mb-0">
                                                        <i class="fas fa-search-plus me-1"></i> Pilih
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @if($transactions->hasPages())
                                <div class="card-footer py-2">
                                    {{ $transactions->links() }}
                                </div>
                            @endif
                        </div>
                    @endif

                    {{-- KONDISI 2: JIKA ADA DATA RELAAS TUNGGAL (Detail & Daftar Pihak Akta) --}}
                    @if ($relaasInfo)
                        <div class="card mb-4 shadow-sm">
                            <div class="card-header bg-primary text-white">
                                <h6 class="mb-0 text-white">Detail Pihak Akta</h6>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <h6><strong>Kode Klien</strong></h6>
                                        <div class="d-flex align-items-center gap-2">
                                            <p class="text-muted text-sm mb-0">{{ $relaasInfo->client_code ?? '-' }}</p>
                                            @if($relaasInfo->client_code)
                                                <button type="button" class="btn btn-link p-0 text-primary copy-btn"
                                                    onclick="copyValue(this, '{{ $relaasInfo->client_code }}')" title="Salin Kode Klien">
                                                    <i class="fa-solid fa-copy"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <h6 class="mb-1"><strong>Nomor Akta</strong></h6>
                                        <div class="d-flex align-items-center gap-2">
                                            <p class="text-muted text-sm mb-0">{{ $relaasInfo->relaas_number ?? '-' }}</p>
                                            @if($relaasInfo->relaas_number)
                                                <button type="button" class="btn btn-link p-0 text-primary copy-btn"
                                                    onclick="copyValue(this, '{{ $relaasInfo->relaas_number }}')" title="Salin Nomor Akta">
                                                    <i class="fa-solid fa-copy"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <h6><strong>Notaris</strong></h6>
                                        <p class="text-muted text-sm">{{ $relaasInfo->notaris->display_name ?? '-' }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <h6><strong>Klien</strong></h6>
                                        <p class="text-muted text-sm">{{ $relaasInfo->client->fullname ?? '-' }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="mb-1"><strong>Jenis Akta</strong></h6>
                                        <p class="text-muted text-sm">{{ $relaasInfo->akta_type->type ?? '-' }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="mb-1"><strong>Status</strong></h6>
                                        <span class="badge text-capitalize
                                            @switch($relaasInfo->status)
                                                @case('draft') bg-secondary @break
                                                @case('diproses') bg-warning @break
                                                @case('selesai') bg-success @break
                                                @case('dibatalkan') bg-danger @break
                                                @default bg-light text-dark
                                            @endswitch
                                        ">
                                            {{ $relaasInfo->status }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-0">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h5>Pihak Akta</h5>
                                <a href="{{ route('relaas-parties.create', $relaasInfo->id) }}"
                                    class="btn btn-primary btn-sm mb-0">+ Tambah Pihak Akta</a>
                            </div>

                            <div class="table-responsive p-0">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Nama</th>
                                            <th>Peran</th>
                                            <th>Alamat</th>
                                            <th>No Identitas</th>
                                            <th>Tipe</th>
                                            <th class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($parties as $party)
                                            <tr class="text-sm">
                                                {{-- Menggunakan logic bawaan pagination jika instance-nya LengthAwarePaginator --}}
                                                <td>{{ method_exists($parties, 'firstItem') ? $parties->firstItem() + $loop->index : $loop->iteration }}</td>
                                                <td>{{ $party->name }}</td>
                                                <td>{{ $party->role }}</td>
                                                <td>{{ $party->address ?? '-' }}</td>
                                                <td>{{ $party->id_number ?? '-' }}</td>
                                                <td>{{ $party->id_type ?? '-' }}</td>
                                                <td class="text-center">
                                                    <a href="{{ route('relaas-parties.edit', [$relaasInfo->id, $party->id]) }}"
                                                        class="btn btn-info btn-sm mb-0">Edit</a>
                                                    <form action="{{ route('relaas-parties.destroy', $party->id) }}"
                                                        method="POST" class="d-inline">
                                                        @csrf @method('DELETE')
                                                        <button class="btn btn-danger btn-sm mb-0">Hapus</button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center text-muted py-4">Belum ada pihak akta</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>

                                @if(method_exists($parties, 'links'))
                                    <div class="d-flex justify-content-end mt-3">
                                        {{ $parties->withQueryString()->links() }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    {{-- JIKA BELUM MELAKUKAN PENCARIAN ATAU DATA TIDAK COCOK --}}
                    @elseif(!isset($transactions) || $transactions->isEmpty())
                        <p class="text-center text-muted text-sm mb-0">Masukkan Kode Transaksi atau Nama Klien untuk melihat daftar pihak akta.</p>
                    @endif

                </div>
            </div>
        </div>
    </div>
    
    <script>
    function copyValue(button, value) {
        navigator.clipboard.writeText(value);
        const icon = button.querySelector('i');
        icon.classList.remove('fa-copy');
        icon.classList.add('fa-check');
        notyf.success('Berhasil disalin');
        setTimeout(() => {
            icon.classList.remove('fa-check');
            icon.classList.add('fa-copy');
        }, 1000);
    }
    </script>
@endsection