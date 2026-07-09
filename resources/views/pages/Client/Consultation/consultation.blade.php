@extends('layouts.app')

@section('title', 'Konsultasi')

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Konsultasi'])

    <div class="row mt-4 mx-4">
        <div class="col-md-12">

            <div class="card">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center mb-3 px-3 flex-wrap">
                    <h5>Konsultasi</h5>

                    <a href="{{ route('consultation.create', ['client_id' => $client->id]) }}"
                        class="btn btn-primary btn-sm mb-0">
                        + Tambah Konsultasi
                    </a>
                </div>

                <hr>

                <div class="card-body px-0 pt-0 pb-0 mt-2">

                    <div class="table-responsive p-0">
                        <table class="table table-hover mb-0">

                            <thead>
                                <tr class="text-center">
                                    <th>#</th>
                                    <th>Klien</th>
                                    <th>Kode Klien</th>
                                    <th>Subjek</th>
                                    <th>Deskripsi</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse ($notaryConsultations as $notaryconsultation)
                                    <tr class="text-center text-sm">

                                        <td>
                                            {{ $notaryConsultations->firstItem() + $loop->index }}
                                        </td>

                                        <td>
                                            {{ $notaryconsultation->client->fullname }}
                                        </td>

                                        <td>
                                            <div class="d-flex justify-content-center align-items-center gap-2">
                                                <span>{{ $notaryconsultation->client_code ?? '-' }}</span>

                                                @if($notaryconsultation->client_code)
                                                    <button
                                                        type="button"
                                                        class="btn btn-link p-0 text-primary"
                                                        onclick="copyValue(this, '{{ $notaryconsultation->client_code }}')"
                                                        title="Salin Kode Klien">
                                                        <i class="fa-solid fa-copy"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>

                                        <td>
                                            {{ $notaryconsultation->subject }}
                                        </td>

                                        <td>
                                            {{ $notaryconsultation->description ?? '-' }}
                                        </td>

                                        <td>
                                            <span class="badge text-capitalize mb-0
                                                @if($notaryconsultation->status == 'done')
                                                    bg-success
                                                @elseif($notaryconsultation->status == 'progress')
                                                    bg-warning
                                                @else
                                                    bg-secondary
                                                @endif">
                                                @if($notaryconsultation->status == 'done')
                                                    Selesai
                                                @elseif($notaryconsultation->status == 'progress')
                                                    Sedang Diproses
                                                @else
                                                    {{ ucfirst($notaryconsultation->status) }}
                                                @endif
                                            </span>
                                        </td>

                                        <td>
                                            <a href="{{ route('consultation.edit', $notaryconsultation->id) }}"
                                                class="btn btn-info btn-xs mb-0">
                                                Edit
                                            </a>
                                        </td>

                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted text-sm">
                                            Belum ada data Konsultasi.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>

                        </table>

                        <div class="mt-3 d-flex justify-content-end">
                            {{ $notaryConsultations->links() }}
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