@extends('layouts.app')

@section('title', 'Tambah Pic')

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Tambah Pic'])

    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0">
                        <h5>Tambah Pic</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('proses-lain-pic.store') }}" method="POST">
                            @csrf
                            <div class="form-group mb-3">
                                <label for="client_code" class="form-control-label">Klien <span class="text-danger">*</span></label>
                                <select name="client_code" id="client_code" class="form-select @error('client_code') is-invalid @enderror">
                                    <option value="" hidden>Pilih Klien</option>
                                    @foreach ($clients as $client)
                                        <option value="{{ $client->client_code }}">{{ $client->fullname }}</option>
                                    @endforeach
                                </select>
                                @error('client_code')
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