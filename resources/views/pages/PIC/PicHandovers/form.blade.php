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
                        <div class="mb-3">
                            <label class="form-label text-sm">Dokumen <span class="text-danger">*</span></label>
                            <select name="pic_document_id"
                                class="form-select @error('pic_document_id') is-invalid @enderror">
                                <option value="" hidden>Pilih Dokumen</option>
                               @foreach($picDocuments->groupBy('transaction_type') as $type => $docs)
                                    <optgroup label="{{ $type }}">
                                        @foreach($docs as $doc)
                                           <option value="{{ $doc->id }}">
                                                {{ $doc->client->fullname ?? '-' }} |
                                                {{ optional($doc->aktaTransaction)->transaction_code ?? optional($doc->relaasTransaction)->transaction_code ?? '-' }} |
                                                {{ optional($doc->relaasTransaction->akta_type)->type ?? '-' }}
                                                {{-- {{ $doc->title }} --}}

                                            </option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                            @error('pic_document_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

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
