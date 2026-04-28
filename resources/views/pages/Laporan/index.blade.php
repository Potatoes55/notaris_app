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
                <div class="card-body px-2 pt-0 pb-2">
                    <form method="GET" action="{{ route('report-payment.index') }}"
                        class="row g-2 mb-4 px-1 flex-wrap no-spinner">
                        <div class="col-md-4 col-xl-4">
                            <label for="start_date" class="form-label text-sm">Tanggal Mulai</label>
                            <input type="date" class="form-control" id="start_date" name="start_date"
                                value="{{ request('start_date') }}">
                        </div>

                        <div class=" col-md-4 col-xl-4">
                            <label for="end_date" class="form-label text-sm">Tanggal Akhir</label>
                            <input type="date" class="form-control" id="end_date" name="end_date"
                                value="{{ request('end_date') }}">
                        </div>

                        <div class=" col-sm-2 col-md-4 col-xl-3">
                            <label for="status" class="form-label text-sm">Status</label>
                            <select class="form-select" name="status" id="status">
                                <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>Semua</option>
                                <option value="full" {{ request('status') == 'full' ? 'selected' : '' }}>Lunas</option>
                                <option value="dp" {{ request('status') == 'dp' ? 'selected' : '' }}>DP</option>
                                <option value="partial" {{ request('status') == 'partial' ? 'selected' : '' }}>Bayar
                                    Sebagian</option>
                                {{-- <option value="belum" {{ request('status') == 'unpaid' ? 'selected' : '' }}>Belum Bayar --}}
                                </option>
                            </select>
                        </div>

                        <div class=" col-lg-2 col-xl-1 d-flex align-items-end mt-0">
                            <button type="submit" class="btn  btn-primary btn-sm w-100 mb-0 mt-0"
                                style="height: 40px; font-size: 14px">
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
