<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Total Biaya</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #000;
            margin: 20px;
        }

        /* Header dengan logo dan informasi kantor (gunakan tabel agar sejajar di mPDF) */
        .header-top {
            width: 100%;
            /* border-bottom: 2px solid #000; */
            padding-bottom: 8px;
            /* border: none; */
            border-bottom: : 1px solid #000;
            margin-bottom: 10px;
        }

        .header-top td {
            vertical-align: middle;
            border-bottom: : 1px solid #000;
            border: none;
        }

        .logo img {
            width: 40px;
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

        .header {
            text-align: center;
            margin-top: 10px;
            margin-bottom: 10px;
            border-top: 1px solid #000;
        }

        .header h2 {
            margin: 0;
            font-size: 18px;
            font-weight: bold;
            text-transform: uppercase;
            display: inline-block;
            margin-top: 10px;
            padding-bottom: 5px;
        }

        .info {
            margin-bottom: 25px;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
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

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 7px 10px;
            font-size: 11px;
        }

        th {
            background-color: #eaeaea;
            text-align: center;
        }

        td.amount {
            text-align: right;
        }

        .total {
            font-weight: bold;
            background-color: #f5f5f5;
        }

        .footer {
            margin-top: 60px;
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
            margin-top: 70px;
        }
    </style>
</head>

<body>
    <table class="header-top">
        <tr>
            <td class="logo">
                <img src="file://{{ public_path('img/logo-ct-dark.png') }}" alt="Logo Notaris"
                    style="width:40px; height:auto;">
            </td>
            <td class="company-info">
                <h3>Notaris App</h3>
                <p>Jl. Melati No. 45, Jakarta Selatan</p>
                <p>Telp: (021) 123-4567</p>
            </td>
        </tr>
    </table>

    <div class="header">
        <h2>Total Biaya</h2>
    </div>

    {{-- Informasi utama --}}
    <div class="info">
        <table class="info-table">
            <tr>
                <td><strong>Kode Pembayaran</strong></td>
                <td>{{ $costs->payment_code }}</td>
                <td><strong>Notaris</strong></td>
                <td>{{ $costs->notaris->display_name ?? '-' }}</td>
            </tr>
            <tr>
                <td><strong>Klien</strong></td>
                <td>{{ $costs->client->fullname ?? '-' }}</td>
                <td><strong>Status Pembayaran</strong></td>
                <td>
                    @if ($costs->payment_status == 'unpaid')
                        Belum Dibayar
                    @elseif($costs->payment_status == 'partial')
                        Sebagian Dibayar
                    @else
                        Lunas
                    @endif
                </td>
            </tr>
        </table>
    </div>

    {{-- Rincian biaya --}}
    <table>
        <thead>
            <tr>
                <th>Keterangan</th>
                <th class="amount">Total (Rp)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Biaya Produk/Jasa</td>
                <td class="amount">{{ number_format($costs->product_cost, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Biaya Administrasi</td>
                <td class="amount">{{ number_format($costs->admin_cost, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Biaya Lain-lain</td>
                <td class="amount">{{ number_format($costs->other_cost, 0, ',', '.') }}</td>
            </tr>
            <tr class="total">
                <td>Total Biaya</td>
                <td class="amount" style="text-align: right;">
                    {{ number_format($costs->total_cost, 0, ',', '.') }}
                </td>
            </tr>
            <tr>
                <td>Jumlah Pembayaran</td>
                <td class="amount" style="text-align: right;">
                    {{ number_format($costs->amount_paid, 0, ',', '.') }}
                </td>
            </tr>
            <tr>
                <td>Piutang</td>
                <td class="amount" style="text-align: right;">
                    {{ number_format($costs->total_cost - $costs->amount_paid, 0, ',', '.') }}
                </td>
            </tr>
        </tbody>
    </table>

    {{-- Tanda tangan --}}
    <div class="footer">
        <div class="left">
            <p>Magelang, {{ now()->format('d F Y') }}</p>
            <p class="signature-space">_________________________<br>Notaris</p>
        </div>
        <div class="right">
            <p>Mengetahui,</p>
            <p class="signature-space">_________________________<br>Klien</p>
        </div>
        <div style="text-align: center; margin-top: 30px;">
            <div style="margin-bottom: 5px;">QR Code Notaris</div>
            
            <div style="border: 1px solid #ccc; padding: 5px; display: inline-block; background-color: #f8f9fa;">
                <img src="data:image/svg+xml;base64,{{ $qrCode }}" alt="QR Code Notaris" style="width: 100px; height: 100px;">
            </div>
            
            <div style="font-size: 10px; margin-top: 5px;">
                {{ $costs->notaris->display_name ?? '-' }}
            </div>
        </div>
    </div>
    </div>
</body>

</html>
