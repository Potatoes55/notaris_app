@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('title', 'Lupa PIN')

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
</style>

@include('layouts.navbars.auth.topnav', ['title' => 'Lupa PIN'])

<div class="container-fluid py-4">

    <div class="card shadow-lg border-0 w-100 w-lg-50 mx-auto">

        <div class="card-body p-5">

            <div class="text-center mb-4">

                <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-xl mx-auto mb-3">
                    <i class="fas fa-key text-white text-lg"></i>
                </div>

                <h4 class="fw-bold">
                    Lupa PIN
                </h4>

                <p class="text-sm text-muted mb-0">
                    Masukkan email yang terdaftar. Kami akan mengirimkan link untuk membuat PIN baru.
                </p>

            </div>

            @if(session('status'))
                <div class="alert alert-success">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('pin.email') }}">

                @csrf

                <div class="input-group input-group-outline mb-3">

                    <input
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        class="form-control @error('email') is-invalid @enderror"
                        placeholder="Masukkan email"
                        required
                        autofocus>

                </div>

                @error('email')
                    <div class="text-danger text-sm mb-3">
                        {{ $message }}
                    </div>
                @enderror

                <div class="d-grid">
                    <button type="submit" class="btn bg-gradient-primary mb-0">
                        <i class="fas fa-paper-plane me-2"></i>
                        Kirim Link Reset PIN
                    </button>
                </div>

            </form>

            <hr>

            <a href="{{ route('settings.pin') }}" class="text-sm fw-bold text-primary">
                <i class="fas fa-arrow-left me-1"></i>
                Kembali
            </a>

        </div>

    </div>

</div>

@endsection