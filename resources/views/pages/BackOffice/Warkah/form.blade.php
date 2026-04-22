@extends('layouts.app')

@section('title', isset($document) ? 'Edit Warkah' : 'Tambah Warkah')

@section('content')
    @include('layouts.navbars.auth.topnav', [
        'title' => isset($document) ? 'Edit Warkah' : 'Tambah Warkah',
    ])

    <div class="row mt-4 mx-4">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0">
                    <h6>Tambah Warkah</h6>
                </div>
                <hr>
                <div class="card-body px-4 pt-0 pb-2">
                    <form action="{{ route('warkah.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        {{-- @if (isset($document))
                            @method('PUT')
                        @endif --}}

                        <input type="hidden" name="notaris_id" value="{{ auth()->user()->notaris_id }}">

                        <div class="row">
                            <input type="hidden" name="client_code" value="{{ $client->client_code }}">

                            <div class="col-md-12 mb-3">
                                <label class="form-label text-sm">Jenis Warkah <span class="text-danger">*</span></label>
                                <select name="warkah_code" class="form-select @error('warkah_code') is-invalid @enderror">
                                    <option value="" hidden>Pilih Dokumen</option>
                                    @foreach ($documents as $doc)
                                        <option value="{{ $doc->code }}"
                                            {{ old('warkah_code', $document->warkah_code ?? '') == $doc->code ? 'selected' : '' }}>
                                            {{ $doc->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('warkah_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>


                            <div class="col-md-12 mb-3">
                                <label class="form-label text-sm">Tanggal Upload</label>
                                <input type="date" name="uploaded_at"
                                    class="form-control @error('uploaded_at') is-invalid @enderror"
                                    value="{{ old('uploaded_at', isset($document) ? $document->uploaded_at->format('Y-m-d') : date('Y-m-d')) }}">
                                @error('uploaded_at')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                                @csrf
                            <div class="mb-3 col-md-12">
                                <label class="form-label text-sm">Dokumen</label>
                                <input type="file" name="warkah_link"
                                    class="form-control @error('warkah_link') is-invalid @enderror"
                                    accept=".jpg,.jpeg,.png,.pdf">
                                <small>Maksimal ukuran file 15MB  (format: JPG,JPEG, PNG atau PDF)</small>
                                @error('warkah_link')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                @if (isset($document) && $document->warkah_link)
                                    <small class="text-muted">File saat ini: <a
                                            href="{{ Storage::url($document->warkah_link) }}"
                                            target="_blank">Lihat</a></small>
                                @endif
                            </div>


                            <div class="col-md-12 mb-3">
                                <label class="form-label text-sm">Catatan</label>
                                <textarea name="note" class="form-control @error('note') is-invalid @enderror">{{ old('note', $document->note ?? '') }}</textarea>
                                @error('note')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-4">
                            <a href="{{ route('warkah.index', $client->id) }}" class="btn btn-secondary">Kembali</a>
                            <button type="submit"
                                class="btn btn-primary">{{ isset($document) ? 'Ubah' : 'Simpan' }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
