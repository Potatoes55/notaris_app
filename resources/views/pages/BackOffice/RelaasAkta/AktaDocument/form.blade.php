@extends('layouts.app')

@section('title', 'Dokumen Akta')

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Dokumen Akta'])

    <div class="row mt-4 mx-4">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0">
                    <h6>{{ $doc ? 'Edit Dokumen Akta' : 'Tambah Dokumen Akta' }}</h6>
                </div>
                <hr>
                <div class="card-body pt-0">
                    <form
                        action="{{ $doc ? route('relaas-documents.update', [$relaas->id, $doc->id]) : route('relaas-documents.store', $relaas->id) }}"
                        method="POST" enctype="multipart/form-data">
                        @csrf
                        @if ($doc)
                            @method('PUT')
                        @endif
                        <div class="mb-3">
                            <label class="form-label text-sm">Nama Dokumen <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name', $doc->name ?? '') }}">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-sm">Tipe Dokumen <span class="text-danger">*</span></label>
                            <input type="text" name="type" class="form-control @error('type') is-invalid @enderror"
                                value="{{ old('type', $doc->type ?? '') }}"
                                placeholder="Contoh: Draft, Final, Dokumen Pendukung">
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="file_url" class="form-label text-sm">File Akta Dokumen <span
                                    class="text-danger">*</span></label>
                            <input type="file" name="file_url" id="file_url"
                                class="form-control @error('file_url') is-invalid @enderror">
                            <small>Maksimal ukuran file<strong> 10MB</strong> (Format: JPG,JPEG, PNG,)</small>

                            @error('file_url')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                            @if ($doc && $doc->file_url)
                                <br>
                                <a href="{{ asset('storage/' . $doc->file_url) }}" target="_blank"
                                    class="btn btn-outline-primary btn-sm mt-2 mb-0">
                                    Lihat File Akta Dokumen
                                </a>
                            @endif
                        </div>

                        <div class="mb-3">
                            <label for="uploaded_at" class="form-label text-sm">Tanggal Upload <span
                                    class="text-danger">*</span></label>
                            <input type="datetime-local" name="uploaded_at" id="uploaded_at"
                                class="form-control @error('uploaded_at') is-invalid @enderror"
                                value="{{ old('uploaded_at', isset($doc) && $doc->uploaded_at ? \Carbon\Carbon::parse($doc->uploaded_at)->format('Y-m-d\TH:i') : '') }}">

                            @error('uploaded_at')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>


                        <a href="{{ route('relaas-documents.index', ['search' => $relaas->client_code]) }}"
                            class="btn btn-secondary  ">Batal</a>
                        <button type="submit" class="btn btn-primary ">
                            {{ $doc ? 'Ubah' : 'Simpan' }}
                        </button>

                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
