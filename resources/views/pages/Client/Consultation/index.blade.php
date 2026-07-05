@extends('layouts.app')

@section('title', 'Konsultasi')

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Konsultasi'])
    @include('components.konsultasi-menu')

    <div class="row mt-4 mx-4">
        <div class="col-md-12">

            <div class="card">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center mb-3 px-3 flex-wrap">
                    <h5>Konsultasi</h5>

                    <div style="width:320px; max-width:100%;">
                        <form method="GET" action="{{ route('consultation.index') }}" class="no-spinner">
                            <div class="input-group">
                                <input
                                    type="text"
                                    name="search"
                                    value="{{ request('search') }}"
                                    class="form-control"
                                    placeholder="Cari nama klien...">

                                <button type="submit" class="btn btn-primary btn-sm mb-0">
                                    Cari
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <hr>

                <div class="card-body px-0 pt-0 pb-0 mt-2">

                    @if ($clients->count())

                        <div class="table-responsive p-0">
                            <table class="table table-hover mb-0">

                                <thead>
                                    <tr class="text-center">
                                        <th>#</th>
                                        <th>Nama Klien</th>
                                        <th>Nama Perusahaan</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach ($clients as $client)
                                        <tr class="text-center text-sm">

                                            <td>
                                                {{ $clients->firstItem() + $loop->index }}
                                            </td>

                                            <td class="text-capitalize">
                                                {{ $client->fullname }}
                                            </td>

                                            <td class="text-capitalize">
                                                {{ $client->company_name ?? '-' }}
                                            </td>

                                            <td>
                                                <a href="{{ route('consultation.getConsultationByClient', $client->id) }}"
                                                    class="btn btn-outline-primary btn-sm rounded-pill mb-0">
                                                    Lihat Konsultasi
                                                </a>
                                            </td>

                                        </tr>
                                    @endforeach
                                </tbody>

                            </table>

                            <div class="mt-3 d-flex justify-content-end">
                                {{ $clients->links() }}
                            </div>
                        </div>

                    @else

                        <div class="text-center text-muted py-5">
                            <p class="mb-0">Belum ada data klien untuk konsultasi.</p>
                        </div>

                    @endif

                </div>

            </div>

        </div>
    </div>
@endsection