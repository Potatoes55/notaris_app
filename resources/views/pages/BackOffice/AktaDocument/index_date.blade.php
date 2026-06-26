@extends('layouts.app')

@section('title', 'Pencarian Dokumen Berdasarkan Tanggal')

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Akta Notaris / Pencarian Tanggal'])

    @include('components.notaris-menu')

    <div class="row mt-4 mx-4">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center mb-0 pb-0">
                    <h5>Hasil Pencarian Berdasarkan Tanggal</h5>
                </div>
                
                <div class="card-body pt-2">
                    {{-- Form Pencarian (Tetap disediakan agar user bisa filter ulang) --}}
                {{-- Form Pencarian --}}
                <form method="GET" action="{{ route('akta-documents.index') }}"
                    class="d-flex flex-wrap gap-2 mb-3 justify-content-end align-items-end no-spinner">
                    @csrf
                    
                    {{-- Input Kode Transaksi --}}
                    <div style="flex: 1; min-width: 300px;">
                        <label for="transaction_code" class="form-label text-xs mb-1 font-weight-bold text-secondary">Kode Transaksi</label>
                        <input type="text" name="transaction_code" id="transaction_code" class="form-control form-control-sm"
                            placeholder="Cari Kode transaksi..." value="{{ $filters['transaction_code'] ?? '' }}">
                    </div>

                    {{-- Input Nomor Akta --}}
                    <div style="flex: 1; min-width: 300px;">
                        <label for="akta_number" class="form-label text-xs mb-1 font-weight-bold text-secondary">Nomor Akta</label>
                        <input type="text" name="akta_number" id="akta_number" class="form-control form-control-sm" 
                            placeholder="Cari nomor akta..." value="{{ $filters['akta_number'] ?? '' }}">
                    </div>

                    {{-- Input Tanggal Mulai --}}
                    <div style="width: 160px;">
                        <label for="start_date" class="form-label text-xs mb-1 font-weight-bold text-secondary">Tanggal Mulai</label>
                        <input type="date" class="form-control form-control-sm" name="start_date" id="start_date"
                            value="{{ request('start_date') }}">
                    </div>

                    {{-- Input Tanggal Selesai --}}
                    <div style="width: 160px;">
                        <label for="end_date" class="form-label text-xs mb-1 font-weight-bold text-secondary">Tanggal Selesai</label>
                        <input type="date" class="form-control form-control-sm" name="end_date" id="end_date"
                            value="{{ request('end_date') }}">
                    </div>

                    {{-- Tombol Cari --}}
                    <div>
                        <button type="submit" class="btn btn-primary btn-sm mb-0" style="height: 36px;">Cari</button>
                    </div>
                </form>


                    {{-- Tabel Hasil Pencarian Dokumen Massal --}}
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
                                {{-- 1. Lakukan perulangan langsung pada koleksi item paginator ($documents) --}}
                                    @forelse ($transactions as $tx)
                                    <tr class="text-center text-sm">
                                        {{-- Penomoran Pagination --}}
                                        <td>{{ $transactions->firstItem() + $loop->index }}</td>

                                        {{-- Nama Klien --}}
                                        <td class="font-weight-bold">{{ $tx->client->fullname ?? '-' }}</td>

                                        {{-- Kode Transaksi --}}
                                        <td>
                                            <span class="badge bg-light text-dark">{{ $tx->transaction_code }}</span>
                                        </td>

                                        {{-- Jumlah Dokumen di dalam transaksi tersebut --}}
                                        <td>
                                            <span class="badge bg-secondary">{{ $tx->documents_count ?? 0 }} Dokumen</span>
                                        </td>
                                        
                                        
                                        {{-- Tanggal Upload --}}
                                        <td>
                                            {{ $tx->date_submission ? \Carbon\Carbon::parse($tx->date_submission)->format('d F Y H:i') : '-' }}
                                        </td>
                                        
                                        {{-- Tombol Detail Kembali ke Index Utama --}}
                                        <td>
                                            <a href="{{ route('akta-documents.index', ['transaction_code' => $tx->transaction_code]) }}" 
                                            class="btn btn-sm btn-info mb-0">
                                                <i class="fa fa-eye me-1"></i> Detail Transaksi
                                            </a>
                                        </td>
                                        </td>
                                    </tr>
                                @empty
                                    {{-- Tampilan jika hasil pencarian tanggal memang tidak menghasilkan dokumen apapun --}}
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">
                                            Tidak ada dokumen akta yang ditemukan pada rentang tanggal tersebut.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    <div class="d-flex justify-content-end mt-3">
                        {{ $transactions->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection