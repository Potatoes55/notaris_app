<div class="mb-3">
                            <label for="transaction_type" class="form-label text-sm">Tipe Transaksi <span
                                    class="text-danger">*</span></label>
                            <select name="transaction_type" id="transaction_type"
                                class="form-select @error('transaction_type') is-invalid @enderror"">
                                <option value="" hidden>Pilih Tipe Transaksi</option>
                                <option value="akta"
                                    {{ old('transaction_type', $picDocument->transaction_type ?? '') == 'akta' ? 'selected' : '' }}>
                                    Notaris
                                </option>
                                <option value="ppat"
                                    {{ old('transaction_type', $picDocument->transaction_type ?? '') == 'ppat' ? 'selected' : '' }}>
                                    PPAT
                                </option>
                            </select>
                            @error('transaction_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Akta Transaction --}}
                        <div class="mb-3" id="akta_section" style="display: none;">
                            <label for="akta_transaction_id" class="form-label text-sm">Transaksi Akta </label>
                            <select id="akta_transaction_id" name="akta_transaction_id"
                                class="form-select @error('akta_transaction_id') is-invalid @enderror">
                                <option value="" hidden>Pilih Transaksi</option>
                                @foreach ($aktaTransaction as $akta)
                                    <option value="{{ $akta->id }}"
                                        {{ isset($picDocument) && $picDocument->transaction_type === 'akta' && $picDocument->transaction_id == $akta->id ? 'selected' : '' }}>
                                        {{ $akta->client->fullname }} - {{ $akta->transaction_code }} -
                                        {{ $akta->akta_type->type }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">
                                Format : Nama Klien – Kode Transaksi – Jenis Akta
                            </small>
                            @error('transaction_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Relaas Transaction --}}
                        <div class="mb-3" id="relaas_section" style="display: none;">
                            <label for="ppat_transaction_id" class="form-label text-sm">Transaksi PPAT</label>
                            <select id="ppat_transaction_id" name="ppat_transaction_id"
                                class="form-select @error('akta_transaction_id') is-invalid @enderror">
                                <option value="" hidden>Pilih Transaksi PPAT</option>
                                @foreach ($relaasTransaction as $relaas)
                                    <option value="{{ $relaas->id }}"
                                        {{ isset($picDocument) && $picDocument->transaction_type === 'relaas' && $picDocument->transaction_id == $relaas->id ? 'selected' : '' }}>
                                        {{ $relaas->client->fullname }} - {{ $relaas->transaction_code }} -
                                        {{ $relaas->akta_type->type }}
                                        {{-- -{{ $relaas->title }} --}}
                                    </option>
                                @endforeach
                            </select>
                        </div>