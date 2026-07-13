@extends('layouts.app')

@section('title', 'Pihak Akta')

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'PPAT / Pihak Akta'])
    @include('components.ppat-menu')

    <div class="row mt-4 mx-4">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center mb-0 pb-0">
                    <h5>Pihak Akta</h5>
                </div>

                <div class="card-body pt-2 pb-0">

                    {{-- Form Pencarian --}}
                    <form method="GET"
                        action="{{ route('relaas-parties.index') }}"
                        class="mb-3">

                        <div class="input-group w-100">
                            <input type="text"
                                name="search"
                                class="form-control"
                                placeholder="Cari Kode, No Akta, atau Nama Klien..."
                                value="{{ request('search') }}">

                            <button type="submit" class="btn btn-primary mb-0">
                                Cari
                            </button>
                        </div>

                    </form>

                    {{-- KONDISI 1: JIKA HASIL PENCARIAN BERUPA DAFTAR TRANSAKSI (Berdasarkan Nama Klien / Parsial) --}}
                    @if (isset($transactions) && $transactions->count() > 0)

                    <div class="mb-0">
                        <h5>Daftar Transaksi Akta</h5>

                        <div class="table-responsive p-0">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr class="text-center text-sm">
                                        <th>#</th>
                                        <th>Kode Transaksi</th>
                                        <th>Nomor Akta</th>
                                        <th>Nama Klien</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach($transactions as $tx)
                                        <tr class="text-center text-sm">

                                            <td>
                                                {{ $transactions->firstItem() + $loop->index }}
                                            </td>

                                            <td>
                                                <div class="d-flex justify-content-center align-items-center gap-2">
                                                    <span>{{ $tx->transaction_code }}</span>

                                                    <button type="button"
                                                        class="btn btn-link p-0 text-primary"
                                                        onclick="copyValue(this,'{{ $tx->transaction_code }}')">
                                                        <i class="fa-solid fa-copy"></i>
                                                    </button>
                                                </div>
                                            </td>

                                            <td>
                                                <div class="d-flex justify-content-center align-items-center gap-2">
                                                    <span>{{ $tx->relaas_number ?? '-' }}</span>

                                                    @if($tx->relaas_number)
                                                        <button type="button"
                                                            class="btn btn-link p-0 text-primary"
                                                            onclick="copyValue(this,'{{ $tx->relaas_number }}')">
                                                            <i class="fa-solid fa-copy"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>

                                            <td>{{ $tx->client->fullname ?? '-' }}</td>

                                            <td>
                                                <a href="{{ route('relaas-parties.index', ['search' => $tx->transaction_code]) }}"
                                                    class="btn btn-sm btn-info">
                                                    <i class="fa fa-users me-1"></i>
                                                    Pilih Pihak
                                                </a>
                                            </td>

                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-end mt-2">
                            {{ $transactions->links() }}
                        </div>
                    </div>

                    @elseif($relaasInfo)
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
                            <div class="d-flex justify-content-between align-items-center mb-0">
                                <h5>Pihak Akta</h5>
                                <a href="{{ route('relaas-parties.create', $relaasInfo->id) }}"
                                    class="btn btn-primary btn-sm mb-2">+ Tambah Pihak Akta</a>
                            </div>

                            <div class="table-responsive p-0">
                                <table class="table align-items-center mb-0">
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
                                            <tr class="text-sm text-center">
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
                                        {{ $parties->links() }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    @elseif(!isset($transactions) || $transactions->isEmpty())
                        <p class="text-center text-muted text-sm py-4">
                            Silakan cari Kode Transaksi, Nomor Akta, atau nama klien untuk menampilkan data pihak.
                        </p>
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