@extends('layouts.app')

@section('title', 'Pihak Akta')

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Pihak Akta'])

    <div class="row mt-4 mx-4">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0">
                    <h6>{{ isset($party) ? 'Edit Pihak Akta' : 'Tambah Pihak Akta' }}</h6>
                </div>
                <hr>
                <div class="card-body px-4 pt-3 pb-2">
                    <form
                        action="{{ isset($party)
                            ? route('relaas-parties.update', [$relaas->id, $party->id])
                            : route('relaas-parties.store', $relaas->id) }}"
                        method="POST">
                        @csrf
                        @if (isset($party))
                            @method('PUT')
                        @endif

                        {{-- Klien --}}
                        {{-- <div class="mb-3">
                            <label for="client_code" class="form-label text-sm">Klien</label>
                            <select name="client_code" id="client_code" class="form-select select2">
                                <option value="" hidden>Pilih Klien</option>
                                @foreach ($clients as $client)
                                    <option value="{{ $client->client_code }}"
                                        {{ isset($party) && $party->client_code == $client->client_code ? 'selected' : '' }}>
                                        {{ $client->fullname }}
                                    </option>
                                @endforeach
                            </select>
                        </div> --}}

                        {{-- Nama --}}
                        <div class="mb-3">
                            <label for="name" class="form-label text-sm">Nama Pihak</label>
                            <input type="text" name="name" id="name"
                                class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name', $party->name ?? '') }}">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Role --}}
                        <div class="mb-3">
                            <label for="role" class="form-label text-sm">Peran</label>
                            <input type="text" name="role" id="role"
                                class="form-control @error('role') is-invalid @enderror"
                                value="{{ old('role', $party->role ?? '') }}" placeholder="Contoh: Pendiri, Penerima Kuasa">
                            @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Alamat --}}
                        <div class="mb-3">
                            <label for="address" class="form-label text-sm">Alamat</label>
                            <textarea name="address" id="address" class="form-control @error('address') is-invalid @enderror" rows="2">{{ old('address', $party->address ?? '') }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="id_type" class="form-label text-sm">Jenis Identitas</label>
                            <input type="text" name="id_type" id="id_type"
                                class="form-control @error('id_type') is-invalid @enderror"
                                value="{{ old('id_type', $party->id_type ?? '') }}" placeholder="Contoh: KTP, SIM, dll">
                            @error('id_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Nomor Identitas --}}
                        <div class="mb-3">
                            <label for="id_number" class="form-label text-sm">No. Identitas</label>
                            <input type="text" name="id_number" id="id_number"
                                class="form-control @error('id_number') is-invalid @enderror"
                                value="{{ old('id_number', $party->id_number ?? '') }}" placeholder="Contoh: Nomor KTP">
                            @error('id_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Jenis Identitas --}}


                        {{-- Catatan --}}
                        {{-- <div class="mb-3">
                        <label for="note" class="form-label">Catatan</label>
                        <textarea name="note" id="note" class="form-control @error('note') is-invalid @enderror"
                            rows="2">{{ old('note', $party->note ?? '') }}</textarea>
                        @error('note')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div> --}}


                        <a href="{{ route('relaas-parties.index', ['search' => $relaas->transaction_code]) }}"
                            class="btn btn-secondary">
                            Kembali
                        </a>
                        <button type="submit" class="btn btn-primary">{{ isset($party) ? 'Ubah' : 'Simpan' }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
