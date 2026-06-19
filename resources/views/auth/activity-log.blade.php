@extends('layouts.app')

@section('title', 'User Activity Log')

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Back Office / Activity Log'])

    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    
                    {{-- <div class="card-header pb-0 d-flex justify-content-between align-items-center mb-4">
                        <h5 class="mb-0">User Activity Log</h5>
                        <p class="text-xs text-muted mb-0">Memantau rekam jejak aksi user pada sistem aplikasi</p>
                        <a href="{{ route('activity_logs.print') }}" class="btn btn-sm btn-danger mb-0" target="_blank">
                        <i class="bi bi-filetype-pdf"></i> Cetak PDF
                    </a>
                    </div> --}}

                    <form action="{{ route('activity_logs.print') }}" method="GET" target="_blank" class="form-inline mb-3">
                    <div class="card-header pb-0 d-flex justify-content-between align-items-center mb-4">
                        <label for="date" class="mr-2">Pilih Tanggal Cetak:</label>
                        <input type="date" name="date" id="date" class="form-control" value="{{ request('date', date('Y-m-d')) }}">
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-print"></i> Cetak PDF Hari Ini/Terpilih
                    </button>
                </form>
                    
                    <hr class="horizontal dark my-0">
                    
                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive p-0">
                            <table class="table align-items-center mb-0" style="width: 100%;">
                                <thead>
                                    <tr class="text-xs font-weight-bold opacity-7 text-secondary">
                                        <th class="ps-4">Waktu</th>
                                        <th class="ps-3">Pengguna</th>
                                        <th class="ps-3">Menu Yang Diakses</th>                   
                                        <th class="ps-3">IP Address</th>
                                        <th class="ps-3">Aktivitas</th>
                                        <th class="ps-3">Deleted At</th>
                                        <th class="ps-3">Created At</th>
                                        <th class="ps-3">Updated At</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($activities as $activity)
                                    <tr class="text-sm">
                                        
                                        <td class="ps-4 align-middle">
                                            <span class="text-secondary text-xs font-weight-bold">
                                                {{ $activity->created_at ? $activity->created_at->format('d M Y H:i:s') : '-' }}
                                            </span>
                                        </td>
                                        
                                        <td class="ps-3 align-middle">
                                            <div class="d-flex flex-column justify-content-center">
                                                <h6 class="mb-0 text-sm font-weight-normal">{{ $activity->causer?->name ?? 'Sistem/Guest' }}</h6>
                                                <p class="text-xs text-secondary mb-0">{{ $activity->causer?->email ?? '-' }}</p>
                                            </div>
                                        </td>
                                        
                                        <td class="ps-3 align-middle">
                                            <span class="badge bg-gradient-light text-dark text-xs px-2 py-1 font-weight-bold">
                                                {{ $activity->menu ?? $activity->log_name ?? '-' }}
                                            </span>
                                        </td>
                                        
                                        <td class="ps-3 align-middle">
                                            <span class="text-secondary text-xs font-weight-normal" style="font-family: monospace;">
                                                {{ $activity->ip_address ?? '127.0.0.1' }}
                                            </span>
                                        </td>
                                        
                                        <td class="ps-3 align-middle text-wrap" style="max-width: 250px;">
                                            <p class="text-xs text-secondary font-weight-bold mb-0" style="white-space: normal; line-height: 1.4;">
                                                {{ $activity->description }}
                                            </p>
                                        </td>
                                        
                                        {{-- <td class="ps-3 align-middle">
                                            @if($activity->changes && count($activity->changes) > 0)
                                                <details class="bg-light p-2 rounded cursor-pointer" style="min-width: 110px; border: 1px solid #dee2e6;">
                                                    <summary class="text-info font-weight-bold text-xs hover:underline" style="outline: none;">Lihat Data</summary>
                                                    <pre class="mt-2 text-white bg-dark p-2 rounded overflow-x-auto text-start" style="font-size: 11px; max-height: 150px; white-space: pre-wrap;">{{ json_encode($activity->changes, JSON_PRETTY_PRINT) }}</pre>
                                                </details>
                                            @else
                                                <span class="text-muted text-xs font-italic">Tidak ada perubahan</span>
                                            @endif
                                        </td> --}}
                                        
                                        <td class="ps-3 align-middle text-secondary text-xs">
                                            {{ $activity->deleted_at ? $activity->deleted_at->format('d M Y H:i:s') : '-' }}
                                        </td>
                                        
                                        <td class="ps-3 align-middle text-secondary text-xs">
                                            {{ $activity->created_at ? $activity->created_at->format('d M Y H:i:s') : '-' }}
                                        </td>
                                        
                                        <td class="ps-3 align-middle text-secondary text-xs">
                                            {{ $activity->updated_at ? $activity->updated_at->format('d M Y H:i:s') : '-' }}
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="9" class="text-center text-muted py-4 text-sm">
                                            Belum ada rekaman aktivitas pengguna.
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                            
                            <div class="d-flex justify-content-end px-4 py-3">
                                {{ $activities->links() }}
                            </div>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
@endsection