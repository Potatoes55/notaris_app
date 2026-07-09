@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('title', 'Reset PIN')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">

        <div class="col-lg-5 col-md-7">

            <div class="card border-0 shadow-lg" style="border-radius:16px;overflow:hidden;">

                <div class="card-header text-center border-0 py-4"
                    style="background:#fb6340;">

                    <h3 class="text-white fw-bold mb-1">
                        NOTARIS APP
                    </h3>

                    <p class="text-white mb-0 opacity-75">
                        Reset PIN Akun
                    </p>

                </div>

                <div class="card-body p-5">

                    <div class="text-center mb-4">

                        <i class="fa-solid fa-key fa-3x mb-3"
                            style="color:#fb6340;"></i>

                        <h4 class="fw-bold text-dark">
                            Buat PIN Baru
                        </h4>

                        <p class="text-muted mb-0">
                            Masukkan PIN baru sebanyak 6 digit angka.
                        </p>

                    </div>

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0 ps-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('pin.update') }}">
                        @csrf

                        <input type="hidden" name="token" value="{{ $token }}">
                        <input type="hidden" name="email" value="{{ $email }}">

                        <div class="mb-4">
                            <label class="form-label fw-semibold">
                                PIN Baru
                            </label>

                            <div class="input-group">

                                <span class="input-group-text">
                                    <i class="fa-solid fa-lock"></i>
                                </span>

                                <input
                                    id="pin"
                                    type="password"
                                    name="pin"
                                    maxlength="6"
                                    inputmode="numeric"
                                    pattern="[0-9]*"
                                    autocomplete="new-password"
                                    autofocus
                                    required
                                    class="form-control @error('pin') is-invalid @enderror"
                                    placeholder="Masukkan 6 digit PIN">

                            </div>

                            @error('pin')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">
                                Konfirmasi PIN Baru
                            </label>

                            <div class="input-group">

                                <span class="input-group-text">
                                    <i class="fa-solid fa-shield-halved"></i>
                                </span>

                                <input
                                    id="pin_confirmation"
                                    type="password"
                                    name="pin_confirmation"
                                    maxlength="6"
                                    inputmode="numeric"
                                    pattern="[0-9]*"
                                    autocomplete="new-password"
                                    required
                                    class="form-control"
                                    placeholder="Ulangi PIN">

                            </div>
                        </div>

                        <button
                            type="submit"
                            class="btn w-100 text-white fw-semibold py-2"
                            style="background:#fb6340;border:none;">

                            <i class="fa-solid fa-check me-2"></i>
                            Perbarui PIN

                        </button>

                    </form>

                </div>

                <div class="card-footer bg-white border-0 text-center py-3">

                    <small class="text-muted">
                        PIN akan langsung disinkronkan ke sistem setelah berhasil diperbarui.
                    </small>

                </div>

            </div>

        </div>

    </div>
</div>

<script>
document.querySelectorAll('input[name="pin"], input[name="pin_confirmation"]').forEach(function(input) {

    input.addEventListener('input', function() {
        this.value = this.value.replace(/\D/g, '').slice(0, 6);
    });

});
</script>
@endsection