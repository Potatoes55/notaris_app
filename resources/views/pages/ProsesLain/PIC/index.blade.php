@extends('layouts.app')

@section('title', 'PIC')

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Pic'])
    @include('components.proseslain-menu')
    <div class="row mt-4 mx-4">
        <div class="col-12">
            <div class="card mb-0">
                <div class="card-header pb-0 mb-0 ">
                    <div class=" d-flex justify-content-between align-items">
                        <h5 class="mb-0">Pic</h5>
                        @if (!is_null(auth()->user()->access_code) && auth()->user()->access_code !== 'staff')
                            <a href="{{ route('proses-lain-pic.create') }}" class="btn btn-primary btn-sm mb-0">
                                + Tambah PIC
                            </a>
                        @else
                            <form method="GET" action="{{ route('proses-lain-pic.index') }}"
                                class="d-flex gap-2 ms-auto mt-0" style="width:550px;">
                                <input type="text" name="search" placeholder="Cari nama pic..."
                                    value="{{ request('search') }}" class="form-control">
                                <button type="submit" class="btn btn-primary btn-sm mb-0">Cari</button>
                            </form>
                        @endif
                    </div>
                </div>
                <hr>
                <div class="card-body px-0 pt-0 pb-0">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr class="text-center">
                                    <th class="th-title">#</th>
                                    <th class="th-title">Notaris</th>
                                    <th class="th-title">Klien</th>
                                    <th class="th-title">Nama Transaksi</th>
                                    <th class="th-title">Pic</th>
                                    <th class="th-title">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($prosesLain as $document)
                                    <tr class="text-center text-sm">
                                        <td>
                                            <p class="text-sm mb-0 text-center">
                                                {{ $prosesLain->firstItem() + $loop->index }}
                                            </p>
                                        </td>
                                        <td>
                                            <p class="text-sm mb-0 text-center"> {{ $document->notaris->display_name ?? '-' }} </p>
                                        </td>
                                        <td>
                                            <p class="text-sm mb-0 text-center"> {{ $document->client->fullname ?? '-' }} </p>
                                        </td>
                                        <td>
                                            <p class="text-sm mb-0 text-center">
                                                {{ $document->name }} 
                                                {{-- Jika ada keterangan tambahan, tampilkan di sini --}}
                                                @if(!empty($document->note))
                                                    <br><small class="text-muted">({{ $document->note }})</small>
                                                @endif
                                            </p>
                                        </td>
                                        <td>
                                            <p class="text-sm mb-0 text-center text-secondary">
                                                {{ $document->picStaff->full_name ?? (optional($document->picDocument)->pic->full_name ?? '-') }}
                                            </p>
                                        </td>
                                        <td class="text-center align-middle">
                                            <form id="delete-form-{{ $document->id }}" action="{{ route('proses-lain-pic.destroy', $document->id) }}"
                                                method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-danger btn-sm mb-0 btn-delete" data-id="{{ $document->id }}">
                                                    Hapus
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted text-sm py-4">Belum ada data pic.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <div class="d-flex justify-content-end mt-3 px-3">
                            {{ $prosesLain->withQueryString()->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Script untuk memunculkan SweetAlert pop-up hapus yang seragam --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const deleteButtons = document.querySelectorAll('.btn-delete');
            
            deleteButtons.forEach(button => {
                button.addEventListener('click', function (e) {
                    const id = this.getAttribute('data-id');
                    
                    Swal.fire({
                        title: 'Apakah Anda yakin?',
                        text: "Data PIC pada transaksi ini akan dihapus!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#f5365c',
                        cancelButtonColor: '#94a3b8',
                        confirmButtonText: 'Ya, Hapus!',
                        cancelButtonText: 'Batal',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            document.getElementById(`delete-form-${id}`).submit();
                        }
                    });
                });
            });
        });
    </script>
@endsection