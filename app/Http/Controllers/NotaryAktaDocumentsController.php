<?php

namespace App\Http\Controllers;

use App\Models\NotaryAktaDocuments;
use App\Models\NotaryAktaTransaction;
use App\Services\NotaryAktaDocumentService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class NotaryAktaDocumentsController extends Controller
{
    protected $service;

    public function __construct(NotaryAktaDocumentService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $notarisId = auth()->user()->notaris_id;
        $filters = $request->only(['search', 'start_date', 'end_date']);
        $search = $request->input('search');

        $transaction = null;
        $transactions = null;
        $documents = collect();

        $hasSearch = $request->filled('search');
        $hasDateFilter = $request->filled('start_date') && $request->filled('end_date');

        if ($hasSearch || $hasDateFilter) {

            if ($hasSearch) {
                $transaction = NotaryAktaTransaction::with(['client', 'akta_type'])
                    ->where('notaris_id', $notarisId)
                    ->where(function ($query) use ($search) {
                        $query->where('transaction_code', $search)
                            ->orWhere('akta_number', $search);
                    })->first();
            }

            if ($transaction) {
                $documents = $this->service->list(['akta_transaction_id' => $transaction->id]);
            } else {
                $query = NotaryAktaTransaction::with(['client', 'akta_type'])
                    ->withCount('documents')
                    ->where('notaris_id', $notarisId);

                if ($hasSearch) {
                    $query->where(function ($q) use ($search) {
                        $q->where('akta_number', 'like', '%'.$search.'%')
                            ->orWhereHas('client', function ($clientQuery) use ($search) {
                                $clientQuery->where('fullname', 'like', '%'.$search.'%');
                            });
                    });
                }
                // dd($transaction);

                if ($hasDateFilter) {
                    $query->whereBetween('date_submission', [
                        $filters['start_date'].' 00:00:00',
                        $filters['end_date'].' 23:59:59',
                    ]);
                }

                $transactions = $query->orderBy('date_submission', 'desc')
                    ->paginate(10)
                    ->withQueryString();

                if ($transactions->isEmpty()) {
                    notyf()
                        ->position('x', 'right')
                        ->position('y', 'top')
                        ->warning('Data transaksi atau dokumen tidak ditemukan.');
                }
            }
        }

        return view('pages.BackOffice.AktaDocument.index', compact('transaction', 'transactions', 'documents', 'filters'));
    }

    public function createData($transaction_id)
    {
        $transaction = NotaryAktaTransaction::with('akta_type', 'notaris', 'client')
            ->findOrFail($transaction_id);
        $category = isset($transaction->akta_type) ? strtolower($transaction->akta_type->category) : '';

        $isSkRequired = in_array($category, ['pendirian', 'perubahan', 'pembubaran']);

        return view('pages.BackOffice.AktaDocument.form', compact('transaction', 'isSkRequired'));
    }

    public function create() {}

    public function store(Request $request) {}

    public function storeData(Request $request, $transaction_id)
    {
        $transaction = NotaryAktaTransaction::findOrFail($transaction_id);
        $aktaType = isset($transaction->akta_type) ? strtolower($transaction->akta_type->category) : '';
        $isSkCategory = Str::contains($aktaType, ['pendirian', 'perubahan', 'pembubaran'], true);

        $rules = [
            'name' => 'required|string',
            'type' => 'nullable|string',
            'file_url' => 'required|max:10240|mimes:png,jpg,jpeg,pdf',
            'uploaded_at' => 'required|date',
        ];
        // dd($aktaType);
        if ($request->input('type') === 'sk_kemenkum' && ! $isSkCategory) {
            notyf()
                ->position('x', 'right')
                ->position('y', 'top')
                ->error('Jenis akta ini tidak memerlukan SK Kemenkum.');

            return redirect()->back()->withInput();
        }

        $messages = [
            'name.required' => 'Nama dokumen harus diisi.',
            'file_url.required' => 'File dokumen harus diupload.',
            'uploaded_at.required' => 'Tanggal upload harus diisi.',
            'file_url.max' => 'Ukuran file maksimal 10MB.',
            'file_url.mimes' => 'Format file harus PDF, JPG, JPEG, atau PNG.',
        ];

        $data = $request->validate($rules, $messages);

        if ($request->input('type') === 'sk_kemenkum') {
            $data['type'] = 'sk_kemenkum';
        }

        $data['notaris_id'] = $transaction->notaris_id;
        $data['akta_transaction_id'] = $transaction->id;
        $data['client_code'] = $transaction->client_code;
        $data['akta_number'] = $transaction->akta_number;

        if ($request->hasFile('file_url')) {
            $file = $request->file('file_url');

            $originalName = $file->getClientOriginalName();
            $fileNameOnly = pathinfo($originalName, PATHINFO_FILENAME);
            $fileExtension = $file->getClientOriginalExtension();

            $storedPath = $file->storeAs('documents', $originalName);

            $data['file_url'] = $storedPath;
            $data['file_name'] = $fileNameOnly;
            $data['file_type'] = $fileExtension;
        }

        NotaryAktaDocuments::create($data);

        notyf()
            ->position('x', 'right')
            ->position('y', 'top')
            ->success('Berhasil menambahkan akta dokumen.');

        return redirect()->route('akta-documents.index', [
            'search' => $transaction->transaction_code,
            'transaction_code' => $transaction->transaction_code,
            'akta_number' => $transaction->akta_number,
        ]);
    }

    public function edit($id)
    {
        $document = $this->service->get($id);

        $transaction = \App\Models\NotaryAktaTransaction::with('akta_type', 'client')
            ->findOrFail($document->akta_transaction_id);
        $category = isset($transaction->akta_type) ? strtolower($transaction->akta_type->category) : '';
        $isSkRequired = in_array($category, ['perubahan', 'pembubaran']);

        return view('pages.BackOffice.AktaDocument.form', compact('document', 'transaction', 'isSkRequired'));
    }

    public function update(Request $request, $id)
    {

        $document = NotaryAktaDocuments::findOrFail($id);
        $transaction = NotaryAktaTransaction::findOrFail($document->akta_transaction_id);

        $data = $request->validate([
            'name' => 'required|string',
            'type' => 'required|string',
            'file_url' => 'nullable|max:10240|mimes:png,jpg,jpeg,pdf',
            'uploaded_at' => 'required|date',
        ], [
            'name.required' => 'Nama dokumen harus diisi.',
            'type.required' => 'Tipe dokumen harus diisi.',
            'file_url.required' => 'File dokumen harus diupload.',
            'uploaded_at.required' => 'Tanggal upload harus diisi.',
            'file_url.max' => 'Ukuran file maksimal 10MB.',
            'file_url.mimes' => 'Format file harus PDF, JPG, JPEG, atau PNG.',
        ]);

        $data['notaris_id'] = $transaction->notaris_id;
        // $data['client_id'] = $transaction->client_id;
        $data['akta_transaction_id'] = $transaction->id;
        $data['client_code'] = $transaction->client_code;
        $data['akta_number'] = $transaction->akta_number;

        if ($request->hasFile('file_url')) {
            $file = $request->file('file_url');
            $originalName = $file->getClientOriginalName(); // contoh: akta_perubahan.pdf
            $fileNameOnly = pathinfo($originalName, PATHINFO_FILENAME); // akta_perubahan
            $fileExtension = $file->getClientOriginalExtension(); // pdf

            // simpan file ke storage/app/documents
            $storedPath = $file->storeAs('documents', $originalName);

            // isi otomatis
            $data['file_url'] = $storedPath;
            $data['file_name'] = $fileNameOnly;
            $data['file_type'] = $fileExtension;
        }

        $this->service->update($id, $data);

        notyf()->position('x', 'right')->position('y', 'top')->success('Berhasil memperbarui akta dokumen.');

        return redirect()->route('akta-documents.index', [
            'transaction_code' => $transaction->transaction_code,
            'akta_number' => $transaction->akta_number,
        ]);
    }

    public function destroy($id)
    {
        $document = NotaryAktaDocuments::findOrFail($id);
        $transaction = NotaryAktaTransaction::findOrFail($document->akta_transaction_id);
        $this->service->delete($id);

        notyf()->position('x', 'right')->position('y', 'top')->success('Berhasil menghapus akta dokumen.');

        return redirect()->route('akta-documents.index', [
            'search' => $transaction->transaction_code
        ]);
    }

    // Tambahkan method ini di dalam NotaryAktaDocumentsController

    public function viewPdf($id)
    {
        $doc = \App\Models\NotaryAktaDocuments::find($id);

        if (! $doc) {
            return response()->json(['error' => 'Data dokumen tidak ditemukan.'], 404);
        }

        $filePath = public_path('storage/'.$doc->file_url);

        if (! file_exists($filePath)) {
            return response()->json(['error' => 'File fisik tidak ditemukan.'], 404);
        }

        $transaction = \App\Models\NotaryAktaTransaction::find($doc->akta_transaction_id);

        if (! $transaction) {
            return response()->json(['error' => 'Data transaksi tidak ditemukan untuk dokumen ini.'], 404);
        }

        // 1. Generate QR Code jika tipenya bukan sk_kemenkum
        $qrCodeCleanSvg = null;
        if ($doc->type !== 'sk_kemenkum') {
            $transactionCode = $transaction->transaction_code;
            $hash = \Illuminate\Support\Facades\Crypt::encryptString($transactionCode);

            $qrCodeSvg = \SimpleSoftwareIO\QrCode\Facades\QrCode::size(100)
                ->margin(0)
                ->generate(route('akta.qr.show', ['transaction_code' => $hash]));

            $qrCodeCleanSvg = str_replace('<?xml version="1.0" encoding="UTF-8"?>', '', $qrCodeSvg);
        }

        $fileType = strtolower($doc->file_type);

        // 2. PROSES CONFIG UNTUK FILE GAMBAR
        if (in_array($fileType, ['png', 'jpg', 'jpeg', 'svg'])) {
            [$imgWidth, $imgHeight] = getimagesize($filePath);

            // Konversi pixel ke milimeter (Menggunakan basis kalkulasi aman 96 DPI: 1px = 0.264583mm)
            $widthMm = $imgWidth * 0.264583;
            $heightMm = $imgHeight * 0.264583;

            // Buat canvas mPDF dengan ukuran pas sesuai gambar (Tanpa template A4/A4-L)
            $mpdf = new \Mpdf\Mpdf([
                'format' => [$widthMm, $heightMm],
                'margin_left' => 0,
                'margin_right' => 0,
                'margin_top' => 0,
                'margin_bottom' => 0,
            ]);

            // Posisi QR Code: Dinamis 40% dari total tinggi gambar yang sebenarnya
            $topPositionMm = $heightMm * 0.4;
            $leftPositionMm = 5;

            // Gambar diset memenuhi 100% canvas dinamis yang sudah kita ciptakan
            $htmlContent = '<img src="'.$filePath.'" style="width: 100%; display: block; margin: 0; padding: 0;" />';

            if ($doc->type !== 'sk_kemenkum' && $qrCodeCleanSvg) {
                $htmlContent .= '
            <tt>
                <div style="position: absolute; top: '.$topPositionMm.'mm; left: '.$leftPositionMm.'mm; width: 65px; height: 65px; z-index: 99999; background-color: #ffffff; padding: 4px; border: 1px solid #dddddd; border-radius: 4px;">
                    '.$qrCodeCleanSvg.'
                </div>
            </tt>';
            }

            $mpdf->WriteHTML($htmlContent);

            // 3. PROSES CONFIG UNTUK FILE PDF ASLI (Bawaan dokumen)
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

                if ($doc->type !== 'sk_kemenkum' && $qrCodeCleanSvg) {
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
    }
}
