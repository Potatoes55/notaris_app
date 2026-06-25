@extends('layouts.app')

@section('title', 'Surat Keluar')

@section('content')

@include('layouts.navbars.auth.topnav', [
    'title' => $module . ' / Surat Keluar'
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
                    <h5 class="mb-0">Surat Keluar</h5>
                    <a href="{{ route('notary-letters.create') }}" class="btn btn-primary btn-sm mb-0">
                        + Tambah Surat Keluar
                    </a>
                </div>
                <form method="GET" action="{{ route('notary-letters.index') }}" class="d-flex gap-2 ms-auto me-4"
                    style="width: 500px; max-width: 100%;" class="no-spinner">
                    <input type="text" name="search" placeholder="Cari nomor surat..." value="{{ request('search') }}"
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
                                    <th class="th-title">Nama Klien</th>
                                    <th class="th-title">Kode Klien</th>
                                    <th class="th-title">Nomor Surat</th>
                                    <th class="th-title">Jenis</th>
                                    <th class="th-title">Penerima</th>
                                    <th class="th-title">Subjek</th>
                                    <th class="th-title">Tanggal</th>
                                    <th class="th-title">Lampiran</th>
                                    <th class="th-title">File</th>
                                    <th class="th-title">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($notaryLetters as $letter)
                                    <tr class="text-sm text-center">
                                        <td>{{ $notaryLetters->firstItem() + $loop->index }}</td>
                                        <td>{{ $letter->client->fullname ?? '-' }}</td>
                                        <td>
                                            <div class="d-flex justify-content-center align-items-center gap-2">
                                                <span>{{ $letter->client_code ?? '-' }}</span>

                                                @if($letter->client_code)
                                                    <button
                                                        type="button"
                                                        class="btn btn-link p-0 text-primary"
                                                        onclick="copyValue(this, '{{ $letter->client_code }}')">
                                                        <i class="fa-solid fa-copy"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>

                                        <td>
                                            <div class="d-flex justify-content-center align-items-center gap-2">
                                                <span>{{ $letter->letter_number ?? '-' }}</span>

                                                @if($letter->letter_number)
                                                    <button
                                                        type="button"
                                                        class="btn btn-link p-0 text-primary"
                                                        onclick="copyValue(this, '{{ $letter->letter_number }}')">
                                                        <i class="fa-solid fa-copy"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                        <td>{{ $letter->type ?? '-' }}</td>
                                        <td>{{ $letter->recipient ?? '-' }}</td>
                                        <td>{{ $letter->subject ?? '-' }}</td>
                                        <td>{{ $letter->date ? \Carbon\Carbon::parse($letter->date)->format('d F Y') : '-' }}
                                        </td>
                                        <td>{{ $letter->attachment ?? '-' }}</td>
                                        <td>
                                            @if ($letter->file_path)
                                                @php
                                                    $extension = strtolower(
                                                        pathinfo($letter->file_path, PATHINFO_EXTENSION),
                                                    );
                                                    $isImage = in_array($extension, [
                                                        'jpg',
                                                        'jpeg',
                                                        'png',
                                                        'svg',
                                                        'webp',
                                                    ]);
                                                    $isPdf = $extension === 'pdf';
                                                @endphp

                                                {{-- BUTTON --}}
                                                <button type="button" class="btn btn-primary btn-sm mb-0"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#fileModal{{ $letter->id }}">
                                                    Lihat File
                                                </button>

                                                {{-- MODAL --}}
                                                <div class="modal fade" id="fileModal{{ $letter->id }}"
                                                    tabindex="-1" aria-hidden="true">

                                                    <div
                                                        class="modal-dialog modal-dialog-centered
                                                        {{ $isPdf ? 'modal-xl' : 'modal-lg' }}">

                                                        <div class="modal-content">

                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Preview Dokumen</h5>
                                                                <button type="button" class="btn-close btn-close-white"
                                                                    data-bs-dismiss="modal">
                                                                </button>
                                                            </div>

                                                            <div class="modal-body text-center">

                                                                {{-- IMAGE --}}
                                                                @if ($isImage)
                                                                    <img src="{{ asset('storage/' . $letter->file_path) }}"
                                                                        class="img-fluid rounded shadow"
                                                                        style="max-height: 85vh; object-fit: contain; margin: auto;">

                                                                    {{-- PDF --}}
                                                                @elseif ($isPdf)
                                                                    <iframe
                                                                        src="{{ asset('storage/' . $letter->file_path) }}"
                                                                        width="100%" height="750px"
                                                                        style="border: none;">
                                                                    </iframe>

                                                                    {{-- OTHER --}}
                                                                @else
                                                                    <div class="text-center">
                                                                        <p class="text-muted">File tidak dapat
                                                                            ditampilkan.</p>
                                                                        <a href="{{ asset('storage/' . $letter->file_path) }}"
                                                                            target="_blank"
                                                                            class="btn btn-secondary btn-sm">
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
                                            <a href="{{ route('notary-letters.edit', $letter->id) }}"
                                                class="btn btn-info btn-sm mb-0">Edit</a>
                                            <form action="{{ route('notary-letters.destroy', $letter->id) }}"
                                                method="POST" class="d-inline-block">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm mb-0">Hapus</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center text-muted text-sm">Belum ada data surat
                                            keluar.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <div class="mt-3 d-flex justify-content-end">
                            {{ $notaryLetters->links() }}
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
