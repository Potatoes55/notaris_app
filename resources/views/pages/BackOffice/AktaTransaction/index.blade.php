@extends('layouts.app')

@section('title', 'Transaksi Akta')

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Akta Notaris / Transaksi Akta'])
    @include('components.notaris-menu')

    <div class="row mt-4 mx-4">
        <div class="col-12">
            <div class="card mb-0">
                <div class="card-header d-flex justify-content-between align-items-center pb-0">
                    <h5>Transaksi Akta</h5>
                    <a href="{{ route('akta-transactions.create', ['client_code' => request('client_code')]) }}"
                        class="btn btn-primary btn-sm">+ Tambah Transaksi</a>
                </div>
                <div class="d-flex justify-content-md-end w-100 justify-content-center">
                    <form method="GET" action="{{ route('akta-transactions.index') }}"
                        class="d-flex  gap-2  justify-content-end flex-wrap flex-md-nowrap px-3"
                        style="max-width: 600px; width: 100%;">

                        <input type="text" name="transaction_code" placeholder="Cari Kode transaksi"
                            value="{{ request('transaction_code') }}" class="form-control">
                        <select name="status" class="form-select">
                            <option value="">Semua Status</option>
                            @foreach (['draft', 'diproses', 'selesai', 'dibatalkan'] as $status)
                                <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                    {{ ucfirst($status) }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-primary btn-sm mb-0">Cari</button>
                    </form>
                </div>

                <hr>
                <div class="card-body px-0 pt-0 pb-0">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Klien</th>
                                    <th>Kode Klien</th>
                                    <th>Kode Transaksi</th>
                                    <th>Jenis Akta</th>
                                    <th>Nomor Akta</th>
                                    <th>Tahun</th>
                                    <th>Waktu Nomor Dibuat</th>
                                    <th>Tanggal Penyerahan</th>
                                    <th>Tanggal Selesai</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transactions as $transaction)
                                    <tr class="text-center text-sm">
                                        <td>{{ $transactions->firstItem() + $loop->index }}</td>
                                        <td>{{ $transaction->client->fullname ?? '-' }}</td>
                                        <td>{{ $transaction->client_code ?? '-' }}</td>
                                        <td>{{ $transaction->transaction_code ?? '-' }}</td>
                                        <td>{{ $transaction->akta_type->type ?? '-' }}</td>
                                        <td>{{ $transaction->akta_number ?? '-' }}</td>
                                        <td>{{ $transaction->year ?? '-' }}</td>
                                        <td>{{ $transaction->akta_number_created_at ? \Carbon\Carbon::parse($transaction->akta_number_created_at)->format('d-m-y H:i:s') : '-' }}
                                        <td>
                                            {{ $transaction->date_submission
                                                ? \Illuminate\Support\Carbon::parse($transaction->date_submission)->format('d-m-y
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    H:i:s')
                                                : '-' }}
                                        </td>
                                        <td>
                                            {{ $transaction->date_finished
                                                ? \Illuminate\Support\Carbon::parse($transaction->date_finished)->format('d-m-y
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    H:i:s')
                                                : '-' }}
                                        </td>
                                        <td>
                                            <span
                                                class="badge text-capitalize
                                                @switch($transaction->status)
                                                    @case('draft') bg-secondary @break
                                                    @case('diproses') bg-warning @break
                                                    @case('selesai') bg-success @break
                                                    @case('dibatalkan') bg-danger @break
                                                    @default bg-light text-dark
                                                @endswitch
                                            ">
                                                {{ ucfirst($transaction->status) }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <button
                                                class="btn btn-dark btn-sm rounded-pill d-inline-flex align-items-center gap-2 px-3 mb-0"
                                                data-bs-toggle="modal" data-bs-target="#qrModal-{{ $transaction->id }}">
                                                <i class="fa fa-qrcode fs-5"></i>
                                                <span>QR Code</span>
                                            </button>
                                            <a href="{{ route('akta-transactions.edit', $transaction->id) }}"
                                                class="btn btn-info btn-sm mb-0">Edit</a>
                                            <button type="button" class="btn btn-danger btn-sm mb-0" data-bs-toggle="modal"
                                                data-bs-target="#deleteModal"
                                                data-url="{{ route('akta-transactions.destroy', $transaction->id) }}">
                                                Hapus
                                            </button>
                                            @include('pages.BackOffice.AktaTransaction.modal.index')
                                        </td>
                                        @php
                                            $dns2d = new \Milon\Barcode\DNS2D();

                                            $encryptedCode = \Illuminate\Support\Facades\Crypt::encryptString(
                                                $transaction->transaction_code,
                                            );
                                            $qrUrl = route('akta.qr.show', $encryptedCode);
                                            // $qrUrl = url('/akta/' . $transaction->transaction_code);

                                            $png = $dns2d->getBarcodePNG($qrUrl, 'QRCODE', 6, 6, [0, 0, 0], true);
                                        @endphp

                                        <div class="modal fade" id="qrModal-{{ $transaction->id }}" tabindex="-1"
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
                                                                download="qr-transaksi-{{ $transaction->transaction_code }}.png"
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
                                        <td colspan="11" class="text-center text-muted text-sm">Belum ada transaksi akta.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>

                        <div class="d-flex justify-content-center mt-3">
                            {{ $transactions->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
