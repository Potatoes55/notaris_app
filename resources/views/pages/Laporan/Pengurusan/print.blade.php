<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Pengurusan</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #000;
            margin: 20px;
        }

        /* HEADER ATAS (Logo dan Info Perusahaan) */
        .header-top {
            width: 100%;
            padding-bottom: 8px;
            margin-bottom: 10px;
        }

        .header-top td {
            vertical-align: middle;
            border: none;
        }

        .logo img {
            width: 50px;
            height: auto;
        }

        .company-info {
            text-align: right;
            font-size: 11px;
            line-height: 1.4;
        }

        .company-info h3 {
            margin: 0;
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
        }

        /* JUDUL LAPORAN */
        .header {
            text-align: center;
            margin-top: 10px;
            margin-bottom: 10px;
            border-top: 1px solid #000;
            padding-top: 10px;
        }

        .header h2 {
            margin: 0;
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
        }

        /* INFORMASI TAMBAHAN */
        .info {
            margin-bottom: 25px;
            margin-top: 15px;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
            border: none;
        }

        .info-table td {
            padding: 5px 8px;
            vertical-align: top;
        }

        .info-table tr td:first-child,
        .info-table tr td:nth-child(3) {
            width: 22%;
            font-weight: bold;
        }

        .info-table tr td:nth-child(2),
        .info-table tr td:nth-child(4) {
            width: 28%;
        }

        .info-table tr:not(:last-child) td {
            border-bottom: 1px solid #e0e0e0;
        }

        /* TABEL DATA */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 6px 8px;
            font-size: 11px;
            text-align: center;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        /* BADGE STATUS */
        .badge {
            display: inline-block;
            padding: 3px 6px;
            border-radius: 4px;
            font-size: 10px;
            color: #fff;
        }

        .bg-success {
            background-color: #28a745;
        }

        .bg-warning {
            background-color: #ffc107;
            color: #000;
        }

        .bg-info {
            background-color: #17a2b8;
        }

        .bg-secondary {
            background-color: #6c757d;
        }
    </style>
</head>

<body>
    <!-- HEADER DENGAN LOGO DAN INFO PERUSAHAAN -->
    <table class="header-top">
        <tr>
            <td class="logo">
                <img src="file://{{ public_path('img/logo-ct-dark.png') }}" alt="Logo Notaris"
                    style="width:40px; height:auto;">
            </td>
            <td class="company-info">
                <h3>Notaris App</h3>
                <div>Jl. Jenderal Sudirman No. 123, Jakarta</div>
                <div>Telp: (021) 123-4567</div>
            </td>
        </tr>
    </table>

    <!-- JUDUL -->
    <div class="header">
        <h2 style="text-transform: capitalize">Laporan Pengurusan</h2>
    </div>

    <!-- INFORMASI TAMBAHAN -->
    <div class="info">
        <table class="info-table">
            <tr>
                <td><strong>Periode</strong></td>
                <td>{{ request('start_date') ?? '-' }} s/d {{ request('end_date') ?? '-' }}</td>
                <td><strong>Tanggal Cetak</strong></td>
                <td>{{ now()->format('d F Y') }}</td>
            </tr>
            <tr>
                <td>Notaris</td>
                {{-- Ganti $processes[0] jadi $processes->first() --}}
                <td>{{ $processes->first()->notaris->display_name ?? '-' }}</td>
                <td>Kode</td>
                <td>{{ $processes->first()->pic_document->pic_document_code ?? '-' }}</td>
            </tr>
        </table>
    </div>

    <!-- TABEL DATA -->
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kode Dokumen</th>
                <th>PIC</th>
                <th>Nama Klien</th>
                <th>Proses</th>
                <th>Tanggal Pengurusan</th>
                <th>Status</th>
                <th>Catatan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($processes as $process)
                @php
                    switch ($process->step_status) {
                        case 'done':
                            $statusText = 'Selesai';
                            $statusColor = 'success';
                            break;
                        case 'in_progress':
                            $statusText = 'Sedang Diproses';
                            $statusColor = 'info';
                            break;
                        case 'pending':
                        default:
                            $statusText = 'Pending';
                            $statusColor = 'warning';
                            break;
                    }
                @endphp
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $process->pic_document->pic_document_code }}</td>
                    <td>{{ $process->pic_document->pic->full_name ?? '-' }}</td>
                    <td>{{ $process->pic_document->client->fullname ?? '-' }}</td>
                    <td>{{ $process->step_name ?? '-' }}</td>
                    <td>{{ \Carbon\Carbon::parse($process->step_date)->format('d-m-Y') }}</td>
                    <td>{{ $statusText }}</td>
                    <td>{{ $process->note ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align:center;">Tidak ada data laporan</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>

</html>
