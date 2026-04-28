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
                    <form action="{{ isset($cost) ? route('notary_costs.update', $cost->id) : route('notary_costs.store') }}"
                        method="POST">
                        @csrf
                        @if (isset($cost))
                            @method('PUT')
                        @endif

                        {{-- Client --}}
                        <div class="mb-3">
                            <label for="client_code" class="form-label text-sm">Klien <span
                                    class="text-danger">*</span></label>
                            <select name="client_code" id="client_code"
                                class="form-control form-control-sm select2 @error('client_code') is-invalid @enderror ">
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

                        {{-- Dokumen --}}
                        <div class="mb-3">
                            <label for="pic_document_id" class="form-label text-sm">Dokumen <span
                                    class="text-danger">*</span></label>
                            <select name="pic_document_id" id="pic_document_id"
                                class="form-control @error('pic_document_id') is-invalid @enderror">
                                <option value="" hidden>Pilih Dokumen</option>
                                @foreach ($picDocuments as $doc)
                                    <option value="{{ $doc->id }}"
                                        {{ old('pic_document_id', $cost->pic_document_id ?? '') == $doc->id ? 'selected' : '' }}
                                        class="text-capitalize">
                                        {{ $doc->client->fullname }} - {{ $doc->pic_document_code }} - {{ $doc->transaction_type }}
                                    </option>
                                @endforeach
                            </select>
                            @error('pic_document_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <x-pilih-transaksi :transactions="$transactions" :selectedTransactionId="old('transaction_id', $cost->transaction_id ?? '')" />
                        {{-- Payment Code --}}
                        {{-- <div class="mb-3">
                        <label for="payment_code" class="form-label text-sm">Kode Pembayaran</label>
                        <input type="text" name="payment_code" id="payment_code" class="form-control form-control-sm"
                            value="{{ old('payment_code', $cost->payment_code ?? '') }}">
                        @error('payment_code') <p class="text-danger mt-2">{{ $message }}</p> @enderror
                    </div> --}}

                        {{-- Biaya --}}
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="product_cost" class="form-label text-sm">Biaya Produk <span
                                        class="text-danger">*</span></label>
                                <div class="input-group flex-nowrap">
                                    <span class="input-group-text" id="addon-wrapping">Rp</span>
                                    <input type="text" name="product_cost" id="product_cost"
                                        class="form-control form-control-sm currency @error('product_cost') is-invalid @enderror"
                                        value="{{ old('product_cost', $cost->product_cost ?? '') }}"
                                        aria-describedby="addon-wrapping">

                                </div>
                                @error('product_cost')
                                    <div class="text-danger mt-1" style="font-size: 0.875em!important">{{ $message }}
                                    </div>
                                @enderror

                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="admin_cost" class="form-label text-sm">Biaya Admin</label>
                                <div class="input-group">
                                    <span class="input-group-text" id="visible-addon">Rp</span>
                                    <input type="text" name="admin_cost" id="admin_cost" class="form-control currency "
                                        value="{{ old('admin_cost', $cost->admin_cost ?? '') }}">
                                </div>
                                @error('admin_cost')
                                    <div class="text-danger mt-1" style="font-size: 0.875em!important">{{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        {{-- Total & Dibayar --}}
                        <div class="row">
                            {{-- <div class="col-md-6 mb-3">
                            <label for="total_cost" class="form-label text-sm">Total Biaya</label>
                            <input type="text" name="total_cost" id="total_cost"
                                class="form-control form-control-sm currency"
                                value="{{ old('total_cost', $cost->total_cost ?? 0) }}">
                        </div> --}}
                            <div class="col-md-6 mb-3">
                                <label for="other_cost" class="form-label text-sm">Biaya Lain</label>
                                <div class="input-group">
                                    <span class="input-group-text" id="visible-addon">Rp</span>
                                    <input type="text" name="other_cost" id="other_cost"
                                        class="form-control form-control-sm currency"
                                        value="{{ old('other_cost', $cost->other_cost ?? '') }}">
                                </div>
                                @error('other_cost')
                                    <div class="text-danger mt-1" style="font-size: 0.875em!important">{{ $message }}
                                    </div>
                                @enderror
                            </div>
                            {{-- <div class="col-md-6 mb-3 ">
                                <label for="amount_paid" class="form-label text-sm">Jumlah Dibayar</label>
                                <div class="input-group">
                                    <span class="input-group-text" id="visible-addon">Rp</span>
                                    <input type="text" name="amount_paid" id="amount_paid"
                                        class="form-control form-control-sm currency"
                                        value="{{ old('amount_paid', $cost->amount_paid ?? '') }}">
                                </div>
                                @error('amount_paid')
                                    <p class="text-danger mt-2">{{ $message }}</p>
                                @enderror
                            </div>
                        </div> --}}

                            {{-- Status --}}
                            <div class="mb-3 col-md-6">
                                <label for="payment_status" class="form-label text-sm">Status <span
                                        class="text-danger">*</span></label>
                                <select name="payment_status" id="payment_status" class="form-control form-control-md">
                                    <option value="unpaid"
                                        {{ old('payment_status', $cost->payment_status ?? '') == 'unpaid' ? 'selected' : '' }}>
                                        Belum Dibayar</option>
                                    <option value="partial"
                                        {{ old('payment_status', $cost->payment_status ?? '') == 'partial' ? 'selected' : '' }}>
                                        Bayar Sebagian</option>
                                    <option value="paid"
                                        {{ old('payment_status', $cost->payment_status ?? '') == 'paid' ? 'selected' : '' }}>
                                        Lunas</option>
                                </select>
                            </div>

                            {{-- Tanggal --}}
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="paid_date" class="form-label text-sm">Tanggal Bayar</label>
                                    <input type="date" name="paid_date" id="paid_date"
                                        class="form-control form-control-md"
                                        value="{{ old('paid_date', isset($cost->paid_date) ? \Carbon\Carbon::parse($cost->paid_date)->format('Y-m-d') : '') }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="due_date" class="form-label text-sm">Jatuh Tempo</label>
                                    <input type="date" name="due_date" id="due_date"
                                        class="form-control form-control-md"
                                        value="{{ old('due_date', isset($cost->due_date) ? \Carbon\Carbon::parse($cost->due_date)->format('Y-m-d') : '') }}">
                                </div>
                            </div>

                            {{-- Catatan --}}
                            <div class="mb-3">
                                <label for="note" class="form-label text-sm">Catatan</label>
                                <textarea name="note" id="note" rows="2" class="form-control form-control-sm">{{ old('note', $cost->note ?? '') }}</textarea>
                            </div>

                            <div class="mt-4">
                                <a href="{{ route('notary_costs.index') }}" class="btn btn-secondary">Kembali</a>
                                <button type="submit"
                                    class="btn btn-primary">{{ isset($cost) ? 'Ubah' : 'Simpan' }}</button>
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
            const currencyInputs = document.querySelectorAll('input.currency');

            const formatRupiah = (value) => {
                if (!value) return '';
                // ambil digit angka
                const digits = value.toString().replace(/\D/g, '');
                if (digits === '') return '';
                // tambahkan titik pemisah ribuan
                return digits.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            };

            const unformat = (value) => {
                if (!value) return '';
                return value.toString().replace(/\./g, '').replace(/\D/g, '');
            };

            // Format nilai yang sudah ada saat load
            currencyInputs.forEach(input => {
                input.value = formatRupiah(input.value);

                // setiap user mengetik → format ulang
                input.addEventListener('input', (e) => {
                    const cursorPos = e.target.selectionStart; // posisi kursor
                    const cleaned = unformat(e.target.value); // angka polos
                    e.target.value = formatRupiah(cleaned);
                    // taruh kursor di akhir (lebih simpel)
                    e.target.setSelectionRange(e.target.value.length, e.target.value.length);
                });
            });

            // sebelum submit → kirim angka polos ke server
            const form = document.querySelector('form');
            if (form) {
                form.addEventListener('submit', () => {
                    currencyInputs.forEach(input => {
                        input.value = unformat(input.value);
                    });
                });
            }
        });
    </script>
@endpush
