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
                        <input type="hidden" name="proses_lain_id" id="proses_lain_id_hidden" value="{{ old('proses_lain_id', $data->proses_lain_id ?? '') }}">

                        {{-- CLIENT CODE --}}
                        <div class="mb-3">
                            <label class="form-label text-sm">Klien <span class="text-danger">*</span></label>
                            <select name="client_code" id="client_select" class="form-select @error('client_code') is-invalid @enderror select2">
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

                        {{-- PIC --}}
                        <div class="mb-3">
                            <label class="form-label text-sm">PIC <span class="text-danger">*</span></label>
                            <select name="pic_id" id="pic_select" class="form-select @error('pic_id') is-invalid @enderror">
                                <option value="" hidden>Pilih PIC</option>
                                
                                @if (!isset($picDocuments) || $picDocuments->isEmpty()) 
                                    <option value="" disabled>buat pic document terlebih dahulu</option>
                                @else 
                                    @foreach ($picDocuments as $item)
                                        <option value="{{ $item->pic_id }}" 
                                                data-client="{{ $item->client_code }}"
                                                data-proses="{{ $item->transaction_id }}" 
                                                class="text-capitalize pic-option"
                                            {{ old('pic_id', $data->pic_id ?? '') == $item->pic_id ? 'selected' : '' }}>
                                            {{ $item->pic->full_name }} - {{ $item->transaction_type }} - {{ $item->prosesLain->name ?? '-' }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            
                            @error('pic_id')
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
    var $clientSelect = $('#client_select');
    var $picSelect = $('#pic_select');
    var $prosesHidden = $('#proses_lain_id_hidden');

    // 1. Jalankan fungsi saat Klien diubah
    $clientSelect.on('change', function() {
        var selectedClient = $(this).val();
        
        $picSelect.val('').trigger('change');
        $prosesHidden.val(''); 
        $picSelect.find('option.pic-option').hide();
        
        if (selectedClient) {
            $picSelect.find('option[data-client="' + selectedClient + '"]').show();
        }
    });

    // 2. Saat PIC dipilih, ambil data-proses (transaction_id) masuk ke input hidden
    $picSelect.on('change', function() {
        var $selectedOption = $(this).find('option:selected');
        var prosesId = $selectedOption.attr('data-proses'); // Membaca data-proses HTML
        
        if (prosesId) {
            $prosesHidden.val(prosesId);
        } else {
            $prosesHidden.val('');
        }
    });

    // 3. Antisipasi reload halaman / gagal validasi awal
    var initialClient = $clientSelect.val();
    if (initialClient) {
        var currentPic = "{{ old('pic_id', $data->pic_id ?? '') }}";
        
        $picSelect.find('option.pic-option').hide();
        $picSelect.find('option[data-client="' + initialClient + '"]').show();
        
        if(currentPic) {
            $picSelect.val(currentPic);
            var initialProses = $picSelect.find('option:selected').attr('data-proses');
            if(initialProses) {
                $prosesHidden.val(initialProses);
            }
        }
    }
});
</script>

 
@endsection