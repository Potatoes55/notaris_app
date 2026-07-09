@extends('layouts.app')

@section('title', 'Logs Akta')

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'PPAT / Logs Akta'])
    @include('components.ppat-menu')

    <div class="row mt-4 mx-4">
        <div class="col-12">
            <div class="card mb-0">
                <div class="card-header d-flex justify-content-between align-items-center pb-0">
                    <h5>Logs Akta</h5>
                    <a href="{{ route('relaas-logs.create') }}" class="btn btn-primary btn-sm">+ Tambah Logs Akta</a>
                </div>
                {{-- Filter & Search --}}
                <form method="GET" action="{{ route('relaas-logs.index') }}" class="d-flex gap-2 ms-auto me-3 mb-0"
                    style="max-width:600px;" onchange="this.submit()" class="no-spinner">
                    <input type="text" name="client_code" placeholder="Cari Kode Klien..."
                        value="{{ request('client_code') }}" class="form-control">
                    <input type="text" name="step" placeholder="Cari step..." value="{{ request('step') }}"
                        class="form-control">
                    <button type="submit" class="btn btn-primary btn-sm mb-0">Cari</button>
                </form>
                <hr>
                <div class="card-body px-0 pt-0 pb-0">


                    {{-- Table --}}
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0 text-sm text-center">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Klien</th>
                                    <th>Kode Klien</th>
                                    <th>Step</th>
                                    <th>Catatan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($logs as $log)
                                    <tr>
                                        <td>{{ $logs->firstItem() + $loop->index }}</td>
                                        <td>{{ $log->clients->fullname ?? '-' }}</td>
                                        <td>
                                            <div class="d-flex align-items-center justify-content-center gap-2">
                                                <span>{{ $log->client_code ?? '-' }}</span>

                                                @if($log->client_code)
                                                    <button
                                                        type="button"
                                                        class="btn btn-link p-0 text-primary copy-btn"
                                                        onclick="copyValue(this, '{{ $log->client_code }}')"
                                                        title="Salin Kode Klien">
                                                        <i class="fa-solid fa-copy"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                        <td>{{ $log->step ?? '-' }}</td>
                                        <td>{{ $log->note ?? '-' }}</td>
                                        <td>
                                            <a href="{{ route('relaas-logs.edit', $log->id) }}"
                                                class="btn btn-info btn-sm mb-0">Edit</a>

                                            <button type="button" class="btn btn-danger btn-sm mb-0" data-bs-toggle="modal"
                                                data-bs-target="#deleteModal{{ $log->id }}">
                                                Hapus
                                            </button>

                                            {{-- Modal Delete --}}
                                            @include('pages.BackOffice.RelaasAkta.AktaLogs.Modal.index', [
                                                'log' => $log,
                                            ])
                                            {{-- End Modal --}}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-muted">Belum ada log.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="px-4 mt-3">
                        {{ $logs->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
    <script>
    function copyValue(button, value) {
        navigator.clipboard.writeText(value);

        const icon = button.querySelector('i');

        icon.classList.remove('fa-copy');
        icon.classList.add('fa-check');

        notyf.success('Berhasil disalin');

        setTimeout(() => {
            icon.classList.remove('fa-check');
            icon.classList.add('fa-copy');
        }, 1000);
    }
    </script>
@endsection
