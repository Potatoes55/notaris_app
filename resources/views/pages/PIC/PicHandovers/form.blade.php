@extends('layouts.app')

@section('title', 'Serah Terima Dokumen')


@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'PIC / Serah Terima Dokumen'])

    <div class="row mt-4 mx-4">
        <div class="col-md-12 mx-auto">
            <div class="card">
                <div class="card-header pb-0">
                    <h6> {{ isset($pic_handover) ? 'Edit Serah Terima Dokumen' : 'Tambah Serah Terima Dokumen' }}</h6>
                </div>
                <hr>
                <div class="card-body pt-0">
                    <form method="POST" action="{{ route('pic_handovers.store') }}" enctype="multipart/form-data">
                        @csrf
                        {{-- Tipe Transaksi --}}
                        <x-pilih-transaksi :aktaTransaction="$aktaTransaction" :relaasTransaction="$relaasTransaction" />

                        <div class="mb-3">
                            <label class="form-label text-sm">Nama Penerima <span class="text-danger">*</span></label>
                            <input type="text" name="recipient_name"
                                class="form-control @error('recipient_name') is-invalid @enderror"
                                value="{{ old('recipient_name') }}">
                            @error('recipient_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-sm">Kontak Penerima <span class="text-danger">*</span></label>
                            <input type="text" name="recipient_contact"
                                class="form-control @error('recipient_contact') is-invalid @enderror"
                                value="{{ old('recipient_contact') }}">
                            @error('recipient_contact')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-sm">Tanggal Serah Terima</label>
                            <input type="date" name="handover_date"
                                class="form-control @error('handover_date') is-invalid @enderror"
                                value="{{ old('handover_date', now()->toDateString()) }}">
                            @error('handover_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-sm">File Serah Terima Dokumen</label>
                            <input type="file" name="file_path" class="form-control">
                            <small>Maksimal ukuran file <strong>10MB</strong> (Format: JPG,JPEG, PNG, atau PDF)</small>
                        </div>


                        <div class="mb-3">
                            <label class="form-label text-sm">Catatan</label>
                            <textarea name="note" class="form-control">{{ old('note') }}</textarea>
                        </div>

                        <a href="{{ route('pic_handovers.index') }}" class="btn btn-secondary">Kembali</a>
                        <button class="btn btn-primary">Simpan</button>
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
