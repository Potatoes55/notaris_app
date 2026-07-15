@extends('layouts.app')

@section('title', 'PIC Proses Pengurusan')

@section('content')

@include('layouts.navbars.auth.topnav', [
    'title' => $module . ' / PIC Proses Pengurusan'
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

    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">

                <div class="card mb-4">
                    <div class="card-header pb-0 d-flex justify-content-between align-items-center ">
                        <h5 class="mb-0">PIC Proses Pengurusan</h5>
                        <form method="GET"
                                action="{{ $module == 'PPAT'
                                    ? route('ppat.pic.process')
                                    : route('notaris.pic.process') }}"
                                class="d-flex gap-2">
                            <input type="text" name="pic_document_code" class="form-control form-control-sm"
                                style="max-width: 350px; width: 350px" placeholder="Cari Kode Dokumen"
                                value="{{ request('pic_document_code') }}">
                            <button class="btn btn-sm btn-primary mb-0" type="submit">Cari</button>
                        </form>
                    </div>
                    <hr>

                    <div class="card-body px-0 pt-0 pb-2">

                        {{-- Informasi Dokumen --}}
                        @if (isset($doc) && $doc)
                            <div class="card mb-4 shadow-sm mx-4">
                                <div class="card-header bg-primary text-white">
                                    <h6 class="mb-0 text-white">Detail PIC Dokumen</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <h6 class="mb-1"><strong>Kode Dokumen</strong></h6>

                                            <div class="d-flex align-items-center gap-2">
                                                <p class="text-muted text-sm mb-0">
                                                    {{ $doc->pic_document_code ?? '-' }}
                                                </p>

                                                @if($doc->pic_document_code)
                                                    <button
                                                        type="button"
                                                        class="btn btn-link p-0 text-primary"
                                                        onclick="copyValue(this, '{{ $doc->pic_document_code }}')">
                                                        <i class="fa-solid fa-copy"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <h6 class="mb-1">PIC</h6>
                                            <p class="text-muted text-sm">{{ $doc->pic->full_name ?? '-' }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <h6 class="mb-1">Klien</h6>
                                            <p class="text-muted text-sm">{{ $doc->client->fullname ?? '-' }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <h6 class="mb-1">Tipe Dokumen</h6>
                                            <p class="text-muted text-sm text-capitalize">
                                                {{ $doc->transaction_type ?? '-' }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <h6 class="mb-1">Tanggal Diterima</h6>
                                            <p class="text-muted text-sm">
                                                {{ $doc->received_date ? \Carbon\Carbon::parse($doc->received_date)->format('d-m-Y') : '-' }}
                                            </p>
                                        </div>
                                        <div class="col-md-6">
                                            <h6 class="mb-1">Status</h6>
                                            @php
                                                $badgeColors = [
                                                    'delivered' => 'primary',
                                                    'completed' => 'success',
                                                    'process' => 'warning',
                                                    'received' => 'info',
                                                ];
                                                $statusText = [
                                                    'delivered' => 'Dikirim',
                                                    'completed' => 'Selesai',
                                                    'process' => 'Diroses',
                                                    'received' => 'Diterima',
                                                ];
                                            @endphp
                                            <span
                                                class="badge text-capitalize bg-{{ $badgeColors[$doc->status] ?? 'secondary' }}">
                                                {{ $statusText[$doc->status] ?? ucfirst($doc->status) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Tabel Proses --}}
                        <div class="table-responsive p-0 mx-4">

                            @if (isset($doc) && $doc)
                                @if (request('pic_document_code'))
                                    <div class="d-flex justify-content-between  align-items-center">
                                        <h5>Proses Pengurusan</h5>
                                        <a href="{{ $module == 'PPAT'
                                                ? route('ppat.pic.process.create', ['pic_document_code' => request('pic_document_code')])
                                                : route('notaris.pic.process.create', ['pic_document_code' => request('pic_document_code')]) }}"
                                            class="btn btn-sm btn-primary mb-3">
                                            + Tambah Proses
                                        </a>
                                    </div>
                                @endif

                                <table class="table align-items-center mb-0">
                                    <thead class="">
                                        <tr class="text-center">
                                            <th>#</th>
                                            <th>Nama</th>
                                            <th>Status</th>
                                            <th>Tanggal Progress</th>
                                            <th>Catatan</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($processes as $process)
                                            <tr class="text-center text-sm">
                                                <td>{{ $processes->firstItem() + $loop->index }}</td>
                                                <td>{{ $process->step_name }}</td>
                                                <td>
                                                    @php
                                                        switch ($process->step_status) {
                                                            case 'done':
                                                                $statusText = 'Selesai';
                                                                $statusColor = 'success';
                                                                break;
                                                            case 'in_progress':
                                                                $statusText = 'Sedang Diproses';
                                                                $statusColor = 'info';
                                                                break;
                                                            default:
                                                                $statusText = 'Pending';
                                                                $statusColor = 'secondary';
                                                                break;
                                                        }
                                                    @endphp
                                                    <span class="badge text-capitalize bg-{{ $statusColor }}">
                                                        {{ $statusText }}
                                                    </span>
                                                </td>
                                                <td>{{ \Carbon\Carbon::parse($process->step_date)->format('d F Y') }}</td>
                                                <td>{{ $process->note ?? '-' }}</td>
                                                <td>
                                                    <a href="{{ route('pic_process.edit', $process->id) }}"
                                                        class="btn btn-sm btn-info mb-0">Edit</a>
                                                    <form action="{{ route('pic_process.destroy', $process->id) }}"
                                                        method="POST" style="display:inline-block;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger mb-0">
                                                            Hapus
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center text-muted text-sm py-3">
                                                    Belum ada proses pengurusan untuk PIC proses pengurusan.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                                {{-- pagination --}}
                                <div class="d-flex justify-content-end mt-3">
                                    {{ $processes->appends(request()->query())->links() }}
                                </div>
                            @else
                                <div class="text-center text-muted text-sm p-4">
                                    Masukkan PIC Kode Dokumen untuk melihat daftar proses pengurusan.
                                </div>
                            @endif
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
