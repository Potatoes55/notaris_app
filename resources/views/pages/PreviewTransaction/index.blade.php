@extends('layouts.app')

@section('title', 'Detail Akta')

@php
    use Carbon\Carbon;

    if (!function_exists('v')) {
        function v($val)
        {
            return filled($val) ? $val : '-';
        }
    }

    if(!function_exists('d')) {
    function d($date)
        {
    
        if (empty($date) || $date === '-') {
            return '-';
        }

        try {
            return \Carbon\Carbon::parse($date)->format('d F Y');
        } catch (\Exception $e) {
            return '-';
        }
    }
    }
@endphp
@php
    $publicRoutes = ['akta.qr.show'];
@endphp
@section('content')
    <main class="main-content mt-0">
        <section>
            <div class="page-header min-vh-100 d-flex align-items-center justify-content-center bg-light">
                <div class="container py-4">
                    <div class="row justify-content-center">
                        <div class="col-lg-8">

                            <div class="card shadow-lg border-0">

                                {{-- HEADER --}}
                                <div class="card-body text-center border-bottom">
                                    <div class="mx-auto mb-2"
                                        style="width:80px;height:80px;border-radius:50%;
                                    background:#f1f1f1;
                                    display:flex;align-items:center;justify-content:center;">
                                        <i class="bi bi-file-earmark-text-fill fs-1 text-primary"></i>
                                    </div>

                                    <h5 class="fw-bold mb-1">
                                        Transaksi Akta
                                    </h5>

                                    <div class="text-muted">
                                        Kode Transaksi: <strong>{{ v($akta->transaction_code) }}</strong>
                                    </div>
                                </div>

                                {{-- BODY --}}
                                <div class="card-body pt-3 pb-2">

                                    {{-- NOTARIS --}}
                                    <div class="mb-4">
                                        <h6 class="fw-bold text-capitalize text-primary mb-2">
                                            <i class="bi bi-person-badge me-1"></i> Notaris
                                        </h6>

                                        <div class="border rounded px-3 py-2 small bg-light">
                                            <h6>Nama </h6>
                                            <div class="fw-semibold">
                                                {{ v($akta->notaris->display_name ?? '-') }}
                                            </div>
                                        </div>
                                    </div>

                                    {{-- KLIEN --}}
                                    <div class="mb-4">
                                        <h6 class="fw-bold text-capitalize text-primary mb-2">
                                            <i class="bi bi-people me-1"></i> Klien
                                        </h6>

                                        <div class="border rounded px-3 py-2 small bg-light">
                                            <div class="row mb-1">
                                                <h6 class=" col-4 col-lg-2">Nama</h6>
                                                <div class="col-8 col-lg-8 fw-semibold">: {{ v($akta->client->fullname ?? '-') }}
                                                </div>
                                            </div>
                                            <div class="row">
                                                <h6 class="col-4 col-lg-2">Kode Klien</h6>
                                                <div class="col-8 fw-semibold">: {{ v($akta->client->client_code ?? '-') }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- TRANSAKSI --}}
                                    @php
                                        $status = strtolower($akta->status ?? '');
                                        $statusClass = match ($status) {
                                            'draft' => 'bg-secondary',
                                            'diproses', 'process' => 'bg-primary',
                                            'selesai', 'done' => 'bg-success',
                                            'dibatalkan', 'cancel' => 'bg-danger',
                                            default => 'bg-info',
                                        };
                                    @endphp

                                    <div class="mb-4">
                                        <h6 class="fw-bold text-capitalize text-primary mb-2">
                                            <i class="bi bi-file-earmark-text me-1"></i> Transaksi
                                        </h6>

                                        <div class="border rounded p-3 small bg-light">
                                            <div class="row g-3">

                                                <div class="col-md-6">
                                                    <h6>Kode Transaksi</h6>
                                                    <div class="fw-semibold">{{ v($akta->transaction_code) }}</div>
                                                </div>

                                                <div class="col-md-6">
                                                    <h6>Status</h6>
                                                    <span class="badge text-capitalize {{ $statusClass }}">
                                                        {{ ucfirst($akta->status) }}
                                                    </span>
                                                </div>

                                                <div class="col-md-6">
                                                    <h6>Nomor Akta</h6>
                                                    <div class="fw-semibold">{{ v($akta->akta_number) }}</div>
                                                </div>

                                                <div class="col-md-6">
                                                    <h6>Tanggal Penetapan Nomor Akta</h6>
                                                    <div class="fw-semibold">{{ d($akta->akta_number_created_at) }}</div>
                                                </div>

                                                <div class="col-md-6">
                                                    <h6>Kategori</h6>
                                                    <div class="fw-semibold text-capitalize">
                                                        {{ v($akta->akta_type->category ?? '-') }}
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <h6>Jenis Akta</h6>
                                                    <div class="fw-semibold">
                                                        {{ v($akta->akta_type->type ?? '-') }}
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <h6>Tanggal Pengajuan</h6>
                                                    <div class="fw-semibold">{{ d($akta->date_submission) }}</div>
                                                </div>

                                                <div class="col-md-6">
                                                    <h6>Tanggal Selesai</h6>
                                                    <div class="fw-semibold">{{ d($akta->date_finished) }}</div>
                                                </div>

                                                <div class="col-6">
                                                    <h6>Catatan</h6>
                                                    <div class="fw-semibold">{{ v($akta->note) }}</div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>

                                    <a href="{{ route('dashboard') }}"
                                        class="btn btn-primary d-inline-flex align-items-center gap-2">
                                        <i class="bi bi-arrow-left-circle"></i>
                                        Kembali ke Dashboard
                                    </a>

                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
@endsection
