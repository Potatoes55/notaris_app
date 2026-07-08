@extends('layouts.app')

@section('title', 'Dokumen Akta')

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Akta Notaris / Dokumen Akta'])

    @include('components.notaris-menu')

    <div class="row mt-4 mx-4">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center  mb-0 pb-0">
                    <h5>Dokumen Akta</h5>
                </div>
                <div class="card-body pt-2 pb-0">
                <form method="GET" action="{{ route('akta-documents.index') }}"
                    class="d-flex flex-wrap gap-2 mb-3 justify-content-end align-items-end no-spinner">
                    @csrf

                    <div style="flex:1; min-width:200px;">
                        <label for="transaction_code" class="form-label text-sm">
                            Kode Transaksi
                        </label>
                        <input
                            type="text"
                            name="transaction_code"
                            id="transaction_code"
                            class="form-control"
                            placeholder="Cari Kode transaksi..."
                            value="{{ $filters['transaction_code'] ?? '' }}">
                    </div>
                    <div style="flex:1; min-width:200px;">
                        <label for="transaction_code" class="form-label text-sm">
                            Nama Client
                        </label>
                        <input
                            type="text"
                            name="fullname"
                            id="fullname"
                            class="form-control"
                            placeholder="Cari Nama Client..."
                            value="{{ $filters['fullname'] ?? '' }}">
                    </div>

                    <div style="flex:1; min-width:200px;">
                        <label for="akta_number" class="form-label text-sm">
                            Nomor Akta
                        </label>
                        <input
                            type="text"
                            name="akta_number"
                            id="akta_number"
                            class="form-control"
                            placeholder="Cari nomor akta..."
                            value="{{ $filters['akta_number'] ?? '' }}">
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
                            <button type="submit" class="btn btn-primary btn-sm mb-0" style="height: 36px;">Cari</button>
                        </div>
                </form>

                    {{-- Tampilkan transaksi jika ada --}}
                    @if ($transaction)
                        <div class="card mb-4 shadow-sm mt-3">
                            <div class="card-header bg-primary text-white">
                                <h6 class="mb-0 text-white">Detail Transaksi Akta</h6>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                <div class="col-md-6">
                                    <h6 class="mb-1"><strong>Kode Klien</strong></h6>

                                    <div class="d-flex align-items-center gap-2">
                                        <p class="text-muted text-sm mb-0">
                                            {{ $transaction->client_code }}
                                        </p>

                                        <button
                                            type="button"
                                            class="btn btn-link p-0 text-primary"
                                            onclick="copyValue(this, '{{ $transaction->client_code }}')">
                                            <i class="fa-solid fa-copy"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <h6 class="mb-1"><strong>Nomor Akta</strong></h6>

                                    <div class="d-flex align-items-center gap-2">
                                        <p class="text-muted text-sm mb-0">
                                            {{ $transaction->akta_number ?? '-' }}
                                        </p>

                                        @if($transaction->akta_number)
                                            <button
                                                type="button"
                                                class="btn btn-link p-0 text-primary"
                                                onclick="copyValue(this, '{{ $transaction->akta_number }}')">
                                                <i class="fa-solid fa-copy"></i>
                                            </button>
                                        @endif
                                    </div>
                                </div>
                                    {{-- <div class="col-md-6">
                                        <h6 class="mb-1"><strong>Tipe Akta</strong></h6>
                                        <p class="text-muted text-sm">{{ $transaction->akta_type->type ?? '-' }}</p>
                                    </div> --}}
                                    <div class="col-md-6">
                                        <h6 class="mb-1"><strong>Notaris</strong></h6>
                                        <p class="text-muted text-sm">{{ $transaction->notaris->display_name ?? '-' }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="mb-1"><strong>Klien</strong></h6>
                                        <p class="text-muted text-sm">{{ $transaction->client->fullname ?? '-' }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="mb-1"><strong>Status</strong></p>
                                        <span
                                            class="badge text-capitalize
                                    @switch($transaction->status)
                                        @case('draft') bg-secondary @break
                                        @case('diproses') bg-warning @break
                                        @case('selesai') bg-success @break
                                        @case('dibatalkan') bg-danger @break
                                        @default bg-light text-dark
                                    @endswitch
                                ">
                                            {{ $transaction->status }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Tabel Dokumen --}}
                        <div class="mb-1">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h5>Dokumen Akta</h5>
                            <div class="d-flex gap-2">
                                
                                <a href="{{ route('akta-documents.createData', ['akta_transaction_id' => $transaction->id]) }}"
                                    class="btn btn-primary btn-sm mb-0">+ Tambah Dokumen Akta</a>

                                {{-- Tombol Tambah SK Kemenkumham --}}
                                @if($transaction->akta_type && in_array(strtolower($transaction->akta_type->category), ['perubahan', 'pembubaran']))
                                    <a href="{{ route('akta-documents.createData', ['akta_transaction_id' => $transaction->id]) }}?type=sk_kemenkum"
                                        class="btn btn-success btn-sm mb-0">
                                        <i class="fa-solid fa-file-signature me-1"></i> + Input SK Kemenkum
                                    </a>
                                @endif
                            </div>
                        </div>
                            <div class="table-responsive p-0">
                                <table class="table align-items-center mb-0">
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
                                                <td>{{ $documents->firstItem() + $loop->index }}</td>
                                                <td>{{ $doc->name }}</td>
                                                <td>{{ $doc->type }}</td>
                                                <td>
                                                    {{ $doc->uploaded_at ? \Carbon\Carbon::parse($doc->uploaded_at)->format('d F Y H:i:s') : '-' }}
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
                                                                                src="{{ route('akta-documents.view-pdf', ['id' => $doc->id]) }}"
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
                                                <td>
                                                    <a href="{{ route('akta-documents.edit', $doc->id) }}"
                                                        class="btn btn-info btn-sm mb-0">Edit</a>
                                                    <form action="{{ route('akta-documents.destroy', $doc->id) }}"
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
                                                <td colspan="6" class="text-center text-sm">Belum ada akta dokumen.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                                <div class="d-flex justify-content-end mt-3">
                                    {{ $documents->links() }}
                                </div>
                            </div>
                        </div>
                    @else
                        <p class="text-center text-muted text-sm mt-4">Silakan cari Kode Klien atau nomor akta untuk
                            menampilkan
                            transaksi.</p>
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

            notyf.success('Berhasil disalin');

            setTimeout(() => {
                icon.classList.remove('fa-check');
                icon.classList.add('fa-copy');
            }, 1000);
        }
    </script>
    @endpush
@endsection
