@extends('layouts.app')

@section('title', 'Jenis Akta')

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Akta Notaris / Jenis Akta'])
    @include('components.notaris-menu')

    <div class="row mt-4 mx-4">
        <div class="col-md-12">

            <div class="card">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center mb-3 px-3 flex-wrap">
                    <h5>Jenis Akta</h5>

                    <a href="{{ route('akta-types.create') }}" class="btn btn-primary btn-sm mb-0">
                        + Tambah Jenis Akta
                    </a>
                </div>

                <div class="d-flex justify-content-end w-100 px-2">
                    <form method="GET"
                        action="{{ route('akta-types.index') }}"
                        class="d-flex gap-2 w-100 no-spinner"
                        style="max-width:400px;">

                        <input
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            class="form-control"
                            placeholder="Cari tipe akta">

                        <button type="submit" class="btn btn-primary btn-sm mb-0">
                            Cari
                        </button>
                    </form>
                </div>

                <hr>

                <div class="card-body px-0 pt-0 pb-0 mt-2">

                    <div class="table-responsive p-0">
                        <table class="table table-hover mb-0">

                            <thead>
                                <tr class="text-center">
                                    <th>#</th>
                                    <th>Kategori</th>
                                    <th>Tipe</th>
                                    <th>Deskripsi</th>
                                    <th>Dokumen</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse ($aktaTypes as $aktaType)
                                    <tr class="text-center text-sm text-capitalize">

                                        <td>
                                            {{ $aktaTypes->firstItem() + $loop->index }}
                                        </td>

                                        <td>
                                            {{ $aktaType->category }}
                                        </td>

                                        <td>
                                            {{ $aktaType->type }}
                                        </td>

                                        <td>
                                            {{ $aktaType->description }}
                                        </td>

                                        <td>
                                            {{ $aktaType->documents }}
                                        </td>

                                        <td>
                                            <a href="{{ route('akta-types.edit', $aktaType->id) }}"
                                                class="btn btn-info btn-sm mb-0">
                                                Edit
                                            </a>

                                            <button
                                                type="button"
                                                class="btn btn-danger btn-sm mb-0"
                                                data-bs-toggle="modal"
                                                data-bs-target="#deleteModal{{ $aktaType->id }}">
                                                Hapus
                                            </button>

                                            @include('pages.BackOffice.AktaType.modal.index')
                                        </td>

                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted text-sm">
                                            Belum ada data tipe akta.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>

                        </table>

                        <div class="mt-3 d-flex justify-content-end">
                            {{ $aktaTypes->links() }}
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
@endsection