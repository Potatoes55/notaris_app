@extends('layouts.app')

@section('title', 'Covernote')

@section('content')

@include('layouts.navbars.auth.topnav', [
    'title' => $module . ' / Covernote'
])

@if ($module == 'PPAT')
    @include('components.ppat-menu')
@else
    @include('components.notaris-menu')
@endif

    <div class="row mt-4 mx-4">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center mb-4">
                    <h5 class="mb-0">Covernote</h5>
                    <div class="d-flex gap-2">
                        {{-- TOMBOL CETAK PDF --}}
                        <a href="{{ route('covernotes.print', request()->all()) }}" target="_blank" class="btn btn-danger btn-sm mb-0">
                            <i class="fas fa-file-pdf me-1"></i> Cetak PDF
                        </a>
                        <a href="{{ route('covernotes.create') }}" class="btn btn-primary btn-sm mb-0">
                            + Tambah Covernote
                        </a>
                    </div>
                </div>
                
                <form method="GET" action="{{ route('covernotes.index') }}" class="d-flex gap-2 ms-auto me-4"
                    style="width: 500px; max-width: 100%;" class="no-spinner">
                    <input type="text" name="search" placeholder="Cari nomor covernote..." value="{{ request('search') }}"
                        class="form-control">
                    <button type="submit" class="btn btn-primary btn-sm mb-0">Cari</button>
                </form>
                <hr>
                
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="th-title">#</th>
                                    <th class="th-title">Klien</th>
                                    <th class="th-title">Kode Klien</th>
                                    <th class="th-title">No Surat</th>
                                    <th class="th-title">Penerima</th>
                                    <th class="th-title">Subjek</th>
                                    <th class="th-title">Tanggal</th>
                                    <th class="th-title">Masa Berlaku</th>
                                    <th class="th-title">Isi Surat/Lampiran</th>
                                    <th class="th-title">File Surat</th>
                                    <th class="th-title">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($covernotes as $covernote)
                                    <tr class="text-sm text-center">
                                        <td>{{ $covernotes->firstItem() + $loop->index }}</td>
                                        <td>{{ $covernote->client->fullname ?? '-' }}</td>
                                        <td>
                                            <div class="d-flex justify-content-center align-items-center gap-2">
                                                <span>{{ $covernote->client_code ?? '-' }}</span>

                                                @if($covernote->client_code)
                                                    <button
                                                        type="button"
                                                        class="btn btn-link p-0 text-primary"
                                                        onclick="copyValue(this, '{{ $covernote->client_code }}')">
                                                        <i class="fa-solid fa-copy"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>

                                        <td>
                                            <div class="d-flex justify-content-center align-items-center gap-2">
                                                <span>{{ $covernote->covernote_number ?? '-' }}</span>

                                                @if($covernote->covernote_number)
                                                    <button
                                                        type="button"
                                                        class="btn btn-link p-0 text-primary"
                                                        onclick="copyValue(this, '{{ $covernote->covernote_number }}')">
                                                        <i class="fa-solid fa-copy"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                        <td>{{ $covernote->recipient ?? '-' }}</td>
                                        <td>{{ $covernote->subject ?? '-' }}</td>
                                        <td>{{ $covernote->date ? \Carbon\Carbon::parse($covernote->date)->format('d F Y') : '-' }}</td>
                                        <td>{{ $covernote->expiry_date ? \Carbon\Carbon::parse($covernote->expiry_date)->format('d F Y') : '-' }}</td>
                                        <td title="{{ $covernote->attachment }}">
                                            {{ \Illuminate\Support\Str::limit($covernote->attachment, 40, '...') }}
                                        </td>
                                        <td>
                                            @if ($covernote->file_path)
                                                @php
                                                    $extension = strtolower(
                                                        pathinfo($covernote->file_path, PATHINFO_EXTENSION),
                                                    );
                                                    $isImage = in_array($extension, [
                                                        'jpg', 'jpeg', 'png', 'svg', 'webp'
                                                    ]);
                                                    $isPdf = $extension === 'pdf';
                                                @endphp

                                                {{-- BUTTON PREVIEW --}}
                                                <button type="button" class="btn btn-primary btn-sm mb-0"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#fileModal{{ $covernote->id }}">
                                                    Lihat File
                                                </button>

                                                {{-- MODAL PREVIEW --}}
                                                <div class="modal fade" id="fileModal{{ $covernote->id }}"
                                                    tabindex="-1" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered {{ $isPdf ? 'modal-xl' : 'modal-lg' }}">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Preview Dokumen Covernote</h5>
                                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <div class="modal-body text-center">
                                                                {{-- IMAGE --}}
                                                                @if ($isImage)
                                                                    <img src="{{ asset('storage/' . $covernote->file_path) }}"
                                                                        class="img-fluid rounded shadow"
                                                                        style="max-height: 85vh; object-fit: contain; margin: auto;">
                                                                {{-- PDF --}}
                                                                @elseif ($isPdf)
                                                                    <iframe src="{{ asset('storage/' . $covernote->file_path) }}"
                                                                        width="100%" height="750px" style="border: none;">
                                                                    </iframe >
                                                                {{-- OTHER --}}
                                                                @else
                                                                    <div class="text-center">
                                                                        <p class="text-muted">File tidak dapat ditampilkan.</p>
                                                                        <a href="{{ asset('storage/' . $covernote->file_path) }}"
                                                                            target="_blank" class="btn btn-secondary btn-sm">
                                                                            Download File
                                                                        </a>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                <span class="text-muted">Tidak ada file</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('covernotes.edit', $covernote->id) }}"
                                                class="btn btn-info btn-sm mb-0">Edit</a>
                                            <form action="{{ route('covernotes.destroy', $covernote->id) }}"
                                                method="POST" class="d-inline-block">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm mb-0">Hapus</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="11" class="text-center text-muted text-sm py-4">
                                            Belum ada data covernote.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <div class="mt-3 d-flex justify-content-end me-4">
                            {{ $covernotes->links() }}
                        </div>
                    </div>
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