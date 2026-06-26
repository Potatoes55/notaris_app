@extends('layouts.app')

@section('title', 'PIC Dokumen')

@section('content')

@include('layouts.navbars.auth.topnav', [
    'title' => $module . ' / PIC Dokumen'
])

@if ($module == 'PPAT')
    @include('components.ppat-menu')
@elseif ($module == 'Proses Lain')
    @include('components.proseslain-menu')
@else
    @include('components.notaris-menu')
@endif

    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0 d-flex justify-content-between align-items-center mb-4">
                        <h5 class="mb-0">PIC Dokumen</h5>
                        <a href="{{ route('pic_documents.create') }}" class="btn btn-sm btn-primary mb-0">
                            + Tambah PIC Dokumen
                        </a>
                    </div>
                    <form method="GET" action="{{ route('pic_documents.index') }}" class="d-flex gap-2 ms-auto me-4 mb-0"
                        style="max-width: 600px; width: 100%" class="no-spinner">
                        <input type="text" name="search" class="form-control form-control"
                            placeholder="Cari Kode Dokumen / Nama PIC" value="{{ request('search') }}">
                        <select name="status" class="form-select form-select">
                            <option value="">Semua Status</option>
                            <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Dikirim
                            </option>
                            <option value="process" {{ request('status') == 'process' ? 'selected' : '' }}>Diproses
                            </option>
                            <option value="received" {{ request('status') == 'received' ? 'selected' : '' }}>Diterima
                            </option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai
                            </option>
                        </select>
                        <button class="btn btn-sm btn-primary mb-0" type="submit">Cari</button>
                    </form>
                    <hr>
                    <div class="card-body px-0 pt-0 pb-0">
                        <div class="table-responsive p-0">
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Kode Dokumen</th>
                                        <th>PIC Staff</th>
                                        <th>Klien</th>
                                        <th>Tipe Dokumen</th>
                                        <th>Tanggal Diterima</th>
                                        <th>Status</th>
                                        <th>
                                            Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($picDocuments as $doc)
                                        <tr class="text-center text-sm">
                                            <td>{{ $picDocuments->firstItem() + $loop->index }}</td>
                                            <td>{{ $doc->pic_document_code }}</td>
                                            <td>{{ $doc->pic->full_name ?? '-' }}</td>
                                            <td>{{ $doc->client->fullname ?? '-' }}</td>
                                            <td class="text-capitalize">{{ $doc->transaction_type }}</td>
                                            <td>
                                                {{ $doc->received_date ? \Carbon\Carbon::parse($doc->received_date)->translatedFormat('d-m-Y H:i:s') : '-' }}
                                            </td>
                                            @php
                                                $badgeColors = [
                                                    'delivered' => 'secondary',
                                                    'completed' => 'success',
                                                    'process' => 'warning',
                                                    'received' => 'info',
                                                ];

                                                $statusText = [
                                                    'delivered' => 'Dikirim',
                                                    'completed' => 'Selesai',
                                                    'process' => 'Diproses',
                                                    'received' => 'Diterima',
                                                ];
                                            @endphp

                                            <td>
                                                <span
                                                    class="badge bg-{{ $badgeColors[$doc->status] ?? 'secondary' }} text-capitalize">
                                                    {{ $statusText[$doc->status] ?? $doc->status }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <a href="{{ route('pic_documents.print', $doc->id) }}"
                                                    class="btn btn-danger btn-xs mb-0" target="_blank" title="Cetak PDF">
                                                    <i class="bi bi-filetype-pdf " style="font-size:14px;"></i> Cetak
                                                </a>
                                                <a href="{{ route('pic_documents.edit', $doc->id) }}"
                                                    class="btn btn-sm btn-info mb-0">Edit</a>
                                                <button type="button" class="btn btn-sm btn-danger mb-0"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#deleteModal{{ $doc->id }}">
                                                    Hapus
                                                </button>
                                                <div class="modal fade" id="deleteModal{{ $doc->id }}" tabindex="-1"
                                                    aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered">
                                                        <div class="modal-content">
                                                            <div class="modal-header  text-white py-2">
                                                                <h5 class="modal-title">Konfirmasi Hapus</h5>
                                                                <button type="button" class="btn-close btn-close-white"
                                                                    data-bs-dismiss="modal"></button>
                                                            </div>

                                                            <div class="modal-body text-center">
                                                                <p class="mb-0 text-sm">Apakah Anda yakin ingin menghapus
                                                                    PIC
                                                                    Dokumen ini?</p>
                                                            </div>

                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary btn-sm"
                                                                    data-bs-dismiss="modal">Batal</button>

                                                                <form
                                                                    action="{{ route('pic_documents.destroy', $doc->id) }}"
                                                                    method="POST" class="d-inline">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit"
                                                                        class="btn btn-danger btn-sm">Hapus</button>
                                                                </form>
                                                            </div>

                                                        </div>
                                                    </div>


                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="10" class="text-center text-muted text-sm">Tidak ada data pic
                                                dokumen
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                            <div class="d-flex justify-content-end mt-3">
                                {{ $picDocuments->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
