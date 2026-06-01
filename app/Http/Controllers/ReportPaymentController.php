<?php

namespace App\Http\Controllers;

use App\Models\NotaryCost;
use App\Models\NotaryPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Mpdf\Mpdf;


class ReportPaymentController extends Controller
{
    // public function index(Request $request)
    // {
    //     $costs = collect(); // default kosong

    //     // cek apakah ada filter
    //     if ($request->filled('start_date') || $request->filled('end_date') || $request->filled('status')) {
    //         $query =  NotaryCost::with('client');

    //         if ($request->filled('start_date')) {
    //             $query->whereDate('created_at', '>=', $request->start_date);
    //         }

    //         if ($request->filled('end_date')) {
    //             $query->whereDate('created_at', '<=', $request->end_date);
    //         }

    //         if ($request->status && $request->status != 'all') {
    //             $query->where('payment_status', $request->status);
    //         }

    //         $costs = $query->latest()->get();
    //     }

    //     return view('pages.Laporan.index', compact('costs'));
    // }

    public function index(Request $request)
    {
        $costs = collect(); // default kosong

        if ($request->filled('start_date') || $request->filled('end_date') || $request->filled('status')) {
            $query = NotaryPayment::with('client')->where('notaris_id', auth()->user()->notaris_id);

            if ($request->filled('start_date')) {
                $query->whereDate('payment_date', '>=', $request->start_date);
            }

            if ($request->filled('end_date')) {
                $query->whereDate('payment_date', '<=', $request->end_date);
            }

            if ($request->filled('status') && $request->status != 'all') {
                $query->whereHas('cost', function ($q) use ($request) {
                    $q->where('payment_status', $request->status);
                });
            }

            $costs = $query->latest()->get();
        }

        return view('pages.Laporan.index', compact('costs'));
    }

public function print(Request $request)
{
    // 1. Samakan query dengan yang ada di index agar datanya konsisten
    $query = NotaryCost::query()->where('notaris_id', auth()->user()->notaris_id);

    if ($request->filled('start_date')) {
        $query->whereDate('created_at', '>=', $request->start_date);
    }
    if ($request->filled('end_date')) {
        $query->whereDate('created_at', '<=', $request->end_date);
    }

    if ($request->status && $request->status != 'all') {
        $query->where('payment_type', $request->status);
    }

    $costs = $query->latest()->get();

    // 2. VALIDASI: Jika data kosong, gagalkan proses cetak
    if ($costs->isEmpty()) {
        notyf()
            ->position('x', 'right')
            ->position('y', 'top')
            ->error('Tidak dapat mencetak laporan: Data tidak ditemukan untuk periode ini.');
            
        return redirect()->back();
    }

    $notaris = auth()->user()->notaris;

    // 3. Lanjutkan proses mPDF jika data ada
    $html = View::make('pages.Laporan.print', compact('costs', 'notaris'))->render();

    $mpdf = new Mpdf([
        'format'  => 'A4',
        'tempDir' => storage_path('app/mpdf-temp'),
    ]);

    $mpdf->WriteHTML($html);

    return response($mpdf->Output('Laporan-Pembayaran.pdf', 'I'))
        ->header('Content-Type', 'application/pdf');
}
}
