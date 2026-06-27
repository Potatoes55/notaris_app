@extends('layouts.app')

@section('title', 'PIC Staff')

@section('content')

@include('layouts.navbars.auth.topnav', [
    'title' => $module . ' / PIC Staff'
])

@if(session('login_role') != 'staff')
    @if ($module == 'PPAT')
        @include('components.ppat-menu')
    @elseif ($module == 'Proses Lain')
        @include('components.proseslain-menu')
    @else
        @include('components.notaris-menu')
    @endif
@endif
    <div class="row mt-4 mx-4">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">PIC Staff</h5>
                    <a href="{{ $module == 'PPAT'
                        ? route('ppat.pic.staff.create')
                        : route('notaris.pic.staff.create') }}"
                        class="btn btn-primary btn-sm mb-0">
                        + Tambah PIC
                    </a>
                </div>
                <form method="GET" action="{{ route('pic_staff.index') }}" class="d-flex gap-2 ms-auto me-4 mb-0"
                    style="width: 500px;" class="no-spinner">
                    <input type="text" name="search" placeholder="Cari nama/email PIC..."
                        value="{{ request('search') }}" class="form-control">
                    <button type="submit" class="btn btn-primary btn-sm mb-0">Cari</button>
                </form>
                <hr>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0">

                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nama Lengkap</th>
                                    <th>Email</th>
                                    <th>No. HP</th>
                                    <th>Jabatan</th>
                                    <th>Alamat</th>
                                    <th>Catatan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($picStaffs as $staff)
                                    <tr class="text-center text-sm">
                                        <td>{{ $picStaffs->firstItem() + $loop->index }}</td>
                                        <td>{{ $staff->full_name }}</td>
                                        <td>{{ $staff->email }}</td>
                                        <td>{{ $staff->phone_number }}</td>
                                        <td>{{ $staff->position }}</td>
                                        <td>{{ $staff->address }}</td>
                                        <td>{{ $staff->note }}</td>
                                        <td>
                                            <a href="{{ route('pic_staff.edit', $staff->id) }}"
                                                class="btn btn-info btn-sm mb-0">Edit</a>
                                            <form action="{{ route('pic_staff.destroy', $staff->id) }}" method="POST"
                                                class="d-inline-block">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm mb-0">Hapus</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted text-sm">Belum ada PIC Staff.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <div class="mt-3 d-flex justify-content-end">
                            {{ $picStaffs->withQueryString()->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
