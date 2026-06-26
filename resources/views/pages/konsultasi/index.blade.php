@extends('layouts.app')

@section('title', 'Konsultasi')

@section('content')

@include('layouts.navbars.auth.topnav', [
    'title' => 'Konsultasi'
])

<div class="container-fluid py-4">

    @include('components.konsultasi-menu')

</div>

@endsection