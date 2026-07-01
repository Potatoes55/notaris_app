@extends('layouts.app')

@section('title', request('type') === 'sk_kemenkumham' ? 'Input SK Kemenkumham' : 'Tambah Dokumen Akta')

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Dokumen Akta / Form Input'])

    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-md-8 mx-auto">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary py-3">
                        <h6 class="text-white mb-0">
                            {{ request('type') === 'sk_kemenkumham' ? 'Form Pencatatan SK Kemenkumham' : 'Form Tambah Dokumen Akta' }}
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

                        {{-- Form Mulai --}}
                        <form action="{{ route('akta-documents.storeData', $transaction->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            @if (request('type') === 'sk_kemenkumham')
                                {{-- JIKA INPUT SK KEMENKUMHAM --}}
                                <input type="hidden" name="type" value="sk_kemenkumham">
                                
                                <div class="form-group mb-3">
                                    <label for="name" class="form-control-label text-sm font-weight-bold">Nomor / Nama SK Kemenkumham <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" 
                                        placeholder="Contoh: SK-KEMENKUMHAM-AHU-001.AH.01.02.2026" value="{{ old('name') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            @else
                                {{-- JIKA INPUT DOKUMEN BIASA --}}
                                <div class="form-group mb-3">
                                    <label for="name" class="form-control-label text-sm font-weight-bold">Nama Dokumen <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" 
                                        placeholder="Masukkan nama dokumen..." value="{{ old('name') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label for="type" class="form-control-label text-sm font-weight-bold">Tipe Dokumen <span class="text-danger">*</span></label>
                                    <select class="form-control @error('type') is-invalid @enderror" id="type" name="type" required>
                                        <option value="">-- Pilih Tipe --</option>
                                        <option value="Akta" {{ old('type') == 'Akta' ? 'selected' : '' }}>Akta</option>
                                        <option value="Salinan" {{ old('type') == 'Salinan' ? 'selected' : '' }}>Salinan</option>
                                        <option value="Berkas Pendukung" {{ old('type') == 'Berkas Pendukung' ? 'selected' : '' }}>Berkas Pendukung</option>
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
                                    value="{{ old('uploaded_at', now()->format('Y-m-d\TH:i')) }}" required>
                                @error('uploaded_at')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- File Upload --}}
                            <div class="form-group mb-4">
                                <label for="file_url" class="form-control-label text-sm font-weight-bold">
                                    {{ request('type') === 'sk_kemenkumham' ? 'Upload File SK Kemenkumham (PDF/Gambar)' : 'Upload File Dokumen' }} <span class="text-danger">*</span>
                                </label>
                                <input type="file" class="form-control @error('file_url') is-invalid @enderror" id="file_url" name="file_url" required>
                                <small class="text-muted d-block mt-1">Format berkas: PDF, JPG, JPEG, PNG. Maksimal ukuran berkas: 5MB.</small>
                                @error('file_url')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Tombol Aksi --}}
                            <div class="d-flex justify-content-end gap-2 mt-4">
                                <a href="{{ route('akta-documents.index', ['transaction_code' => $transaction->transaction_code, 'akta_number' => $transaction->akta_number]) }}" 
                                   class="btn btn-light mb-0">Batal</a>
                                <button type="submit" class="btn btn-primary mb-0">Simpan Data</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection