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
                        class="no-spinner d-flex
                        gap-2  justify-content-end mb-3">

                        {{-- <input type="text" name="search" class="form-control" placeholder="Masukkan Kode Transaksi"
                                value="{{ request('search') }}"> --}}
                        <input type="text" name="transaction_code" class="form-control"
                            placeholder="Masukkan Kode Transaksi" value="{{ request('transaction_code') }}">
                        <input type="text" name="relaas_number" class="form-control" placeholder="Masukkan Nomor Akta"
                            value="{{ request('relaas_number') }}">
                        <button type="submit" class="btn btn-primary btn-sm mb-0">Cari</button>

                    </form>

                    {{-- Jika ada data relaas --}}
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
                                                <button
                                                    type="button"
                                                    class="btn btn-link p-0 text-primary copy-btn"
                                                    onclick="copyValue(this, '{{ $relaasInfo->client_code }}')"
                                                    title="Salin Kode Klien">
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
                                                <button
                                                    type="button"
                                                    class="btn btn-link p-0 text-primary copy-btn"
                                                    onclick="copyValue(this, '{{ $relaasInfo->relaas_number }}')"
                                                    title="Salin Nomor Akta">
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
                                        <p class="text-muted text-sm">{{ $relaasInfo->first()->akta_type->type ?? '-' }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="mb-1"><strong>Status</strong></h6>
                                        <span
                                            class="badge text-capitalize
                                    @switch($relaasInfo->first()->status)
                                        @case('draft') bg-secondary @break
                                        @case('diproses') bg-warning @break
                                        @case('selesai') bg-success @break
                                        @case('dibatalkan') bg-danger @break
                                        @default bg-light text-dark
                                    @endswitch
                                ">
                                            {{ $relaasInfo->first()->status }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-0">
                            <div class="d-flex justify-content-between align-items-center mb-0">
                                <h5>Pihak Akta</h5>
                                <a href="{{ route('relaas-parties.create', $relaasInfo->id) }}"
                                    class="btn btn-primary btn-sm">+
                                    Tambah Pihak Akta</a>
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
                                            {{-- <th>Catatan</th> --}}
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($parties as $party)
                                            <tr class="text-center text-sm">
                                                <td>{{ $parties->firstItem() + $loop->index }}</td>
                                                <td>{{ $party->name }}</td>
                                                <td>{{ $party->role }}</td>
                                                <td>{{ $party->address ?? '-' }}</td>
                                                <td>{{ $party->id_number ?? '-' }}</td>
                                                <td>{{ $party->id_type ?? '-' }}</td>
                                                {{-- <td>{{ $party->note ?? '-' }}</td> --}}
                                                <td>
                                                    <a href="{{ route('relaas-parties.edit', [$relaasInfo->id, $party->id]) }}"
                                                        class="btn btn-info btn-sm btn-sm mb-0">Edit</a>
                                                    <form action="{{ route('relaas-parties.destroy', $party->id) }}"
                                                        method="POST" class="d-inline">
                                                        @csrf @method('DELETE')
                                                        <button class="btn btn-danger btn-sm mb-0">Hapus</button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center text-muted">Belum ada pihak akta</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>

                                <div class="d-flex justify-content-end mt-3">
                                    {{ $parties->withQueryString()->links() }}
                                </div>
                            </div>
                        </div>
                    @else
                        <p class="text-center text-muted text-sm mb-0">Masukkan Kode Transaksi untuk melihat daftar pihak
                            akta.
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
