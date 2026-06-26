@extends('layouts.app')

@section('title', 'Transaksi')

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Proses Lain / Transaksi'])
    @include('components.proseslain-menu')
    <div class="row mt-4 mx-4">
        <div class="col-12">
            <div class="card mb-0">
                <div class="card-header pb-0 mb-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Transaksi</h5>
                        @if (!is_null(auth()->user()->access_code) && auth()->user()->access_code !== 'staff')
                            <a href="{{ route('proses-lain-transaksi.create') }}" class="btn btn-primary btn-sm mb-0">+ Tambah Transaksi</a>
                        @else
                            <form method="GET" action="{{ route('proses-lain-transaksi.index') }}" class="d-flex gap-2 ms-auto" style="width:550px;">
                                <input type="text" name="search" placeholder="Cari nama transaksi..." value="{{ request('search') }}" class="form-control">
                                <button type="submit" class="btn btn-primary btn-sm mb-0">Cari</button>
                            </form>
                        @endif
                    </div>
                    @if (!is_null(auth()->user()->access_code) && auth()->user()->access_code !== 'staff')
                        <form method="GET" action="{{ route('proses-lain-transaksi.index') }}" class="d-flex gap-2 ms-auto mt-3" style="max-width:550px;">
                            <input type="text" name="search" placeholder="Cari nama transaksi..." value="{{ request('search') }}" class="form-control">
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
                                        <td>{{ $prosesLain->firstItem() + $loop->index }}</td>
                                        <td>{{ $document->notaris->display_name ?? '-' }}</td>
                                        <td>{{ $document->transaction_code }}</td>
                                        <td>{{ $document->client->fullname ?? '-' }}</td>
                                        <td>{{ $document->name }}</td>
                                        <td>{{ $document->time_estimation }} Hari</td>
                                        <td style="min-width: 140px">
                                            <form method="POST" class="status-form">
                                                @csrf
                                                @method('PUT')
                                                
                                                <select
                                                    name="status"
                                                    class="form-select form-select-sm text-white font-weight-bold"
                                                    data-url="{{ route('proses-lain-transaksi.status', ['id' => $document->id, 'status' => 'PLACEHOLDER']) }}"
                                                    onchange="changeStatus(this)"
                                                    style="cursor: pointer; border: none; 
                                                        background-color: {{ $document->status == 'Baru' ? '#17a2b8' : ($document->status == 'Proses' ? '#fb6340' : '#2dce89') }};">
                                                    <option value="Baru" style="background-color: white; color: #333;" {{ $document->status == 'Baru' ? 'selected' : '' }}>Baru</option>
                                                    <option value="Proses" style="background-color: white; color: #333;" {{ $document->status == 'Proses' ? 'selected' : '' }}>Proses</option>
                                                    <option value="Selesai" style="background-color: white; color: #333;" {{ $document->status == 'Selesai' ? 'selected' : '' }}>Selesai</option>
                                                </select>
                                            </form>
                                        </td>
                                        <td class="text-center align-middle">
                                            @if (auth()->user()->access_code !== 'staff')
                                                <a href="{{ route('proses-lain-transaksi.edit', $document->id) }}" class="btn btn-info btn-sm mb-0">Edit</a>
                                                <form id="delete-form-{{ $document->id }}" action="{{ route('proses-lain-transaksi.destroy', $document->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-danger btn-sm mb-0 btn-delete" data-id="{{ $document->id }}">Hapus</button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-4">Belum ada data transaksi.</td>
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

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.btn-delete').forEach(button => {
                button.addEventListener('click', function () {
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

        function changeStatus(select) {
            let status = select.value;
            let baseUrl = select.getAttribute('data-url');
            let form = select.closest('form');
            form.action = baseUrl.replace('PLACEHOLDER', status);
            form.submit();
        }
    </script>
@endsection