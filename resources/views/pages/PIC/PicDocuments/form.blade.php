@extends('layouts.app')

@section('title', 'Pic Dokumen')

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'PIC / PIC Dokumen'])

    <div class="row mt-4 mx-4">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0">
                    <h6>{{ isset($picDocument) ? 'Edit' : 'Tambah' }} PIC Dokumen</h6>
                </div>
                <hr>
                <div class="card-body px-4 pt-0 pb-2">
                    <form
                        action="{{ isset($picDocument) ? route('pic_documents.update', $picDocument) : route('pic_documents.store') }}"
                        method="POST" id="picDocumentForm">
                        @csrf
                        @if (isset($picDocument))
                            @method('PUT')
                        @endif

                        <input type="hidden" name="transaction_id" id="transaction_id">

                        {{-- PIC Staff --}}
                        <div class="mb-3">
                            <label for="pic_id" class="form-label text-sm">PIC Staff <span
                                    class="text-danger">*</span></label>
                            <select name="pic_id" id="pic_id" class="form-select @error('pic_id') is-invalid @enderror">
                                <option value="" hidden>Pilih PIC Staff</option>
                                @foreach ($picStaffList as $pic)
                                    <option value="{{ $pic->id }}"
                                        {{ old('pic_id', $picDocument->pic_id ?? '') == $pic->id ? 'selected' : '' }}>
                                        {{ $pic->full_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('pic_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Tipe Transaksi --}}
                        <div class="mb-3">
                            <label for="transaction_type" class="form-label text-sm">Tipe Transaksi <span
                                    class="text-danger">*</span></label>
                            <select name="transaction_type" id="transaction_type"
                                class="form-select @error('transaction_type') is-invalid @enderror">
                                <option value="" hidden>Pilih Tipe Transaksi</option>
                                <option value="akta"
                                    {{ old('transaction_type', $picDocument->transaction_type ?? '') == 'akta' ? 'selected' : '' }}>
                                    Notaris
                                </option>
                                <option value="ppat"
                                    {{ old('transaction_type', $picDocument->transaction_type ?? '') == 'ppat' ? 'selected' : '' }}>
                                    PPAT
                                </option>
                                <option value="proses_lain"
                                    {{ old('transaction_type', $picDocument->transaction_type ?? '') == 'proses_lain' ? 'selected' : '' }}>
                                    Proses Lain
                                </option>
                            </select>
                            @error('transaction_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Akta Transaction (Notaris) --}}
                        <div class="mb-3" id="akta_section" style="display: none;">
                            <label for="akta_transaction_id" class="form-label text-sm">Transaksi Akta </label>
                            <select id="akta_transaction_id" name="akta_transaction_id"
                                class="form-select @error('akta_transaction_id') is-invalid @enderror">
                                <option value="" hidden>Pilih Transaksi Notaris</option>
                                @foreach ($aktaTransaction as $akta)
                                    <option value="{{ $akta->id }}"
                                        {{ isset($picDocument) && $picDocument->transaction_type === 'akta' && $picDocument->transaction_id == $akta->id ? 'selected' : '' }}>
                                        {{ $akta->client?->fullname ?? 'Tanpa Klien' }} - {{ $akta->transaction_code }} -
                                        {{ $akta->akta_type?->type ?? 'Tanpa Jenis' }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Format : Nama Klien – Kode Transaksi – Jenis Akta</small>
                        </div>

                        {{-- Relaas Transaction (PPAT) --}}
                        <div class="mb-3" id="relaas_section" style="display: none;">
                            <label for="ppat_transaction_id" class="form-label text-sm">Transaksi PPAT</label>
                            <select id="ppat_transaction_id" name="ppat_transaction_id"
                                class="form-select @error('ppat_transaction_id') is-invalid @enderror">
                                <option value="" hidden>Pilih Transaksi PPAT</option>
                                @foreach ($relaasTransaction as $relaas)
                                    <option value="{{ $relaas->id }}"
                                        {{ isset($picDocument) && $picDocument->transaction_type === 'ppat' && $picDocument->transaction_id == $relaas->id ? 'selected' : '' }}>
                                        {{ $relaas->client?->fullname ?? 'Tanpa Klien' }} - {{ $relaas->transaction_code }} -
                                        {{ $relaas->akta_type?->type ?? 'Tanpa Jenis' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Proses Lain Section --}}
                        <div class="mb-3" id="proses_lain_section" style="display: none;">
                            <label for="proses_lain_transaction_id" class="form-label text-sm">Transaksi Proses Lain <span class="text-danger">*</span></label>
                            
                            <select id="proses_lain_transaction_id" name="proses_lain_transaction_id" class="form-select">
                                {{-- Tambahkan 'disabled' dan 'selected' agar opsi ini hanya menjadi petunjuk --}}
                                <option value="" disabled selected hidden>Pilih Transaksi Proses Lain</option>

                                @if(isset($prosesLainTransaction) && count($prosesLainTransaction) > 0)
                                    @foreach ($prosesLainTransaction as $proses)
                                        <option value="{{ $proses->id }}"
                                            {{ isset($picDocument) && $picDocument->transaction_type === 'proses_lain' && $picDocument->transaction_id == $proses->id ? 'selected' : '' }}>
                                            {{ $proses->client->fullname ?? 'Klien Tanpa Nama' }} - 
                                            {{ $proses->transaction_code }} - 
                                            Proses Lain ({{ $proses->name ?? 'Tidak ada nama transaksi' }})
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        {{-- Tanggal Terima --}}
                        <div class="mb-3">
                            <label for="received_date" class="form-label text-sm">Tanggal Terima <span
                                    class="text-danger">*</span></label>
                            <input type="datetime-local" name="received_date" id="received_date"
                                class="form-control @error('received_date') is-invalid @enderror"
                                value="{{ old('received_date', isset($picDocument->received_date) ? \Carbon\Carbon::parse($picDocument->received_date)->format('Y-m-d\TH:i') : '') }}">
                            @error('received_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Status --}}
                        <div class="mb-3">
                            <label for="status" class="form-label text-sm">Status</label>
                            <select name="status" id="status" class="form-select" required>
                                <option value="">Pilih Status</option>
                                <option value="delivered" {{ old('status', $picDocument->status ?? '') == 'delivered' ? 'selected' : '' }}>Dikirim</option>
                                <option value="process" {{ old('status', $picDocument->status ?? '') == 'process' ? 'selected' : '' }}>Proses</option>
                                <option value="received" {{ old('status', $picDocument->status ?? '') == 'received' ? 'selected' : '' }}>Diterima</option>
                                <option value="completed" {{ old('status', $picDocument->status ?? '') == 'completed' ? 'selected' : '' }}>Selesai</option>
                            </select>
                        </div>

                        {{-- Catatan --}}
                        <div class="mb-3">
                            <label for="note" class="form-label text-sm">Catatan</label>
                            <textarea name="note" id="note" class="form-control" rows="3">{{ old('note', $picDocument->note ?? '') }}</textarea>
                        </div>

                        <div class="mt-4">
                            <a href="{{ route('pic_documents.index') }}" class="btn btn-secondary">Kembali</a>
                            <button type="submit" class="btn btn-primary">
                                {{ isset($picDocument) ? 'Ubah' : 'Simpan' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const typeSelect = document.getElementById('transaction_type');
            const akta = document.getElementById('akta_transaction_id');
            const ppat = document.getElementById('ppat_transaction_id');
            const prosesLain = document.getElementById('proses_lain_transaction_id');
            const transactionId = document.getElementById('transaction_id');
            const aktaSection = document.getElementById('akta_section');
            const relaasSection = document.getElementById('relaas_section');
            const prosesLainSection = document.getElementById('proses_lain_section');

            function toggleSections() {
                const value = typeSelect.value;
                aktaSection.style.display = value === 'akta' ? 'block' : 'none';
                relaasSection.style.display = value === 'ppat' ? 'block' : 'none';
                prosesLainSection.style.display = value === 'proses_lain' ? 'block' : 'none';
                if (value === 'akta') transactionId.value = akta.value;
                if (value === 'ppat') transactionId.value = ppat.value;
                if (value === 'proses_lain') transactionId.value = prosesLain.value;
            }

            if (typeSelect.value) {
                toggleSections();
            }

            typeSelect.addEventListener('change', toggleSections);
            [akta, ppat, prosesLain].forEach(el => {
                el.addEventListener('change', function() {
                    transactionId.value = this.value;
                });
            });

            const form = document.getElementById("picDocumentForm");
            form.addEventListener("submit", function(e) {
                if (typeSelect.value === 'proses_lain' && prosesLain.value === "") {
                    e.preventDefault();
                    alert("Harap pilih transaksi proses lain terlebih dahulu!");
                    prosesLain.focus();
                }
            });
        });
    </script>
@endpush