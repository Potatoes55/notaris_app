<div>
<label for="select_client" class="form-label text-sm">Klien <span class="text-danger">*</span></label>
<select name="client_code" id="select_client" class="form-select">
    <option value="" hidden>Pilih Klien</option>
    @foreach ($clients as $client)
        <option value="{{ $client->client_code }}">
            {{ $client->fullname }} - {{ $client->client_code }}
        </option>
    @endforeach
</select>
@error('client_code')
    <div class="invalid-feedback">{{ $message }}</div>
@enderror
</div>

<div class="mb-3">
    <label for="select_transaction" class="form-label text-sm">Transaksi Akta <span class="text-danger">*</span></label>
    <select name="akta_transaction_id" id="select_transaction" class="form-select">
        <option value="" hidden>Pilih Transaksi Akta</option>
        @foreach ($transactions as $trx)
            <option value="{{ $trx->id }}" >
                {{ $trx->client->fullname }} - {{ $trx->transaction_code }} - {{ $trx->akta_type->type ?? '-' }}
            </option>
        @endforeach
    </select>
    @error('akta_transaction_id')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const clientSelect = document.getElementById('select_client');
        const transactionSelect = document.getElementById('select_transaction');

        clientSelect.addEventListener('change', function() {
            const selectedClientCode = this.value;

            // Reset transaksi options
            transactionSelect.innerHTML = '<option value="" hidden>Pilih Transaksi Akta</option>';
            // Filter transaksi berdasarkan klien yang dipilih
            @foreach ($transactions as $trx)
                if ("{{ $trx->client->client_code }}" === selectedClientCode) {
                    const option = document.createElement('option');
                    option.value = "{{ $trx->id }}";
                    option.textContent = "{{ $trx->client->fullname }} - {{ $trx->transaction_code }} - {{ $trx->akta_type->type ?? '-' }}";
                    transactionSelect.appendChild(option);
                }
            @endforeach
        });
    });
</script>

