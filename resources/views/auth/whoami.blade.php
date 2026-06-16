<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Pilih Hak Akses - WhoAmI</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background-color: #f4f6f9;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .choice-card {
            transition: all 0.3s ease;
            border: 2px solid transparent;
            cursor: pointer;
        }
        .choice-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.08) !important;
        }
        .btn-check:checked + .choice-card-pic {
            border-color: #0d6efd;
            background-color: #f0f7ff;
        }
        .btn-check:checked + .choice-card-notaris {
            border-color: #198754;
            background-color: #f0fff4;
        }
    </style>
</head>
<body>

<div class="container d-flex align-items-center justify-content-center" style="min-height: 100vh;">
    <div class="card shadow border-0 p-4 p-md-5 text-center" style="max-width: 650px; width: 100%; border-radius: 16px;">
        
        <div class="mb-4">
            <h2 class="fw-bold text-dark mb-2">Masuk Sebagai Apa Hari Ini?</h2>
            <p class="text-muted">Silakan tentukan hak akses halaman yang ingin Anda buka terlebih dahulu</p>
        </div>

        <form action="{{ route('whoami.select') }}" method="POST">
            @csrf
            
            <div class="row g-3 text-start">
                <div class="col-md-6">
                    <button type="submit" name="role" value="staff" class="w-100 btn p-0 text-start">
                        <div class="card h-100 p-4 shadow-sm choice-card">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-primary bg-opacity-10 text-primary rounded p-3 me-3">
                                    <i class="bi bi-person-workspace fs-3"></i>
                                </div>
                                <h4 class="fw-bold m-0 text-dark">PIC Staff</h4>
                            </div>
                            <p class="text-muted small m-0">Masuk langsung ke menu operasional, kelola data klien, dan pekerjaan staff.</p>
                        </div>
                    </button>
                </div>

                <div class="col-md-6">
                    <button type="submit" name="role" value="notaris" class="w-100 btn p-0 text-start">
                        <div class="card h-100 p-4 shadow-sm choice-card">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-success bg-opacity-10 text-success rounded p-3 me-3">
                                    <i class="bi bi-journal-text fs-3"></i>
                                </div>
                                <h4 class="fw-bold m-0 text-dark">Notaris/PPAT</h4>
                            </div>
                            <p class="text-muted small m-0">Masuk ke halaman pengaturan otorisasi pembukaan akses menu khusus Notaris.</p>
                        </div>
                    </button>
                </div>
            </div>

        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>