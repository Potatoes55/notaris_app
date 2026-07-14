@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('title', 'Reset PIN')

@section('content')

<style>
    .sidenav,
    aside,
    .navbar-vertical {
        display: none !important;
    }

    .main-content,
    #main-content {
        margin-left: 0 !important;
        padding-left: 0 !important;
    }

    .input-container {
        max-width: 350px;
        margin: 0 auto;
    }
</style>

@include('layouts.navbars.auth.topnav', ['title' => 'Reset PIN'])

<div class="container-fluid py-4">
    <div class="card shadow-lg border-0 w-100 w-lg-50 mx-auto">
        <div class="card-body p-5">

            <div class="text-center mb-4">
                <div class="icon icon-shape bg-gradient-primary shadow border-radius-xl mx-auto mb-3">
                    <i class="fas fa-key text-white text-lg"></i>
                </div>

                <h4 class="fw-bold">Reset PIN Akun</h4>

                <p class="text-sm text-muted mb-0">
                    Masukkan PIN baru sebanyak 6 digit angka.
                </p>
            </div>

            @if ($errors->any())
                <div class="alert border-0 text-white mb-4" style="background:#f5365c;">
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('pin.update') }}" method="POST">
                @csrf

                <input type="hidden" name="token" value="{{ $token }}">
                <input type="hidden" name="email" value="{{ $email }}">

                <div class="input-container">

                    <div class="input-group input-group-outline mb-3">
                        <input
                            type="password"
                            name="pin"
                            id="pin"
                            class="form-control form-control-sm text-center @error('pin') is-invalid @enderror"
                            maxlength="6"
                            inputmode="numeric"
                            pattern="[0-9]*"
                            placeholder="Masukkan 6 Digit PIN"
                            required
                            autofocus>
                    </div>

                    @error('pin')
                        <small class="text-danger d-block mb-3">{{ $message }}</small>
                    @enderror

                    <div class="input-group input-group-outline mb-4">
                        <input
                            type="password"
                            name="pin_confirmation"
                            id="pin_confirmation"
                            class="form-control form-control-sm text-center"
                            maxlength="6"
                            inputmode="numeric"
                            pattern="[0-9]*"
                            placeholder="Konfirmasi PIN"
                            required>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn bg-gradient-primary mb-0">
                            <i class="fas fa-check me-2"></i>
                            Perbarui PIN
                        </button>
                    </div>

                </div>
            </form>

        </div>
    </div>
</div>

<script>
document.querySelectorAll('#pin,#pin_confirmation').forEach(function(input){
    input.addEventListener('input',function(){
        this.value=this.value.replace(/\D/g,'').slice(0,6);
    });
});
</script>

@endsection