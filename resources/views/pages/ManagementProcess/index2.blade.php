@extends('layouts.app')

@section('title', 'Proses Pengurusan')

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Proses Pengurusan'])
    @include('components.konsultasi-menu')

    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                {{-- Card Utama --}}
                <div class="card mb-4">
                    <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Proses Pengurusan</h5>
                        <form method="GET" action="{{ route('pic-progress.indexProcess') }}" class="d-flex gap-2"
                            style="max-width: 500px;" class="no-spinner">
                            <input type="text" name="pic_document_code" class="form-control form-control-sm"
                                style="max-width: 350px; width: 350px" placeholder="Masukkan Kode  Dokumen"
                                value="{{ request('pic_document_code') }}">
                            <button class="btn btn-sm btn-primary mb-0" type="submit">Cari</button>
                        </form>
                    </div>
                    <hr>
                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive p-0">
                            @if (request('pic_document_code'))
                                <table class="table align-items-center mb-0">
                                    <thead>
                                        <tr class="text-center">
                                            <th>#</th>
                                            <th>Nama Proses</th>
                                            <th>Status</th>
                                            <th>Tanggal Proses</th>
                                            <th>Catatan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($processes as $process)
                                            <tr class="text-center text-sm">
                                                <td>{{ $loop->iteration }}</td>
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
                                                            case 'pending':
                                                            default:
                                                                $statusText = 'Pending';
                                                                $statusColor = 'warning';
                                                                break;
                                                        }
                                                    @endphp
                                                    <span class="badge bg-{{ $statusColor }}">
                                                        {{ $statusText }}
                                                    </span>
                                                </td>
                                                {{-- step date datetime --}}
                                                <td>{{ \Carbon\Carbon::parse($process->step_date)->format('d-m-Y') }}</td>
                                                <td>{{ $process->note ?? '-' }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center text-muted text-sm">
                                                    Belum ada proses pengurusan untuk PIC Document ini.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            @else
                                <div class="text-center text-muted text-sm p-4">
                                    Masukkan kode dokumen pic untuk melihat daftar proses pengurusan.
                                </div>
                            @endif

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
