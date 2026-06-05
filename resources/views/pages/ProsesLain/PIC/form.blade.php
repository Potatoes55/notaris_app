@extends('layouts.app')

@section('title', 'Pic')

@section('content')
    @include('layouts.navbars.auth.topnav', [
        'title' => isset($data) ? 'Edit Pic' : 'Tambah Pic',
    ])

    <div class="row mt-4 mx-4">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0">
                    <h6>{{ isset($data) ? 'Edit' : 'Tambah' }} Pic</h6>
                </div>
                <hr>
                <div class="card-body px-4 pt-0 pb-2">

                    <form
                        action="{{ isset($data) ? route('proses-lain-pic.update', $data->id) : route('proses-lain-pic.store') }}"
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
                                <option value="" disabled selected hidden>Pilih Klien</option>
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

                        {{-- pic --}}
                        <div class="mb-3">
                            <label class="form-label text-sm">PIC <span class="text-danger">*</span></label>
                            <select name="pic_id" class="form-select @error('pic_id') is-invalid @enderror">
                                <option value="" disabled selected hidden>Pilih PIC</option>
                                @foreach ($picDocuments as $client)
                                    <option value="{{ $client->pic_id }}" class="text-capitalize"
                                        {{ old('pic_id', $data->pic_id ?? '') == $client->pic_id ? 'selected' : '' }}>
                                        {{ $client->pic->full_name }} - {{ $client->transaction_type }}
                                    </option>
                                @endforeach
                            </select>
                            @error('client_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>


                        <div class="mt-4">
                            <a href="{{ route('proses-lain-pic.index') }}" class="btn btn-secondary">
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
