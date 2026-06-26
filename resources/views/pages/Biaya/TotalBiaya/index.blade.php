@extends('layouts.app')

@section('title', 'Total Biaya')

@section('content')

@include('layouts.navbars.auth.topnav', [
    'title' => $module . ' / Total Biaya'
])

@if ($module == 'PPAT')
    @include('components.ppat-menu')
@elseif ($module == 'Proses Lain')
    @include('components.proseslain-menu')
@else
    @include('components.notaris-menu')
@endif

    <div class="row mt-4 mx-4">
        <div class="col-12">
            <div class="card mb-0">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center mb-4">
                    <h5 class="mb-0">Total Biaya</h5>
                    <a href="{{ route('notary_costs.create') }}" class="btn btn-primary btn-sm mb-0">
                        + Tambah Biaya
                    </a>
                </div>
                <form method="GET" action="{{ route('notary_costs.index') }}" class="d-flex gap-2 ms-auto me-4"
                    style="width: 500px;">
                    <input type="text" name="search" placeholder="Cari kode pembayaran..."
                        value="{{ request('search') }}" class="form-control form-control-sm">
                    <button type="submit" class="btn btn-primary btn-sm mb-0">Cari</button>
                </form>
                <hr>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr class="text-center text-sm">
                                    <th>#</th>
                                    <th>Kode Pembayaran</th>
                                    <th>Klien</th>
                                    <th>Kode Klien</th>
                                    <th>Kode Dokumen</th>
                                    <th>Total Biaya</th>
                                    <th>Jumlah Pembayaran</th>
                                    <th>Piutang</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($costs as $cost)
                                    <tr class="text-center text-sm">
                                        <td>{{ $costs->firstItem() + $loop->index }}</td>
                                        <td>
                                            <div class="d-flex justify-content-center align-items-center gap-2">
                                                <span>{{ $cost->payment_code }}</span>

                                                <button
                                                    type="button"
                                                    class="btn btn-link p-0 text-primary"
                                                    onclick="copyValue(this, '{{ $cost->payment_code }}')">
                                                    <i class="fa-solid fa-copy"></i>
                                                </button>
                                            </div>
                                        </td>
                                        <td>{{ $cost->client->fullname }}</td>
                                        <td>
                                            <div class="d-flex justify-content-center align-items-center gap-2">
                                                <span>{{ $cost->client->client_code }}</span>

                                                <button
                                                    type="button"
                                                    class="btn btn-link p-0 text-primary"
                                                    onclick="copyValue(this, '{{ $cost->client->client_code }}')">
                                                    <i class="fa-solid fa-copy"></i>
                                                </button>
                                            </div>
                                        </td>

                                        <td>
                                            <div class="d-flex justify-content-center align-items-center gap-2">
                                                <span>{{ $cost->picDocument?->pic_document_code ?? '-' }}</span>

                                                @if($cost->picDocument?->pic_document_code)
                                                    <button
                                                        type="button"
                                                        class="btn btn-link p-0 text-primary"
                                                        onclick="copyValue(this, '{{ $cost->picDocument->pic_document_code }}')">
                                                        <i class="fa-solid fa-copy"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                        @php
                                            $totalPaid = $cost->payments->where('is_valid', true)->sum('amount');
                                            $remaining = max(0, $cost->total_cost - $totalPaid);
                                        @endphp

                                        <td>Rp {{ number_format($cost->total_cost, 0, ',', '.') }}</td>
                                        <td>Rp {{ number_format($totalPaid, 0, ',', '.') }}</td>
                                        <td>Rp {{ number_format($remaining, 0, ',', '.') }}</td>
                                        @php
                                            $statusMap = [
                                                'paid' => ['class' => 'success', 'label' => 'Lunas'],
                                                'partial' => ['class' => 'warning', 'label' => 'Bayar Sebagian'],
                                                'unpaid' => ['class' => 'danger', 'label' => 'Belum Bayar'],
                                            ];

                                            $status = $statusMap[$cost->payment_status] ?? [
                                                'class' => 'secondary',
                                                'label' => ucfirst($cost->payment_status),
                                            ];
                                        @endphp

                                        <td>
                                            <span class="badge bg-{{ $status['class'] }} px-3 py-2 text-capitalize"
                                                style="font-size: 12px">
                                                {{ $status['label'] }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('notary_costs.print', $cost->id) }}"
                                                class="btn btn-danger btn-xs mb-0" target="_blank" title="Cetak PDF">
                                                <i class="bi bi-filetype-pdf " style="font-size:14px;"></i> Cetak
                                            </a>
                                            <button type="button" class="btn btn-dark btn-xs mb-0" data-bs-toggle="modal"
                                                data-bs-target="#detailModal{{ $cost->id }}">
                                                <i class="bi bi-eye"></i> Lihat
                                            </button>
                                            <div class="modal fade" id="detailModal{{ $cost->id }}" tabindex="-1"
                                                aria-labelledby="detailModalLabel{{ $cost->id }}" aria-hidden="true">
                                                <div class="modal-dialog modal-lg modal-dialog-centered">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title"
                                                                id="detailModalLabel{{ $cost->id }}">
                                                                Detail Pembayaran - {{ $cost->payment_code }}
                                                            </h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                                aria-label="Tutup"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <form>
                                                                <div class="row g-3">
                                                                    <div class="col-md-6">
                                                                        <label class="form-label text-start w-100 mb-1">Kode
                                                                            Pembayaran</label>
                                                                        <input type="text" class="form-control"
                                                                            value="{{ $cost->payment_code }}" readonly>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <label
                                                                            class="form-label text-start w-100 mb-1">Klien</label>
                                                                        <input type="text" class="form-control"
                                                                            value="{{ $cost->client->fullname ?? '-' }}"
                                                                            readonly>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <label
                                                                            class="form-label text-start w-100 mb-1">Notaris</label>
                                                                        <input type="text" class="form-control"
                                                                            value="{{ $cost->notaris->display_name ?? '-' }}"
                                                                            readonly>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <label class="form-label text-start w-100 mb-1">Kode
                                                                            Dokumen</label>
                                                                        <input type="text" class="form-control"
                                                                            value="{{ $cost->picDocument->pic_document_code ?? '-' }}"
                                                                            readonly>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <label
                                                                            class="form-label text-start w-100 mb-1">Biaya
                                                                            Produk/Jasa</label>
                                                                        <input type="text" class="form-control"
                                                                            value="Rp {{ number_format($cost->product_cost, 0, ',', '.') }}"
                                                                            readonly>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <label
                                                                            class="form-label text-start w-100 mb-1">Biaya
                                                                            Admin</label>
                                                                        <input type="text" class="form-control"
                                                                            value="Rp {{ number_format($cost->admin_cost, 0, ',', '.') }}"
                                                                            readonly>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <label
                                                                            class="form-label text-start w-100 mb-1">Biaya
                                                                            Lain-lain</label>
                                                                        <input type="text" class="form-control"
                                                                            value="Rp {{ number_format($cost->other_cost, 0, ',', '.') }}"
                                                                            readonly>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <label
                                                                            class="form-label text-start w-100 mb-1">Total
                                                                            Biaya</label>
                                                                        <input type="text" class="form-control"
                                                                            value="Rp {{ number_format($cost->total_cost, 0, ',', '.') }}"
                                                                            readonly>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <label
                                                                            class="form-label text-start w-100 mb-1">Jumlah
                                                                            Dibayar</label>
                                                                        <input type="text" class="form-control"
                                                                            value="Rp {{ number_format($cost->amount_paid, 0, ',', '.') }}"
                                                                            readonly>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <label
                                                                            class="form-label text-start w-100 mb-1">Status
                                                                            Pembayaran</label>
                                                                        <input type="text" class="form-control"
                                                                            value="@if ($cost->payment_status == 'unpaid') Belum Dibayar @elseif($cost->payment_status == 'partial') Sebagian Dibayar @else Lunas @endif"
                                                                            readonly>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <label
                                                                            class="form-label text-start w-100 mb-1">Tanggal
                                                                            Jatuh Tempo</label>
                                                                        <input type="text" class="form-control"
                                                                            value="{{ $cost->due_date ? \Carbon\Carbon::parse($cost->due_date)->format('d/m/Y') : '-' }}"
                                                                            readonly>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <label
                                                                            class="form-label text-start w-100 mb-1">Tanggal
                                                                            Bayar</label>
                                                                        <input type="text" class="form-control"
                                                                            value="{{ $cost->paid_date ? \Carbon\Carbon::parse($cost->paid_date)->format('d/m/Y') : '-' }}"
                                                                            readonly>
                                                                    </div>
                                                                    <div class="col-12">
                                                                        <label
                                                                            class="form-label text-start w-100 mb-1">Catatan</label>
                                                                        <textarea class="form-control" rows="2" readonly>{{ $cost->note ?? '-' }}</textarea>
                                                                    </div>
                                                                    <div class="col-12 text-center mt-3">
                                                                        <label class="form-label d-block fw-bold">QR Code Notaris</label>
                                                                        <div class="p-2 d-inline-block border rounded bg-light">
                                                                            {!! SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')
                                                                                ->size(130)
                                                                                ->margin(1)
                                                                                ->generate(route('profileNotaris', \Illuminate\Support\Facades\Crypt::encryptString($cost->notaris->id))) !!}
                                                                        </div>
                                                                        <small class="text-muted d-block mt-1 fw-bold">{{ $cost->notaris->display_name ?? '-' }}</small>
                                                                    </div>
                                                                </div>
                                                            </form>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary"
                                                                data-bs-dismiss="modal">Tutup</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <a href="{{ route('notary_costs.edit', $cost->id) }}"
                                                class="btn btn-info btn-sm mb-0">Edit</a>
                                            <!-- Tombol Hapus -->
                                            <button type="button" class="btn btn-danger btn-sm mb-0"
                                                data-bs-toggle="modal" data-bs-target="#deleteModal{{ $cost->id }}">
                                                Hapus
                                            </button>

                                            <!-- Modal Konfirmasi -->
                                            <div class="modal fade" id="deleteModal{{ $cost->id }}" tabindex="-1"
                                                aria-labelledby="deleteModalLabel{{ $cost->id }}" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title"
                                                                id="deleteModalLabel{{ $cost->id }}">
                                                                Konfirmasi Hapus</h5>
                                                            <button type="button" class="btn-close"
                                                                data-bs-dismiss="modal" aria-label="Tutup"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            Apakah Anda yakin ingin menghapus data pembayaran
                                                            <strong>{{ $cost->payment_code }}</strong>?
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary"
                                                                data-bs-dismiss="modal">Batal</button>
                                                            <form action="{{ route('notary_costs.destroy', $cost->id) }}"
                                                                method="POST" class="d-inline-block">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit"
                                                                    class="btn btn-danger">Hapus</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center text-muted text-sm">Belum ada data total
                                            biaya
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <div class="d-flex justify-content-end mt-3 me-3">
                            {{ $costs->links() }}
                        </div>
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
