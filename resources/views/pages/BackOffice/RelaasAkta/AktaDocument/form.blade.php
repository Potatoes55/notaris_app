@extends('layouts.app')

@php
    $isEditMode = isset($doc);
    $pageTitle = $isEditMode ? 'Edit Dokumen Akta' : 'Tambah Dokumen Akta';
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
                                <div class="col-md-6 mb-1"><strong>Relaas ID</strong><br>{{ $relaas->id ?? '-' }}</div>
                                <div class="col-md-6 mb-1"><strong>Kode Klien</strong><br>{{ $relaas->client_code ?? '-' }}</div>
                            </div>
                        </div>
                    </div>

                    <form action="{{ $isEditMode ? route('relaas-documents.update', [$relaas->id, $doc->id]) : route('relaas-documents.store', $relaas->id) }}"
                          method="POST" enctype="multipart/form-data">
                        @csrf
                        @if($isEditMode)
                            @method('PUT')
                        @endif

                        <div class="mb-3">
                            <label class="form-control-label">Nama Dokumen</label>
                            <div class="input-group input-group-outline {{ old('name', $doc->name ?? '') ? 'is-filled' : '' }}">
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                       value="{{ old('name', $doc->name ?? '') }}" required>
                            </div>
                            @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        {{-- DROPDOWN TIPE DOKUMEN --}}
                        <div class="mb-3">
                            <label class="form-control-label">Tipe Dokumen</label>
                            <div class="input-group input-group-outline">
                                <select name="type" class="form-control @error('type') is-invalid @enderror" required>
                                    @php
                                        $currentType = old('type', $doc->type ?? '');
                                    @endphp

                                    <option value="">Pilih</option>
                                    <option value="Minuta Akta" {{ $currentType == 'Minuta Akta' ? 'selected' : '' }}>
                                        Minuta Akta
                                    </option>
                                    <option value="Salinan Akta" {{ $currentType == 'Salinan Akta' ? 'selected' : '' }}>
                                        Salinan Akta
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

                        <div class="mb-3">
                            <label class="form-control-label">Tanggal Upload</label>
                            <div class="input-group input-group-outline is-filled">
                                <input type="datetime-local" name="uploaded_at"
                                       class="form-control @error('uploaded_at') is-invalid @enderror"
                                       value="{{ old('uploaded_at', isset($doc) && $doc->uploaded_at ? \Carbon\Carbon::parse($doc->uploaded_at)->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i')) }}"
                                       required>
                            </div>
                            @error('uploaded_at') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-control-label">Upload Dokumen</label>
                            <div class="input-group input-group-outline">
                                <input type="file" name="file_url" class="form-control @error('file_url') is-invalid @enderror" {{ $isEditMode ? '' : 'required' }}>
                            </div>
                            <small class="text-xs text-secondary">Maksimal ukuran file 10MB (Format: JPG, JPEG, PNG)</small>

                            @if($isEditMode && $doc->file_url)
                                <div class="mt-2">
                                    <a href="{{ asset('storage/' . $doc->file_url) }}" target="_blank" class="btn btn-outline-primary btn-sm mb-0">
                                        Lihat File Saat Ini
                                    </a>
                                </div>
                            @endif

                            @error('file_url') <small class="text-danger d-block">{{ $message }}</small> @enderror
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('relaas-documents.index', ['search' => $relaas->transaction_code]) }}" class="btn btn-light mb-0">
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