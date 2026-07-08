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
        $filters = $request->only(['transaction_code', 'akta_number', 'start_date', 'end_date', 'fullname']);
        $transaction = null;
        $documents = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10);

        $hasDateFilter = ! empty($filters['start_date']) && ! empty($filters['end_date']);
        $hasClientFilter = ! empty($filters['fullname']);

        if ($hasClientFilter || ($hasDateFilter && empty($filters['transaction_code']) && empty($filters['akta_number']))) {

            $query = NotaryAktaTransaction::with(['client'])
                ->withCount('documents')
                ->where('notaris_id', auth()->user()->notaris_id);

            if ($hasDateFilter) {
                $query->whereBetween('date_submission', [$filters['start_date'].' 00:00:00', $filters['end_date'].' 23:59:59']);
            }

            if ($hasClientFilter) {
                $query->whereHas('client', function ($clientQuery) use ($filters) {
                    $clientQuery->where('fullname', 'like', '%'.$filters['fullname'].'%');
                });
            }
            $transactions = $query->orderBy('date_submission', 'desc')->paginate(10)
                ->withQueryString();

            if ($transactions->isEmpty()) {
                notyf()->position('x', 'right')->position('y', 'top')->warning('Tidak ada transaksi akta yang ditemukan pada rentang tanggal tersebut.');
            }

            return view('pages.BackOffice.AktaDocument.index_date', compact('transactions', 'filters', 'transaction'));
        }

        if (! empty($filters['transaction_code']) || ! empty($filters['akta_number']) || ! empty($filters['fullname']) || $hasDateFilter) {

            $documents = $this->service->list($filters);

            $transaction = NotaryAktaTransaction::where('notaris_id', auth()->user()->notaris_id)
                ->where(function ($q) use ($filters) {
                    if (! empty($filters['transaction_code'])) {
                        $q->where('transaction_code', $filters['transaction_code']);
                    }
                    if (! empty($filters['akta_number'])) {
                        $q->where('akta_number', 'like', '%'.$filters['akta_number'].'%');
                    }
                })->first();

            if ($documents->isEmpty() && ! $transaction) {
                notyf()
                    ->position('x', 'right')
                    ->position('y', 'top')
                    ->warning('Data transaksi tidak ditemukan.');
            }

        } else {
            $documents = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10);
        }

        return view('pages.BackOffice.AktaDocument.index', compact('transaction', 'documents', 'filters'));
    }

    public function createData($transaction_id)
    {
        $transaction = NotaryAktaTransaction::with('akta_type', 'notaris', 'client')
            ->findOrFail($transaction_id);
        $category = isset($transaction->akta_type) ? strtolower($transaction->akta_type->category) : '';

        $isSkRequired = in_array($category, ['perubahan', 'pembubaran']);

        return view('pages.BackOffice.AktaDocument.form', compact('transaction', 'isSkRequired'));
    }

    public function create() {}

    public function store(Request $request) {}

    public function storeData(Request $request, $transaction_id)
    {
        $transaction = NotaryAktaTransaction::findOrFail($transaction_id);
        $category = isset($transaction->akta_type) ? strtolower($transaction->akta_type->category) : '';
        $isSkCategory = in_array($category, ['perubahan', 'pembubaran']);

        $rules = [
            'name' => 'required|string',
            'type' => 'required|string',
            'file_url' => 'required|max:10240|mimes:png,jpg,jpeg,pdf',
            'uploaded_at' => 'required|date',
        ];

        // JIKA form dikhususkan untuk SK Kemenkumham (misal di input type-nya sengaja di-set 'sk_kemenkumham')
        if ($request->input('type') === 'sk_kemenkum' && ! $isSkCategory) {
            notyf()->position('x', 'right')->position('y', 'top')->error('Jenis akta ini tidak memerlukan SK Kemenkum.');

            return redirect()->back()->withInput();
        }

        $messages = [
            'name.required' => 'Nama dokumen harus diisi.',
            'type.required' => 'Tipe dokumen harus diisi.',
            'file_url.required' => 'File dokumen harus diupload.',
            'uploaded_at.required' => 'Tanggal upload harus diisi.',
            'file_url.max' => 'Ukuran file maksimal 10MB.',
            'file_url.mimes' => 'Format file harus PDF, JPG, JPEG, atau PNG.',
        ];

        $data = $request->validate($rules, $messages);

        $data['notaris_id'] = $transaction->notaris_id;
        $data['akta_transaction_id'] = $transaction->id;
        $data['client_code'] = $transaction->client_code;
        $data['akta_number'] = $transaction->akta_number;

        if ($request->hasFile('file_url')) {
            $file = $request->file('file_url');
            $originalName = $file->getClientOriginalName();
            $fileNameOnly = pathinfo($originalName, PATHINFO_FILENAME);
            $fileExtension = $file->getClientOriginalExtension();

            // Simpan file ke storage/app/documents
            $storedPath = $file->storeAs('documents', $originalName);

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

        $qrCodeCleanSvg = null;
        if ($doc->type !== 'sk_kemenkum') {
            $transactionCode = $transaction->transaction_code;
            $hash = \Illuminate\Support\Facades\Crypt::encryptString($transactionCode);

            $qrCodeSvg = \SimpleSoftwareIO\QrCode\Facades\QrCode::size(100)
                ->margin(0)
                ->generate(route('akta.qr.show', ['transaction_code' => $hash]));

            $qrCodeCleanSvg = str_replace('<?xml version="1.0" encoding="UTF-8"?>', '', $qrCodeSvg);
        }

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
        </div>';

            if ($doc->type !== 'sk_kemenkum' && $qrCodeCleanSvg) {
                $htmlContent .= '
            <tt>
                <div style="position: absolute; top: '.$topPositionMm.'mm; left: '.$leftPositionMm.'mm; width: 65px; height: 65px; z-index: 99999; background-color: #ffffff; padding: 4px; border: 1px solid #dddddd; border-radius: 4px;">
                    '.$qrCodeCleanSvg.'
                </div>
            </tt>';
            }

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

                // Tempel QR Code di halaman PDF hanya jika tipenya bukan sk_kemenkum
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

        // } catch (\Exception $e) {
        //     return response()->json([
        //         'error' => 'Terjadi kesalahan sistem.',
        //         'message' => $e->getMessage(),
        //     ], 500);
        // }
    }
}
