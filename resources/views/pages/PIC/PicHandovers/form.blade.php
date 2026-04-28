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
                    <form id="picHandoverForm" method="POST" action="{{ route('pic_handovers.store') }}" enctype="multipart/form-data">
                        @csrf
                        {{-- Tipe Transaksi --}}
                        {{-- <div class="mb-3">
                            <label for="transaction_type" class="form-label text-sm">Tipe Transaksi <span
                                    class="text-danger">*</span></label>
                            <select name="transaction_type" id="transaction_type"
                                class="form-select @error('transaction_type') is-invalid @enderror"">
                                <option value="" hidden>Pilih Dokumen</option>
                                <option value="akta"
                                    {{ old('transaction_type', $picDocument->transaction_type ?? '') == 'akta' ? 'selected' : '' }}>
                                    Notaris
                                </option>
                                <option value="ppat"
                                    {{ old('transaction_type', $picDocument->transaction_type ?? '') == 'ppat' ? 'selected' : '' }}>
                                    PPAT
                                </option>
                            </select>
                            @error('transaction_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Akta Transaction --}}
                        {{-- <div class="mb-3" id="akta_section" style="display: none;">
                            <label for="akta_transaction_id" class="form-label text-sm">Dokumen Akta </label>
                            <select id="akta_transaction_id" name="akta_transaction_id"
                                class="form-select @error('akta_transaction_id') is-invalid @enderror">
                                <option value="" hidden>Pilih Dokumen Akta</option>
                                @foreach ($aktaDocuments as $akta)
                                    <option value="{{ $akta->id }}"
                                        {{ isset($picDocument) && $picDocument->transaction_type === 'akta' ? 'selected' : '' }}>
                                        {{ $akta->client->fullname }} - {{ $akta->pic_document_code }} -
                                        {{ $akta->transaction_type }}
                                        {{ $akta->title  }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">
                                Format : Nama Klien – Kode Transaksi – Jenis Akta
                            </small>
                            @error('transaction_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div> --}}

                        {{-- Relaas Transaction --}}
                        {{-- <div class="mb-3" id="relaas_section" style="display: none;">
                            <label for="ppat_transaction_id" class="form-label text-sm">Dokumen PPAT</label>
                            <select id="ppat_transaction_id" name="ppat_transaction_id"
                                class="form-select @error('ppat_transaction_id') is-invalid @enderror">
                                <option value="" hidden>Pilih Dokumen PPAT</option>
                                @foreach ($ppatDocuments as $relaas)
                                    <option value="{{ $relaas->id }}"
                                        {{ isset($picDocument) && $picDocument->transaction_type === 'relaas' ? 'selected' : '' }}>
                                        {{ $relaas->client->fullname }} - {{ $relaas->pic_document_code }} -
                                        {{ $relaas->transaction_type }}
                                        {{ $relaas->title  }}
                                    </option>
                                @endforeach
                            </select>
                        </div> --}} 


                    <div class="mb-3">
                            <label class="form-label text-sm">Dokumen <span class="text-danger">*</span></label>
                            <select name="pic_document_id"
                                class="form-select @error('pic_document_id') is-invalid @enderror">
                                <option value="" hidden>Pilih Dokumen</option>
                                @foreach ($picDocuments as $doc)
                                    <option value="{{ $doc->id }}" class="text-capitalize">
                                       {{ $doc->client->fullname }} - {{ $doc->pic_document_code }} -
                                        {{ $doc->transaction_type }}</option>
                                @endforeach
                            </select>
                            @error('pic_document_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>


                        
                        
                        

                        {{-- Nama Penerima --}}
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
                            <input type="file" name="file_path" class="form-control @error('file_path') is-invalid @enderror" accept=".jpg,.jpeg,.png,.pdf">
                            @error('file_path')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small>Maksimal ukuran file <strong>10MB</strong> (Format: JPG,JPEG, PNG, atau PDF)</small>
                        </div>


                        <div class="mb-3">
                            <label class="form-label text-sm">Catatan</label>
                            <textarea name="note" class="form-control">{{ old('note') }}</textarea>
                        </div>

                        <a href="{{ route('pic_handovers.index') }}" class="btn btn-secondary">Kembali</a>
                        <button type="submit" class="btn btn-primary">Simpan</button>
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

            const aktaSection = document.getElementById('akta_section');
            const relaasSection = document.getElementById('relaas_section');

            function toggleSections() {
                const value = typeSelect.value;
                aktaSection.style.display = value === 'akta' ? 'block' : 'none';
                relaasSection.style.display = value === 'ppat' ? 'block' : 'none';
            }

            toggleSections();
            typeSelect.addEventListener('change', toggleSections);


        });
    </script>
@endpush
