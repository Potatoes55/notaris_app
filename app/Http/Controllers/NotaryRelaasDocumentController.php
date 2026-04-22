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

        // cek minimal salah satu input terisi
        if ($request->filled('transaction_code') || $request->filled('relaas_number')) {

            $relaasInfo = $this->service->searchRelaas(
                $request->transaction_code,
                $request->relaas_number
            );

            if ($relaasInfo) {
                $documents = $this->service->getDocuments($relaasInfo->id);
            } else {
                notyf()
                    ->position('x', 'right')
                    ->position('y', 'top')
                    ->warning('Data dokumen akta tidak ditemukan');
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
}
