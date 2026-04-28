@extends('layouts.app')

@section('title', 'Laporan Akta')


@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Laporan Akta'])

    <div class="row mt-4 mx-4">
        <div class="col md-12">
            <div class="card mb-4">
                <div class="card-header mb-0 pb-0">
                    <h5>Laporan Akta</h5>
                </div>
                <hr>
                <div class="card-body pt-0">
                    <form method="GET" action="{{ route('laporan-akta.index') }}" class="no-spinner">
                        <div class="row g-1 align-items-end">
                            {{-- Pilih Type --}}
                            <div class="col-lg-4">
                                <label for="type" class="form-label text-sm">Jenis Akta</label>
                                <select name="type" id="type" class="form-select" required>
                                    <option value="" hidden>Pilih Jenis</option>
                                    <option value="notaris" {{ request('type') == 'notaris' ? 'selected' : '' }}>
                                        Notaris
                                    </option>
                                    <option value="ppat" {{ request('type') == 'ppat' ? 'selected' : '' }}>PPAT
                                    </option>
                                </select>
                            </div>

                            <div class="col-lg-2">
                                <label for="start_date" class="form-label text-sm">Tanggal Mulai</label>
                                <input type="date" class="form-control" name="start_date" id="start_date"
                                    value="{{ request('start_date') }}" required>
                            </div>

                            {{-- End Date --}}
                            <div class="col-lg-2">
                                <label for="end_date" class="form-label text-sm">Tanggal Selesai</label>
                                <input type="date" class="form-control" name="end_date" id="end_date"
                                    value="{{ request('end_date') }}" required>
                            </div>
                            <div class="col-lg-2">
                                <label for="status" class="form-label text-sm">Status</label>
                                <select name="status" id="status" class="form-select">
                                    <option value="">Semua Status</option>
                                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft
                                    </option>
                                    <option value="diproses" {{ request('status') == 'diproses' ? 'selected' : '' }}>
                                        Diproses</option>
                                    <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai
                                    </option>
                                    <option value="dibatalkan" {{ request('status') == 'dibatalkan' ? 'selected' : '' }}>
                                        Dibatalkan</option>
                                </select>
                            </div>

                            {{-- Tombol Filter --}}
                            <div class="col-lg-2">
                                <button type="submit" class="btn btn-primary w-100 mb-0">Cari</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Tabel Data --}}
            @if ($data->isNotEmpty())
                <div class="card">
                    <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                        <h6 class="text-capitalize">{{ ucfirst($queryType) }}</h6>
                        <a href="{{ route('laporan-akta.export-pdf', [
                            'type' => $queryType,
                            'start_date' => $startDate,
                            'end_date' => $endDate,
                        ]) }}"
                            class="btn btn-danger mb-3 d-flex align-items-center gap-2" target="_blank">
                            <i class="fa-solid fa-file-pdf"></i>
                            Export PDF
                        </a>
                    </div>

                    <div class="card-body pt-0 pb-0">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nomor Akta</th>
                                    <th>Kode Klien</th>
                                    <th>Nama Klien / Pihak</th>
                                    <th>Tanggal Dibuat</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $index => $row)
                                    <tr class="text-center text-sm">
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $row->akta_number ?? ($row->relaas_number ?? '-') }}</td>
                                        <td>{{ $row->client_code ?? '-' }}</td>
                                        <td>{{ $row->client->fullname ?? '-' }}</td>
                                        <td>{{ $row->created_at->format('d-m-Y H:i') }}</td>
                                        <td>
                                            <span
                                                class="badge text-capitalize
                                                @switch($row->status)
                                                    @case('draft') bg-secondary @break
                                                    @case('diproses') bg-warning @break
                                                    @case('selesai') bg-success @break
                                                    @case('dibatalkan') bg-danger @break
                                                    @default bg-light text-dark
                                                @endswitch
                                            ">
                                                {{ ucfirst($row->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>

@endsection
