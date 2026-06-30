@extends('layouts.app') 

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Buat PIN Baru</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('pin.update') }}">
                        @csrf

                        <input type="hidden" name="token" value="{{ $token }}">
                        <input type="hidden" name="email" value="{{ $email }}">

                        <div class="alert alert-info">
                            Silakan masukkan 6 digit PIN baru Anda untuk aplikasi Notaris.
                        </div>

                        @error('email')
                            <div class="alert alert-danger">{{ $message }}</div>
                        @enderror

                        <div class="form-group mb-3">
                            <label for="pin" class="form-label">PIN Baru (6 Digit Angka)</label>
                            <input id="pin" type="password" 
                                   class="form-control @error('pin') is-invalid @enderror" 
                                   name="pin" required maxlength="6" inputmode="numeric" pattern="[0-9]*" autocomplete="new-password" autofocus>

                            @error('pin')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group mb-4">
                            <label for="pin-confirm" class="form-label">Konfirmasi PIN Baru</label>
                            <input id="pin-confirm" type="password" 
                                   class="form-control" 
                                   name="pin_confirmation" required maxlength="6" inputmode="numeric" pattern="[0-9]*" autocomplete="new-password">
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success">
                                Perbarui PIN & Sinkronisasi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
    document.querySelectorAll('input[name^="pin"]').forEach(function(input) {
        input.addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, ''); 
        });
    });
</script>
@endsection