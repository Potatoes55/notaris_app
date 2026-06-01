@extends('layouts.app')

@section('title', 'Backup & Restore Data')

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Backup & Restore Data'])

    <div class="row mt-4 mx-4">
        {{-- ================= BACKUP CARD ================= --}}
        <div class="col-lg-6 col-md-12 mb-4">
            <div class="card shadow-sm">
                <div class="card-header pb-0">
                    <h5 class="mb-0">
                        <i class="bi bi-cloud-arrow-down me-2 text-warning"></i>
                        Backup Data
                    </h5>
                    <small class="text-muted">Download data notaris dalam format JSON</small>
                </div>
                <div class="card-body">
                    <form action="{{ route('backup') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-sm">Start Date</label>
                                <input type="date" name="start_date" value="{{ now()->subYear()->toDateString() }}" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-sm">End Date</label>
                                <input type="date" name="end_date" value="{{ now()->toDateString() }}" class="form-control form-control-sm">
                            </div>
                        </div>
                        <div class="alert alert-light border text-sm">
                            Backup akan mencakup seluruh data sesuai rentang tanggal yang dipilih.
                        </div>
                        <button type="submit" class="btn btn-warning w-100 mt-4">
                            <i class="bi bi-download me-1"></i> Download Backup
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- ================= RESTORE CARD ================= --}}
        <div class="col-lg-6 col-md-12 mb-4">
            <div class="card shadow-sm border-danger">
                <div class="card-header pb-0">
                    <h5 class="mb-0 text-danger">
                        <i class="bi bi-arrow-repeat me-2"></i> Restore Data
                    </h5>
                    <small class="text-muted">Upload file backup untuk mengembalikan data</small>
                </div>
                <div class="card-body">
                    <form action="{{ route('restore') }}" method="POST" enctype="multipart/form-data" id="restoreForm">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label text-sm">File Backup (.json)</label>
                            <input type="file" name="file" class="form-control form-control-sm" accept=".json" required>
                            @error('file') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="alert alert-danger text-sm text-white">
                            <strong>⚠ Peringatan:</strong><br>
                            Restore akan memasukkan semua data yang ada di file <b>.json</b>.
                        </div>
                        <button type="button" class="btn btn-danger w-100" data-bs-toggle="modal" data-bs-target="#confirmRestoreModal">
                            <i class="bi bi-upload me-1"></i> Restore Data
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

{{-- BAGIAN INI YANG PENTING: MODAL HARUS DI DALAM PUSH --}}
@push('modal_luar')
    <div class="modal fade" id="confirmRestoreModal" tabindex="-1" aria-hidden="true" style="z-index: 9999;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title text-white">
                        <i class="bi bi-exclamation-triangle me-2"></i> Konfirmasi Restore
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-2">Restore akan memasukkan semua data yang ada di file backup.</p>
                    <p class="text-danger small mb-0">Pastikan file yang Anda upload sudah benar.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" form="restoreForm" class="btn btn-danger">Ya, Restore Sekarang</button>
                </div>
            </div>
        </div>
    </div>
@endpush