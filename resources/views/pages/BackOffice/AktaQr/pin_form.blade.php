@extends('layouts.app') {{-- Ganti dengan master layout Anda jika ada, atau gunakan HTML standar --}}

@section('content')
<div class="container d-flex align-items-center justify-content-center" style="min-height: 80vh;">
    <div class="col-12 col-md-5 col-lg-4">
        
        <div class="text-center mb-4">
            <div class="bg-primary text-white d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 60px; height: 60px;">
                <i class="bi bi-shield-lock-fill fs-3"></i>
            </div>
            <h4 class="mt-3 fw-bold text-dark">Dokumen Terproteksi</h4>
            <p class="text-muted small">Silakan masukkan PIN atau Kode Akses Notaris untuk melihat dokumen ini.</p>
        </div>

        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-body p-4">
                
                {{-- Alert Error Global --}}
                @if($errors->has('pin') || $errors->has('access_code'))
                    <div class="alert alert-danger border-0 small text-center mb-3 py-2">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i>
                        {{ $errors->first('pin') ?: $errors->first('access_code') }}
                    </div>
                @endif

                <form action="{{ route('akta.qr.pin.check', ['transaction_code' => $transaction_code]) }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="pin" class="form-label fw-semibold small text-secondary">PIN Notaris</label>
                        <input type="password" 
                               inputmode="numeric" 
                               pattern="[0-9]*" 
                               class="form-control form-control-lg text-center fs-4" 
                               id="pin" 
                               name="pin" 
                               placeholder="••••••" 
                               maxlength="6"
                               autofocus>
                    </div>

                    <div class="text-center my-3 position-relative">
                        <hr class="text-muted">
                        <span class="position-absolute top-50 start-50 translate-middle bg-white px-3 text-muted small">ATAU</span>
                    </div>

                    <div class="mb-4">
                        <label for="access_code" class="form-label fw-semibold small text-secondary">Kode Akses</label>
                        <input type="password"
                               class="form-control text-center fs-4"
                               id="access_code" 
                               name="access_code" 
                               placeholder="Masukkan kode akses...">
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg w-100 rounded-2 fs-6 fw-semibold">
                        Buka Dokumen <i class="bi bi-arrow-right ms-1"></i>
                    </button>
                </form>

            </div>
        </div>

        <div class="text-center mt-4">
            <span class="text-muted small">&copy; {{ date('Y') }} Kantor Notaris & PPAT</span>
        </div>

    </div>
</div>
@endsection