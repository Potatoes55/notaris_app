@extends('layouts.app')

@section('title', 'Pihak Akta')

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Akta Notaris / Pihak Akta'])
    @include('components.notaris-menu')

    <div class="row mt-4 mx-4">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center mb-0 pb-0">
                    <h5>Pihak Akta</h5>
                </div>
                <div class="card-body pt-2 pb-0">
                    <form method="GET" action="{{ route('akta-parties.index') }}" class="d-flex gap-2 mb-3 justify-content-end">
                        <div class="input-group style="max-width: 400px;">
                            <input type="text" name="search" class="form-control"
                                placeholder="Cari Kode, No Akta, atau Nama Klien..." value="{{ request('search') }}">
                            <button type="submit" class="btn btn-primary mb-0">Cari</button>
                        </div>
                    </form>

                    {{-- KONDISI 1: Tampilkan Daftar Transaksi (Jika hasil berupa partial/banyak data) --}}
                    @if (isset($transactions) && $transactions->count() > 0)
                        <div class="card mb-4 shadow-sm">
                            <div class="card-header bg-light">
                                <h6 class="mb-0 text-dark">Hasil Pencarian Transaksi</h6>
                            </div>
                            <div class="table-responsive p-0">
                                <table class="table align-items-center mb-0">
                                    <thead>
                                        <tr>
                                            <th>Kode Transaksi</th>
                                            <th>Nomor Akta</th>
                                            <th>Klien</th>
                                            <th>Jenis Akta</th>
                                            <th class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($transactions as $trans)
                                            <tr class="text-sm">
                                                <td>{{ $trans->transaction_code }}</td>
                                                <td>{{ $trans->akta_number ?? '-' }}</td>
                                                <td>{{ $trans->client->fullname ?? '-' }}</td>
                                                <td>{{ $trans->akta_type->type ?? '-' }}</td>
                                                <td class="text-center">
                                                    <a href="{{ route('akta-parties.index', ['search' => $trans->transaction_code]) }}" 
                                                       class="btn btn-sm btn-outline-primary mb-0">
                                                        Pilih Pihak
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="d-flex justify-content-end p-3">
                                {{ $transactions->links() }}
                            </div>
                        </div>

                    {{-- KONDISI 2: Jika data akta pas (Exact Match) ditemukan, tampilkan Detail & Daftar Pihak --}}
                    @elseif ($aktaInfo)
                        <div class="card mb-4 shadow-sm">
                            <div class="card-header bg-primary text-white">
                                <h6 class="mb-0 text-white">Detail Pihak Akta</h6>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <h6 class="mb-1"><strong>Kode Klien</strong></h6>
                                        <div class="d-flex align-items-center gap-2">
                                            <p class="text-muted text-sm mb-0">{{ $aktaInfo->client_code }}</p>
                                            <button type="button" class="btn btn-link p-0 text-primary"
                                                onclick="copyValue(this, '{{ $aktaInfo->client_code }}')">
                                                <i class="fa-solid fa-copy"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <h6 class="mb-1"><strong>Nomor Akta</strong></h6>
                                        <div class="d-flex align-items-center gap-2">
                                            <p class="text-muted text-sm mb-0">{{ $aktaInfo->akta_number ?? '-' }}</p>
                                            @if($aktaInfo->akta_number)
                                                <button type="button" class="btn btn-link p-0 text-primary"
                                                    onclick="copyValue(this, '{{ $aktaInfo->akta_number }}')">
                                                    <i class="fa-solid fa-copy"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <h6 class="mb-1"><strong>Notaris</strong></h6>
                                        <p class="text-muted text-sm">{{ $aktaInfo->notaris->display_name ?? '-' }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="mb-1"><strong>Klien</strong></h6>
                                        <p class="text-muted text-sm">{{ $aktaInfo->client->fullname ?? '-' }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="mb-1"><strong>Status</strong></p>
                                        <span class="badge text-capitalize
                                            @switch($aktaInfo->status)
                                                @case('draft') bg-secondary @break
                                                @case('diproses') bg-warning @break
                                                @case('selesai') bg-success @break
                                                @case('dibatalkan') bg-danger @break
                                                @default bg-light text-dark
                                            @endswitch">
                                            {{ $aktaInfo->status }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-0">
                            <div class="d-flex justify-content-between align-items-center mb-0">
                                <h5>Pihak Akta</h5>
                                <a href="{{ route('akta-parties.createData', ['akta_transaction_id' => $aktaInfo->id]) }}"
                                    class="btn btn-primary btn-sm mb-2">
                                    + Tambah Pihak Akta
                                </a>
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
                                            <th>Catatan</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($parties as $party)
                                            <tr class="text-sm text-center">
                                                <td>{{ $parties->firstItem() + $loop->index }}</td>
                                                <td>{{ $party->name }}</td>
                                                <td>{{ $party->role }}</td>
                                                <td>{{ $party->address ?? '-' }}</td>
                                                <td>{{ $party->id_number ?? '-' }}</td>
                                                <td>{{ $party->id_type ?? '-' }}</td>
                                                <td>{{ $party->note ?? '-' }}</td>
                                                <td class="text-center">
                                                    <a href="{{ route('akta-parties.edit', $party->id) }}"
                                                        class="btn btn-info btn-sm mb-0">Edit</a>
                                                    <form action="{{ route('akta-parties.destroy', $party->id) }}"
                                                        method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm mb-0">Hapus</button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center text-muted text-sm">Belum ada pihak akta.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                                <div class="d-flex justify-content-end mt-3">
                                    {{ $parties->links() }}
                                </div>
                            </div>
                        </div>
                    @else
                        {{-- KONDISI 3: Default State ketika belum ada pencarian atau tidak ada data --}}
                        <p class="text-center text-muted text-sm py-4">Silakan cari Kode Transaksi, Nomor Akta, atau nama klien untuk menampilkan data pihak.</p>
                    @endif

                </div>
            </div>
        </div>
    </div>

    @push('js')
    <script>
        function copyValue(button, value) {
            navigator.clipboard.writeText(value);
            const icon = button.querySelector('i');
            icon.classList.remove('fa-copy');
            icon.classList.add('fa-check');

            if(typeof notyf !== 'undefined') {
                notyf.success('Berhasil disalin');
            }

            setTimeout(() => {
                icon.classList.remove('fa-check');
                icon.classList.add('fa-copy');
            }, 1000);
        }
    </script>
    @endpush
@endsection