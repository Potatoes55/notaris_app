<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Laporan Pembayaran</title>
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
            /* border-bottom: 1px solid #000; */
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
        }

        th {
            background-color: #f2f2f2;
            text-align: center;
        }

        td.amount {
            text-align: right;
        }

        .total {
            font-weight: bold;
            background-color: #f9f9f9;
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

        /* FOOTER */
        .footer {
            margin-top: 50px;
            width: 100%;
            font-size: 11px;
            clear: both;
        }

        .footer .left {
            float: left;
            width: 45%;
            text-align: center;
        }

        .footer .right {
            float: right;
            width: 45%;
            text-align: center;
        }

        .signature-space {
            margin-top: 60px;
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
                <p>{{ $notaris->office_name }}</p>
                <p>{{ $notaris->office_address }}</p>
                <div>Telp: {{ $notaris->phone }}</div>
            </td>
        </tr>
    </table>

    <!-- JUDUL -->
    <div class="info">
        <table class="info-table">
            <tr>
                <td><strong>Periode</strong></td>
                <td>{{ request('start_date') ?? '-' }} s/d {{ request('end_date') ?? '-' }}</td>
                <td><strong>Tanggal Cetak</strong></td>
                <td>{{ now()->format('d F Y') }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold">Notaris</td>
                <td>{{ $notaris->display_name ?? '-' }}</td>
                
                <td>Kode Dokumen</td>
                <td>{{ $costs->first()->picDocument->pic_document_code ?? '-' }}</td>
            </tr>
        </table>
    </div>

<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Kode Pembayaran</th>
            <th>Nama Klien</th>
            <th>Tanggal Pelunasan</th>
            <th>Total Biaya</th>
            <th>Total Pembayaran</th>
            <th>Piutang</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($costs as $payment)
            @php
                $status = $payment->payment_status;
                $statusText = match ($status) {
                    'full' => 'Lunas',
                    'partial' => 'Bayar Sebagian',
                    'dp' => 'DP',
                    default => ucfirst($status),
                };
            @endphp
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $payment->payment_code }}</td>
                <td>{{ $payment->client->fullname ?? '-' }}</td>
                <td>{{ $payment->paid_date ? \Carbon\Carbon::parse($payment->paid_date)->format('d-m-Y') : '-' }}</td>
                <td class="amount">Rp {{ number_format($payment->total_cost, 0, ',', '.') }}</td>
                <td class="amount">Rp {{ number_format($payment->amount_paid, 0, ',', '.') }}</td>
                <td class="amount">Rp {{ number_format($payment->total_cost - $payment->amount_paid, 0, ',', '.') }}</td>
                <td>{{ $statusText }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="8" style="text-align:center;">Tidak ada data ditemukan untuk periode ini</td>
            </tr>
        @endforelse
    </tbody>
</table>
</body>

</html>
