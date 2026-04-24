@extends('layouts.app')

@section('title', 'Legalisasi')

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Legalisasi'])

    <div class="row mt-4 mx-4">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0">
                    <h6>{{ isset($data) ? 'Edit Legalisasi' : 'Tambah Legalisasi' }}</h6>
                </div>
                <hr>
                <div class="card-body px-4 pt-0 pb-2">
                    <form method="POST"
                        action="{{ isset($data) ? route('notary-legalisasi.update', $data->id) : route('notary-legalisasi.store') }}"
                        enctype="multipart/form-data">
                        @csrf
                        @if (isset($data))
                            @method('PUT')
                        @endif
                        <div class="mb-3">
                            <label class="form-label text-sm">Klien <span class="text-danger">*</span></label>
                            <select name="client_code"
                                class="form-select @error('client_code') is-invalid @enderror select2">
                                <option value="" hidden>Pilih Klien</option>
                                @foreach ($clients as $client)
                                    <option value="{{ $client->client_code }}"
                                        {{ old('client_code', $data->client_code ?? '') == $client->client_code ? 'selected' : '' }}>
                                        {{ $client->fullname ?? $client->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('client_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-sm">
                                Nomor Legalisasi <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="legalisasi_number"
                                class="form-control @error('legalisasi_number') is-invalid @enderror"
                                value="{{ old('legalisasi_number', $data->legalisasi_number ?? '') }}">
                            @error('legalisasi_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-sm">Nama Pemohon <span class="text-danger">*</span></label>
                            <input type="text" name="applicant_name"
                                class="form-control @error('applicant_name') is-invalid @enderror"
                                value="{{ old('applicant_name', $data->applicant_name ?? '') }}">
                            @error('applicant_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-sm">Nama Petugas <span class="text-danger">*</span></label>
                            <input type="text" name="officer_name"
                                class="form-control @error('officer_name') is-invalid @enderror"
                                value="{{ old('officer_name', $data->officer_name ?? '') }}">
                            @error('officer_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-sm">Jenis Dokumen</label>
                            <input type="text" name="document_type" class="form-control"
                                value="{{ old('document_type', $data->document_type ?? '') }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-sm">Nomor Dokumen</label>
                            <input type="text" name="document_number" class="form-control"
                                value="{{ old('document_number', $data->document_number ?? '') }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-sm">Tanggal Permintaan</label>
                            <input type="date" name="request_date" class="form-control"
                                value="{{ old('request_date', isset($data->request_date) ? \Carbon\Carbon::parse($data->request_date)->format('Y-m-d') : '') }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-sm">Tanggal Rilis</label>
                            <input type="date" name="release_date" class="form-control"
                                value="{{ old('release_date', isset($data->release_date) ? \Carbon\Carbon::parse($data->release_date)->format('Y-m-d') : '') }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-sm">Catatan</label>
                            <textarea name="notes" class="form-control">{{ old('notes', $data->notes ?? '') }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-sm">File</label>
                            <input type="file" name="file_path" class="form-control" @error('file_path') is-invalid @enderror" accept=".pdf,.jpg,.jpeg,.png"
                                {{ old('file_path') ? 'value=' . old('file_path') : '' }}>
                            <small>Maksimal ukuran file <strong>10MB </strong>(Format: JPG,JPEG, PNG, atau PDF)</small>

                            @if (isset($data) && $data->file_path)
                                @php
                                    $ext = pathinfo($data->file_path, PATHINFO_EXTENSION);
                                @endphp

                                @if (in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'gif', 'webp']))
                                    <div class="mt-2">
                                        <button type="button" class="btn btn-primary mb-0 text-white">

                                            <a href="{{ asset('storage/' . $data->file_path) }}" alt="Preview"
                                                target="_blank" class="img-fluid" style="max-height: 200px; color: white">
                                                Lihat Gambar
                                            </a>

                                        </button>
                                    </div>
                                @else
                                    <br>
                                    <button class="btn btn-primary mb-0 text-white">
                                        <a href="{{ asset('storage/' . $data->file_path) }}" target="_blank"
                                            style="color: white">Lihat File Legalisasi</a>
                                    </button>
                                @endif
                            @endif
                        </div>

                        <div class="">
                            <a href="{{ route('notary-legalisasi.index') }}" class="btn btn-secondary">Batal</a>
                            <button type="submit" class="btn btn-primary">{{ isset($data) ? 'Ubah' : 'Simpan' }}</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
