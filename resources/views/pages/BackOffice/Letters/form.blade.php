@extends('layouts.app')

@php
    // Menentukan teks & route secara dinamis berdasarkan $letterType
    $isIncoming = ($letterType ?? 'surat_keluar') === 'surat_masuk';
    $titleText = $isIncoming ? 'Surat Masuk' : 'Surat Keluar';

    // Route Action Form
    if (isset($data)) {
        $actionRoute = $isIncoming 
            ? route('notary-letters.incoming.update', $data->id) 
            : route('notary-letters.update', $data->id);
    } else {
        $actionRoute = $isIncoming 
            ? route('notary-letters.incoming.store') 
            : route('notary-letters.store');
    }

    // Route Batal
    $cancelRoute = $isIncoming 
        ? route('notary-letters.incoming.index') 
        : route('notary-letters.index');
@endphp

@section('title', $titleText)

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => $titleText])

    <div class="row mt-4 mx-4">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0">
                    <h6>{{ (isset($data) ? 'Edit ' : 'Tambah ') . $titleText }}</h6>
                </div>
                <hr>
                <div class="card-body px-4 pt-0 pb-2">
                    <form method="POST" action="{{ $actionRoute }}" enctype="multipart/form-data">
                        @csrf
                        @if (isset($data))
                            @method('PUT')
                        @endif

                        {{-- Hidden input jika ingin memastikan letter_type terkirim --}}
                        <input type="hidden" name="letter_type" value="{{ $letterType ?? 'surat_keluar' }}">

                        <div class="mb-3">
                            <label class="form-label text-sm">Klien</label>
                            <select name="client_code" class="form-select select2 @error('client_code') is-invalid @enderror">
                                <option value="" hidden>Pilih Klien</option>
                                @foreach ($clients as $client)
                                    <option value="{{ $client->client_code }}"
                                        {{ old('client_code', $data->client_code ?? '') == $client->client_code ? 'selected' : '' }}>
                                        {{ $client->fullname ?? $client->name }} - {{ $client->client_code }}
                                    </option>
                                @endforeach
                            </select>
                            @error('client_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-sm">Nomor Surat</label>
                            <input type="text" name="letter_number"
                                class="form-control @error('letter_number') is-invalid @enderror"
                                value="{{ old('letter_number', $data->letter_number ?? '') }}">
                            @error('letter_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-sm">Jenis Surat</label>
                            <input type="text" name="type" class="form-control"
                                value="{{ old('type', $data->type ?? '') }}">
                        </div>

                        {{-- Dynamic Label untuk Pengirim / Penerima --}}
                        <div class="mb-3">
                            <label class="form-label text-sm">{{ $isIncoming ? 'Pengirim Surat' : 'Penerima Surat' }}</label>
                            <input type="text" name="recipient"
                                class="form-control @error('recipient') is-invalid @enderror"
                                value="{{ old('recipient', $data->recipient ?? '') }}"
                                placeholder="{{ $isIncoming ? 'Masukkan nama pengirim' : 'Masukkan nama penerima' }}">
                            @error('recipient')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-sm">Subjek / Perihal</label>
                            <input type="text" name="subject" class="form-control @error('subject') is-invalid @enderror"
                                value="{{ old('subject', $data->subject ?? '') }}">
                            @error('subject')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-sm">{{ $isIncoming ? 'Tanggal Diterima' : 'Tanggal Surat' }}</label>
                            <input type="date" name="date" class="form-control @error('date') is-invalid @enderror"
                                value="{{ old('date', isset($data->date) ? \Carbon\Carbon::parse($data->date)->format('Y-m-d') : '') }}">
                            @error('date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-sm">Ringkasan</label>
                            <textarea name="summary" class="form-control">{{ old('summary', $data->summary ?? '') }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-sm">Lampiran</label>
                            <input type="text" name="attachment" class="form-control"
                                value="{{ old('attachment', $data->attachment ?? '') }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-sm">{{ $isIncoming ? 'Catatan / Disposisi' : 'Catatan' }}</label>
                            <textarea name="notes" class="form-control">{{ old('notes', $data->notes ?? '') }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-sm">File {{ $titleText }}</label>
                            <input type="file" name="file_path" class="form-control" id="fileInput">
                            <small>Maksimal ukuran file <strong>10 MB </strong>(Format: JPG, JPEG, PNG, DOC, DOCX, atau PDF)</small>
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
                            <a href="{{ $cancelRoute }}" class="btn btn-secondary">Batal</a>
                            <button type="submit" class="btn btn-primary">{{ isset($data) ? 'Ubah' : 'Simpan' }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection