@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('title', 'Pilih Hak Akses')

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

    .choice-card {
        border: 1px solid #e9ecef;
        border-radius: 1rem;
        transition: all .25s ease;
        cursor: pointer;
        background: #fff;
    }

    .choice-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 1rem 2rem rgba(0,0,0,.12);
        border-color: #fb6340;
    }

    .choice-card .icon-box {
        width: 65px;
        height: 65px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    button.choice-btn {
        border: 0;
        background: transparent;
        padding: 0;
        width: 100%;
    }
</style>

@include('layouts.navbars.auth.topnav', ['title' => 'Pilih Hak Akses'])

<div class="container-fluid py-4">

    <div class="row justify-content-center">
        <div class="col-xl-8 col-lg-10">

            <div class="card shadow-lg border-0">
                <div class="card-body p-5">

                    <div class="text-center mb-5">
                        <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-xl mx-auto mb-3">
                            <i class="fas fa-user-shield text-white text-lg"></i>
                        </div>

                        <h4 class="fw-bold mb-2">
                            Masuk Sebagai Apa Hari Ini?
                        </h4>

                        <p class="text-sm text-muted mb-0">
                            Pilih hak akses yang ingin digunakan untuk melanjutkan ke dalam aplikasi.
                        </p>
                    </div>

                    <form action="{{ route('whoami.select') }}" method="POST">
                        @csrf

                        <div class="row g-4">

                            <div class="col-md-6">
                                <button type="submit"
                                        name="role"
                                        value="staff"
                                        class="choice-btn">

                                    <div class="choice-card h-100 p-4">

                                        <div class="icon-box bg-gradient-primary shadow-sm mb-4">
                                            <i class="fas fa-user-tie text-white fa-2x"></i>
                                        </div>

                                        <h5 class="fw-bold mb-2">
                                            PIC Staff
                                        </h5>

                                        <p class="text-sm text-muted mb-4">
                                            Kelola data klien, progress pekerjaan, dokumen, dan aktivitas operasional.
                                        </p>

                                        <span class="btn bg-gradient-primary btn-sm mb-0">
                                            Masuk Sebagai Staff
                                        </span>

                                    </div>

                                </button>
                            </div>

                            <div class="col-md-6">
                                <button type="submit"
                                        name="role"
                                        value="notaris"
                                        class="choice-btn">

                                    <div class="choice-card h-100 p-4">

                                        <div class="icon-box bg-gradient-success shadow-sm mb-4">
                                            <i class="fas fa-gavel text-white fa-2x"></i>
                                        </div>

                                        <h5 class="fw-bold mb-2">
                                            Notaris / PPAT
                                        </h5>

                                        <p class="text-sm text-muted mb-4">
                                            Akses menu khusus Notaris dan PPAT untuk proses otorisasi serta dokumen.
                                        </p>

                                        <span class="btn bg-gradient-success btn-sm mb-0">
                                            Masuk Sebagai Notaris
                                        </span>

                                    </div>

                                </button>
                            </div>

                        </div>

                    </form>

                </div>
            </div>

        </div>
    </div>

</div>

@endsection