@extends('layouts.app')

@section('title', 'Legalisasi')

@section('content')

    @include('layouts.navbars.auth.topnav', ['title' => 'Legalisasi'])
    @include('components.notaris-menu')

    <div class="row mt-4 mx-4">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Legalisasi</h5>
                        <a href="{{ route('notary-legalisasi.create') }}" class="btn btn-primary btn-sm mb-0">
                            + Tambah Legalisasi
                        </a>
                    </div>
                    <form method="GET" action="{{ route('notary-legalisasi.index') }}"
                        class="d-flex justify-content-end flex-nowrap gap-2 ms-auto  mt-3">
                        <input type="text" name="legalisasi_number" placeholder="Cari nomor legalisasi..."
                            value="{{ request('legalisasi_number') }}" class="form-control" style="max-width: 200px;">

                        <select name="sort" class="form-select flex-shrink-0" style="max-width: 150px;">
                            <option value="" {{ request('sort') == '' ? 'selected' : '' }}>Urutkan</option>
                            <option value="asc" {{ request('sort') == 'asc' ? 'selected' : '' }}>Tanggal Awal</option>
                            <option value="desc" {{ request('sort') == 'desc' ? 'selected' : '' }}>Tanggal Terbaru
                            </option>
                        </select>
                        <button type="submit" class="btn btn-primary btn-sm flex-shrink-0 mb-0">Cari</button>
                    </form>
                </div>
                <hr>
                <div class="card-body px-0 pt-0 pb-0">

                    <div class="table-responsive p-0">
                        <div class="table-wrapper">
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr class="text-center">
                                        <th>#</th>
                                        <th>Nama Klien</th>
                                        <th>Kode Klien</th>
                                        <th>Nomor Legalisasi</th>
                                        <th>Nama Pemohon</th>
                                        <th>Nama Petugas</th>
                                        <th>Jenis Dokumen</th>
                                        <th>Nomor Dokumen</th>
                                        <th>Tanggal Permintaan</th>
                                        <th>Tanggal Rilis</th>
                                        {{-- <th>Catatan</th> --}}
                                        <th>File</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($data as $item)
                                        <tr class="text-center text-sm">
                                            <td>{{ $data->firstItem() + $loop->index }}</td>
                                            <td>{{ $item->client->fullname }}</td>
                                            <td>{{ $item->client_code }}</td>
                                            <td>{{ $item->legalisasi_number }}</td>
                                            <td>{{ $item->applicant_name }}</td>
                                            <td>{{ $item->officer_name }}</td>
                                            <td>{{ $item->document_type }}</td>
                                            <td>{{ $item->document_number }}</td>
                                            <td>{{ $item->request_date ? \Carbon\Carbon::parse($item->request_date)->format('d-m-Y') : '-' }}
                                            </td>
                                            <td>{{ $item->release_date ? \Carbon\Carbon::parse($item->release_date)->format('d-m-Y') : '-' }}
                                            </td>
                                            {{-- <td>{{ $item->notes }}</td> --}}
                                            {{-- file image --}}
                                            <td>
                                                @if ($item->file_path)
                                                    @php
                                                        $extension = strtolower(
                                                            pathinfo($item->file_path, PATHINFO_EXTENSION),
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
                                                        data-bs-target="#fileModal{{ $item->id }}">
                                                        Lihat File
                                                    </button>

                                                    {{-- MODAL --}}
                                                    <div class="modal fade" id="fileModal{{ $item->id }}"
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
                                                                        <img src="{{ asset('storage/' . $item->file_path) }}"
                                                                            class="img-fluid rounded shadow"
                                                                            style="max-height: 85vh; object-fit: contain; margin: auto;">

                                                                        {{-- PDF --}}
                                                                    @elseif ($isPdf)
                                                                        <iframe
                                                                            src="{{ asset('storage/' . $item->file_path) }}"
                                                                            width="100%" height="750px"
                                                                            style="border: none;">
                                                                        </iframe>

                                                                        {{-- OTHER --}}
                                                                    @else
                                                                        <div class="text-center">
                                                                            <p class="text-muted">File tidak dapat
                                                                                ditampilkan.</p>
                                                                            <a href="{{ asset('storage/' . $item->file_path) }}"
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
                                                <a href="{{ route('notary-legalisasi.edit', $item->id) }}"
                                                    class="btn btn-info btn-sm mb-0">Edit</a>
                                                <form action="{{ route('notary-legalisasi.destroy', $item->id) }}"
                                                    method="POST" class="d-inline-block">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm mb-0">Hapus</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="11" class="text-center text-muted text-sm">Belum ada data
                                                legalisasi.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>

                            <div class="mt-3 d-flex justify-content-end ">
                                {{ $data->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
