@extends('layouts.app')

@section('title', 'PPAT')

@section('content')
@include('layouts.navbars.auth.topnav', ['title' => 'PPAT'])

<div class="container-fluid py-4">

    @include('components.ppat-menu')

</div>
@endsection