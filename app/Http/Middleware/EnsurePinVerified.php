<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePinVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Ambil transaction_code dari URL route saat ini
        $transactionCode = $request->route('transaction_code');

        // Cek apakah di session sudah tersimpan status verifikasi untuk kode transaksi ini
        if (! session()->has("pin_verified_{$transactionCode}")) {
            // Simpan URL yang ingin dituju agar setelah isi PIN bisa kembali ke sini
            session(['url.intended' => $request->fullUrl()]);

            // Oper ke halaman input PIN bawa serta transaction_code nya
            return redirect()->route('akta.qr.pin.form', $transactionCode)
                ->with('error', 'Silakan masukkan kode akses untuk melihat dokumen.');
        }

        return $next($request);
    }
}
