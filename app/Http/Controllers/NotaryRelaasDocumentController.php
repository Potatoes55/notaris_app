<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\NotaryRelaasAkta;
use App\Models\NotaryRelaasDocument;
use App\Services\NotaryRelaasDocumentService;
use Illuminate\Http\Request;

class NotaryRelaasDocumentController extends Controller
{
    protected $service;

    public function __construct(NotaryRelaasDocumentService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $relaasInfo = null;
        $documents = collect();
        $transactions = null; // Tambahkan penampung data range tanggal

        $hasDateFilter = $request->filled('start_date') && $request->filled('end_date');

        // 1. KONDISI KHUSUS: Jika user HANYA menginputkan range tanggal saja
        if ($hasDateFilter && ! $request->filled('transaction_code') && ! $request->filled('relaas_number')) {

            // Panggil fungsi service baru
            $transactions = $this->service->searchRelaasByDateRange(
                $request->start_date,
                $request->end_date
            );

            if ($transactions->isEmpty()) {
                notyf()
                    ->position('x', 'right')
                    ->position('y', 'top')
                    ->warning('Tidak ada transaksi relaas yang ditemukan pada rentang tanggal tersebut.');
            }

            // Return ke blade khusus pencarian tanggal relaas
            return view('pages.BackOffice.RelaasAkta.AktaDocument.index_date', compact('transactions'));
        }

        // 2. KONDISI DEFAULT: Jika minimal salah satu filter utama terisi (Kode/Nomor Relaas)
        if ($request->filled('transaction_code') || $request->filled('relaas_number')) {

            // Tetap menggunakan service bawaan Anda (parameter ke-3 diset null karena sudah pakai range)
            $relaasInfo = $this->service->searchRelaas(
                $request->transaction_code,
                $request->relaas_number,
                null
            );

            if ($relaasInfo) {
                $documents = $this->service->getDocuments($relaasInfo->id);
            } else {
                notyf()
                    ->position('x', 'right')
                    ->position('y', 'top')
                    ->warning('Data dokumen relaas akta tidak ditemukan');
            }
        }

        return view(
            'pages.BackOffice.RelaasAkta.AktaDocument.index',
            compact('relaasInfo', 'documents')
        );
    }

    public function create($relaasId)
    {
        $relaas = NotaryRelaasAkta::findOrFail($relaasId);
        $doc = null;
        $clients = Client::where('deleted_at', null)->where('notaris_id', auth()->user()->notaris_id)->get();

        return view('pages.BackOffice.RelaasAkta.AktaDocument.form', compact('relaas', 'doc', 'clients'));
    }

    public function edit($relaasId, $id)
    {
        $doc = $this->service->findById($id);
        $relaas = NotaryRelaasAkta::findOrFail($doc->relaas_id);
        $clients = Client::where('deleted_at', null)->where('notaris_id', auth()->user()->notaris_id)->get();

        return view('pages.BackOffice.RelaasAkta.AktaDocument.form', compact('relaas', 'doc', 'clients'));
    }

    public function store(Request $request, $relaasId)
    {
        $relaas = NotaryRelaasAkta::findOrFail($relaasId);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'uploaded_at' => 'required|date',
            'file_url' => 'required|max:5240|mimes:pdf,jpg,jpeg,png',
        ], [
            'name.required' => 'Nama dokumen harus diisi.',
            'type.required' => 'Tipe dokumen harus diisi.',
            'uploaded_at.required' => 'Tanggal upload harus diisi.',
            'file_url.required' => 'File dokumen harus diupload.',
            'file_url.max' => 'Ukuran file maksimal 5MB.',
            'file_url.mimes' => 'Format file harus PDF, JPG, JPEG, atau PNG.',
        ]);

        if ($request->hasFile('file_url')) {
            $file = $request->file('file_url');
            $originalName = $file->getClientOriginalName();
            $fileNameOnly = pathinfo($originalName, PATHINFO_FILENAME);
            $fileExtension = $file->getClientOriginalExtension();

            // simpan file ke storage/app/documents
            $storedPath = $file->storeAs('documents', $originalName);

            // isi otomatis
            $validated['file_url'] = $storedPath;
            $validated['file_name'] = $fileNameOnly;
            $validated['file_type'] = $fileExtension;
        }

        $validated['relaas_id'] = $relaas->id;
        // $validated['registration_code'] = $relaas->registration_code;
        $validated['notaris_id'] = $relaas->notaris_id;
        $validated['client_code'] = $relaas->client_code;
        // $validated['uploaded_at'] = now();

        // $this->service->store($validated);
        NotaryRelaasDocument::create($validated);

        notyf()->position('x', 'right')->position('y', 'top')->success('Dokumen akta berhasil ditambahkan.');

        return redirect()->route('relaas-documents.index', ['transaction_code' => $relaas->transaction_code]);
    }

    public function update(Request $request, $relaasId, $id)
    {
        $relaas = NotaryRelaasAkta::findOrFail($relaasId);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'uploaded_at' => 'nullable|date',
            'file_url' => 'nullable|max:5000|mimes:pdf,jpg,jpeg,png',
        ], [
            'name.required' => 'Nama dokumen harus diisi.',
            'type.required' => 'Jenis dokumen harus diisi.',
            'file_url.required' => 'File dokumen harus diupload.',
            'file_url.max' => 'Ukuran file maksimal 5 MB.',
            'file_url.mimes' => 'Format file harus PDF, JPG, JPEG, atau PNG.',
        ]);

        if ($request->hasFile('file_url')) {
            $file = $request->file('file_url');
            $file = $request->file('file_url');
            $originalName = $file->getClientOriginalName(); // contoh: akta_perubahan.pdf
            $fileNameOnly = pathinfo($originalName, PATHINFO_FILENAME);
            $fileExtension = $file->getClientOriginalExtension();

            // simpan file ke storage/app/documents
            $storedPath = $file->storeAs('documents', $originalName);

            // isi otomatis
            $validated['file_url'] = $storedPath;
            $validated['file_name'] = $fileNameOnly;
            $validated['file_type'] = $fileExtension;
        }

        $validated['relaas_id'] = $relaas->id;
        // $validated['registration_code'] = $relaas->registration_code;
        $validated['notaris_id'] = $relaas->notaris_id;
        $validated['client_code'] = $relaas->client_code;

        $this->service->update($id, $validated);

        notyf()->position('x', 'right')->position('y', 'top')->success('Dokumen akta berhasil diperbarui.');

        return redirect()->route('relaas-documents.index', ['transaction_code' => $relaas->transaction_code]);
    }

    public function destroy($id)
    {
        $this->service->destroy($id);

        notyf()->position('x', 'right')->position('y', 'top')->success('Dokumen akta berhasil dihapus.');

        return redirect()->back();
    }

    public function toggleStatus($id)
    {
        $this->service->toggleStatus($id);

        notyf()->position('x', 'right')->position('y', 'top')->success('Status dokumen berhasil diperbarui.');

        return redirect()->back();
    }

    public function viewPdf($id)
    {
        try {
            $doc = \App\Models\NotaryRelaasDocument::find($id);
            // dd($doc);
            if (! $doc) {
                return response()->json(['error' => 'Data dokumen tidak ditemukan.'], 404);
            }

            $filePath = public_path('storage/'.$doc->file_url);

            if (! file_exists($filePath)) {
                return response()->json(['error' => 'File fisik tidak ditemukan.'], 404);
            }

            $transaction = $doc->relaases;

            if (! $transaction) {
                return response()->json(['error' => 'Data transaksi tidak ditemukan untuk dokumen ini.'], 404);
            }

            $transactionCode = $transaction->transaction_code;
            $hash = \Illuminate\Support\Facades\Crypt::encryptString($transactionCode);

            $qrCodeSvg = \SimpleSoftwareIO\QrCode\Facades\QrCode::size(100)
                ->margin(0)
                ->generate(route('akta.qr.show', ['transaction_code' => $hash]));

            $qrCodeCleanSvg = str_replace('<?xml version="1.0" encoding="UTF-8"?>', '', $qrCodeSvg);

            $mpdf = new \Mpdf\Mpdf([
                'format' => 'A4',
                'margin_left' => 0,
                'margin_right' => 0,
                'margin_top' => 0,
                'margin_bottom' => 0,
            ]);

            $fileType = strtolower($doc->file_type);

            if (in_array($fileType, ['png', 'jpg', 'jpeg', 'svg'])) {

                $widthMm = 210;
                $heightMm = 297;

                $topPositionMm = $heightMm * 0.4;
                $leftPositionMm = 5;

                $htmlContent = '
            <div style="position: absolute; top: 0; left: 0; width: 210mm; height: 297mm; z-index: 1; margin: 0; padding: 0;">
                <img src="'.$filePath.'" style="width: 210mm; height: 297mm; object-fit: contain; margin: 0; padding: 0;" />
            </div>

            <tt>
                <div style="position: absolute; top: '.$topPositionMm.'mm; left: '.$leftPositionMm.'mm; width: 65px; height: 65px; z-index: 99999; background-color: #ffffff; padding: 4px; border: 1px solid #dddddd; border-radius: 4px;">
                    '.$qrCodeCleanSvg.'
                </div>
            </tt>
            ';

                $mpdf->WriteHTML($htmlContent);

            } else {
                $pageCount = $mpdf->setSourceFile($filePath);

                for ($i = 1; $i <= $pageCount; $i++) {
                    $importPage = $mpdf->importPage($i);
                    $pageSize = $mpdf->getTemplateSize($importPage);

                    $widthMm = $pageSize['width'];
                    $heightMm = $pageSize['height'];
                    $orientation = ($widthMm > $heightMm) ? 'L' : 'P';

                    if ($i > 1) {
                        $mpdf->WriteHTML('<pagebreak sheet-size="'.$widthMm.'mm '.$heightMm.'mm" margin-left="0" margin-right="0" margin-top="0" margin-bottom="0" />');
                    } else {
                        $mpdf->_setPageSize([$widthMm, $heightMm], $orientation);
                    }

                    $mpdf->useTemplate($importPage);
                    $mpdf->page = $i;

                    $topPositionMm = $heightMm * 0.4;
                    $leftPositionMm = 4;

                    $htmlQrLeftCenter = '
                <tt>
                    <div style="position: absolute; top: '.$topPositionMm.'mm; left: '.$leftPositionMm.'mm; width: 65px; height: 65px; z-index: 99999; background-color: #ffffff; padding: 4px; border: 1px solid #dddddd; border-radius: 4px;">
                        '.$qrCodeCleanSvg.'
                    </div>
                </tt>';

                    $mpdf->WriteHTML($htmlQrLeftCenter);
                }
            }

            return response($mpdf->Output("preview_dokumen_{$id}.pdf", 'I'))
                ->header('Content-Type', 'application/pdf');

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Terjadi kesalahan sistem.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
