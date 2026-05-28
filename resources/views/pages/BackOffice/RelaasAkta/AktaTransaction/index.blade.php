@extends('layouts.app')

@section('title', 'Transaksi Akta')

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'PPAT / Transaksi Akta'])

    <div class="row mt-4 mx-4">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center pb-0">
                    <h5>Transaksi Akta</h5>
                    <a href="{{ route('relaas-aktas.create', ['client_code' => request('client_code')]) }}"
                        class="btn btn-primary btn-sm">+ Tambah Transaksi</a>
                </div>
                <form method="GET" action="{{ route('relaas-aktas.index') }}" class="d-flex gap-2 ms-auto me-4 mb-0"
                    style="width:500px;" onchange="this.submit()" class="no-spinner">
                    <input type="text" name="transaction_code" placeholder="Cari Kode Transaksi"
                        value="{{ request('transaction_code') }}" class="form-control">
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        @foreach (['draft', 'diproses', 'selesai', 'dibatalkan'] as $status)
                            <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                {{ ucfirst($status) }}
                            </option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-primary btn-sm mb-0">Cari</button>
                </form>
                <hr>
                <div class="card-body px-0 pt-0 pb-0">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0 text-sm text-c   enter">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Klien</th>
                                    <th>Kode Klien</th>
                                    <th>Kode Transaksi</th>
                                    <th>Jenis Akta</th>
                                    <th>Tahun</th>
                                    <th>Nomor Akta</th>
                                    <th>Waktu Nomor Dibuat</th>
                                    <th>Judul</th>
                                    <th>Tanggal</th>
                                    <th>Lokasi</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data as $akta)
                                    <tr>
                                        <td>{{ $data->firstItem() + $loop->index }}</td>
                                        <td>{{ $akta->client->fullname ?? '-' }}</td>
                                        <td>{{ $akta->client_code ?? '-' }}</td>
                                        <td>{{ $akta->transaction_code ?? '-' }}</td>
                                        <td>{{ ucfirst($akta->akta_type->type) ?? '-' }}</td>
                                        <td>{{ $akta->year ?? '-' }}</td>
                                        <td>{{ $akta->relaas_number ?? '-' }}</td>
                                        <td>{{ $akta->relaas_number_created_at ? \Carbon\Carbon::parse
                                        ($akta->relaas_number_created_at)->format('d-m-y H:i:s') : '-' }}
                                        </td>
                                        <td>{{ $akta->title ?? '-' }}</td>
                                        <td>{{ $akta->story_date ? \Carbon\Carbon::parse($akta->story_date)->format('d-m-y H:i:s') : '-' }}
                                        </td>
                                        <td>{{ $akta->story_location ?? '-' }}</td>
                                        <td>{{ ucfirst($akta->status) ?? '-' }}</td>
                                        <td>
                                            <button
                                                class="btn btn-dark btn-sm rounded-pill d-inline-flex align-items-center gap-2 px-3 mb-0"
                                                data-bs-toggle="modal" data-bs-target="#qrModal-{{ $akta->id }}">
                                                <i class="fa fa-qrcode fs-5"></i>
                                                <span>QR Code</span>
                                            </button>
                                            <a href="{{ route('relaas-aktas.edit', $akta->id) }}"
                                                class="btn btn-info btn-sm mb-0">Edit</a>
                                            <button type="button" class="btn btn-danger btn-sm mb-0" data-bs-toggle="modal"
                                                data-bs-target="#deleteModal{{ $akta->id }}">
                                                Hapus
                                            </button>
                                            @include(
                                                'pages.BackOffice.RelaasAkta.AktaTransaction.Modal.index',
                                                ['akta' => $akta]
                                            )
                                        </td>
                                        @php
                                            // use Milon\Barcode\DNS2D;

                                            $dns2d = new \Milon\Barcode\DNS2D();

                                            $encryptedCode = \Illuminate\Support\Facades\Crypt::encryptString(
                                                $akta->transaction_code,
                                            );
                                            $qrUrl = route('akta.qr.show', $encryptedCode);

                                            $png = $dns2d->getBarcodePNG($qrUrl, 'QRCODE', 6, 6, [0, 0, 0], true);
                                        @endphp

                                        <div class="modal fade" id="qrModal-{{ $akta->id }}" tabindex="-1"
                                            aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">

                                                    <div class="modal-header">
                                                        <h5 class="modal-title">
                                                            QR Transaksi Akta
                                                        </h5>
                                                        <button type="button" class="btn-close btn-close-white"
                                                            data-bs-dismiss="modal"></button>
                                                    </div>

                                                    <div class="modal-body text-center">

                                                        <img src="data:image/png;base64,{{ $png }}" alt="QR Code"
                                                            style="
                                                    width:160px;
                                                    background:#fff;
                                                    padding:14px;
                                                    border-radius:14px;
                                                    margin: 0 auto;
                                                    box-shadow:0 10px 25px rgba(251,98,64,.35);
                                                ">

                                                        <p class="small text-muted mt-3 mb-1">Link Transaksi</p>
                                                        <p class="fw-semibold text-break">
                                                            {{ $qrUrl }}
                                                        </p>

                                                        <div class="mt-3">
                                                            <a href="data:image/png;base64,{{ $png }}"
                                                                download="qr-transaksi-{{ $akta->transaction_code }}.png"
                                                                class="btn btn-outline-primary btn-sm">
                                                                <i class="bi bi-download me-2"></i>
                                                                Download QR
                                                            </a>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="12" class="text-muted text-center">Belum ada transaksi akta.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>

                        <div class="px-4 mt-3">
                            {{ $data->links() }}
                        </div>

                    </div>
                </div>
            </div>
        </div>
    @endsection
