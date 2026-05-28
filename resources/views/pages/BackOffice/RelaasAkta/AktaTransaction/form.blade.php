@extends('layouts.app')

@section('title', 'Transaksi Akta')

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => ' Transaksi Akta'])

    <div class="row mt-4 mx-4">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0">
                    <h6>{{ isset($data) ? 'Edit Transaksi Akta' : 'Tambah Transaksi Akta' }}</h6>
                </div>
                <hr>
                <div class="card-body px-4 pt-0 pb-2">
                    <form action="{{ isset($data) ? route('relaas-aktas.update', $data->id) : route('relaas-aktas.store') }}"
                        method="POST">
                        @csrf
                        @if (isset($data))
                            @method('PUT')
                        @endif

                        <input type="hidden" name="client_code" value="{{ $clientCode }}">

                        <div class="mb-3">
                            <label for="relaas_type_id" class="form-label text-sm">Jenis Akta (Kategori - Tipe)</label>
                            <select name="relaas_type_id" id="relaas_type_id"
                                class="form-select text-capitalize @error('relaas_type_id') is-invalid @enderror">
                                <option value="" hidden>Pilih Jenis Akta</option>
                                @foreach ($relaasType as $relaasTypes)
                                    <option value="{{ $relaasTypes->id }}"
                                        {{ old('relaas_type_id', $data->relaas_type_id ?? '') == $relaasTypes->id ? 'selected' : '' }}
                                        class="text-capitalize">
                                        {{ $relaasTypes->category }} - {{ $relaasTypes->type }}
                                    </option>
                                @endforeach
                            </select>
                            @error('relaas_type_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>


                        {{-- title --}}
                        <div class="mb-3">
                            <label for="title" class="form-label text-sm">Judul</label>
                            <input type="text" name="title" id="title"
                                class="form-control @error('title') is-invalid @enderror"
                                value="{{ old('title', $data->title ?? '') }}" placeholder="Contoh: Pembukaan Wasiat">
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- story --}}
                        <div class="mb-3">
                            <label for="story" class="form-label text-sm">Cerita</label>
                            {{-- textarea --}}
                            <textarea placeholder="Detail Peristiwa" name="story" id="story"
                                class="form-control @error('story') is-invalid @enderror" rows="3">{{ old('story', $data->story ?? '') }}</textarea>
                            @error('story')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>


                        {{-- Story Date --}}
                        <div class="mb-3">
                            <label for="story_date" class="form-label text-sm">Tanggal</label>
                            <input type="datetime-local" name="story_date" id="story_date"
                                class="form-control @error('story_date') is-invalid @enderror"
                                value="{{ old('story_date', isset($data->story_date) ? \Carbon\Carbon::parse($data->story_date)->format('Y-m-d\TH:i') : '') }}">
                            @error('story_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Story Location --}}
                        <div class="mb-3">
                            <label for="story_location" class="form-label text-sm">Lokasi</label>
                            <input type="text" name="story_location" id="story_location"
                                class="form-control @error('story_location') is-invalid @enderror"
                                value="{{ old('story_location', $data->story_location ?? '') }}">
                            @error('story_location')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>



                        {{-- Status --}}
                        <div class="mb-3">
                            <label for="status" class="form-label text-sm">Status</label>
                            <select name="status" id="status" class="form-select @error('status') is-invalid @enderror">
                                @foreach (['draft', 'diproses', 'selesai', 'dibatalkan'] as $status)
                                    <option value="" hidden>Pilih Status</option>
                                    <option value="{{ $status }}"
                                        {{ old('status', $data->status ?? '') == $status ? 'selected' : '' }}>
                                        {{ ucfirst($status) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>


                        {{-- Note --}}
                        <div class="mb-3">
                            <label for="note" class="form-label text-sm">Catatan</label>
                            <textarea name="note" id="note" class="form-control @error('note') is-invalid @enderror" rows="3">{{ old('note', $data->note ?? '') }}</textarea>
                            @error('note')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <a href="{{ route('relaas-aktas.selectClient', ['client_id' => request('client_id')]) }}" class="btn btn-secondary">Kembali</a>
                        <button type="submit" class="btn btn-primary">{{ isset($data) ? 'Ubah' : 'Simpan' }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
