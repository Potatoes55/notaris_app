@extends('layouts.app')

@section('title', 'Dokumen Akta')

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Akta Notaris / Dokumen Akta'])
    @include('components.notaris-menu')

    <div class="row mt-4 mx-4">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0">
                <h5>Dokumen Akta</h5>
            </div>
            <div class="card-body pt-1 pb-0">

                {{-- Form Pencarian Terpadu --}}
                <form method="GET" action="{{ route('akta-documents.index') }}"
                    class="d-flex flex-wrap gap-2 mb-3 justify-content-end align-items-end no-spinner">
                    
                    <div style="flex:1; min-width:250px;">
                        <label for="search" class="form-label text-sm">Kata Kunci Pencarian</label>
                        <input type="text" name="search" id="search" class="form-control"
                            placeholder="Cari Kode, No. Akta, atau Nama Klien..." value="{{ request('search') }}">
                    </div>

                    <div style="width:160px;">
                        <label for="start_date" class="form-label text-sm">Tanggal Mulai</label>
                        <input type="date" name="start_date" id="start_date" class="form-control" value="{{ request('start_date') }}">
                    </div>

                    <div style="width:160px;">
                        <label for="end_date" class="form-label text-sm">Tanggal Selesai</label>
                        <input type="date" name="end_date" id="end_date" class="form-control" value="{{ request('end_date') }}">
                    </div>

                    <div>
                        <button type="submit" class="btn btn-primary mb-0 px-4">Cari</button>
                    </div>
                </form>

                {{-- KONDISI 1: Jika Berhasil Menemukan Spesifik 1 Transaksi/Akta --}}
                @if (isset($transaction) && $transaction)
                    <div class="card mb-4 shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0 text-white">Detail Transaksi Akta</h5>
                        </div>
                        <div class="card-body pb-2">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <h6><strong>Kode Transaksi</strong></h6>
                                    <div class="d-flex align-items-center gap-2">
                                        <p class="text-muted text-sm mb-0">{{ $transaction->transaction_code ?? '-' }}</p>
                                        <button type="button" class="btn btn-link p-0 text-primary copy-btn" onclick="copyValue(this, '{{ $transaction->transaction_code }}')" title="Salin Kode">
                                            <i class="fa-solid fa-copy"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <h6><strong>Nomor Akta</strong></h6>
                                    <div class="d-flex align-items-center gap-2">
                                        <p class="text-muted text-sm mb-0">{{ $transaction->akta_number ?? '-' }}</p>
                                        <button type="button" class="btn btn-link p-0 text-primary copy-btn" onclick="copyValue(this, '{{ $transaction->akta_number }}')" title="Salin No. Akta">
                                            <i class="fa-solid fa-copy"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <h6><strong>Notaris</strong></h6>
                                    <p class="text-muted text-sm">{{ $transaction->notaris->display_name ?? '-' }}</p>
                                </div>
                                <div class="col-md-6">
                                    <h6><strong>Klien</strong></h6>
                                    <p class="text-muted text-sm">{{ $transaction->client->fullname ?? '-' }}</p>
                                </div>
                                <div class="col-md-6">
                                    <h6><strong>Status</strong></h6>
                                    <span class="badge text-capitalize 
                                        @switch($transaction->status)
                                            @case('draft') bg-secondary @break
                                            @case('diproses') bg-warning @break
                                            @case('selesai') bg-success @break
                                            @case('dibatalkan') bg-danger @break
                                            @default bg-light text-dark
                                        @endswitch">
                                        {{ $transaction->status }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h5>Dokumen Akta</h5>

                            <div class="d-flex gap-2">
                                <a href="{{ route('akta-documents.createData', [
                                    'akta_transaction_id' => $transaction->id
                                ]) }}"
                                    class="btn btn-primary btn-sm mb-0">
                                    <i class="fa fa-file-circle-plus me-1"></i>
                                    Tambah Dokumen
                                </a>

                                <a href="{{ route('akta-documents.createData', [
                                    'akta_transaction_id' => $transaction->id,
                                    'type' => 'sk_kemenkum'
                                ]) }}"
                                    class="btn btn-success btn-sm mb-0">
                                    <i class="fa fa-file-signature me-1"></i>
                                    SK Kemenkum
                                </a>
                            </div>
                        </div>
                        <div class="table-responsive p-0">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr class="text-center text-sm">
                                        <th>#</th>
                                        <th>Nama Dokumen</th>
                                        <th>Tipe</th>
                                        <th>Tanggal Upload</th>
                                        <th>File Dokumen Akta</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($documents as $doc)
                                        <tr class="text-sm">
                                            <td class="ps-3">
                                                {{ method_exists($documents, 'firstItem') ? ($documents->firstItem() + $loop->index) : ($loop->iteration) }}
                                            </td>
                                            <td>{{ $doc->name }}</td>
                                            <td>{{ $doc->type }}</td>
                                            <td>
                                                {{ $doc->uploaded_at ? \Carbon\Carbon::parse($doc->uploaded_at)->format('d F Y H:i:s') : '-' }}
                                            </td>
                                            <td class="text-center">
                                                @if ($doc->file_url)
                                                    <button type="button" class="btn btn-sm btn-primary mb-0"
                                                        data-bs-toggle="modal" data-bs-target="#fileModal{{ $doc->id }}">
                                                        <i class="fa fa-file me-1"></i> Lihat Akta Dokumen
                                                    </button>

                                                    @php
                                                        $isPdf = $doc->file_type === 'pdf';
                                                        $modalSize = $isPdf ? 'modal-xl' : 'modal-lg';
                                                    @endphp

                                                    <div class="modal fade" id="fileModal{{ $doc->id }}" tabindex="-1" aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-centered {{ $modalSize }}">
                                                            <div class="modal-content">
                                                                <div class="modal-header py-2">
                                                                    <h5 class="modal-title">Preview: {{ $doc->name }}</h5>
                                                                    <button
                                                                        type="button"
                                                                        class="btn-close btn-close-white"
                                                                        data-bs-dismiss="modal"
                                                                        aria-label="Close">
                                                                    </button>
                                                                </div>
                                                                <div class="modal-body text-center p-0">
                                                                    @if (in_array($doc->file_type, ['pdf', 'png', 'jpg', 'jpeg', 'svg', 'webp']))
                                                                        <embed src="{{ route('akta-documents.view-pdf', ['id' => $doc->id]) }}"
                                                                            type="application/pdf" width="100%" height="700px" />
                                                                    @else
                                                                        <p class="text-muted p-4">File tidak dapat ditampilkan langsung.</p>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @else
                                                    <span class="badge bg-secondary">Tidak Ada File</span>
                                                @endif
                                            </td>
                                            
                                            {{-- PERBAIKAN PADA KOLOM AKSI --}}
                                            <td class="text-center">
                                                <div class="d-flex gap-1 justify-content-center">
                                                    {{-- 1. Tombol Edit --}}
                                                    <a href="{{ route('akta-documents.edit', $doc->id) }}" class="btn btn-info btn-sm mb-0">
                                                        <i class="fa fa-edit me-1"></i> Edit
                                                    </a>

                                                    {{-- 2. Tombol Pemicu Modal Hapus --}}
                                                    <button type="button" class="btn btn-danger btn-sm mb-0" 
                                                        data-bs-toggle="modal" data-bs-target="#deleteModal{{ $doc->id }}">
                                                        <i class="fa fa-trash me-1"></i> Hapus
                                                    </button>
                                                </div>

                                                {{-- 3. Struktur Modal Konfirmasi Hapus (Unik per Dokumen) --}}
                                                <div class="modal fade" id="deleteModal{{ $doc->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $doc->id }}" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered">
                                                        <div class="modal-content">
                                                            <div class="modal-header py-2">
                                                                <h5 class="modal-title" id="deleteModalLabel{{ $doc->id }}">Konfirmasi Hapus</h5>
                                                                <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body text-start">
                                                                Apakah Anda yakin ingin menghapus dokumen <strong>{{ $doc->name }}</strong>? Tindakan ini tidak dapat dibatalkan.
                                                            </div>
                                                            <div class="modal-footer py-2">
                                                                {{-- Tombol Batal --}}
                                                                <button type="button" class="btn btn-secondary btn-sm mb-0" data-bs-dismiss="modal">Batal</button>
                                                                
                                                                {{-- Form Proses Hapus Sesungguhnya --}}
                                                                <form action="{{ route('akta-documents.destroy', $doc->id) }}" method="POST" class="d-inline">
                                                                    @csrf 
                                                                    @method('DELETE')
                                                                    <button type="submit" class="btn btn-danger btn-sm mb-0">Ya, Hapus</button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="6" class="text-center text-muted text-sm">Belum ada dokumen akta.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    

                    {{-- KONDISI 2: Jika Pencarian Berupa Daftar Transaksi --}}
                    @elseif (isset($transactions) && $transactions->isNotEmpty())

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
                                        <th>Tanggal Masuk</th>
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
                                                    <span>{{ $tx->akta_number ?? '-' }}</span>

                                                    @if($tx->akta_number)
                                                        <button type="button"
                                                            class="btn btn-link p-0 text-primary"
                                                            onclick="copyValue(this,'{{ $tx->akta_number }}')">
                                                            <i class="fa-solid fa-copy"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>

                                            <td>{{ $tx->client->fullname ?? '-' }}</td>

                                            <td>
                                                {{ \Carbon\Carbon::parse($tx->date_submission)->format('d-m-Y') }}
                                            </td>

                                            <td>
                                                <a href="{{ route('akta-documents.index', ['search' => $tx->transaction_code]) }}"
                                                    class="btn btn-sm btn-info">
                                                    <i class="fa fa-folder-open me-1"></i>
                                                    Buka Dokumen
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

                    @else
                        <p class="text-center text-muted text-sm mb-3">Masukkan Kata Kunci atau Rentang Tanggal Untuk Menampilkan Dokumen akta.</p>
                    @endif

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
@endsection