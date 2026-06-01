<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Laporan Covernote</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #000;
            margin: 20px;
        }

        /* Header dengan logo dan informasi kantor */
        .header-top {
            width: 100%;
            padding-bottom: 8px;
            border-bottom: 1px solid #000;
            margin-bottom: 10px;
        }

        .header-top td {
            vertical-align: middle;
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
            /* border-top: 1px solid #000; */
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

        /* Informasi Utama */
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

        /* Tabel biaya */
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

        h3 {
            text-align: center;
        }
    </style>
</head>

<body>
    <table class="header-top">
        <tr>
            <td class="logo">
                <img src="file://{{ public_path('img/logo-ct-dark.png') }}" alt="Logo Notaris"
                    style="width:40px; height:auto;">

                {{-- <img src="data:image/png;base64,{{ $qrCode }}"
                    style="
            width:80px;
            background:#fff;
            padding:10px;
            border:1px solid #000;
         "> --}}
            </td>
            <td class="company-info">
                <h3>Notaris App</h3>
                <p>{{ $notaris->office_name }}</p>
                <p>{{ $notaris->office_address }}</p>
                <p>{{ $notaris->phone }}</p>
            </td>
        </tr>
    </table>

<h3>Laporan Data Covernote</h3>

    <table>
        <thead>
            <tr>
                <th style="width: 30pt;">No</th>
                <th style="width: 90pt;">Nomor<br>Covernote</th>
                <th style="width: 80pt;">Kode<br>Klien</th>
                <th style="width: 120pt;">Nama<br>Klien</th>
                <th style="width: 90pt;">Penerima</th>
                <th style="width: 100pt;">Perihal /<br>Subjek</th>
                <th style="width: 60pt;">Tanggal<br>Surat</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($covernotes as $i => $row)
                <tr>
                    <td style="text-align: center;">{{ $i + 1 }}</td>
                    <td>{{ $row->covernote_number }}</td>
                    <td>{{ $row->client_code ?? '-' }}</td>
                    <td>{{ $row->client->fullname ?? '-' }}</td>
                    <td>{{ $row->recipient ?? '-' }}</td>
                    <td>{{ $row->subject ?? '-' }}</td>
                    <td style="text-align: center;">
                        {{ $row->date ? \Carbon\Carbon::parse($row->date)->format('d-m-Y') : '-' }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>