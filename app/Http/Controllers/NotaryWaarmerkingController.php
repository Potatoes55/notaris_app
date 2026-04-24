<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Services\WaarmerkingService;
use Illuminate\Http\Request;

class NotaryWaarmerkingController extends Controller
{
    protected $service;

    public function __construct(WaarmerkingService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['legalisasi_number', 'sort']);
        $perPage = $request->get('perPage', 1);

        $data = $this->service->list($filters, $perPage);

        return view('pages.BackOffice.Waarmerking.index', compact('data'));
    }

    public function create()
    {
        $clients = Client::all()->where('notaris_id', auth()->user()->notaris_id);

        return view('pages.BackOffice.Waarmerking.form', compact('clients'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_code' => 'required',
            'waarmerking_number' => 'required|string',
            'applicant_name' => 'required|string',
            'officer_name' => 'required|string',
            'document_type' => 'required|string',
            'document_number' => 'nullable|string',
            'request_date' => 'nullable|date',
            'release_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'file_path' => 'nullable|mimes:pdf,jpg,jpeg,png|max:5000',
        ], [
            'client_code.required' => 'Klien harus dipilih.',
            'waarmerking_number.required' => 'Nomor Waarmarking harus diisi.',
            'officer_name.required' => 'Nama Petugas harus diisi.',
            'document_type.required' => 'Jenis Dokumen harus diisi.',
            'applicant_name.required' => 'Nama Pemohon harus diisi.',
            'file_path.max' => 'Ukuran file maksimal 5 MB.',
            'file_path.mimes' => 'Format file harus PDF, JPG, JPEG, atau PNG.',
        ]);

        if ($request->hasFile('file_path')) {
            $validated['file_path'] = $request->file('file_path')->storeAs('documents', $request->file('file_path')->getClientOriginalName());
        }

        $validated['notaris_id'] = auth()->user()->notaris_id;

        $this->service->store($validated);

        notyf()->position('x', 'right')->position('y', 'top')->success('Waarmarking berhasil ditambahkan.');

        return redirect()->route('notary-waarmerking.index');
    }

    public function edit($id)
    {
        $clients = Client::all();
        $data = $this->service->get($id);

        return view('pages.BackOffice.Waarmerking.form', compact('data', 'clients'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'client_code' => 'required',
            'waarmerking_number' => 'required|string',
            'applicant_name' => 'required|string',
            'officer_name' => 'required|string',
            'document_type' => 'required|string',
            'document_number' => 'nullable|string',
            'request_date' => 'nullable|date',
            'release_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'file_path' => 'nullable|mimes:pdf,jpg,jpeg,png|max:10240',
        ], [
            'client_code.required' => 'Klien harus dipilih.',
            'waarmerking_number.required' => 'Nomor Waarmarking harus diisi.',
            'officer_name.required' => 'Nama Petugas harus diisi.',
            'document_type.required' => 'Jenis Dokumen harus diisi.',
            'applicant_name.required' => 'Nama Pemohon harus diisi.',
            'file_path.max' => 'Ukuran file maksimal 10 MB.',
            'file_path.mimes' => 'Format file harus PDF, JPG, JPEG, atau PNG.',
        ]);

        if ($request->hasFile('file_path')) {
            $validated['file_path'] = $request->file('file_path')->storeAs('documents', $request->file('file_path')->getClientOriginalName());
        }

        $validated['notaris_id'] = auth()->user()->notaris_id;

        $this->service->update($id, $validated);
        notyf()->position('x', 'right')->position('y', 'top')->success('Waarmerking berhasil diubah.');

        return redirect()->route('notary-waarmerking.index');
    }

    public function destroy($id)
    {
        $this->service->delete($id);
        notyf()->position('x', 'right')->position('y', 'top')->success('Waarmerking berhasil dihapus.');

        return redirect()->route('notary-waarmerking.index');
    }
}
