@extends('layouts.app')


@section('title', 'Klien')

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Klien'])
    <div class="row mt-4 mx-4 ">
        <div class="col-md-12">
            <div class="card mb-0  p-3 shadow-lg pb-0">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center px-2 flex-wrap pt-0">
                    <h5 class="mb-0">Klien</h5>
                    <div class="d-flex gap-2 flex-wrap">
                        @php
                            $encryptedId = Crypt::encrypt(auth()->user()->notaris_id);
                            $shareUrl = route('client.public.create', ['encryptedNotarisId' => $encryptedId]);
                        @endphp
                        <button class="btn btn-outline-primary btn-sm mb-0" data-bs-toggle="modal"
                            data-bs-target="#shareLinkModal">
                            <i class="fas fa-link"></i> Salin Link Form Klien
                        </button>
                        <div class="modal fade" id="shareLinkModal" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content p-3 text-center">
                                    <h5 class="modal-title w-100 text-center mb-3">Link Form Klien</h5>
                                    <p class="text-muted mb-2 px-3">
                                        Bagikan link berikut kepada klien agar mereka dapat mengisi form secara langsung.
                                    </p>
                                    <button type="button"
                                        class="btn-close btn-close-white position-absolute end-0 me-4 p-2"
                                        style="background-color: var(--bs-primary); border-radius: 50%;"
                                        data-bs-dismiss="modal" aria-label="Close">
                                    </button>

                                    <div class="input-group my-4 shadow-sm rounded" style="max-width: 600px; margin:auto;">
                                        <input type="text" class="form-control" id="shareUrlInput"
                                            value="{{ $shareUrl }}" readonly onclick="this.select()">
                                        <button class="btn btn-primary mb-0 d-flex gap-2 align-items-center"
                                            onclick="copyToClipboard('{{ $shareUrl }}')">
                                            <i class="fa-regular fa-clipboard"></i> Salin
                                        </button>
                                    </div>

                                    <small class="text-muted">Klik tombol <strong>Salin</strong> untuk menyalin link ke
                                        clipboard.</small>
                                </div>
                            </div>
                        </div>

                        <a href="{{ route('clients.create') }}" class="btn btn-primary btn-sm mb-0">+ Tambah Klien</a>
                    </div>
                    <div class="d-flex  justify-content-end w-100 mt-3">
                        <form method="GET" action="{{ route('clients.index') }}"
                            class="d-flex flex-wrap gap-2   justify-content-end" style="max-width: 500px; width: 100%;">

                            <input type="text" name="search" placeholder="Cari Nama, NIK/No KTP"
                                value="{{ request('search') }}" class="form-control w-100 w-md-auto"
                                style="flex: 1 1 auto;">

                            <select name="status" class="form-select w-100 w-md-auto" style="flex: 1 1 auto;">
                                <option value="">Semua Status</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending
                                </option>
                                <option value="valid" {{ request('status') == 'valid' ? 'selected' : '' }}>Valid
                                </option>
                                <option value="revisi" {{ request('status') == 'revisi' ? 'selected' : '' }}>Revisi
                                </option>
                            </select>

                            <button type="submit" id="searchBtn"
                                class="btn btn-primary btn-sm mb-0 d-flex align-items-center justify-content-center"
                                style="width: 90px; height: 38px;">
                                <span id="searchBtnText">Cari</span>
                                <div id="searchSpinner" class="spinner-border spinner-border-sm text-light ms-2 d-none"
                                    role="status" aria-hidden="true"></div>
                            </button>
                        </form>
                    </div>
                </div>
                <hr>
                <div class="card-body px-0 pt-0 pb-0">

                    <div class="table-responsive p-0">
                        <div style="min-width: max-content;">

                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th>
                                            #
                                        </th>
                                        <th>Kode Klien</th>
                                        <th>
                                            Nama Klien
                                        </th>
                                        <th>
                                            NIK
                                        </th>
                                        <th>
                                            NPWP
                                        </th>
                                        <th>
                                            Nama Perusahaan
                                        </th>
                                        <th>
                                            Alamat
                                        </th>
                                        <th>
                                            Status
                                        </th>
                                        <th>
                                            Aksi
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($clients as $client)
                                        <tr class="text-sm mb-0 text-center">
                                            <td>
                                                {{ $clients->firstItem() + $loop->index }}
                                            </td>
                                            <td>
                                                {{ $client->client_code }}

                                            </td>
                                            <td>
                                                {{ $client->fullname }}

                                            </td>
                                            <td>
                                                {{ $client->nik }}

                                            </td>
                                            <td>
                                                {{ $client->npwp ?? '-' }}
                                            </td>
                                            <td>
                                                {{ $client->company_name ?? '-' }}
                                            </td>
                                            <td title="{{ $client->address }}">
                                                {{ \Illuminate\Support\Str::limit($client->address, 50, '...') }}
                                            </td>
                                            <td>
                                                <span
                                                    class="badge text-capitalize text-xs
                                        bg-{{ $client->status == 'valid' ? 'success' : ($client->status == 'revisi' ? 'warning' : 'secondary') }}">
                                                    {{ ucfirst($client->status) }}
                                                </span>
                                            </td>
                                            <td class="text-center align-middle">
                                                @if ($client->status != 'valid')
                                                    <form action="{{ route('clients.setRevision', $client->id) }}"
                                                        method="POST" class="d-inline-block">
                                                        @csrf
                                                        @method('PUT')
                                                        <button type="submit" class="btn btn-warning btn-xs mb-0"
                                                            data-bs-toggle="tooltip" data-bs-placement="top"
                                                            title="Revisi data klien">
                                                            <i class="fa fa-rotate-left"></i> Revisi
                                                        </button>
                                                    </form>
                                                @endif
                                                @php
                                                    $encryptedClientId = encrypt($client->id);
                                                    $revisionLink = url(
                                                        "/client/public/{$encryptedClientId}?mode=revision",
                                                    );

                                                @endphp
                                                <div class="modal fade" id="revisionModal" tabindex="-1"
                                                    aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered">
                                                        <div class="modal-content p-3 text-center">
                                                            <h5 class="modal-title mb-3">Link Revisi Klien</h5>
                                                            <hr class="mt-0">
                                                            <button type="button"
                                                                class="btn-close btn-close-white position-absolute end-0 me-4 p-2"
                                                                style="background-color: var(--bs-primary); border-radius: 50%;"
                                                                data-bs-dismiss="modal" aria-label="Close">
                                                            </button>

                                                            <p class="text-muted">Bagikan link berikut ke klien untuk
                                                                memperbaiki data mereka.</p>

                                                            <div class="input-group my-1 shadow-sm rounded">
                                                                <input type="text" class="form-control"
                                                                    value="{{ $revisionLink }}" readonly
                                                                    onclick="this.select()">
                                                                <button
                                                                    class="btn btn-primary d-flex gap-2 align-items-center mb-0"
                                                                    onclick="copyToClipboard('{{ $revisionLink }}')">
                                                                    <i class="fa-regular fa-clipboard"></i> Salin
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                @if ($client->uuid != null)
                                                    <button type="button" class="btn btn-dark btn-xs mb-0"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#qrModal{{ $client->id }}">
                                                        <i class="fa-solid fa-qrcode"></i>
                                                    </button>

                                                    <div class="modal fade" id="qrModal{{ $client->id }}"
                                                        tabindex="-1" aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-centered">
                                                            <div class="modal-content p-3 text-center ">
                                                                <h5 class="modal-title w-100 text-center">QR Code
                                                                </h5>
                                                                <hr class="mt-1 mb-1">
                                                                <button type="button"
                                                                    class="btn-close btn-close-white position-absolute end-0 me-4 p-2"
                                                                    style="background-color: var(--bs-primary); border-radius: 50%;"
                                                                    data-bs-dismiss="modal" aria-label="Close">
                                                                </button>
                                                                @php
                                                                    $link = url("/public-client/{$client->uuid}");
                                                                    $dns2d = new \Milon\Barcode\DNS2D();

                                                                    // true di sini artinya return base64 data langsung
                                                                    $png = $dns2d->getBarcodePNG(
                                                                        $link,
                                                                        'QRCODE',
                                                                        5,
                                                                        5,
                                                                        [0, 0, 0],
                                                                        true,
                                                                    );
                                                                @endphp
                                                                {{-- <img src="data:image/png;base64,{{ $png }}"
                                                                    alt="QR Code" class="img-fluid w-50 mx-auto mt-4" /> --}}
                                                                <img src="data:image/png;base64,{{ $png }}"
                                                                    alt="QR Code"
                                                                    class="img-fluid w-50 mx-auto mt-4 d-block"
                                                                    style="background: #fff;padding: 12px;border-radius: 8px;box-shadow: 0 4px 10px rgba(0,0,0,0.2);" />

                                                                <h6 class="mt-4">Link Klien</h6>
                                                                <div class="input-group my-2" style="max-width: 600px;">
                                                                    <input type="text" class="form-control"
                                                                        value="{{ $link }}" readonly
                                                                        onclick="this.select()">
                                                                    <button class="btn btn-primary mb-0 me-1"
                                                                        onclick="copyToClipboard('{{ $link }}')"
                                                                        title="Copy link">
                                                                        <i class="fa-regular fa-clipboard fa-lg"></i>
                                                                        Salin
                                                                    </button>
                                                                </div>

                                                                <div class="d-flex justify-content-center gap-2 mt-3">
                                                                    <a href="data:image/png;base64,{{ $png }}"
                                                                        download="qrcode-{{ $client->fullname }}.png"
                                                                        class="btn btn-primary btn-sm">
                                                                        Download
                                                                    </a>
                                                                    @php
                                                                        // ubah no HP dari 08xxxx -> 628xxxx
                                                                        $phone = !empty($client->phone)
                                                                            ? preg_replace('/^0/', '62', $client->phone)
                                                                            : null;
                                                                    @endphp

                                                                    @if ($phone)
                                                                        <a href="https://wa.me/{{ $phone }}?text={{ urlencode('Halo ' . $client->fullname . ', silakan akses link berikut: ' . $link) }}"
                                                                            target="_blank"
                                                                            class="btn btn-success btn-sm">
                                                                            Share WhatsApp
                                                                        </a>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif

                                                @if ($client->status == 'revisi')
                                                    @php
                                                        $encryptedClientId = encrypt($client->id); // Laravel encrypt helper
                                                        $revisionLink = url("/client/{$encryptedClientId}");
                                                    @endphp

                                                    <button class="btn btn-xs btn-info mb-0"
                                                        onclick="copyToClipboard('{{ $revisionLink }}')"
                                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                                        title="Bagikan link revisi">
                                                        <i class="fa fa-share-alt"></i>
                                                    </button>
                                                @endif

                                                @if ($client->status !== 'valid')
                                                    <form action="{{ route('clients.markAsValid', $client->id) }}"
                                                        method="POST" class="d-inline-block">
                                                        @csrf
                                                        @method('PUT')
                                                        <button type="submit" class="btn btn-success btn-xs mb-0"
                                                            data-bs-toggle="tooltip" data-bs-placement="top"
                                                            title="Memvalidasi data"><i
                                                                class="fa-solid fa-circle-check fa-3x me-1"
                                                                style="font-size: 14px">
                                                            </i> Valid</button>
                                                    </form>
                                                @endif

                                                <a href="{{ route('clients.edit', $client->id) }}"
                                                    class="btn btn-info btn-xs mb-0" data-bs-toggle="tooltip"
                                                    data-bs-placement="top" title="Mengubah data">
                                                    Edit
                                                </a>
                                                <button type="button" class="btn btn-danger btn-xs mb-0"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#deleteModal{{ $client->id }}"
                                                    data-bs-toggle="tooltip" data-bs-placement="top"
                                                    title="Menghapus data">
                                                    Hapus
                                                </button>
                                                @include('pages.Client.modal.index')
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center text-muted text-sm">Belum ada data
                                                klien.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                            <div class="d-flex justify-content-end mt-3 px-4">
                                {{ $clients->links() }}
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text)
                .then(() => {
                    if (window.notyf) {
                        window.notyf.success('Link berhasil disalin ke clipboard!');
                    } else {
                        alert('Link berhasil disalin ke clipboard!');
                    }
                })
                .catch(() => {
                    if (window.notyf) {
                        window.notyf.error('Gagal menyalin link.');
                    } else {
                        alert('Gagal menyalin link.');
                    }
                });
        }
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            @if (session('revisionLink'))
                let modal = new bootstrap.Modal(document.getElementById('revisionModal'));
                modal.show();
            @endif
        });
    </script>
@endpush
