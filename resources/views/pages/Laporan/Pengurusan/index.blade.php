@extends('layouts.app')

@section('title', 'Laporan Pengurusan')

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Laporan Pengurusan'])
    <div class="row mt-4 mx-4">
        <div class="col-12">
            <div class="card shadow-sm border-0 pb-0">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold">Laporan Pengurusan</h5>
                        <a href="{{ route('report-progress.print', request()->all()) }}" 
                        target="_blank"
                        class="btn btn-danger mb-0 btn-sm"
                        onclick="return confirmPrint(event, this)">
                        <i class="bi bi-file-earmark-pdf"></i> PDF
                        </a>
                </div>

                <hr>
                <div class="card-body pt-1">
                    <form method="GET" action="{{ route('report-progress.index') }}" class="row g-3 mb-4 px-0 no-spinner">
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

                        <div class=" col-md-2 col-xl-2">
                            <label for="status" class="form-label text-sm">Status</label>
                            <select class="form-select" name="status" id="status">
                                <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>Semua</option>
                                <option value="lunas" {{ request('status') == 'lunas' ? 'selected' : '' }}>Lunas</option>
                                <option value="dp" {{ request('status') == 'dp' ? 'selected' : '' }}>DP</option>
                                <option value="belum" {{ request('status') == 'belum' ? 'selected' : '' }}>Belum Bayar
                                </option>
                            </select>
                        </div>

                        <div class=" col-md-2 col-xl-2 d-flex align-items-end py-1 justify-content-end">
                            <button type="submit" class="btn btn-primary btn-sm w-100 mb-0">
                                Cari
                            </button>
                        </div>
                    </form>
                    {{-- Tabel --}}
                    <div class="table-responsive">
                        <table class="table table-hover align-items-center mb-0">
                            <thead>
                                <tr class="text-center">
                                    <th>#</th>
                                    <th>Kode Dokumen</th>
                                    <th>Pic Staff</th>
                                    <th>Nama Klien</th>
                                    <th>Proses</th>
                                    <th>Status</th>
                                    <th>Tanggal Pengurusan</th>
                                    <th>Catatan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($processes as $process)
                                    <tr class="text-center text-sm">
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $process->pic_document->pic_document_code }}
                                        </td>
                                        <td>{{ $process->pic_document->pic->full_name }}</td>
                                        <td>{{ $process->pic_document->client->fullname ?? '-' }}</td>
                                        <td>{{ $process->step_name ?? '-' }}</td>
                                        <td class="text-capitalize">
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
                                            <span class="badge bg-{{ $statusColor }} text-capitalize badge-md">
                                                {{ $statusText }}
                                            </span>
                                        </td>
                                        <td>{{ $process->step_date->format('d-m-Y') }}</td>
                                        <td>{{ $process->note }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-sm text-muted">Tidak ada data laporan
                                            pengurusan
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination (kalau pakai paginate) --}}
                    {{-- <div class="mt-3 px-2">
                    {{ $processes->links() }}
                </div> --}}
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
