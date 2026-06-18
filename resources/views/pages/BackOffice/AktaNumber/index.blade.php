@extends('layouts.app')

@section('title', 'Penomoran Akta')

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Akta Notaris / Penomoran akta'])
    @include('components.notaris-menu')

    <div class="row mt-4 mx-4">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center mb-0 pb-0">
                    <h5>Penomoran Akta</h5>
                </div>
                <div class="card-body pt-1">

                    {{-- Nomor Akta Terakhir --}}
                    @if ($lastAkta)
                        <div class="mb-3 bg-warning p-3 rounded-3 text-white my-2">
                            <h6 class="text-white"> Nomor Akta Terakhir: {{ $lastAkta->akta_number }}</h6>
                            <h6 class="text-white"> Waktu Dibuat:
                                {{ $lastAkta->akta_number_created_at ? $lastAkta->akta_number_created_at->format('d-m-Y H:i:s') : '-' }}
                            </h6>
                        </div>
                    @else
                        <div class="alert alert-warning text-white">Belum ada nomor akta tersimpan.</div>
                    @endif

                    {{-- Form Pencarian --}}
                    <form method="GET" action="{{ route('akta_number.index') }}" class="mb-3" class="no-spinner">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control"
                                placeholder="Masukkan Kode transaksi atau Nomor Akta" value="{{ request('search') }}">
                            <button type="submit" class="btn btn-primary btn-sm mb-0">Cari</button>
                        </div>
                    </form>

                    {{-- Jika ada transaksi ditemukan --}}
                    @if ($aktaInfo)
                        <div class="card mb-4 shadow-sm">
                            <div class="card-header bg-primary text-white">
                                <h6 class="mb-0 text-white">Detail Akta Transaksi</h6>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <h6 class="mb-1"><strong>Kode Klien</strong></h6>
                                        <p class="text-muted text-sm">{{ $aktaInfo->client_code }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="mb-1"><strong>Nomor Akta</strong></h6>
                                        <p class="text-muted text-sm">{{ $aktaInfo->akta_number ?? '-' }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="mb-1"><strong>Jenis Akta</strong></h6>
                                        <p class="text-muted text-sm">{{ $aktaInfo->akta_type->type ?? '-' }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="mb-1"><strong>Notaris</strong></h6>
                                        <p class="text-muted text-sm">{{ $aktaInfo->notaris->display_name ?? '-' }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="mb-1"><strong>Status</strong></p>
                                        <span
                                            class="badge text-capitalize
                                    @switch($aktaInfo->status)
                                        @case('draft') bg-secondary @break
                                        @case('diproses') bg-warning @break
                                        @case('selesai') bg-success @break
                                        @case('dibatalkan') bg-danger @break
                                        @default bg-light text-dark
                                    @endswitch
                                ">
                                            {{ $aktaInfo->status }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Form Input Nomor Akta Baru --}}
                    @if ($aktaInfo)
                        <div class="card">
                            <div class="card-header pb-0 text-bold d-flex justify-content-between align-items-center">
                                <h6>Input Penomoran Akta</h6>
                            </div>
                            <hr>
                            <div class="card-body pt-2">
                                <form method="POST" action="{{ route('akta_number.store') }}">
                                    @csrf
                                    <input type="hidden" name="transaction_id" value="{{ $aktaInfo->id }}">

                                    <div class="mb-3">
                                        <label for="year" class="form-label text-sm">Tahun</label>
                                        <input type="text" class="form-control" id="year" name="year"
                                            value="{{ now()->year }}" readonly>
                                    </div>

                                    <div class="mb-3 position-relative">
                                        <label for="akta_number" class="form-label text-sm">Nomor Akta</label>
                                        <div class="input-group">
                                            <input type="text"
                                                class="form-control @error('akta_number') is-invalid @enderror"
                                                id="akta_number" name="akta_number"
                                                value="{{ old('akta_number', $aktaInfo->akta_number ?? '') }}"
                                                {{ $aktaInfo->akta_number ? 'disabled' : '' }} required>

                                            {{-- Tampilkan tombol edit hanya jika sudah ada data --}}
                                            @if ($aktaInfo->akta_number)
                                                <button type="button" id="editButton" class="btn btn-primary mb-0">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                            @endif

                                            @error('akta_number')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <button type="submit" class="btn btn-primary">Simpan</button>
                                </form>
                            </div>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const editButton = document.getElementById('editButton');
            const aktaNumberInput = document.getElementById('akta_number');

            if (editButton) {
                editButton.addEventListener('click', function() {
                    aktaNumberInput.disabled = false; // aktifkan kembali input
                    aktaNumberInput.focus();
                    // this.remove(); // hapus tombol edit setelah diklik
                });
            }
        });
    </script>
@endpush
