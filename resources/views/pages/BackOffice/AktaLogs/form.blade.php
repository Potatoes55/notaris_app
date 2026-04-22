@extends('layouts.app')

@section('title', 'Logs Akta')

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Akta Notaris / Logs Akta'])

    <div class="row mt-4 mx-4">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0">
                    <h6>{{ isset($log) ? 'Edit Logs Akta' : 'Tambah Logs Akta' }}</h6>
                </div>
                <hr>
                <div class="card-body px-4 pt-1 pb-2">
                    <form action="{{ isset($log) ? route('akta-logs.update', $log->id) : route('akta-logs.store') }}"
                        method="POST">
                        @csrf
                        @if (isset($log))
                            @method('PUT')
                        @endif

                        <div class="mb-3">
                            <label for="client_code" class="form-label text-sm">Klien</label>
                            <select name="client_code" id="client_code" class="form-select select2">
                                <option value="" hidden>Pilih Klien</option>
                                @foreach ($clients as $client)
                                    <option value="{{ $client->client_code }}"
                                        {{ isset($log) && $log->client_code == $client->client_code ? 'selected' : '' }}>
                                        {{ $client->fullname }} - {{ $client->client_code }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="akta_transaction_id" class="form-label text-sm">Transaksi Akta</label>
                            <select name="akta_transaction_id" id="akta_transaction_id" class="form-select">
                                <option value="" hidden>Pilih Transaksi Akta</option>
                                @foreach ($transactions as $trx)
                                    <option value="{{ $trx->id }}"
                                        {{ isset($log) && $log->akta_transaction_id == $trx->id ? 'selected' : '' }}>
                                        {{ $trx->transaction_code }} - {{ $trx->akta_type->type ?? '-' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- <div class="mb-3">
                        <label for="client_code" class="form-label">Registration Code</label>
                        <input type="text" name="client_code" id="client_code" class="form-control"
                            value="{{ $log->client_code ?? old('client_code') }}">
                    </div> --}}

                        <div class="mb-3">
                            <label for="step" class="form-label text-sm">Step</label>
                            <input type="text" name="step" id="step" class="form-control"
                                value="{{ $log->step ?? old('step') }}">
                        </div>

                        <div class="mb-3">
                            <label for="note" class="form-label text-sm ">Catatan</label>
                            <textarea name="note" id="note" class="form-control">{{ $log->note ?? old('note') }}</textarea>
                        </div>

                        <a href="{{ route('akta-logs.index') }}" class="btn btn-secondary">Kembali</a>
                        <button type="submit" class="btn btn-primary">{{ isset($log) ? 'Ubah' : 'Simpan' }}</button>

                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
