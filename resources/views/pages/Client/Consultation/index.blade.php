@extends('layouts.app')


@section('title', 'Konsultasi')

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Konsultasi'])
    @include('components.konsultasi-menu')

    <<div class="row mt-4 mx-4 ">
        <div class="col-md-12">
            <div class="card mb-0  shadow-lg pb-0">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center  p-3 flex-wrap ">
                    <h5 class="mb-lg-1 fw-bold">Konsultasi</h5>
                    {{-- search --}}
                    <div class="w-lg-25">
                        <form method="GET" action="{{ route('consultation.index') }}" class="no-spinner">
                            <div class="input-group">
                                <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                                    placeholder="Cari nama klien...">
                                <button class="btn btn-primary mb-0" type="submit">Cari</button>
                            </div>
                        </form>
                    </div>
                </div>
                <hr>
                <div class="card-body pb-0 px-0 pt-0">
                    @if ($clients->count())
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr class="">
                                        <th>#</th>
                                        <th>Nama Klien</th>
                                        <th>Nama Perusahaan</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($clients as $index => $client)
                                        <tr class="text-sm text-center">
                                            <td>{{ $clients->firstItem() + $loop->index }}</td>
                                            <td class="text-capitalize">{{ $client->fullname }}</td>
                                            <td class="text-capitalize">{{ $client->company_name ?? '-' }}</td>
                                            <td class="text-center">
                                                <a href="{{ route('consultation.getConsultationByClient', $client->id) }}"
                                                    class="btn btn-outline-primary btn-sm rounded-pill mb-0">
                                                    Lihat Konsultasi
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- pagination --}}
                        <div class="d-flex justify-content-end mt-2 px-4">
                            {{ $clients->links() }}
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
