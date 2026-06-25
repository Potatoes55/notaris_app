@extends('layouts.app')

@section('title', 'Log Akta')

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Akta Notaris / Log Akta'])
    @include('components.notaris-menu')

    <div class="row mt-4 mx-4">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center pb-0">
                    <h5>Log Akta</h5>
                    <a href="{{ route('akta-logs.create') }}" class="btn btn-primary btn-sm">+ Tambah Log Akta</a>
                </div>
                <form method="GET" action="{{ route('akta-logs.index') }}" class="d-flex gap-2  justify-content-end me-4"
                    style="max-width: 500px; margin-left: auto;" class="no-spinner">
                    <input type="text" name="client_code" class="form-control" placeholder="Cari Kode Klien..."
                        value="{{ $filters['client_code'] ?? '' }}">
                    <input type="text" name="step" class="form-control" placeholder="Cari step..."
                        value="{{ $filters['step'] ?? '' }}">
                    <button type="submit" class="btn btn-primary btn-sm mb-0">Cari</button>
                </form>

                <hr>
                <div class="card-body pt-0 pb-0">

                    <div class="table-responsive">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Notaris</th>
                                    <th>Klien</th>
                                    <th>Transaksi Akta</th>
                                    <th>Kode Klien</th>
                                    <th>Step</th>
                                    <th>Note</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($logs as $log)
                                    <tr class="text-center text-sm">
                                        <td>{{ $logs->firstItem() + $loop->index }}</td>
                                        <td>{{ $log->notaris->display_name ?? '-' }}</td>
                                        <td>{{ $log->client->fullname ?? '-' }}</td>
                                        <td>
                                            <div class="d-flex justify-content-center align-items-center gap-2">
                                                <span>{{ $log->akta_transaction->client_code ?? '-' }}</span>

                                                @if($log->akta_transaction?->client_code)
                                                    <button
                                                        type="button"
                                                        class="btn btn-link p-0 text-primary"
                                                        onclick="copyValue(this, '{{ $log->akta_transaction->client_code }}')">
                                                        <i class="fa-solid fa-copy"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>

                                        <td>
                                            <div class="d-flex justify-content-center align-items-center gap-2">
                                                <span>{{ $log->client_code }}</span>

                                                <button
                                                    type="button"
                                                    class="btn btn-link p-0 text-primary"
                                                    onclick="copyValue(this, '{{ $log->client_code }}')">
                                                    <i class="fa-solid fa-copy"></i>
                                                </button>
                                            </div>
                                        </td>
                                        <td>{{ $log->step }}</td>
                                        <td>{{ $log->note }}</td>
                                        <td class="text-center">
                                            <a href="{{ route('akta-logs.edit', $log->id) }}"
                                                class="btn btn-info btn-sm mb-0">Edit</a>
                                            <button type="button" class="btn btn-danger btn-sm mb-0" data-bs-toggle="modal"
                                                data-bs-target="#deleteModal{{ $log->id }}">
                                                Hapus
                                            </button>
                                        </td>
                                    </tr>

                                    <!-- Delete Modal -->
                                    <div class="modal fade" id="deleteModal{{ $log->id }}" tabindex="-1"
                                        aria-labelledby="deleteModalLabel{{ $log->id }}" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="deleteModalLabel{{ $log->id }}">Hapus
                                                        Log</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    Apakah Anda yakin ingin menghapus log ini?
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Batal</button>
                                                    <form action="{{ route('akta-logs.destroy', $log->id) }}"
                                                        method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger">Hapus</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted text-sm">Belum ada data log.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <div class="d-flex justify-content-end  mt-3">
                            {{ $logs->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @push('js')
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
    @endpush
@endsection
