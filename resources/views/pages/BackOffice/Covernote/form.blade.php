@extends('layouts.app')

@section('title', 'Covernote')

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Covernote'])

    <div class="row mt-4 mx-4">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0">
                    <h6>{{ isset($data) ? 'Edit Covernote' : 'Tambah Covernote' }}</h6>
                </div>
                <hr>
                <div class="card-body px-4 pt-0 pb-2">
                    <form method="POST"
                        action="{{ isset($data) ? route('covernotes.update', $data->id) : route('covernotes.store') }}"
                        enctype="multipart/form-data">
                        @csrf
                        @if (isset($data))
                            @method('PUT')
                        @endif

                        {{-- KLIEN --}}
                        <div class="mb-3">
                            <label class="form-label text-sm">Klien</label>
                            <select name="client_id"
                                class="form-select select2 @error('client_id') is-invalid @enderror">
                                <option value="" hidden>Pilih Klien</option>
                                @foreach ($clients as $client)
                                    <option value="{{ $client->id }}"
                                        {{ old('client_id', $data->client_id ?? '') == $client->id ? 'selected' : '' }}>
                                        {{ $client->fullname ?? $client->name }} - {{ $client->client_code }}
                                    </option>
                                @endforeach
                            </select>
                            @error('client_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- NOMOR SURAT --}}
                        <div class="mb-3">
                            <label class="form-label text-sm">No Surat</label>
                            <input type="text" name="covernote_number"
                                class="form-control @error('covernote_number') is-invalid @enderror"
                                value="{{ old('covernote_number', $data->covernote_number ?? '') }}">
                            @error('covernote_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- PENERIMA --}}
                        <div class="mb-3">
                            <label class="form-label text-sm">Penerima</label>
                            <input type="text" name="recipient"
                                class="form-control @error('recipient') is-invalid @enderror"
                                value="{{ old('recipient', $data->recipient ?? '') }}">
                            @error('recipient')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- SUBJEK --}}
                        <div class="mb-3">
                            <label class="form-label text-sm">Subjek</label>
                            <input type="text" name="subject" class="form-control @error('subject') is-invalid @enderror"
                                value="{{ old('subject', $data->subject ?? '') }}">
                            @error('subject')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- TANGGAL SURAT --}}
                        <div class="mb-3">
                            <label class="form-label text-sm">Tanggal Surat</label>
                            <input type="date" name="date" class="form-control @error('date') is-invalid @enderror"
                                value="{{ old('date', isset($data->date) ? \Carbon\Carbon::parse($data->date)->format('Y-m-d') : '') }}">
                            @error('date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- MASA BERLAKU SURAT --}}
                        <div class="mb-3">
                            <label class="form-label text-sm">Masa Berlaku Surat</label>
                            <input type="date" name="expiry_date" class="form-control @error('expiry_date') is-invalid @enderror"
                                value="{{ old('expiry_date', isset($data->expiry_date) ? \Carbon\Carbon::parse($data->expiry_date)->format('Y-m-d') : '') }}">
                            @error('expiry_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- ISI SURAT --}}
                        <div class="mb-3">
                            <label class="form-label text-sm">Isi Surat / Lampiran</label>
                            <textarea name="attachment" 
                                    class="form-control @error('attachment') is-invalid @enderror" 
                                    rows="4" 
                                    placeholder="Masukkan rincian isi surat atau daftar lampiran di sini...">{{ old('attachment', $data->attachment ?? '') }}</textarea>
                            @error('attachment')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- FILE SURAT --}}
                        <div class="mb-3">
                            <label class="form-label text-sm">File Surat</label>
                            <input type="file" name="file" class="form-control @error('file') is-invalid @enderror" id="fileInput">
                            <small>Maksimal ukuran file <strong>10 MB</strong> (Format: JPG, JPEG, PNG, atau PDF)</small>
                            @error('file')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <br>
                            @if (isset($data) && $data->file_path)
                                @php
                                    $ext = pathinfo($data->file_path, PATHINFO_EXTENSION);
                                @endphp

                                @if (in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'gif', 'webp']))
                                    <div class="mt-2">
                                        <button type="button" class="btn btn-primary mb-0 text-white">
                                            <a href="{{ asset('storage/' . $data->file_path) }}" 
                                                target="_blank" style="color: white">
                                                Lihat Gambar
                                            </a>
                                        </button>
                                    </div>
                                @else
                                    <br>
                                    <button type="button" class="btn btn-primary mb-0 text-white">
                                        <a href="{{ asset('storage/' . $data->file_path) }}" target="_blank"
                                            style="color: white">Lihat File</a>
                                    </button>
                                @endif
                            @endif
                        </div>

                        <div class="mt-4">
                            <a href="{{ route('covernotes.index') }}" class="btn btn-secondary">Batal</a>
                            <button type="submit" class="btn btn-primary">{{ isset($data) ? 'Ubah' : 'Simpan' }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection