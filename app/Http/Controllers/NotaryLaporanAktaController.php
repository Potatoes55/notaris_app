<?php

namespace App\Http\Controllers;

use App\Models\NotaryAktaTransaction;
use App\Models\NotaryRelaasAkta;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Mpdf\Mpdf;

class NotaryLaporanAktaController extends Controller
{
    //

    public function index(Request $request)
    {
        $queryType = $request->get('type');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $status = $request->get('status');
        $notaris = auth()->user()->notaris;

        $data = collect();

        if ($queryType && $startDate && $endDate) {

            $query = null;

            if ($queryType === 'notaris') {
                $query = NotaryAktaTransaction::query();
            } elseif ($queryType === 'ppat') {
                $query = NotaryRelaasAkta::query();
            }

            if ($query) {
                $query->where('notaris_id', auth()->user()->notaris_id)
                    ->whereBetween('created_at', [
                        Carbon::parse($startDate)->startOfDay(),
                        Carbon::parse($endDate)->endOfDay(),
                    ]);

                // 🔥 FILTER STATUS (OPSIONAL)
                if ($status) {
                    $query->where('status', $status);
                }

                $data = $query->get();
            }
        }

        return view('pages.BackOffice.LaporanAkta.index', [
            'data' => $data,
            'queryType' => $queryType,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'status' => $status,
            'notaris' => $notaris,
        ]);
    }

    public function exportPdf(Request $request)
    {
        $queryType = $request->get('type'); // partij / relaas
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $notaris = auth()->user()->notaris;

        $data = collect(); // default kosong

        if ($queryType && $startDate && $endDate) {
            if ($queryType === 'notaris') {
                $data = NotaryAktaTransaction::whereBetween('created_at', [
                    Carbon::parse($startDate)->startOfDay(),
                    Carbon::parse($endDate)->endOfDay(),
                ])->get();
            } elseif ($queryType === 'ppat') {
                $data = NotaryRelaasAkta::whereBetween('created_at', [
                    Carbon::parse($startDate)->startOfDay(),
                    Carbon::parse($endDate)->endOfDay(),
                ])->get();
            }
        }

        // render view jadi HTML untuk mPDF
        $html = view('pages.BackOffice.LaporanAkta.export', [
            'data' => $data,
            'queryType' => $queryType,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'notaris' => $notaris,
        ])->render();

        // generate PDF
        $mpdf = new \Mpdf\Mpdf([
            'tempDir' => storage_path('app/mpdf-temp'),
        ]);
        $mpdf->WriteHTML($html);

        // Output PDF
        // return response($mpdf->Output('laporan-akta.pdf', 'S'))
        //     ->header('Content-Type', 'application/pdf')
        //     ->header('Content-Disposition', 'attachment; filename="laporan-akta.pdf"');
        return response($mpdf->Output('laporan-akta.pdf', 'I'))
            ->header('Content-Type', 'application/pdf');
    }
}
