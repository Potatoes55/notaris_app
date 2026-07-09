@extends('layouts.app')

@section('title', 'Log')

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Log'])
    @include('components.proseslain-menu')
    <div class="row mt-4 mx-4">
        <div class="col-12">
            <div class="card mb-0">
                <div class="card-header pb-0 mb-0 ">
                    <div class="d-flex justify-content-between align-items">
                        <h5 class="mb-0">Progress</h5>
                        <form method="GET" action="{{ route('proses-lain-progress.index') }}" class="d-flex  gap-2"
                            style="width:500px;">
                            <input type="text" name="search" placeholder="Cari kode transaksi"
                                value="{{ request('search') }}" class="form-control">
                            <button type="submit" class="btn btn-primary btn-sm mb-0">Cari</button>
                        </form>
                    </div>

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
                                        Notaris
                                    </th>
                                    <th class="th-title">
                                        Klien
                                    </th>
                                    <th class="th-title">
                                        Nama Transaksi
                                    </th>
                                    <th>
                                        Pic
                                    </th>
                                    <th class="th-title">
                                        Progress
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($prosesLain as $document)
                                    <tr class="text-center text-sm">
                                        <td>
                                            <p class="text-sm mb-0 text-center">
                                                {{ $prosesLain->firstItem() + $loop->index }}
                                            </p>
                                        </td>
                                        <td>
                                            <p class="text-sm mb-0  text-center"> {{ $document->notaris->display_name }}
                                            </p>
                                        </td>
                                        <td>
                                            <p class="text-sm mb-0  text-center"> {{ $document->client->fullname }}
                                            </p>
                                        </td>
                                        <td>
                                            <p class="text-sm mb-0  text-center">{{ $document->name }}
                                            </p>
                                        </td>
                                        <td>
                                            <p class="text-sm mb-0  text-center">
                                                {{ $document->picDocument->pic->full_name ?? '-' }}
                                            </p>
                                        </td>
                                        <td>
                                            {{ $document->status }}
                                        </td>

                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted text-sm">Belum ada data progress.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <div class="d-flex justify-content-end mt-3">
                            {{-- {{ $prosesLain->withQueryString()->links() }} --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
