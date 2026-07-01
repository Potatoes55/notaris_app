@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('title', 'PIN')

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

@include('layouts.navbars.auth.topnav', ['title' => 'PIN'])

<div class="container-fluid py-4">

    <div class="card shadow-lg border-0 w-100 w-lg-50 mx-auto">

        <div class="card-body p-5">

            <div class="text-center mb-4">

                <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-xl mx-auto mb-3">
                    <i class="fas fa-lock text-white text-lg"></i>
                </div>

                <h4 class="fw-bold">
                    Akses PIN
                </h4>

                <p class="text-sm text-muted mb-0">
                    Masukkan PIN Anda untuk membuka akses menu Notaris dan PPAT.
                </p>

            </div>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('profile.unlock') }}">
                @csrf

                <div class="input-group input-group-outline mb-3">

                    <input
                        type="password"
                        id="pin"
                        name="pin"
                        class="form-control @error('pin') is-invalid @enderror"
                        placeholder="Masukkan PIN"
                        maxlength="6"
                        inputmode="numeric"
                        pattern="[0-9]*"
                        autocomplete="off"
                        required>

                </div>

                @error('pin')
                    <div class="text-danger text-sm mb-3">
                        {{ $message }}
                    </div>
                @enderror

                <div class="d-grid">
                    <button type="submit" class="btn bg-gradient-primary mb-0">
                        <i class="fas fa-unlock-alt me-2"></i>
                        Buka Akses
                    </button>
                </div>

            </form>

            <hr>

            <a href="{{ route('pin.request') }}" class="text-sm fw-bold text-primary">
                <i class="fas fa-key me-1"></i>
                Lupa PIN
            </a>

        </div>

    </div>

</div>

@endsection