@extends('layouts.app')

@section('title', 'Dokumen Akta')

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'PPAT / Dokumen Akta'])
    @include('components.ppat-menu')

    <div class="row mt-4 mx-4">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0">
                    <h5>Dokumen Akta</h5>
                </div>
                <div class="card-body pt-1 pb-0">

                {{-- Form Pencarian Tunggal Sesuai Controller --}}
                <form method="GET" action="{{ route('relaas-documents.index') }}"
                    class="d-flex flex-wrap gap-2 mb-3 justify-content-end align-items-end no-spinner">
                    
                    <div style="flex:1; min-width:250px;">
                        <label for="search" class="form-label text-sm">
                            Kata Kunci Pencarian
                        </label>
                        <input
                            type="text"
                            name="search"
                            id="search"
                            class="form-control"
                            placeholder="Cari Kode, No. Relaas, atau Nama Klien..."
                            value="{{ request('search') }}">
                    </div>

                    <div style="width:160px;">
                        <label for="start_date" class="form-label text-sm">
                            Tanggal Mulai
                        </label>
                        <input
                            type="date"
                            name="start_date"
                            id="start_date"
                            class="form-control"
                            value="{{ request('start_date') }}">
                    </div>

                    <div style="width:160px;">
                        <label for="end_date" class="form-label text-sm">
                            Tanggal Selesai
                        </label>
                        <input
                            type="date"
                            name="end_date"
                            id="end_date"
                            class="form-control"
                            value="{{ request('end_date') }}">
                    </div>

                    <div>
                        <button type="submit" class="btn btn-primary mb-0 px-4">
                            Cari
                        </button>
                    </div>
                </form>

                {{-- KONDISI 1: Jika Berhasil Menemukan Spesifik 1 Relaas Info beserta Dokumennya --}}
                @if ($relaasInfo)
                    <div class="card mb-4 shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0 text-white">Detail Transaksi Akta</h5>
                        </div>
                        <div class="card-body pb-2">
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
                                    <h6><strong>Nomor Transaksi / Relaas</strong></h6>
                                    <div class="d-flex align-items-center gap-2">
                                        <p class="text-muted text-sm mb-0">{{ $relaasInfo->relaas_number ?? '-' }}</p>
                                        @if($relaasInfo->relaas_number)
                                            <button
                                                type="button"
                                                class="btn btn-link p-0 text-primary copy-btn"
                                                onclick="copyValue(this, '{{ $relaasInfo->relaas_number }}')"
                                                title="Salin Nomor Transaksi">
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
                                    <h6 class="mb-1"><strong>Tipe Akta</strong></h6>
                                    <p class="text-muted text-sm">{{ $relaasInfo->akta_type->type ?? '-' }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>Status</strong></p>
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
                            <h5>Dokumen Akta</h5>
                            <a href="{{ route('relaas-documents.create', $relaasInfo->id) }}"
                                class="btn btn-primary btn-sm">+ Tambah Dokumen</a>
                        </div>

                        <div class="table-responsive p-0">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
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
                                        <tr class="text-center text-sm">
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $doc->name }}</td>
                                            <td>{{ $doc->type ?? '-' }}</td>
                                            <td>{{ $doc->uploaded_at ? $doc->uploaded_at->format('d F Y H:i:s') : '-' }}</td>
                                            <td class="text-center">
                                                @if ($doc->file_url)
                                                    <button type="button" class="btn btn-sm btn-primary mb-0"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#fileModal{{ $doc->id }}">
                                                        <i class="fa fa-file me-1"></i> Lihat Akta Dokumen
                                                    </button>

                                                    @php
                                                        $isImage = in_array($doc->file_type, ['jpg', 'jpeg', 'png', 'svg', 'webp']);
                                                        $isPdf = $doc->file_type === 'pdf';
                                                        $modalSize = $isPdf ? 'modal-xl' : ($isImage ? 'modal-lg' : '');
                                                    @endphp
                                                    
                                                    <div class="modal fade" id="fileModal{{ $doc->id }}" tabindex="-1" aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-centered {{ $modalSize }}">
                                                            <div class="modal-content">
                                                                <div class="modal-header py-2">
                                                                    <h5 class="modal-title">File Dokumen Akta</h5>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                </div>
                                                                <div class="modal-body text-center">
                                                                    @if (in_array($doc->file_type, ['pdf', 'png', 'jpg', 'jpeg', 'svg']))
                                                                        <embed src="{{ route('ppat-documents.view-pdf', ['id' => $doc->id]) }}" type="application/pdf" width="100%" height="700px" />
                                                                    @else
                                                                        <p class="text-muted">File tidak dapat ditampilkan.</p>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @else
                                                    <span class="badge bg-secondary">Tidak Ada File</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex gap-1 justify-content-center">
                                                    {{-- Tombol Edit --}}
                                                    <a href="{{ route('relaas-documents.edit', [$relaasInfo->id, $doc->id]) }}" class="btn btn-info btn-sm mb-0">Edit</a>
                                                    
                                                    {{-- Tombol Pemicu Konfirmasi Hapus --}}
                                                    <button type="button" class="btn btn-danger btn-sm mb-0" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#deleteModal{{ $doc->id }}">
                                                        Hapus
                                                    </button>
                                                </div>

                                                {{-- Modal Konfirmasi Hapus --}}
                                                <div class="modal fade" id="deleteModal{{ $doc->id }}" tabindex="-1" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered">
                                                        <div class="modal-content">
                                                            <div class="modal-header py-2">
                                                                <h5 class="modal-title text-start w-100">Konfirmasi Hapus</h5>
                                                                <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body text-start">
                                                                Apakah Anda yakin ingin menghapus dokumen <strong>{{ $doc->name }}</strong>? Tindakan ini tidak dapat dibatalkan.
                                                            </div>
                                                            <div class="modal-footer py-2">
                                                                <button type="button" class="btn btn-secondary btn-sm mb-0" data-bs-dismiss="modal">Batal</button>
                                                                
                                                                {{-- Form Hapus dengan Route Relaas --}}
                                                                <form action="{{ route('relaas-documents.destroy', $doc->id) }}" method="POST" class="d-inline">
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
                                        <tr>
                                            <td colspan="6" class="text-center text-muted text-sm">Belum ada dokumen akta.</td>
                                        </tr>
                                    @endempty
                                </tbody>
                            </table>
                        </div>
                    </div>

                {{-- KONDISI 2: Jika Menampilkan Banyak Hasil / Hasil Pencarian Rentang Tanggal ($transactions) --}}
                @elseif ($transactions && $transactions->isNotEmpty())
                    <div class="mb-0">
                        <h5>Daftar Transaksi Relaas</h5>
                        <div class="table-responsive p-0">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr class="text-center text-sm">
                                        <th>#</th>
                                        <th>No. Relaas</th>
                                        <th>Nama Klien</th>
                                        <th>Tipe Akta</th>
                                        <th>Tanggal</th>
                                        <th>Jumlah Dokumen</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($transactions as $tx)
                                        <tr class="text-center text-sm">
                                            <td>{{ $transactions->firstItem() + $loop->index }}</td>
                                            <td>{{ $tx->relaas_number ?? '-' }}</td>
                                            <td>{{ $tx->client->fullname ?? '-' }}</td>
                                            <td>{{ $tx->akta_type->type ?? '-' }}</td>
                                            <td>{{ $tx->story_date ? \Carbon\Carbon::parse($tx->story_date)->format('d F Y') : '-' }}</td>
                                            <td><span class="badge bg-info">{{ $tx->documents_count }} Dokumen</span></td>
                                            <td>
                                                <a href="{{ route('relaas-documents.index', ['search' => $tx->transaction_code]) }}" 
                                                class="btn btn-sm btn-info">
                                                    <i class="fa fa-eye"></i> Lihat Dokumen
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