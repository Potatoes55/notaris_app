<?php

namespace App\Http\Controllers;

use App\Models\NotaryAktaDocuments;
use App\Models\NotaryAktaTransaction;
use App\Services\NotaryAktaDocumentService;
use Illuminate\Http\Request;

class NotaryAktaDocumentsController extends Controller
{
    protected $service;

    public function __construct(NotaryAktaDocumentService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        // 1. Tambahkan 'year' ke dalam array only()
        $filters = $request->only(['transaction_code', 'akta_number', 'year']);
        $transaction = null;
        $documents = collect();

        // 2. Cek apakah ada salah satu filter yang diisi (termasuk year)
        if (! empty($filters['transaction_code']) || ! empty($filters['akta_number']) || ! empty($filters['year'])) {

            $transaction = NotaryAktaTransaction::with('akta_type')
                ->where('notaris_id', auth()->user()->notaris_id)
                ->where(function ($q) use ($filters) {

                    if (! empty($filters['transaction_code'])) {
                        $q->where('transaction_code', $filters['transaction_code']);
                    }

                    if (! empty($filters['akta_number'])) {
                        // Menggunakan orWhere jika sebelumnya ada filter transaction_code,
                        // atau where biasa jika ini adalah filter pertama yang aktif
                        $q->orWhere('akta_number', $filters['akta_number']);
                    }

                    if (! empty($filters['year'])) {
                        // CATATAN: Ganti 'created_at' dengan kolom tanggal transaksi Anda yang sesuai (misal: 'tanggal_akta' jika ada)
                        if (! empty($filters['transaction_code']) || ! empty($filters['akta_number'])) {
                            $q->orWhereYear('created_at', $filters['year']);
                        } else {
                            $q->whereYear('created_at', $filters['year']);
                        }
                    }

                })->first();

            if ($transaction) {
                $documents = NotaryAktaDocuments::where('akta_transaction_id', $transaction->id)
                    ->where('notaris_id', auth()->user()->notaris_id)
                    ->orderBy('created_at', 'desc')
                    ->paginate(10)
                    ->withQueryString();
            } else {
                notyf()
                    ->position('x', 'right')
                    ->position('y', 'top')
                    ->warning('Data transaksi dengan kriteria pencarian tersebut tidak ditemukan.');
            }
        }

        return view('pages.BackOffice.AktaDocument.index', compact('transaction', 'documents', 'filters'));
    }

    public function createData($transaction_id)
    {
        $transaction = NotaryAktaTransaction::with('akta_type', 'notaris', 'client')
            ->findOrFail($transaction_id);

        return view('pages.BackOffice.AktaDocument.form', compact('transaction'));
    }

    public function create() {}

    public function store(Request $request) {}

    public function storeData(Request $request, $transaction_id)
    {
        $transaction = NotaryAktaTransaction::findOrFail($transaction_id);

        $data = $request->validate([
            'name' => 'required|string',
            'type' => 'required|string',
            // 'file_name' => 'required|string',
            'file_url' => 'required|max:5000|mimes:png,jpg,jpeg,pdf',
            // 'file_type' => 'required|string',
            'uploaded_at' => 'required|date',
        ], [
            'name.required' => 'Nama dokumen harus diisi.',
            'type.required' => 'Tipe dokumen harus diisi.',
            'file_url.required' => 'File dokumen harus diupload.',
            'uploaded_at.required' => 'Tanggal upload harus diisi.',
            'file_url.max' => 'Ukuran file maksimal 1MB.',
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

        NotaryAktaDocuments::create($data);

        notyf()->position('x', 'right')->position('y', 'top')->success('Berhasil menambahkan akta dokumen.');

        return redirect()->route('akta-documents.index', ['transaction_code' => $transaction->transaction_code, 'akta_number' => $transaction->akta_number]);
    }

    public function edit($id)
    {
        $document = $this->service->get($id);

        return view('pages.BackOffice.AktaDocument.form', compact('document'));
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

        return redirect()->route('akta-documents.index', ['transaction_code' => $transaction->transaction_code, 'akta_number' => $transaction->akta_number]);
    }

    // Tambahkan method ini di dalam NotaryAktaDocumentsController

    public function viewPdf($id)
    {
        // try {
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

        // } catch (\Exception $e) {
        //     return response()->json([
        //         'error' => 'Terjadi kesalahan sistem.',
        //         'message' => $e->getMessage(),
        //     ], 500);
        // }
    }
}
