@extends('layouts.app')

@section('title', 'Transaksi')

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Transaksi'])
    <div class="row mt-4 mx-4">
        <div class="col-12">
            <div class="card mb-0">
                <div class="card-header pb-0 mb-0 ">
                    <div class=" d-flex justify-content-between align-items">
                        <h5 class="mb-0">Transaksi</h5>

                        @if (!is_null(auth()->user()->access_code) && auth()->user()->access_code !== 'staff')
                            <a href="{{ route('proses-lain-transaksi.create') }}" class="btn btn-primary btn-sm mb-0">
                                + Tambah Transaksi
                            </a>
                        @else
                            <form method="GET" action="{{ route('proses-lain-transaksi.index') }}"
                                class="d-flex gap-2 ms-auto " style="width:550px;">
                                <input type="text" name="search" placeholder="Cari nama transaksi..."
                                    value="{{ request('search') }}" class="form-control">
                                <button type="submit" class="btn btn-primary btn-sm mb-0">Cari</button>
                            </form>
                        @endif
                    </div>
                    @if (!is_null(auth()->user()->access_code) && auth()->user()->access_code !== 'staff')
                        <form method="GET" action="{{ route('proses-lain-transaksi.index') }}"
                            class="d-flex gap-2 ms-auto mt-3" style="max-width:550px;">
                            <input type="text" name="search" placeholder="Cari nama transaksi..."
                                value="{{ request('search') }}" class="form-control">
                            <button type="submit" class="btn btn-primary btn-sm mb-0">Cari</button>
                        </form>
                    @endif
                </div>
                <hr>
                <div class="card-body px-0 pt-0 pb-0">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr class="text-center">
                                    <th class="th-title">#</th>
                                    <th class="th-title">Notaris</th>
                                    <th class="th-title">Kode Transaksi</th>
                                    <th class="th-title">Klien</th>
                                    <th class="th-title">Nama</th>
                                    <th class="th-title">Estimasi</th>
                                    <th class="th-title">Status</th>
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
                                            <p class="text-sm mb-0 text-center">{{ $document->notaris->display_name ?? '-' }}</p>
                                        </td>
                                        <td>
                                            <p class="text-sm mb-0 text-center">{{ $document->transaction_code }}</p>
                                        </td>
                                        <td>
                                            <p class="text-sm mb-0 text-center">{{ $document->client->fullname ?? '-' }}</p>
                                        </td>
                                        <td>
                                            <p class="text-sm mb-0 text-center">{{ $document->name }}</p>
                                        </td>
                                        <td>
                                            <p class="text-sm mb-0 text-center">{{ $document->time_estimation }} Hari</p>
                                        </td>
                                        <td>
                                            <p class="text-sm mb-0 text-center">{{ $document->status }}</p>
                                        </td>
                                        <td class="text-center align-middle">
                                            @if (auth()->user()->access_code !== 'staff')
                                                <a href="{{ route('proses-lain-transaksi.edit', $document->id) }}"
                                                    class="btn btn-info btn-sm mb-0">
                                                    Edit
                                                </a>
                                                
                                                <form id="delete-form-{{ $document->id }}" action="{{ route('proses-lain-transaksi.destroy', $document->id) }}"
                                                    method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-danger btn-sm mb-0 btn-delete" data-id="{{ $document->id }}">
                                                        Hapus
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted text-sm py-4">Belum ada data transaksi.</td>
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

    {{-- Script SweetAlert Komponen Konfirmasi Hapus Data --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const deleteButtons = document.querySelectorAll('.btn-delete');
            
            deleteButtons.forEach(button => {
                button.addEventListener('click', function (e) {
                    const id = this.getAttribute('data-id');
                    
                    Swal.fire({
                        title: 'Apakah Anda yakin?',
                        text: "Data transaksi ini akan dihapus secara permanen!",
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