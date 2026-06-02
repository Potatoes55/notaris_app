@extends('layouts.app')

@section('title', 'Tambah Pic')

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Tambah Pic'])

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

                        {{-- pic --}}
                        <div class="mb-3">
                            <label class="form-label text-sm">PIC <span class="text-danger">*</span></label>
                            <select name="pic_id" class="form-select @error('pic_id') is-invalid @enderror">
                                <option value="" hidden>Pilih PIC</option>
                                @if (isset($picDocuments)) 
                                    <option value=""disabled>buat pic document terlebih dahulu</option>
                                @else
                                @foreach ($picDocuments as $client)
                                    <option value="{{ $client->pic_id }}" class="text-capitalize"
                                        {{ old('pic_id', $data->pic_id ?? '') == $client->pic_id ? 'selected' : '' }}>
                                        {{ $client->pic->full_name }} - {{ $client->transaction_type }}
                                    </option>
                                @endforeach
                                @endif
                            </select>
                            @error('pic_id  ')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                            <div class="form-group mb-3">
                                <label for="proses_lain_id" class="form-control-label">PIC / Transaksi <span class="text-danger">*</span></label>
                                <select name="proses_lain_id" id="proses_lain_id" class="form-select @error('proses_lain_id') is-invalid @enderror">
                                    <option value="" hidden>Pilih PIC</option>
                                </select>
                                @error('proses_lain_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex gap-2">
                                <a href="{{ route('proses-lain-pic.index') }}" class="btn btn-secondary">Kembali</a>
                                <button type="submit" class="btn btn-primary">Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#client_code').on('change', function() {
                var clientCode = $(this).val();
                var selectTransaksi = $('#proses_lain_id');
                
                selectTransaksi.empty().append('<option value="" hidden>Pilih PIC</option>');

                if (clientCode) {
                    $.ajax({
                        url: '/proses-lain-pic/get-pic/' + clientCode,
                        type: 'GET',
                        dataType: 'json',
                        success: function(data) {
                            if(data.length > 0) {
                                $.each(data, function(key, value) {
                                    var optionText = value.full_name + ' - ' + value.transaction_type + ' (' + value.transaction_name + ')';
                                    selectTransaksi.append('<option value="'+ value.proses_lain_id +'">'+ optionText +'</option>');
                                });
                            } else {
                                selectTransaksi.append('<option value="" disabled>Tidak ada data transaksi untuk klien ini</option>');
                            }
                        },
                        error: function() {
                            alert('Gagal mengambil data transaksi.');
                        }
                    });
                }
            });
        });
    </script>
@endsection