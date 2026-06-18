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
                    <form method="GET" action="{{ route('akta-parties.index') }}"
                        class="d-flex gap-2 mb-3 justify-content-end">
                        <input type="text" name="transaction_code" class="form-control"
                            placeholder="Cari Kode transaksi..." value="{{ request('transaction_code') }}">
                        <input type="text" name="akta_number" class="form-control" placeholder="Cari nomor akta..."
                            value="{{ request('akta_number') }}">
                        <button type="submit" class="btn btn-primary btn-sm mb-0">Cari</button>
                    </form>

                    {{-- Jika ada transaksi ditemukan --}}
                    @if ($aktaInfo && $aktaInfo->count() > 0)
                        <div class="card mb-4 shadow-sm">
                            <div class="card-header bg-primary text-white">
                                <h6 class="mb-0 text-white">Detail Pihak Akta</h6>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <h6 class="mb-1"><strong>Kode Klien</strong></h6>
                                        <p class="text-muted text-sm">{{ $aktaInfo->first()->client_code }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="mb-1"><strong>Nomor Akta</strong></h6>
                                        <p class="text-muted text-sm">{{ $aktaInfo->first()->akta_number ?? '-' }}</p>
                                    </div>
                                    {{-- <div class="col-md-6">
                                        <h6 class="mb-1"><strong>Jenis Akta</strong></h6>
                                        <p class="text-muted text-sm">{{ $aktaInfo->first()->akta_type->type ?? '-' }}</p>
                                    </div> --}}
                                    <div class="col-md-6">
                                        <h6 class="mb-1"><strong>Notaris</strong></h6>
                                        <p class="text-muted text-sm">{{ $aktaInfo->first()->notaris->display_name ?? '-' }}
                                        </p>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="mb-1"><strong>Klien</strong></h6>
                                        <p class="text-muted text-sm">{{ $aktaInfo->first()->client->fullname ?? '-' }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="mb-1"><strong>Status</strong></p>
                                        <span
                                            class="badge text-capitalize
                                    @switch($aktaInfo->first()->status)
                                        @case('draft') bg-secondary @break
                                        @case('diproses') bg-warning @break
                                        @case('selesai') bg-success @break
                                        @case('dibatalkan') bg-danger @break
                                        @default bg-light text-dark
                                    @endswitch
                                ">
                                            {{ $aktaInfo->first()->status }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-0">
                            <div class="d-flex justify-content-between align-items-center mb-0">
                                <h5>Pihak Akta</h5>
                                <a href="{{ route('akta-parties.createData', ['akta_transaction_id' => $aktaInfo->first()->id]) }}"
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
                                                <td>{{ $party->role }}</span></td>
                                                <td>{{ $party->address ?? '-' }}</td>
                                                <td>{{ $party->id_number ?? '-' }}</td>
                                                <td>{{ $party->id_type ?? '-' }}</td>
                                                <td>{{ $party->note ?? '-' }}</td>
                                                <td class="text-center">
                                                    <a href="{{ route('akta-parties.edit', $party->id) }}"
                                                        class="btn btn-info btn-sm mb-0">
                                                        Edit
                                                    </a>
                                                    <form action="{{ route('akta-parties.destroy', $party->id) }}"
                                                        method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="btn btn-danger btn-sm mb-0">Hapus</button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center text-muted text-sm">Belum ada pihak
                                                    akta.
                                                </td>
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
                        <p class="text-center text-muted text-sm">Silakan cari Kode Klien atau nomor akta untuk
                            menampilkan
                            pihak-pihak.</p>
                    @endif

                </div>
            </div>
        </div>
    </div>
@endsection
