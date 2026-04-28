@extends('layouts.app')

@section('title', 'Logs Akta')

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Logs Akta'])

    <div class="row mt-4 mx-4">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0">
                    <h6>{{ isset($data) ? 'Edit Logs Akta' : 'Tambah  Logs Akta' }}</h6>
                </div>
                <hr>
                <div class="card-body px-4 pt-0 pb-2">
                    @dump($relaasAktas, $clients);
                    <form action="{{ isset($data) ? route('relaas-logs.update', $data->id) : route('relaas-logs.store') }}"
                        method="POST">
                        @csrf
                        @if (isset($data))
                            @method('PUT')
                        @endif

                        {{-- clients --}}
                        <div class="mb-3">
                            <label for="client_code" class="form-label text-sm">Klien</label>
                            <select name="client_code" id="client_code" class="form-select select2">
                                <option value="" hidden>Pilih Klien</option>
                                @foreach ($clients as $client)
                                    <option value="{{ $client->client_code }}"
                                        {{-- {{ isset($data) && $data->client_code == $client->client_code ? 'selected' : '' }}> --}}
                                        {{ $client->fullname }} - {{ $client->client_code }}
                                    </option>
                                @endforeach
                            </select>
                        </div>


                        {{-- Relaas ID --}}
                        <div class="mb-3">
                            <label for="relaas_id" class="form-label text-sm">Transaksi Akta</label>
                            <select name="relaas_id" id="relaas_id"
                                class="form-select @error('relaas_id') is-invalid @enderror">
                                <option value="" hidden>Pilih Transaksi Akta</option>
                                @foreach ($relaasAktas as $ra)
                                        {{-- <option value="{{ $ra->id }}"
                                            {{ isset($data) && $data->relaas_id == $ra->id ? 'selected' : '' }}>
                                            {{ $ra->client->fullname }} - {{ $ra->transaction_code }} - {{ $ra->title }}
                                        </option> --}}
                                    <option value="{{ $ra->id }}"
                                        {{ isset($data) && $data->relaas_id == $ra->id ? 'selected' : '' }}>
                                        {{ $ra->client->fullname }} - {{ $ra->transaction_code }} - {{ $ra->title }}
                                    </option>

                                @endforeach
                            </select>
                            @error('relaas_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror

                            {{-- Step --}}
                            <div class="mb-3">
                                <label for="step" class="form-label text-sm">Step</label>
                                <input type="text" name="step" id="step"
                                    class="form-control @error('step') is-invalid @enderror"
                                    value="{{ old('step', $data->step ?? '') }}">
                                @error('step')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Note --}}
                            <div class="mb-3">
                                <label for="note" class="form-label text-sm">Catatan</label>
                                <textarea name="note" id="note" rows="3" class="form-control @error('note') is-invalid @enderror">{{ old('note', $data->note ?? '') }}</textarea>
                                @error('note')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <a href="{{ route('relaas-logs.index') }}" class="btn btn-secondary">Kembali</a>

                            <button type="submit" class="btn btn-primary">
                                {{ isset($data) ? 'Ubah' : 'Simpan' }}
                            </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

{{-- @push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const clientSelect = document.getElementById('client_code');
        const relaasSelect = document.getElementById('relaas_id');
        
        // Ambil ID yang tersimpan jika dalam mode EDIT atau OLD input
        const selectedRelaasId = "{{ old('relaas_id', $data->relaas_id ?? '') }}";

        function updateRelaasOptions() {
            const selectedClientCode = clientSelect.value;
            
            // Bersihkan dropdown
            relaasSelect.innerHTML = '<option value="" hidden>Pilih Transaksi Akta</option>';

            // Filter data berdasarkan client_code
            const filtered = relaasData.filter(item => {
                return item.client && String(item.client.client_code) === String(selectedClientCode);
            });

            // Isi dropdown
            filtered.forEach(ra => {
                const option = document.createElement('option');
                option.value = ra.id;
                option.textContent = `${ra.client.fullname} - ${ra.transaction_code} - ${ra.title}`;
                
                // Set 'selected' jika ID cocok
                if (String(ra.id) === String(selectedRelaasId)) {
                    option.selected = true;
                }
                
                relaasSelect.appendChild(option);
            });
        }

        // Jalankan saat client dipilih
        clientSelect.addEventListener('change', updateRelaasOptions);

        // Jika pakai Select2, gunakan event khusus Select2
        $(clientSelect).on('select2:select', function() {
            updateRelaasOptions();
        });

        // Jalankan sekali saat halaman load (untuk mode Edit)
        if (clientSelect.value) {
            updateRelaasOptions();
        }
    });
</script>
@endpush --}}



@endsection

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const clientSelect = document.getElementById('client_code');');
        const transactionSelect = document.getElementById('relaas_id');

        clientSelect.addEventListener('change', function() {
            const selectedClientCode = this.value;

            // Reset transaksi options
            transactionSelect.innerHTML = '<option value="" hidden>Pilih Transaksi Akta</option>';
            // Filter transaksi berdasarkan klien yang dipilih
            @foreach ($relaasAktas as $ra)
                if ("{{ $ra->client->client_code }}" === selectedClientCode) {
                    const option = document.createElement('option');
                    option.value = "{{ $ra->id }}";
                    option.textContent = "{{ $ra->client->fullname }} - {{ $ra->transaction_code }} - {{ $ra->akta_type->type ?? '-' }}";
                    transactionSelect.appendChild(option);
                }
            @endforeach
        });
    });
</script>

