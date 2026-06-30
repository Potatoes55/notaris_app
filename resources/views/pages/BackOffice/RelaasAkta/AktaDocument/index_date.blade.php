@extends('layouts.app')

@section('title', 'Pencarian Dokumen Relaas Berdasarkan Tanggal')

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Relaas Akta / Pencarian Tanggal'])

    <div class="row mt-4 mx-4">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center mb-0 pb-0">
                    <h5>Hasil Pencarian Relaas Berdasarkan Tanggal</h5>
                </div>
                
                <div class="card-body pt-2">
                    {{-- Form Pencarian yang sudah disamakan tingginya --}}
                    <form method="GET" action="{{ route('relaas-documents.index') }}"
                        class="d-flex flex-wrap gap-2 mb-3 justify-content-end align-items-end no-spinner">
                        @csrf
                        
                        <div style="flex: 1; min-width: 200px;">
                            <label for="transaction_code" class="form-label text-xs mb-1 font-weight-bold text-secondary">Kode Transaksi</label>
                            <input type="text" name="transaction_code" id="transaction_code" class="form-control form-control-sm"
                                placeholder="Cari Kode transaksi...">
                        </div>

                        <div style="flex: 1; min-width: 200px;">
                            <label for="relaas_number" class="form-label text-xs mb-1 font-weight-bold text-secondary">Nomor Relaas</label>
                            <input type="text" name="relaas_number" id="relaas_number" class="form-control form-control-sm" 
                                placeholder="Cari nomor relaas...">
                        </div>

                        <div style="width: 160px;">
                            <label for="start_date" class="form-label text-xs mb-1 font-weight-bold text-secondary">Tanggal Mulai</label>
                            <input type="date" class="form-control form-control-sm" name="start_date" id="start_date"
                                value="{{ request('start_date') }}">
                        </div>

                        <div style="width: 160px;">
                            <label for="end_date" class="form-label text-xs mb-1 font-weight-bold text-secondary">Tanggal Selesai</label>
                            <input type="date" class="form-control form-control-sm" name="end_date" id="end_date"
                                value="{{ request('end_date') }}">
                        </div>

                        <div>
                            <button type="submit" class="btn btn-primary btn-sm mb-0" style="height: 36px;">Cari</button>
                        </div>
                    </form>

                    {{-- Tabel Hasil Pencarian Unik per Relaas --}}
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr class="text-center text-xs font-weight-bold text-uppercase text-secondary opacity-7">
                                    <th>#</th>
                                    <th>Nama Klien</th>
                                    <th>Kode Transaksi</th>
                                    <th>Jumlah Dokumen</th>
                                    <th>Tanggal Submit</th>

                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($transactions as $tx)
                                    <tr class="text-center text-sm">
                                        <td>{{ $transactions->firstItem() + $loop->index }}</td>
                                        <td class="font-weight-bold">{{ $tx->client->fullname ?? '-' }}</td>
                                        <td>
                                            <span class="badge bg-light text-dark">{{ $tx->transaction_code }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">{{ $tx->documents_count ?? 0 }} Dokumen</span>
                                        </td>
                                                                                <td>
                                            {{ $tx->story_date ? \Carbon\Carbon::parse($tx->story_date)->format('d F Y H:i') : '-' }}
                                        </td>
                                        <td>
                                            {{-- Tombol merujuk kembali ke index utama Relaas --}}
                                            <a href="{{ route('relaas-documents.index', ['transaction_code' => $tx->transaction_code]) }}" 
                                               class="btn btn-sm btn-info mb-0">
                                                <i class="fa fa-eye me-1"></i> Detail Relaas
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">
                                            Tidak ada data relaas yang ditemukan pada rentang tanggal tersebut.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-end mt-3">
                        {{ $transactions->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection