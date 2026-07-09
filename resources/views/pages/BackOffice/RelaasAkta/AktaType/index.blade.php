@extends('layouts.app')

@section('title', 'Jenis Akta')

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'PPAT / Jenis Akta'])
    @include('components.ppat-menu')
    <div class="row mt-4 mx-4">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center mb-3 px-3">
                    <h5 class="mb-0">Jenis Akta</h5>
                    <a href="{{ route('relaas-types.create') }}" class="btn btn-primary btn-sm mb-0">
                        + Tambah Jenis Akta
                    </a>
                </div>
                <div class="d-flex justify-content-end w-100 px-2">
                    <form method="GET" action="{{ route('relaas-types.index') }}" class="d-flex gap-2 w-100"
                        style="max-width: 400px;" class="no-spinner">
                        <input type="text" name="search" placeholder="Cari tipe akta" value="{{ request('search') }}"
                            class="form-control">
                        <button type="submit" class="btn btn-primary btn-sm mb-0">Cari</button>
                    </form>
                </div>
                <hr>
                <div class="card-body px-0 pt-0 pb-0">

                    <div class="table-responsive">
                        <table class="table align-items-center mb-0">

                            <thead>
                                <tr>
                                    <th class="text-center align-middle" style="width:70px;">#</th>
                                    <th class="text-center align-middle">Kategori</th>
                                    <th class="text-center align-middle">Tipe</th>
                                    <th class="text-center align-middle">Deskripsi</th>
                                    <th class="text-center align-middle">Dokumen</th>
                                    <th class="text-center align-middle" style="width:180px;">Aksi</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse ($aktaTypes as $aktaType)
                                    <tr>

                                        <td class="text-center align-middle">
                                            <p class="text-sm mb-0">
                                                {{ $aktaTypes->firstItem() + $loop->index }}
                                            </p>
                                        </td>

                                        <td class="text-center align-middle">
                                            <p class="text-sm text-capitalize mb-0">
                                                {{ $aktaType->category }}
                                            </p>
                                        </td>

                                        <td class="text-center align-middle">
                                            <p class="text-sm text-capitalize mb-0">
                                                {{ $aktaType->type }}
                                            </p>
                                        </td>

                                        <td class="text-center align-middle">
                                            <p class="text-sm mb-0">
                                                {{ $aktaType->description }}
                                            </p>
                                        </td>

                                        <td class="text-center align-middle">
                                            <p class="text-sm mb-0">
                                                {{ $aktaType->documents }}
                                            </p>
                                        </td>

                                        <td class="text-center align-middle">
                                            <a href="{{ route('relaas-types.edit', $aktaType->id) }}"
                                                class="btn btn-info btn-xs mb-0">
                                                Edit
                                            </a>

                                            <button
                                                type="button"
                                                class="btn btn-danger btn-xs mb-0"
                                                data-bs-toggle="modal"
                                                data-bs-target="#deleteModal{{ $aktaType->id }}">
                                                Hapus
                                            </button>

                                            @include('pages.BackOffice.RelaasAkta.AktaType.modal.index')
                                        </td>

                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">
                                            Belum ada data tipe akta.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>

                        </table>
                    </div>

                    <div class="d-flex justify-content-end px-4 py-3">
                        {{ $aktaTypes->links() }}
                    </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
