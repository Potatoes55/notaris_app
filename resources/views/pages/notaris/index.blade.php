@extends('layouts.app')

@section('title', 'Notaris')

@section('content')
@include('layouts.navbars.auth.topnav', ['title' => 'Notaris'])

<div class="container-fluid py-4">

    @include('components.notaris-menu')

</div>
@endsection