@extends('layouts.app')

@section('title', 'Jenis Akta')

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Akta Notaris / Jenis Akta '])
    @include('components.notaris-menu')
    
    <div class="row mt-4 mx-4">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center mb-3 px-3">
                    <h5 class="mb-0">Jenis Akta</h5>
                    <a href="{{ route('akta-types.create') }}" class="btn btn-primary btn-sm mb-0">
                        + Tambah Jenis Akta
                    </a>
                </div>
                <div class="d-flex justify-content-end w-100 px-2">
                    <form method="GET" action="{{ route('akta-types.index') }}" class="d-flex gap-2 w-100"
                        style="max-width: 400px;" class="no-spinner">
                        <input type="text" name="search" placeholder="Cari tipe akta" value="{{ request('search') }}"
                            class="form-control">
                        <button type="submit" class="btn btn-primary btn-sm mb-0">Cari</button>
                    </form>
                </div>
                <hr>
                <div class="card-body px-0 pt-0 pb-0">


                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="th-title">
                                        #
                                    </th>
                                    <th class="th-title">
                                        Kategori
                                    </th>
                                    <th class="th-title">
                                        Tipe
                                    </th>
                                    <th class="th-title">
                                        Deskripsi
                                    </th>
                                    <th class="th-title">
                                        Dokumen
                                    </th>
                                    <th class="th-title">
                                        Aksi
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($aktaTypes as $aktaType)
                                    <tr class="text-center text-sm text-capitalize"">
                                        <td>
                                            {{ $aktaTypes->firstItem() + $loop->index }}
                                        </td>
                                        <td>{{ $aktaType->category }}</td>
                                        <td>{{ $aktaType->type }}
                                        </td>
                                        <td>{{ $aktaType->description }}

                                        </td>
                                        <td>
                                            {{ $aktaType->documents }}

                                        </td>
                                        <td class="">
                                            <a href=" {{ route('akta-types.edit', $aktaType->id) }}"
                                                class="btn btn-info btn-sm mb-0">Edit</a>
                                            <button type="button" class="btn btn-danger btn-sm mb-0" data-bs-toggle="modal"
                                                data-bs-target="#deleteModal{{ $aktaType->id }}">
                                                Hapus
                                            </button>
                                            @include('pages.BackOffice.AktaType.modal.index')
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted text-sm">Belum ada data tipe akta.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <div class="d-flex justify-content-end mt-3">
                            {{ $aktaTypes->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
