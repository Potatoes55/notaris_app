@extends('layouts.app')

@section('title', 'Transaksi')

@section('content')
    @include('layouts.navbars.auth.topnav', [
        'title' => isset($data) ? 'Edit Transaksi' : 'Tambah Transaksi',
    ])

    <div class="row mt-4 mx-4">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0">
                    <h6>{{ isset($data) ? 'Edit' : 'Tambah' }} Transaksi</h6>
                </div>
                <hr>
                <div class="card-body px-4 pt-0 pb-2">

                    <form
                        action="{{ isset($data) ? route('proses-lain-transaksi.update', $data->id) : route('proses-lain-transaksi.store') }}"
                        method="POST">

                        @csrf
                        @if (isset($data))
                            @method('PUT')
                        @endif

                        {{-- CLIENT CODE --}}
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

                        {{-- NAMA --}}
                        <div class="mb-3">
                            <label for="name" class="form-label text-sm">
                                Nama Transaksi <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="name" id="name" class="form-control"
                                value="{{ old('name', $data->name ?? '') }}">

                            @error('name')
                                <p class="text-danger mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- ESTIMASI --}}
                        <div class="mb-3">
                            <label for="time_estimation" class="form-label text-sm">
                                Estimasi Waktu (Hari) <span class="text-danger">*</span>
                            </label>
                            <input type="number" name="time_estimation" id="time_estimation" class="form-control"
                                value="{{ old('time_estimation', $data->time_estimation ?? '') }}">

                            @error('time_estimation')
                                <p class="text-danger mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Status --}}
                        {{-- <div class="mb-3">
                            <label for="status" class="form-label text-sm">Status <span class="text-danger">*</span>
                            </label>
                            <select name="status" id="status" class="form-select">
                                <option value="" hidden>Pilih Status</option>
                                <option value="1"
                                    {{ old('status', $data->status ?? '') == 'Baru' ? 'selected' : '' }}>
                                    Baru</option>
                                <option value="0"
                                    {{ old('status', $data->status ?? '') == '0' ? 'selected' : '' }}>
                                    Proses</option>
                            </select>
                            @error('status')
                                <p class="text-danger mt-2">{{ $message }}</p>
                            @enderror --}}
                        {{--
                            </div> --}}

                        <div class="mt-4">
                            <a href="{{ route('proses-lain-transaksi.index') }}" class="btn btn-secondary">
                                Kembali
                            </a>

                            <button type="submit" class="btn btn-primary">
                                {{ isset($data) ? 'Ubah' : 'Simpan' }}
                            </button>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    </div>
@endsection
