@extends('layouts.app')

@php
    $isSkMode = (request('type') === 'sk_kemenkum' || (isset($document) && $document->type === 'sk_kemenkum'));
    $isEditMode = isset($document);

    $pageTitle = $isEditMode
        ? ($isSkMode ? 'Edit SK Kemenkum' : 'Edit Dokumen Akta')
        : ($isSkMode ? 'Input SK Kemenkum' : 'Tambah Dokumen Akta');
@endphp

@section('title', $pageTitle)

@section('content')

@include('layouts.navbars.auth.topnav', ['title' => 'Dokumen Akta / Form'])

<div class="container-fluid py-4">

    <div class="row">
        <div class="col-md-8 mx-auto">

            <div class="card shadow-lg border-0">

                <div class="card-body p-4">

                    <div class="text-center mb-4">
                        <div class="icon icon-shape bg-gradient-primary shadow border-radius-xl mx-auto mb-3">
                            <i class="fas fa-file-contract text-white"></i>
                        </div>

                        <h4 class="fw-bold mb-1">{{ $pageTitle }}</h4>
                        <p class="text-sm text-muted mb-0">Lengkapi data dokumen</p>
                    </div>

                    <div class="card bg-gray-100 border-0 mb-4">
                        <div class="card-body py-3">
                            <div class="row text-sm">
                                <div class="col-md-6 mb-1"><strong>Nomor Akta</strong><br>{{ $transaction->akta_number ?? '-' }}</div>
                                <div class="col-md-6 mb-1"><strong>Klien</strong><br>{{ $transaction->client->fullname ?? '-' }}</div>
                                <div class="col-md-6 mb-1">
                                    <strong>Jenis Akta</strong><br>
                                    {{ $transaction->akta_type->type ?? $transaction->akta_type->name ?? '-' }}
                                </div>
                                <div class="col-md-6 mb-1"><strong>Kategori</strong><br><span class="badge bg-gradient-info">{{ $transaction->akta_type->category ?? '-' }}</span></div>
                            </div>
                        </div>
                    </div>

                    <form action="{{ $isEditMode ? route('akta-documents.update', $document->id) : route('akta-documents.storeData', $transaction->id) }}"
                          method="POST" enctype="multipart/form-data">

                        @csrf
                        @if($isEditMode)
                            @method('PUT')
                        @endif

                        @if($isSkMode)

                            <input type="hidden" name="type" value="sk_kemenkum">

                            <div class="mb-3">
                                <label class="form-control-label">Nomor / SK Kemenkum</label>
                                <div class="input-group input-group-outline">
                                    <input type="text"
                                        name="name"
                                        class="form-control @error('name') is-invalid @enderror"
                                        value="{{ old('name', $document->name ?? '') }}"
                                        required>
                                </div>
                                @error('name')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                        @else

                            <div class="mb-3">
                                <label class="form-control-label">Nama Dokumen</label>
                                <div class="input-group input-group-outline">
                                    <input type="text"
                                        name="name"
                                        class="form-control @error('name') is-invalid @enderror"
                                        value="{{ old('name', $document->name ?? '') }}"
                                        required>
                                </div>
                                @error('name')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-control-label">Tipe</label>
                                <div class="input-group input-group-outline">
                                    <select name="type" class="form-control @error('type') is-invalid @enderror" required>
                                        @php
                                            $currentType = old('type', $document->type ?? '');
                                        @endphp

                                        <option value="">Pilih</option>
                                        <option value="Akta" {{ $currentType == 'Akta' ? 'selected' : '' }}>
                                            Akta
                                        </option>
                                        <option value="Salinan" {{ $currentType == 'Salinan' ? 'selected' : '' }}>
                                            Salinan
                                        </option>
                                        <option value="Berkas Pendukung" {{ $currentType == 'Berkas Pendukung' ? 'selected' : '' }}>
                                            Berkas Pendukung
                                        </option>
                                    </select>
                                </div>

                                @error('type')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                        @endif
                        <div class="mb-3">
                            <label class="form-control-label">Tanggal Upload</label>
                            <div class="input-group input-group-outline">
                                <input type="datetime-local" name="uploaded_at"
                                       class="form-control @error('uploaded_at') is-invalid @enderror"
                                       value="{{ old('uploaded_at', isset($document->uploaded_at) ? \Carbon\Carbon::parse($document->uploaded_at)->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i')) }}"
                                       required>
                            </div>
                            @error('uploaded_at') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-control-label">{{ $isSkMode ? 'Upload SK Kemenkum' : 'Upload Dokumen' }}</label>

                            <div class="input-group input-group-outline">
                                <input type="file" name="file_url"
                                       class="form-control @error('file_url') is-invalid @enderror"
                                       {{ $isEditMode ? '' : 'required' }}>
                            </div>

                            <small class="text-xs text-secondary">
                                PDF, JPG, JPEG, PNG maksimal 5MB
                            </small>

                            @if($isEditMode && $document->file_url)
                                <div class="text-xs text-secondary mt-1">
                                    File: {{ $document->file_name }}.{{ $document->file_type }}
                                </div>
                            @endif

                            @error('file_url') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('akta-documents.index', [
                                'search' => $transaction->transaction_code
                            ]) }}"
                            class="btn btn-light mb-0">
                                Batal
                            </a>

                            <button type="submit" class="btn bg-gradient-primary mb-0">
                                Simpan
                            </button>
                        </div>
                    </form>

                </div>
            </div>

        </div>
    </div>

</div>

@endsection