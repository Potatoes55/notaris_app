<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Laporan User Activity Log</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { margin: 0; padding: 0; color: #111; }
        .header p { margin: 5px 0 0 0; color: #666; font-size: 12px; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; vertical-align: top; }
        th { background-color: #f2f2f2; font-weight: bold; color: #444; }
        
        .badge { background: #e2e8f0; padding: 2px 5px; border-radius: 3px; font-size: 10px; }
        .text-muted { color: #888; font-size: 10px; }
        pre { margin: 0; white-space: pre-wrap; font-family: monospace; font-size: 9px; }
    </style>
</head>
<body>

    <div class="header">
        <h2>LAPORAN USER ACTIVITY LOG</h2>
        <p>Aplikasi Notaris/PPAT - Dicetak pada: {{ date('d M Y H:i:s') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 12%">Waktu</th>
                <th style="width: 15%">Pengguna</th>
                <th style="width: 12%">Menu</th>
                <th style="width: 10%">IP Address</th>
                <th style="width: 20%">Aktivitas</th>
            </tr>
        </thead>
        <tbody>
            @forelse($activities as $index => $activity)
            <tr>
                <td>{{ $activity->created_at ? $activity->created_at->format('d-m-Y H:i:s') : '-' }}</td>
                <td>
                    <strong>{{ $activity->causer?->name ?? 'Sistem/Guest' }}</strong><br>
                    <span class="text-muted">{{ $activity->causer?->email ?? '-' }}</span>
                </td>
                <td><span class="badge">{{ $activity->menu ?? $activity->log_name ?? '-' }}</span></td>
                <td style="font-family: monospace;">{{ $activity->ip_address ?? '127.0.0.1' }}</td>
                <td>{{ $activity->description }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align: center; color: #999; padding: 20px;">Tidak ada data aktivitas.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>