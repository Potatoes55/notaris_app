@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('title', 'PIN')

@section('content')
<style>
        /* Sembunyikan sidebar vertical */
        .sidenav, aside, .navbar-vertical {
            display: none !important;
        }
       
        .main-content, #main-content {
            margin-left: 0 !important;
            padding-left: 0 !important;
        }
    </style>
    @include('layouts.navbars.auth.topnav', ['title' => 'PIN'])
<div class="container" style="max-width: 400px; margin-top: 50px;">
    <div class="card">
        <div class="card-header text-center">
            <h4>Akses Fitur Notaris Via PIN</h4>
        </div>
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('profile.unlock') }}" method="POST">
                @csrf
                
                <div class="form-group mb-3">
                    <label for="pin" class="form-label">Masukkan PIN anda</label>
                    <input type="password" 
                           name="pin" 
                           id="pin" 
                           class="form-control text-center fs-4" 
                           maxlength="6" 
                           inputmode="numeric" 
                           pattern="[0-9]*" 
                           required 
                           placeholder="******">
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">Buka Akses</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection