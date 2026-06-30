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

                    {{-- Form Pencarian --}}
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
                                        <h6><strong>Nomor Transaksi</strong></h6>
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
                                        <span
                                            class="badge text-capitalize
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
                                    {{-- <div class="col-md-6">
                                        <h6><strong>Jenis Akta</strong></h6>
                                        <p class="text-muted text-sm">{{ $relaasInfo->title?? '-' }}</p>
                                    </div> --}}
                                </div>
                            </div>
                        </div>

                        <div class="mb-0">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h5>Dokumen Akta</h5>
                                <a href="{{ route('relaas-documents.create', $relaasInfo->id) }}"
                                    class="btn btn-primary btn-sm">+
                                    Tambah Dokumen</a>
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
                                            {{-- <th>Status</th> --}}
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($documents as $doc)
                                            <tr class="text-center text-sm">
                                                <td>{{ $documents->firstItem() + $loop->index }}</td>
                                                <td>{{ $doc->name }}</td>
                                                <td>{{ $doc->type ?? '-' }}</td>
                                                <td>{{ $doc->uploaded_at ? $doc->uploaded_at->format('d F Y H:i:s') : '-' }}
                                                </td>
                                                <td class="text-center">
                                                    @if ($doc->file_url)
                                                        <!-- Tombol buka modal -->
                                                        <button type="button" class="btn btn-sm btn-primary mb-0"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#fileModal{{ $doc->id }}">
                                                            <i class="fa fa-file me-1"></i> Lihat Akta Dokumen
                                                        </button>

                                                        @php
                                                            $isImage = in_array($doc->file_type, [
                                                                'jpg',
                                                                'jpeg',
                                                                'png',
                                                                'svg',
                                                                'webp',
                                                            ]);
                                                            $isPdf = $doc->file_type === 'pdf';

                                                            $modalSize = $isPdf
                                                                ? 'modal-xl'
                                                                : ($isImage
                                                                    ? 'modal-lg'
                                                                    : '');
                                                        @endphp
                                                        <div class="modal fade" id="fileModal{{ $doc->id }}"
                                                            tabindex="-1"
                                                            aria-labelledby="fileModalLabel{{ $doc->id }}"
                                                            aria-hidden="true">
                                                            <div
                                                                class="modal-dialog modal-dialog-centered {{ $modalSize }}">
                                                                <div class="modal-content">
                                                                    <div class="modal-header py-2">
                                                                        <h5 class="modal-title"
                                                                            id="fileModalLabel{{ $doc->id }}">
                                                                            File Dokumen Akta
                                                                        </h5>
                                                                        <button type="button"
                                                                            class="btn-close btn-close-white"
                                                                            data-bs-dismiss="modal"
                                                                            aria-label="Close"></button>
                                                                    </div>

                                                                    <div class="modal-body text-center">

                                                                  @if (in_array($doc->file_type, ['pdf', 'png', 'jpg', 'jpeg', 'svg']))
                                                                            <embed
                                                                                src="{{ route('ppat-documents.view-pdf', ['id' => $doc->id]) }}"
                                                                                type="application/pdf" 
                                                                                width="100%"
                                                                                height="700px" />
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
                                                <td class="d-flex gap-1 justify-content-center">
                                                    <a href="{{ route('relaas-documents.edit', [$relaasInfo->id, $doc->id]) }}"
                                                        class="btn btn-info btn-sm mb-0">Edit</a>
                                                    <form action="{{ route('relaas-documents.destroy', $doc->id) }}"
                                                        method="POST" class="d-inline">
                                                        @csrf @method('DELETE')
                                                        <button class="btn btn-danger btn-sm mb-0">Hapus</button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center text-muted text-sm">Belum ada dokumen
                                                    akta.
                                                    .</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div class="d-flex justify-content-end mt-2">
                                {{ $documents->appends(request()->query())->links() }}
                            </div>
                        </div>
                    @else
                        <p class="text-center text-muted text-sm mb-3">Masukkan Kode Transaksi untuk melihat daftar dokumen
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
