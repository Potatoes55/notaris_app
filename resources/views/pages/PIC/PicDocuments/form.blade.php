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
                        <x-pilih-transaksi :aktaTransaction="$aktaTransaction" :relaasTransaction="$relaasTransaction" />

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
                                <option value="delivered"
                                    {{ old('status', $picDocument->status ?? '') == 'delivered' ? 'selected' : '' }}>
                                    Dikirim</option>
                                <option value="process"
                                    {{ old('status', $picDocument->status ?? '') == 'process' ? 'selected' : '' }}>Proses
                                </option>
                                <option value="received"
                                    {{ old('status', $picDocument->status ?? '') == 'received' ? 'selected' : '' }}>
                                    Diterima</option>
                                <option value="completed"
                                    {{ old('status', $picDocument->status ?? '') == 'completed' ? 'selected' : '' }}>
                                    Selesai</option>
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

{{-- JS untuk toggle --}}
@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const typeSelect = document.getElementById('transaction_type');
            const akta = document.getElementById('akta_transaction_id');
            const ppat = document.getElementById('ppat_transaction_id');
            const transactionId = document.getElementById('transaction_id');

            const aktaSection = document.getElementById('akta_section');
            const relaasSection = document.getElementById('relaas_section');

            function toggleSections() {
                const value = typeSelect.value;
                aktaSection.style.display = value === 'akta' ? 'block' : 'none';
                relaasSection.style.display = value === 'ppat' ? 'block' : 'none';
            }

            toggleSections();
            typeSelect.addEventListener('change', toggleSections);

            const form = document.getElementById("picDocumentForm");

            form.addEventListener("submit", function() {
                if (typeSelect.value === 'akta') {
                    transactionId.value = akta.value || "";
                } else if (typeSelect.value === 'ppat') {
                    transactionId.value = ppat.value || "";
                }
            });

        });
    </script>
@endpush
