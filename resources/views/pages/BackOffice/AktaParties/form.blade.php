@extends('layouts.app')

@section('title', 'Pihak Akta')

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Akta Notaris / Pihak Akta'])

    <div class="row mt-4 mx-4">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0">
                    <h6>{{ isset($aktaParty) ? 'Edit Pihak Akta' : 'Tambah Pihak Akta' }}</h6>
                </div>
                <hr>
                <div class="card-body px-4 pt-0 pb-0">
                    <form method="POST"
                        action="{{ isset($aktaParty)
                            ? route('akta-parties.update', $aktaParty->id)
                            : route('akta-parties.storeData', $transaction->id) }}">

                        @csrf
                        @if (isset($aktaParty))
                            @method('PUT')
                        @endif

                        {{-- Hidden untuk transaksi --}}
                        <input type="hidden" name="akta_transaction_id"
                            value="{{ old('akta_transaction_id', $transaction->id) }}">
                        {{-- hidden untuk client_id --}}
                        <input type="hidden" name="client_code"
                            value="{{ old('client_code', $transaction->client_code) }}">
                        {{-- hidden untuk client_code --}}
                        <input type="hidden" name="client_code"
                            value="{{ old('client_code', $transaction->client_code) }}">
                        {{-- hidden notaris --}}
                        <input type="hidden" name="notaris_id" value="{{ old('notaris_id', $transaction->notaris_id) }}">
                        {{-- Hidden untuk navigasi redirect --}}
                        <input type="hidden" name="transaction_code" 
                            value="{{ old('transaction_code', $transaction->transaction_code) }}">
                        <input type="hidden" name="akta_number" 
                            value="{{ old('akta_number', $transaction->akta_number) }}">

                        {{-- <div class="mb-3">
                            <label for="client_code" class="form-label text-sm">Klien</label>
                            <select name="client_code" id="client_code" class="form-select select2">
                                <option value="" hidden>Pilih Klien</option>
                                @foreach ($clients as $client)
                                    <option value="{{ $client->client_code }}"
                                        {{ isset($party) && $party->client_code == $client->client_code ? 'selected' : '' }}>
                                        {{ $client->fullname }} - {{ $client->client_code }}
                                    </option>
                                @endforeach
                            </select>
                        </div> --}}

                        <div class="mb-3">
                            <label class="form-label text-sm">Nama</label>
                            <input type="text" name="name" class="form-control"
                                value="{{ old('name', $aktaParty->name ?? '') }}">
                            @error('name')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-sm">Peran</label>
                            <input type="text" name="role" class="form-control"
                                value="{{ old('role', $aktaParty->role ?? '') }}" placeholder="Contoh: Pendiri, Penerima Kuasa">
                            @error('role')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-sm">Alamat</label>
                            <input type="text" name="address" class="form-control"
                                value="{{ old('address', $aktaParty->address ?? '') }}">
                            @error('address')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-sm">Tipe Identitas</label>
                            <input type="text" name="id_type" class="form-control"
                                value="{{ old('id_type', $aktaParty->id_type ?? '') }}" placeholder="Contoh: KTP, SIM, dll">
                            @error('id_type')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-sm">No. Identitas</label>
                            <input type="text" name="id_number" class="form-control"
                                value="{{ old('id_number', $aktaParty->id_number ?? '') }}"
                                placeholder="Contoh: Nomor KTP">
                            @error('id_number')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>



                        {{-- <div class="mb-3">
                            <label class="form-label text-sm">Catatan</label>
                            <textarea name="note" class="form-control">{{ old('note', $aktaParty->note ?? '') }}</textarea>
                            @error('note')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div> --}}

                        <div class="mt-3">

                            <a href="{{ route('akta-parties.index', ['transaction_code' => $transaction->transaction_code, 'akta_number' => $transaction->akta_number]) }}" class="btn btn-secondary">
                                Batal
                            </a>
                            <button type="submit" class="btn btn-primary">
                                {{ isset($aktaParty) ? 'Ubah' : 'Simpan' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
