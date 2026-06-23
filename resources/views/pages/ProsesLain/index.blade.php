@extends('layouts.app')

@section('title', 'Proses Lain')

@section('content')
@include('layouts.navbars.auth.topnav', ['title' => 'Proses Lain'])

<div class="container-fluid py-4">

    @include('components.proseslain-menu')

</div>
@endsection