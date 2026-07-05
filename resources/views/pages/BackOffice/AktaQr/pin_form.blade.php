@extends('layouts.app')

@section('title', 'Dokumen Terproteksi')

@section('content')

<div class="container py-5">

    <div class="row justify-content-center">
        <div class="col-xl-4 col-lg-5 col-md-6">

            <div class="text-center mb-4">

                <div class="icon icon-shape bg-gradient-primary shadow border-radius-xl mx-auto mb-3">
                    <i class="bi bi-shield-lock-fill text-white"></i>
                </div>

                <h5 class="fw-bold mb-1">Dokumen Terproteksi</h5>

                <p class="text-sm text-muted mb-0">
                    Masukkan PIN atau Kode Akses untuk melanjutkan
                </p>

            </div>

            <div class="card shadow-sm border-0">
                <div class="card-body p-4">

                    @if($errors->has('pin') || $errors->has('access_code'))
                        <div class="alert alert-danger text-sm py-2 text-center mb-3">
                            {{ $errors->first('pin') ?: $errors->first('access_code') }}
                        </div>
                    @endif

                    <form action="{{ route('akta.qr.pin.check', ['transaction_code' => $transaction_code]) }}" method="POST">
                        @csrf

                        {{-- PIN --}}
                        <div class="mb-3">
                            <label class="form-label small text-secondary">PIN Notaris</label>

                            <div class="input-group">
                                <input type="password"
                                       name="pin"
                                       id="pin"
                                       inputmode="numeric"
                                       pattern="[0-9]*"
                                       maxlength="6"
                                       class="form-control text-center fs-5"
                                       placeholder="Masukan PIN"
                                       autofocus>

                                <span class="input-group-text bg-transparent" onclick="togglePin()">
                                    <i class="fas fa-eye" id="pinIcon"></i>
                                </span>
                            </div>
                        </div>

                        {{-- separator --}}
                        <div class="text-center text-muted small mb-3">
                            atau
                        </div>

                        {{-- ACCESS CODE --}}
                        <div class="mb-4">
                            <label class="form-label small text-secondary">Kode Akses</label>

                            <div class="input-group">
                                <input type="password"
                                       name="access_code"
                                       id="access_code"
                                       class="form-control text-center fs-5"
                                       placeholder="Masukkan kode akses">

                                <span class="input-group-text bg-transparent" onclick="toggleAccess()">
                                    <i class="fas fa-eye" id="accessIcon"></i>
                                </span>
                            </div>
                        </div>

                        <button type="submit" class="btn bg-gradient-primary w-100 mb-0">
                            Buka Dokumen
                        </button>

                    </form>

                </div>
            </div>

            <div class="text-center mt-4">
                <span class="text-xs text-muted">
                    © {{ date('Y') }} Kantor Notaris & PPAT
                </span>
            </div>

        </div>
    </div>

</div>

<script>
function togglePin() {
    const input = document.getElementById('pin');
    const icon = document.getElementById('pinIcon');

    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

function toggleAccess() {
    const input = document.getElementById('access_code');
    const icon = document.getElementById('accessIcon');

    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}
</script>

@endsection