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
        $filters = $request->only(['transaction_code', 'akta_number']);
        $transaction = null;
        $documents = collect();

        // Cari Akta Transaction dulu
        if (! empty($filters['transaction_code']) || ! empty($filters['akta_number'])) {
            $transaction = NotaryAktaTransaction::with('akta_type')->where('notaris_id', auth()->user()->notaris_id)->where(function ($q) use ($filters) {
                if (! empty($filters['transaction_code'])) {
                    $q->where('transaction_code', $filters['transaction_code']);
                }
                if (! empty($filters['akta_number'])) {
                    $q->orWhere('akta_number', $filters['akta_number']);
                }
            })->first();
            // dd($transaction);

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
                    ->warning('Data transaksi dengan kode transaksi atau nomor akta tersebut tidak ditemukan.');
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
}
