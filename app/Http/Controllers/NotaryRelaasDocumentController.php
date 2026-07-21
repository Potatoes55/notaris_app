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
        $notarisId = auth()->user()->notaris_id;
        $filters = $request->only(['search', 'start_date', 'end_date']);
        $search = $request->input('search');

        $relaasInfo = null;
        $documents = collect();
        $transactions = null;

        $hasSearch = $request->filled('search');
        $hasDateFilter = $request->filled('start_date') && $request->filled('end_date');

        if ($hasSearch || $hasDateFilter) {

            if ($hasSearch) {

                $relaasInfo = $this->service->searchRelaas(['search' => $search]);
            }

            if ($relaasInfo) {

                $documents = $relaasInfo->documents;
            } else {

                $query = NotaryRelaasAkta::with(['client', 'akta_type'])
                    ->withCount('documents')
                    ->where('notaris_id', $notarisId);

                if ($hasSearch) {
                    $query->where(function ($q) use ($search) {
                        $q->where('relaas_number', 'like', '%'.$search.'%')
                            ->orWhereHas('client', function ($clientQuery) use ($search) {
                                $clientQuery->where('fullname', 'like', '%'.$search.'%');
                            });
                    });
                }

                if ($hasDateFilter) {
                    $query->whereBetween('story_date', [
                        $filters['start_date'].' 00:00:00',
                        $filters['end_date'].' 23:59:59',
                    ]);
                }

                $transactions = $query->orderBy('story_date', 'desc')
                    ->paginate(10)
                    ->withQueryString();

                if ($transactions->isEmpty()) {
                    notyf()
                        ->position('x', 'right')
                        ->position('y', 'top')
                        ->warning('Tidak ada transaksi relaas yang ditemukan.');
                }
            }
        }

        return view(
            'pages.BackOffice.RelaasAkta.AktaDocument.index',
            compact('relaasInfo', 'transactions', 'documents', 'filters')
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

        return redirect()->route('relaas-documents.index', ['search' => $relaas->transaction_code]);
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

        return redirect()->route('relaas-documents.index', ['search' => $relaas->transaction_code]);
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

            // 1. Generate QR Code HANYA jika tipe dokumen BUKAN 'Minuta Akta'
            $qrCodeCleanSvg = null;
            if ($doc->type !== 'Minuta Akta') {
                $transactionCode = $transaction->transaction_code;
                $hash = \Illuminate\Support\Facades\Crypt::encryptString($transactionCode);

                $qrCodeSvg = \SimpleSoftwareIO\QrCode\Facades\QrCode::size(100)
                    ->margin(0)
                    ->generate(route('akta.qr.show', ['transaction_code' => $hash]));

                $qrCodeCleanSvg = str_replace('<?xml version="1.0" encoding="UTF-8"?>', '', $qrCodeSvg);
            }

            $fileType = strtolower($doc->file_type);

            // 2. LOGIKA UNTUK FILE GAMBAR (PNG, JPG, JPEG, SVG)
            if (in_array($fileType, ['png', 'jpg', 'jpeg', 'svg'])) {
                [$imgWidth, $imgHeight] = getimagesize($filePath);

                // Konversi piksel asli gambar ke milimeter (Basis kalkulasi 96 DPI: 1px = 0.264583mm)
                $widthMm = $imgWidth * 0.264583;
                $heightMm = $imgHeight * 0.264583;

                // Inisialisasi mPDF dengan format kustom seukuran gambar asli
                $mpdf = new \Mpdf\Mpdf([
                    'format' => [$widthMm, $heightMm],
                    'margin_left' => 0,
                    'margin_right' => 0,
                    'margin_top' => 0,
                    'margin_bottom' => 0,
                ]);

                // Posisi QR Code dinamis: 40% dari total tinggi gambar asli
                $topPositionMm = $heightMm * 0.4;
                $leftPositionMm = 5;

                // Render gambar langsung memenuhi 100% canvas dinamis
                $htmlContent = '<img src="'.$filePath.'" style="width: 100%; display: block; margin: 0; padding: 0;" />';

                if ($qrCodeCleanSvg) {
                    $htmlContent .= '
            <tt>
                <div style="position: absolute; top: '.$topPositionMm.'mm; left: '.$leftPositionMm.'mm; width: 65px; height: 65px; z-index: 99999; background-color: #ffffff; padding: 4px; border: 1px solid #dddddd; border-radius: 4px;">
                    '.$qrCodeCleanSvg.'
                </div>
            </tt>';
                }

                $mpdf->WriteHTML($htmlContent);

                // 3. LOGIKA UNTUK FILE PDF ASLI
            } else {
                $mpdf = new \Mpdf\Mpdf([
                    'format' => 'A4',
                    'margin_left' => 0,
                    'margin_right' => 0,
                    'margin_top' => 0,
                    'margin_bottom' => 0,
                ]);

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

                    if ($qrCodeCleanSvg) {
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
