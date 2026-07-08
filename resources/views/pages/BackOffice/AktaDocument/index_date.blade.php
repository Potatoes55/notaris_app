@extends('layouts.app')

@section('title', 'Pencarian Dokumen Berdasarkan Tanggal')

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Akta Notaris / Pencarian Tanggal'])

    @include('components.notaris-menu')

    <div class="row mt-4 mx-4">
        <div class="col-12">
            <div class="card shadow-lg">

                <div class="card-header px-3 py-3">
                    <h5 class="mb-0">Hasil Pencarian Berdasarkan Tanggal</h5>
                </div>

                <hr class="my-0">

                <div class="card-body p-0">

                    <div class="px-3 py-3">
                        <form method="GET" action="{{ route('akta-documents.index') }}"
                            class="d-flex flex-wrap gap-2 justify-content-end align-items-end no-spinner">
                            @csrf

                            <div style="flex:1; min-width:200px;">
                                <label for="transaction_code" class="form-label text-sm">
                                    Kode Transaksi
                                </label>
                                <input
                                    type="text"
                                    name="transaction_code"
                                    id="transaction_code"
                                    class="form-control"
                                    placeholder="Cari Kode transaksi..."
                                    value="{{ $filters['transaction_code'] ?? '' }}">
                            </div>
                                                <div style="flex:1; min-width:200px;">
                        <label for="transaction_code" class="form-label text-sm">
                            Nama Client
                        </label>
                        <input
                            type="text"
                            name="fullname"
                            id="fullname"
                            class="form-control"
                            placeholder="Cari Nama Client..."
                            value="{{ $filters['fullname'] ?? '' }}">
                    </div>

                            <div style="flex:1; min-width:200px;">
                                <label for="akta_number" class="form-label text-sm">
                                    Nomor Akta
                                </label>
                                <input
                                    type="text"
                                    name="akta_number"
                                    id="akta_number"
                                    class="form-control"
                                    placeholder="Cari nomor akta..."
                                    value="{{ $filters['akta_number'] ?? '' }}">
                            </div>

                            <div style="width:160px;">
                                <label for="start_date" class="form-label text-sm">
                                    Tanggal Mulai
                                </label>
                                <input
                                    type="date"
                                    class="form-control"
                                    name="start_date"
                                    id="start_date"
                                    value="{{ request('start_date') }}">
                            </div>

                            <div style="width:160px;">
                                <label for="end_date" class="form-label text-sm">
                                    Tanggal Selesai
                                </label>
                                <input
                                    type="date"
                                    class="form-control"
                                    name="end_date"
                                    id="end_date"
                                    value="{{ request('end_date') }}">
                            </div>

                            <div>
                                <button type="submit" class="btn btn-primary mb-0 px-4">
                                    Cari
                                </button>
                            </div>
                        </form>
                    </div>

                    <div class="table-responsive">
                        <table class="table align-items-center mb-0">

<thead>
    <tr>

        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
            No
        </th>

        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
            Nama Klien
        </th>

        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
            Kode Transaksi
        </th>

        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">
            Jumlah Dokumen
        </th>

        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">
            Tanggal Submit
        </th>

        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
            Aksi
        </th>

    </tr>
</thead>

                            <tbody>

                                @forelse ($transactions as $tx)

                                    <tr>

                                        <td class="align-middle text-center">
                                            <p class="text-xs font-weight-bold mb-0">
                                                {{ $transactions->firstItem() + $loop->index }}
                                            </p>
                                        </td>

                                        <td class="align-middle text-center">
                                            <p class="text-sm font-weight-normal mb-0">
                                                {{ $tx->client->fullname ?? '-' }}
                                            </p>
                                        </td>

                                        <td class="align-middle">
                                            <div class="d-flex justify-content-center align-items-center gap-2">

                                                <p class="text-sm font-weight-normal mb-0">
                                                    {{ $tx->transaction_code }}
                                                </p>

                                                @if($tx->transaction_code)
                                                    <button
                                                        type="button"
                                                        class="btn btn-link text-primary p-0 mb-0"
                                                        onclick="copyValue(this, '{{ $tx->transaction_code }}')">

                                                        <i class="fa-solid fa-copy text-sm"></i>

                                                    </button>
                                                @endif

                                            </div>
                                        </td>

                                        <td class="align-middle text-center">
                                            <p class="text-sm font-weight-normal mb-0">
                                                {{ $tx->documents_count ?? 0 }} Dokumen
                                            </p>
                                        </td>

                                        <td class="align-middle text-center">
                                            <p class="text-sm font-weight-normal mb-0">
                                                {{ $tx->date_submission ? \Carbon\Carbon::parse($tx->date_submission)->format('d F Y H:i') : '-' }}
                                            </p>
                                        </td>

                                        <td class="align-middle text-center">
                                            <a href="{{ route('akta-documents.index', ['transaction_code' => $tx->transaction_code]) }}"
                                                class="btn btn-info btn-xs mb-0">
                                                Detail Transaksi
                                            </a>
                                        </td>

                                    </tr>

                                @empty

                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            <p class="text-sm text-secondary mb-0">
                                                Belum ada data dokumen.
                                            </p>
                                        </td>
                                    </tr>

                                @endforelse

                            </tbody>

                        </table>
                    </div>

                    <div class="d-flex justify-content-end px-4 py-3">
                        {{ $transactions->links() }}
                    </div>

                </div>

            </div>
        </div>
    </div>
@push('js')
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
@endpush
@endsection