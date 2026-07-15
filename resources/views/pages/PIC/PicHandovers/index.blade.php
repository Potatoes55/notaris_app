@extends('layouts.app')

@section('title', 'Serah Terima Dokumen')

@section('content')

@include('layouts.navbars.auth.topnav', [
    'title' => $module . ' / Serah Terima Dokumen'
])

@php
    $role = session('login_role');
@endphp

@if($role !== 'staff')
    @if ($module === 'PPAT')
        @include('components.ppat-menu')
    @elseif ($module === 'Proses Lain')
        @include('components.proseslain-menu')
    @else
        @include('components.notaris-menu')
    @endif
@endif

    <div class="row mt-4 mx-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                    <h5>Serah Terima Dokumen</h5>
                    @if ($module == 'PPAT')
                        <a href="{{ route('ppat.pic.handovers.create') }}"
                            class="btn btn-primary btn-sm mb-0">
                            + Tambah Serah Terima
                        </a>
                    @else
                        <a href="{{ route('notaris.pic.handovers.create') }}"
                            class="btn btn-primary btn-sm mb-0">
                            + Tambah Serah Terima
                        </a>
                    @endif
                </div>
                <form method="GET" action="{{ route('pic_handovers.index') }}"
                    class="d-flex justify-content-end gap-2 mb-0 mx-3 mt-3">
                    <div class="input-group" style="max-width: 400px;">
                        <input type="text" name="search" class="form-control" placeholder="Cari kode dokumen"
                            value="{{ request('search') }}">
                        <button class="btn btn-primary mb-0" type="submit">Cari</button>
                    </div>
                </form>
                <hr>
                <div class="card-body pb-0 pt-0">
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr class="text-center">
                                    <th>#</th>
                                    <th>Kode Dokumen</th>
                                    <th>Tanggal</th>
                                    <th>Nama Penerima</th>
                                    <th>Kontak Penerima</th>
                                    <th>Catatan</th>
                                    <th>File</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($handovers as $handover)
                                    <tr class="text-center text-sm">
                                        <td>{{ $handovers->firstItem() + $loop->index }}</td>
                                        <td>{{ $handover->picDocument?->pic_document_code ?? 'Dokumen Tidak Ditemukan' }}</td>
                                        <td>{{ $handover->handover_date }}</td>
                                        <td>{{ $handover->recipient_name }}</td>
                                        <td>{{ $handover->recipient_contact }}</td>
                                        <td>{{ $handover->note }}</td>
                                        <td>
                                            @if ($handover->file_path)
                                                @php
                                                    $extension = strtolower(pathinfo($handover->file_path, PATHINFO_EXTENSION));
                                                    $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'svg', 'webp']);
                                                    $isPdf = $extension === 'pdf';
                                                @endphp
                                                <button type="button" class="btn btn-primary btn-sm mb-0" data-bs-toggle="modal"
                                                    data-bs-target="#fileModal{{ $handover->id }}">
                                                    Lihat File
                                                </button>
                                                {{-- Modal File --}}
                                                <div class="modal fade" id="fileModal{{ $handover->id }}" tabindex="-1" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered {{ $isPdf ? 'modal-xl' : 'modal-lg' }}">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Preview Dokumen</h5>
                                                                <button type="button" class="btn" data-bs-dismiss="modal" style="font-size: 1.5rem; line-height: 1; color: #000; opacity: .5; padding: 0.5rem; border: none; background: transparent;">
                                                                    &times;
                                                                </button>
                                                            </div>
                                                            <div class="modal-body text-center">
                                                                @if ($isImage)
                                                                    <img src="{{ asset('storage/' . $handover->file_path) }}" class="img-fluid rounded shadow" style="max-height: 85vh; object-fit: contain;">
                                                                @elseif ($isPdf)
                                                                    <iframe src="{{ asset('storage/' . $handover->file_path) }}" width="100%" height="750px" style="border: none;"></iframe>
                                                                @else
                                                                    <a href="{{ asset('storage/' . $handover->file_path) }}" target="_blank" class="btn btn-secondary btn-sm">Download File</a>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('pic_handovers.print', $handover->id) }}" class="btn btn-sm btn-dark mb-0" target="_blank">
                                                <i class="fas fa-file-pdf" style="font-size: 15px;"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-danger mb-0" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $handover->id }}">
                                                Hapus
                                            </button>
                                            {{-- Modal Delete --}}
                                            <div class="modal fade" id="deleteModal{{ $handover->id }}" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Konfirmasi Hapus</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body text-start">
                                                            Apakah Anda yakin ingin menghapus data serah terima ini?<br>
                                                            <strong>Kode Dokumen: {{ $handover->picDocument?->pic_document_code ?? 'Data Terhapus' }}</strong>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                                                            <form action="{{ route('pic_handovers.destroy', $handover->id) }}" method="POST" class="d-inline">
                                                                @csrf @method('DELETE')
                                                                <button class="btn btn-danger btn-sm">Hapus</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-sm">Tidak ada data serah terima.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-end mt-3">
                        {{ $handovers->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection