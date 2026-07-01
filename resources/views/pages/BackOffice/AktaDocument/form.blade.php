@extends('layouts.app')

@php
    // Deteksi Mode SK Kemenkum
    $isSkMode = (request('type') === 'sk_kemenkum' || (isset($document) && $document->type === 'sk_kemenkum'));
    
    // Deteksi Apakah Sedang Mode Edit
    $isEditMode = isset($document);
    
    // Tentukan Judul Halaman
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
                <div class="card shadow-sm">
                    <div class="card-header bg-primary py-3">
                        <h6 class="text-white mb-0">
                            {{ $isEditMode ? ($isSkMode ? 'Form Ubah SK Kemenkum' : 'Form Ubah Dokumen Akta') : ($isSkMode ? 'Form Pencatatan SK Kemenkum' : 'Form Tambah Dokumen Akta') }}
                        </h6>
                    </div>
                    <div class="card-body">
                        {{-- Info Singkat Transaksi --}}
                        <div class="alert alert-secondary text-sm py-2 mb-4 text-dark" style="background-color: #f1f3f5; border: none;">
                            <div class="row">
                                <div class="col-sm-6"><strong>Nomor Akta:</strong> {{ $transaction->akta_number ?? '-' }}</div>
                                <div class="col-sm-6"><strong>Klien:</strong> {{ $transaction->client->fullname ?? '-' }}</div>
                                <div class="col-sm-6"><strong>Jenis Akta:</strong> {{ $transaction->akta_type->name ?? '-' }}</div>
                                <div class="col-sm-6"><strong>Kategori:</strong> <span class="badge bg-info text-capitalize">{{ $transaction->akta_type->category ?? '-' }}</span></div>
                            </div>
                        </div>

                        {{-- Form Dinamis: Menyesuaikan Action URL berdasarkan mode Create/Edit --}}
                        <form action="{{ $isEditMode ? route('akta-documents.update', $document->id) : route('akta-documents.storeData', $transaction->id) }}" 
                              method="POST" 
                              enctype="multipart/form-data">
                            @csrf
                            
                            {{-- Directive Method PUT wajib ditambahkan jika melakukan Update --}}
                            @if($isEditMode)
                                @method('PUT')
                            @endif

                            @if ($isSkMode)
                                {{-- JIKA INPUT SK KEMENKUM --}}
                                <input type="hidden" name="type" value="sk_kemenkum">
                                
                                <div class="form-group mb-3">
                                    <label for="name" class="form-control-label text-sm font-weight-bold">Nomor / Nama SK Kemenkum <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" 
                                        placeholder="Contoh: SK-KEMENKUM-AHU-001.AH.01.02.2026" 
                                        value="{{ old('name', $document->name ?? '') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            @else
                                {{-- JIKA INPUT DOKUMEN BIASA --}}
                                <div class="form-group mb-3">
                                    <label for="name" class="form-control-label text-sm font-weight-bold">Nama Dokumen <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" 
                                        placeholder="Masukkan nama dokumen..." 
                                        value="{{ old('name', $document->name ?? '') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label for="type" class="form-control-label text-sm font-weight-bold">Tipe Dokumen <span class="text-danger">*</span></label>
                                    <select class="form-control @error('type') is-invalid @enderror" id="type" name="type" required>
                                        @php $currentType = old('type', $document->type ?? ''); @endphp
                                        <option value="">-- Pilih Tipe --</option>
                                        <option value="Akta" {{ $currentType == 'Akta' ? 'selected' : '' }}>Akta</option>
                                        <option value="Salinan" {{ $currentType == 'Salinan' ? 'selected' : '' }}>Salinan</option>
                                        <option value="Berkas Pendukung" {{ $currentType == 'Berkas Pendukung' ? 'selected' : '' }}>Berkas Pendukung</option>
                                    </select>
                                    @error('type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            @endif

                            {{-- Tanggal Upload --}}
                            <div class="form-group mb-3">
                                <label for="uploaded_at" class="form-control-label text-sm font-weight-bold">Tanggal Upload / Pengesahan <span class="text-danger">*</span></label>
                                <input type="datetime-local" class="form-control @error('uploaded_at') is-invalid @enderror" id="uploaded_at" name="uploaded_at" 
                                    value="{{ old('uploaded_at', isset($document->uploaded_at) ? \Carbon\Carbon::parse($document->uploaded_at)->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i')) }}" required>
                                @error('uploaded_at')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- File Upload --}}
                            <div class="form-group mb-4">
                                <label for="file_url" class="form-control-label text-sm font-weight-bold">
                                    {{ $isSkMode ? 'Upload File SK Kemenkum (PDF/Gambar)' : 'Upload File Dokumen' }} 
                                    @if(!$isEditMode) <span class="text-danger">*</span> @endif
                                </label>
                                
                                {{-- Jika edit, file tidak required --}}
                                <input type="file" class="form-control @error('file_url') is-invalid @enderror" id="file_url" name="file_url" {{ $isEditMode ? '' : 'required' }}>
                                
                                <small class="text-muted d-block mt-1">Format berkas: PDF, JPG, JPEG, PNG. Maksimal ukuran berkas: 5MB.</small>
                                
                                {{-- Tampilkan informasi file lama jika sedang mengedit --}}
                                @if($isEditMode && $document->file_url)
                                    <div class="mt-2 p-2 bg-light border rounded d-flex align-items-center gap-2">
                                        <i class="fa fa-file text-primary"></i>
                                        <span class="text-xs text-secondary text-truncate" style="max-width: 80%;">File Saat Ini: <strong>{{ $document->file_name }}.{{ $document->file_type }}</strong></span>
                                        <small class="text-danger text-xs ms-auto">(Biarkan kosong jika tidak ingin mengubah file)</small>
                                    </div>
                                @endif
                                
                                @error('file_url')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Tombol Aksi --}}
                            <div class="d-flex justify-content-end gap-2 mt-4">
                                <a href="{{ route('akta-documents.index', ['transaction_code' => $transaction->transaction_code, 'akta_number' => $transaction->akta_number]) }}" 
                                   class="btn btn-light mb-0">Batal</a>
                                <button type="submit" class="btn btn-primary mb-0">{{ $isEditMode ? 'Simpan Perubahan' : 'Simpan Data' }}</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection