@extends('layouts.app')

@section('title', 'Penomoran Akta')

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'PPAT / Penomoran akta'])
    @include('components.ppat-menu')

    <div class="row mt-4 mx-4">
        <div class="col-12">
        <div class="card border">
            <div class="card-header pb-0">
                <h6>Input Penomoran Akta</h6>
            </div>
            <hr>
            <div class="card-body pt-2">

                    {{-- Nomor Akta Terakhir --}}
                    @if ($lastAkta)
                        <div class="mb-3 bg-warning p-3 rounded-3 text-white">
                            <h6 class="text-white"> Nomor Akta Terakhir: {{ $lastAkta->relaas_number }}</h6>
                            <h6 class="text-white">
                                Waktu Dibuat:
                                {{ $lastAkta->relaas_number_created_at ? $lastAkta->relaas_number_created_at->format('d-m-Y H:i:s') : '-' }}
                            </h6>
                        </div>
                    @else
                        <div class="alert alert-warning text-white">Belum ada nomor akta tersimpan.</div>
                    @endif

                    {{-- Form Pencarian --}}
                    <form method="GET" action="{{ route('relaas_akta.indexNumber') }}" class="mb-3 no-spinner">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control"
                                placeholder="Masukkan Kode Transaksi, Nama Klien, atau Nomor Akta" value="{{ request('search') }}">
                            <button type="submit" class="btn btn-primary btn-sm mb-0">Cari</button>
                        </div>
                    </form>

                    {{-- KONDISI 1: JIKA HASIL PENCARIAN BERUPA DAFTAR TRANSAKSI (Cari Berdasarkan Nama Klien / Parsial) --}}
                    @if(isset($transactions) && $transactions->count() > 0)

                    <div class="mb-0">
                        <h5>Daftar Transaksi Akta</h5>

                        <div class="table-responsive p-0">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr class="text-center text-sm">
                                        <th>#</th>
                                        <th>Kode Transaksi</th>
                                        <th>Nomor Akta</th>
                                        <th>Nama Klien</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach($transactions as $tx)
                                        <tr class="text-center text-sm">
                                            <td>{{ $transactions->firstItem() + $loop->index }}</td>

                                            <td>
                                                <div class="d-flex justify-content-center align-items-center gap-2">
                                                    <span>{{ $tx->transaction_code }}</span>
                                                    <button type="button" class="btn btn-link p-0 text-primary"
                                                        onclick="copyValue(this,'{{ $tx->transaction_code }}')">
                                                        <i class="fa-solid fa-copy"></i>
                                                    </button>
                                                </div>
                                            </td>

                                            <td>
                                                <div class="d-flex justify-content-center align-items-center gap-2">
                                                    <span>{{ $tx->relaas_number ?? '-' }}</span>

                                                    @if($tx->relaas_number)
                                                        <button type="button" class="btn btn-link p-0 text-primary"
                                                            onclick="copyValue(this,'{{ $tx->relaas_number }}')">
                                                            <i class="fa-solid fa-copy"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>

                                            <td>{{ $tx->client->fullname ?? '-' }}</td>

                                            <td>
                                                <a href="{{ route('relaas_akta.indexNumber',['search'=>$tx->transaction_code]) }}"
                                                    class="btn btn-sm btn-info">
                                                    <i class="fa fa-eye me-1"></i>
                                                    Detail & Beri Nomor
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-end mt-2">
                            {{ $transactions->links() }}
                        </div>
                    </div>

                    @endif

                    {{-- KONDISI 2: JIKA TRANSAKSI TUNGGAL DITEMUKAN (Tampilkan Detail & Form Input) --}}
                    @if ($aktaInfo)
                        <div class="card mb-4 shadow-sm">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0 text-white">Detail Transaksi Akta</h5>
                            </div>

                            <div class="card-body pb-2">
                                <div class="row g-3">

                                    <div class="col-md-6">
                                        <h6><strong>Kode Klien</strong></h6>
                                        <div class="d-flex align-items-center gap-2">
                                            <p class="text-muted text-sm mb-0">{{ $aktaInfo->client_code }}</p>

                                            <button type="button"
                                                class="btn btn-link p-0 text-primary"
                                                onclick="copyValue(this,'{{ $aktaInfo->client_code }}')">
                                                <i class="fa-solid fa-copy"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <h6><strong>Nomor Akta</strong></h6>

                                        <div class="d-flex align-items-center gap-2">
                                            <p class="text-muted text-sm mb-0">
                                                {{ $aktaInfo->relaas_number ?? '-' }}
                                            </p>

                                            @if($aktaInfo->relaas_number)
                                                <button type="button"
                                                    class="btn btn-link p-0 text-primary"
                                                    onclick="copyValue(this,'{{ $aktaInfo->relaas_number }}')">
                                                    <i class="fa-solid fa-copy"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <h6><strong>Jenis Akta</strong></h6>
                                        <p class="text-muted text-sm mb-0">
                                            {{ $aktaInfo->relaasType->type ?? '-' }}
                                        </p>
                                    </div>

                                    <div class="col-md-6">
                                        <h6><strong>Notaris</strong></h6>
                                        <p class="text-muted text-sm mb-0">
                                            {{ $aktaInfo->notaris->display_name ?? '-' }}
                                        </p>
                                    </div>

                                    <div class="col-md-6">
                                        <h6><strong>Klien</strong></h6>
                                        <p class="text-muted text-sm mb-0">
                                            {{ $aktaInfo->client->fullname ?? '-' }}
                                        </p>
                                    </div>

                                    <div class="col-md-6">
                                        <h6><strong>Status</strong></h6>

                                        <span class="badge text-capitalize
                                            @switch($aktaInfo->status)
                                                @case('draft') bg-secondary @break
                                                @case('diproses') bg-warning @break
                                                @case('selesai') bg-success @break
                                                @case('dibatalkan') bg-danger @break
                                                @default bg-light text-dark
                                            @endswitch">
                                            {{ $aktaInfo->status }}
                                        </span>
                                    </div>

                                </div>
                            </div>
                        </div>

                        {{-- Form Input Nomor Akta Baru --}}
                        <div class="card">
                            <div class="card-header pb-0 text-bold">Input Penomoran Akta</div>
                            <hr>
                            <div class="card-body pt-0 pb-0">
                                <form method="POST" action="{{ route('relaas-akta.store') }}">
                                    @csrf
                                    <input type="hidden" name="relaas_id" value="{{ $aktaInfo->id }}">

                                    <div class="mb-3">
                                        <label for="year" class="form-label text-sm">Tahun</label>
                                        <input type="number" class="form-control" id="year" name="year"
                                            value="{{ now()->year }}" readonly>
                                    </div>

                                    <div class="mb-3">
                                        <label for="relaas_number" class="form-label text-sm">Nomor Akta</label>
                                        <div class="input-group">
                                            <input type="text"
                                                class="form-control @error('relaas_number') is-invalid @enderror"
                                                id="relaas_number" name="relaas_number"
                                                value="{{ old('relaas_number', $aktaInfo->relaas_number ?? '') }}"
                                                {{ $aktaInfo->relaas_number ? 'disabled' : '' }}>

                                            @if ($aktaInfo->relaas_number)
                                                <button type="button" id="editButtons" class="btn btn-primary mb-0">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                            @endif
                                        </div>
                                        @error('relaas_number')
                                            <div class="text-danger text-xs mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <button type="submit" class="btn btn-primary mb-0">Simpan</button>
                                </form>
                            </div>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>

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
@endsection

@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const editButton = document.getElementById('editButtons');
            const aktaNumberInput = document.getElementById('relaas_number');

            if (editButton) {
                editButton.addEventListener('click', function() {
                    aktaNumberInput.disabled = false;
                    aktaNumberInput.focus();
                });
            }
        });
    </script>
@endpush