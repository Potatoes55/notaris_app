@extends('layouts.app')

@section('title', 'Total Biaya')

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Total Biaya'])

    <div class="row mt-4 mx-4">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0">
                    <h6>{{ isset($cost) ? 'Edit' : 'Tambah' }} Biaya</h6>
                </div>
                <hr>
                <div class="card-body px-4 pt-0 pb-2">
                    @php
                        $formAction = match (request()->segment(1)) {
                            'notaris' => isset($cost)
                                ? route('notaris.costs.update', $cost->id)
                                : route('notaris.costs.store'),

                            'ppat' => isset($cost)
                                ? route('ppat.costs.update', $cost->id)
                                : route('ppat.costs.store'),

                            'proses-lain' => isset($cost)
                                ? route('proses-lain.biaya.total.update', $cost->id)
                                : route('proses-lain.biaya.total.store'),

                            default => isset($cost)
                                ? route('notary_costs.update', $cost->id)
                                : route('notary_costs.store'),
                        };

                        $backRoute = match (request()->segment(1)) {
                            'notaris' => route('notaris.costs'),
                            'ppat' => route('ppat.costs'),
                            'proses-lain' => route('proses-lain.biaya.total'),
                            default => route('notary_costs.index'),
                        };
                    @endphp
                                                <form action="{{ $formAction }}" method="POST">
                                                    @csrf
                                                    @if (isset($cost))
                                                        @method('PUT')
                                                    @endif

                                                    {{-- Klien & Dokumen --}}
                                                    <div class="row">
                                                        <div class="col-md-6 mb-3">
                                                            <label for="client_code" class="form-label text-sm">
                                                                Klien <span class="text-danger">*</span>
                                                            </label>
                                                            <select name="client_code" id="client_code"
                                                                class="form-control form-control-sm select2 @error('client_code') is-invalid @enderror">
                                                                <option value="" hidden>Pilih Klien</option>
                                                                @foreach ($clients as $client)
                                                                    <option value="{{ $client->client_code }}"
                                                                        {{ old('client_code', $cost->client_code ?? '') == $client->client_code ? 'selected' : '' }}>
                                                                        {{ $client->fullname }} - {{ $client->client_code }}
                                                                    </option>
                                                                @endforeach
                                                            </select>

                                                            @error('client_code')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>

                                                        <div class="col-md-6 mb-3">
                                                            <label for="pic_document_id" class="form-label text-sm">
                                                                Dokumen <span class="text-danger">*</span>
                                                            </label>

                                                            <select name="pic_document_id" id="pic_document_id"
                                                                class="form-control form-control-sm @error('pic_document_id') is-invalid @enderror">

                                                                <option value="" hidden>Pilih Dokumen</option>

                                                                @foreach ($picDocuments as $doc)
                                                                    <option
                                                                        value="{{ $doc->id }}"
                                                                        data-client="{{ $doc->client_code }}"
                                                                        data-transaction-type="{{ strtolower($doc->transaction_type) }}"
                                                                        {{ old('pic_document_id', $cost->pic_document_id ?? '') == $doc->id ? 'selected' : '' }}>
                                                                        {{ $doc->client->fullname }} -
                                                                        {{ $doc->pic_document_code }} -
                                                                        {{ $doc->transaction_type }}
                                                                    </option>
                                                                @endforeach

                                                            </select>

                                                            @error('pic_document_id')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    {{-- Biaya --}}
                                                    <div class="row">

                                                        <div class="col-md-6 mb-3">
                                                            <label for="product_cost" class="form-label text-sm">
                                                                Biaya Produk <span class="text-danger">*</span>
                                                            </label>

                                                            <div class="input-group">
                                                                <span class="input-group-text">Rp</span>

                                                                <input
                                                                    type="text"
                                                                    name="product_cost"
                                                                    id="product_cost"
                                                                    class="form-control form-control-sm currency @error('product_cost') is-invalid @enderror"
                                                                    value="{{ old('product_cost', $cost->product_cost ?? '') }}">
                                                            </div>

                                                            @error('product_cost')
                                                                <div class="text-danger mt-1" style="font-size:.875em">
                                                                    {{ $message }}
                                                                </div>
                                                            @enderror
                                                        </div>

                                                        <div class="col-md-6 mb-3">
                                                            <label for="admin_cost" class="form-label text-sm">
                                                                Biaya Admin
                                                            </label>

                                                            <div class="input-group">
                                                                <span class="input-group-text">Rp</span>

                                                                <input
                                                                    type="text"
                                                                    name="admin_cost"
                                                                    id="admin_cost"
                                                                    class="form-control form-control-sm currency"
                                                                    value="{{ old('admin_cost', $cost->admin_cost ?? '') }}">
                                                            </div>

                                                            @error('admin_cost')
                                                                <div class="text-danger mt-1" style="font-size:.875em">
                                                                    {{ $message }}
                                                                </div>
                                                            @enderror
                                                        </div>

                                                    </div>

                                                    {{-- PPAT --}}
                                                    <div id="ppat-fields" class="row" style="display:none;">

                                                        <div class="col-md-6 mb-3">
                                                            <label for="pph" class="form-label text-sm">
                                                                PPh
                                                            </label>

                                                            <div class="input-group">
                                                                <span class="input-group-text">Rp</span>

                                                                <input
                                                                    type="text"
                                                                    name="pph"
                                                                    id="pph"
                                                                    class="form-control form-control-sm currency"
                                                                    value="{{ old('pph', $cost->pph ?? '') }}">
                                                            </div>
                                                        </div>

                                                        <div class="col-md-6 mb-3">
                                                            <label for="bphtb" class="form-label text-sm">
                                                                BPHTB
                                                            </label>

                                                            <div class="input-group">
                                                                <span class="input-group-text">Rp</span>

                                                                <input
                                                                    type="text"
                                                                    name="bphtb"
                                                                    id="bphtb"
                                                                    class="form-control form-control-sm currency"
                                                                    value="{{ old('bphtb', $cost->bphtb ?? '') }}">
                                                            </div>
                                                        </div>

                                                    </div>

                                                    {{-- Biaya Lain & Status --}}
                                                    <div class="row">

                                                        <div class="col-md-6 mb-3">
                                                            <label for="other_cost" class="form-label text-sm">
                                                                Biaya Lain
                                                            </label>

                                                            <div class="input-group">
                                                                <span class="input-group-text">Rp</span>

                                                                <input
                                                                    type="text"
                                                                    name="other_cost"
                                                                    id="other_cost"
                                                                    class="form-control form-control-sm currency"
                                                                    value="{{ old('other_cost', $cost->other_cost ?? '') }}">
                                                            </div>
                                                        </div>

                                                        <div class="col-md-6 mb-3">
                                                            <label for="payment_status" class="form-label text-sm">
                                                                Status <span class="text-danger">*</span>
                                                            </label>

                                                            <select
                                                                name="payment_status"
                                                                id="payment_status"
                                                                class="form-control form-control-sm">

                                                                <option value="unpaid" {{ old('payment_status',$cost->payment_status ?? '')=='unpaid'?'selected':'' }}>
                                                                    Belum Dibayar
                                                                </option>

                                                                <option value="partial" {{ old('payment_status',$cost->payment_status ?? '')=='partial'?'selected':'' }}>
                                                                    Bayar Sebagian
                                                                </option>

                                                                <option value="paid" {{ old('payment_status',$cost->payment_status ?? '')=='paid'?'selected':'' }}>
                                                                    Lunas
                                                                </option>

                                                            </select>
                                                        </div>

                                                    </div>

                                                    {{-- Tanggal --}}
                                                    <div class="row">

                                                        <div class="col-md-6 mb-3">
                                                            <label for="paid_date" class="form-label text-sm">
                                                                Tanggal Bayar
                                                            </label>

                                                            <input
                                                                type="date"
                                                                name="paid_date"
                                                                id="paid_date"
                                                                class="form-control form-control-sm"
                                                                value="{{ old('paid_date', isset($cost->paid_date) ? \Carbon\Carbon::parse($cost->paid_date)->format('Y-m-d') : '') }}">
                                                        </div>

                                                        <div class="col-md-6 mb-3">
                                                            <label for="due_date" class="form-label text-sm">
                                                                Jatuh Tempo
                                                            </label>

                                                            <input
                                                                type="date"
                                                                name="due_date"
                                                                id="due_date"
                                                                class="form-control form-control-sm"
                                                                value="{{ old('due_date', isset($cost->due_date) ? \Carbon\Carbon::parse($cost->due_date)->format('Y-m-d') : '') }}">
                                                        </div>

                                                    </div>

                                                    {{-- Catatan --}}
                                                    <div class="mb-3">
                                                        <label for="note" class="form-label text-sm">
                                                            Catatan
                                                        </label>

                                                        <textarea
                                                            name="note"
                                                            id="note"
                                                            rows="3"
                                                            class="form-control form-control-sm">{{ old('note', $cost->note ?? '') }}</textarea>
                                                    </div>

                                                    <div class="d-flex justify-content-end gap-2 mt-4">
                                                        <a href="{{ $backRoute }}"
                                                        class="btn btn-secondary btn-sm mb-0">
                                                        Kembali
                                                    </a>
                            <button
                                type="submit"
                                class="btn btn-primary btn-sm mb-0">
                                {{ isset($cost) ? 'Ubah' : 'Simpan' }}
                            </button>
                        </div>

                    </form>
                </div>
        </div>
    </div>

    @push('js')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Format rupiah
            const currencyInputs = document.querySelectorAll('input.currency');
            const formatRupiah = (value) => {
                if (!value) return '';
                const digits = value.toString().replace(/\D/g, '');
                if (digits === '') return '';
                return digits.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            };
            const unformat = (value) => {
                if (!value) return '';
                return value.toString().replace(/\./g, '').replace(/\D/g, '');
            };
            currencyInputs.forEach(input => {
                input.value = formatRupiah(input.value);
                input.addEventListener('input', (e) => {
                    const cleaned = unformat(e.target.value);
                    e.target.value = formatRupiah(cleaned);
                    e.target.setSelectionRange(e.target.value.length, e.target.value.length);
                });
            });

            // Kirim angka asli saat submit
            const form = document.querySelector('form');
            if (form) {
                form.addEventListener('submit', () => {
                    currencyInputs.forEach(input => {
                        input.value = unformat(input.value);
                    });
                });
            }

            // Filter dokumen berdasarkan klien & Toggle PPAT Fields Integrasi
            const clientSelect = document.getElementById('client_code');
            const documentSelect = document.getElementById('pic_document_id');
            
            if (clientSelect && documentSelect) {
                // Simpan element options awal ke dalam array objek agar data-attributes aman
                const allOptions = Array.from(documentSelect.querySelectorAll('option')).map(opt => {
                    return {
                        value: opt.value,
                        text: opt.textContent,
                        client: opt.dataset.client,
                        type: opt.dataset.transactionType,
                        selected: opt.hasAttribute('selected') || opt.selected
                    };
                });

                function filterDocuments() {
                    const selectedClient = clientSelect.value;
                    const currentValue = documentSelect.value; // simpan nilai lama
                    
                    documentSelect.innerHTML = '<option value="" hidden>Pilih Dokumen</option>';
                    
                    allOptions.forEach(opt => {
                        if (!opt.value) return;
                        if (opt.client == selectedClient) {
                            const newOpt = document.createElement('option');
                            newOpt.value = opt.value;
                            newOpt.textContent = opt.text;
                            newOpt.setAttribute('data-client', opt.client);
                            newOpt.setAttribute('data-transaction-type', opt.type);
                            
                            // Pertahankan kondisi terpilih jika cocok
                            if (opt.value == currentValue || opt.selected) {
                                newOpt.selected = true;
                            }
                            documentSelect.appendChild(newOpt);
                        }
                    });

                    // Trigger toggle PPAT sehabis list dokumen difilter
                    togglePpatFields();
                }

                clientSelect.addEventListener('change', filterDocuments);
                // Jalankan di awal untuk inisialisasi awal (Edit mode / Old input)
                filterDocuments();
            }

            // Fungsi Toggle Kolom PPAT
            function togglePpatFields() {
                // PERBAIKAN: Memperbaiki selector dan mengambil attribute dari element murni vanilla JS
                const selectedOption = documentSelect.options[documentSelect.selectedIndex];
                const transactionType = selectedOption ? selectedOption.getAttribute('data-transaction-type') : '';
                
                if (transactionType === 'ppat') {
                    $('#ppat-fields').slideDown();
                } else {
                    $('#ppat-fields').slideUp();
                    // Kosongkan nilai pph & bphtb jika diganti ke non-ppat
                    $('#pph, #bphtb').val(''); 
                }
            }

            // Jalankan fungsi setiap kali dropdown dokumen berubah secara manual
            documentSelect.addEventListener('change', togglePpatFields);
        });
    </script>
    @endpush
@endsection