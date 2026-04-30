@extends('layouts.app')

@section('title', 'Laporan Pembayaran')

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Laporan Pembayaran'])
    <div class="row mt-4 mx-4">
        <div class="col-12">
            <div class="card shadow-sm border-0 pb-0">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                    <h5>Laporan Pembayaran</h5>
                    @if (request()->anyFilled(['start_date', 'end_date', 'status']))
                        <a href="{{ route('report-payment.print', request()->all()) }}" 
                        target="_blank"
                        class="btn btn-danger mb-0 btn-sm"
                        onclick="return confirmPrint(event, this)">
                        <i class="bi bi-file-earmark-pdf"></i> PDF
                        </a>
                    @endif
                </div>
                <hr>

                <div class="card-body pt-1">
                    <form method="GET" action="{{ route('report-payment.index') }}"
                        class="row g-3 mb-4 px-0 no-spinner">

                        <div class="col-md-4 col-xl-4">
                            <label class="form-label text-sm">Tanggal Mulai</label>
                            <input type="date" class="form-control" name="start_date"
                                value="{{ request('start_date') }}">
                        </div>

                        <div class="col-md-4 col-xl-4">
                            <label class="form-label text-sm">Tanggal Akhir</label>
                            <input type="date" class="form-control" name="end_date"
                                value="{{ request('end_date') }}">
                        </div>

                        <div class="col-md-2 col-xl-2">
                            <label class="form-label text-sm">Status</label>
                            <select class="form-select" name="status">
                                <option value="all">Semua</option>
                                <option value="full">Lunas</option>
                                <option value="dp">DP</option>
                                <option value="partial">Bayar Sebagian</option>
                            </select>
                        </div>

                        <div class="col-md-2 col-xl-2 d-flex align-items-end py-1 justify-content-end">
                            <button type="submit" class="btn btn-primary btn-sm w-100 mb-0">
                                Cari
                            </button>
                        </div>
                    </form>
                    
                    <div class="table-responsive p-0">
                        <table class="table table-hover align-items-center mb-0">
                            <thead>
                                <tr class="text-center">
                                    <th>#</th>
                                    <th>Kode Pembayaran</th>
                                    <th>Nama Klien</th>
                                    <th>Tanggal Pembayaran</th>
                                    <th>Total Biaya</th>
                                    <th>Total Pembayaran</th>
                                    <th>Piutang</th>
                                    <th>Status Pembayaran</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($costs as $payment)
                                    <tr class="text-center text-sm">
                                        <td>{{ $loop->iteration }}</td>
                                        {{-- <td>{{ $costs->firstItem() + $loop->index }}</td> --}}
                                        <td>{{ $payment->payment_code }}</td>
                                        <td>{{ $payment->client->fullname ?? '-' }}</td>
                                        <td>{{ $payment->payment_date ? \Carbon\Carbon::parse($payment->payment_date)->format('d-m-Y') : '-' }}
                                        </td>
                                        <td>Rp
                                            {{ number_format($payment->cost->total_cost ?? 0, 0, ',', '.') }}
                                        </td>
                                        <td>Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                                        <td>Rp
                                            {{ number_format($payment->cost->total_cost - $payment->amount, 0, ',', '.') }}
                                        </td>
                                        <td>
                                            @php
                                                $status = $payment->cost->payment_status;
                                                // $status = $payment->payment_type;
                                                $badgeColor = match ($status) {
                                                    'full' => 'success',
                                                    'partial' => 'warning',
                                                    'unpaid' => 'info',
                                                    default => 'secondary',
                                                };
                                                $statusText = match ($status) {
                                                    'full' => 'Lunas',
                                                    'partial' => 'Bayar sebagian',
                                                    'unpaid' => 'Belum Dibayar',
                                                    default => $status,
                                                };
                                            @endphp
                                            <span class="badge bg-{{ $badgeColor }} text-capitalize">
                                                {{ $statusText }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-sm">Tidak ada data laporan pembayaran.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">
<script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>

<script>
function confirmPrint(event, element) {
    const table = document.querySelector('table');

    if (table && table.innerText.includes('Tidak ada data')) {
        event.preventDefault();
        const notyf = new Notyf({
            duration: 4000,
            position: { x: 'right', y: 'top' },
            types: [{
                type: 'error',
                background: '#f5365c',
                dismissible: true
            }]
        });

        notyf.error('Data Pada Rentang Jarak Tersebut Kosong.');
        return false;
    }
    return true;
}
</script>
@endsection
