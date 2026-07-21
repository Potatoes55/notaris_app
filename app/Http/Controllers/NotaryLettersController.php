<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Services\NotaryLetterService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class NotaryLettersController extends Controller
{
    protected $service;

    public function __construct(NotaryLetterService $service)
    {
        $this->service = $service;
    }

    /**
     * Helper untuk menentukan letter_type berdasarkan URL route saat ini
     */
    private function getLetterType(): string
    {
        if (
            request()->routeIs('*incoming*') ||
            request()->is('*surat-masuk*') ||
            request()->segment(2) === 'surat-masuk'
        ) {
            return 'surat_masuk';
        }

        return 'surat_keluar';
    }

    public function index(Request $request)
    {
        $search = $request->query('search');
        $letterType = $this->getLetterType();
        // dd($letterType);
        $notaryLetters = $this->service->getAll($search, $letterType);

        $module = request()->segment(1) === 'ppat' ? 'PPAT' : 'Notaris';

        return view('pages.BackOffice.Letters.index', compact(
            'notaryLetters',
            'module',
            'letterType'
        ));
    }

    public function create()
    {
        $letterType = $this->getLetterType();
        $clients = Client::where('notaris_id', auth()->user()->notaris_id)->get();

        return view('pages.BackOffice.Letters.form', compact('clients', 'letterType'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'client_code' => 'required',
            'letter_number' => 'required|string',
            'type' => 'nullable|string',
            'recipient' => 'required|string',
            'subject' => 'required|string',
            'date' => 'required|date',
            'summary' => 'nullable|string',
            'attachment' => 'nullable|string',
            'notes' => 'nullable|string',
            'file_path' => 'nullable|file|mimes:pdf,jpg,png,doc,docx|max:10240',
        ], [
            'client_code.required' => 'Klien harus dipilih.',
            'letter_number.required' => 'Nomor surat harus diisi.',
            'recipient.required' => 'Penerima harus diisi.',
            'subject.required' => 'Perihal harus diisi.',
            'date.required' => 'Tanggal harus diisi.',
            'file_path.max' => 'Ukuran file maksimal 10 MB.',
            'file_path.mimes' => 'Format file harus PDF, JPG, PNG, DOC, atau DOCX.',
        ]);

        if ($request->hasFile('file_path')) {
            $data['file_path'] = $request->file('file_path')->store('notary_letters', 'public');
        }

        // Set otomatis letter_type dan notaris_id
        $data['letter_type'] = $this->getLetterType();
        $data['notaris_id'] = auth()->user()->notaris_id;

        $this->service->create($data);

        notyf()->position('x', 'right')->position('y', 'top')->success('Surat berhasil ditambahkan.');

        // Redirect sesuai tipe suratnya
        $redirectRoute = $data['letter_type'] === 'surat_masuk'
            ? 'notary-letters.incoming.index'
            : 'notary-letters.index';

        return redirect()->route($redirectRoute);
    }

    public function edit($id)
    {
        $data = $this->service->getById($id);
        $letterType = $data->letter_type ?? $this->getLetterType();

        // Filter client berdasarkan notaris_id
        $clients = Client::where('notaris_id', auth()->user()->notaris_id)->get();

        return view('pages.BackOffice.Letters.form', compact('data', 'clients', 'letterType'));
    }

    public function update(Request $request, $id)
    {
        $letter = $this->service->getById($id);

        $data = $request->validate([
            'client_code' => 'required',
            'letter_number' => 'required|string',
            'type' => 'nullable|string',
            'recipient' => 'required|string',
            'subject' => 'required|string',
            'date' => 'required|date',
            'summary' => 'nullable|string',
            'attachment' => 'nullable|string',
            'notes' => 'nullable|string',
            'file_path' => 'nullable|file|mimes:pdf,jpg,png,doc,docx|max:10240',
        ], [
            'client_code.required' => 'Klien harus dipilih.',
            'letter_number.required' => 'Nomor surat harus diisi.',
            'recipient.required' => 'Penerima harus diisi.',
            'subject.required' => 'Perihal harus diisi.',
            'date.required' => 'Tanggal harus diisi.',
            'file_path.max' => 'Ukuran file maksimal 10 MB.',
            'file_path.mimes' => 'Format file harus PDF, JPG, PNG, DOC, atau DOCX.',
        ]);

        if ($request->hasFile('file_path')) {
            // Hapus file lama jika ada
            if ($letter->file_path && Storage::disk('public')->exists($letter->file_path)) {
                Storage::disk('public')->delete($letter->file_path);
            }
            $data['file_path'] = $request->file('file_path')->store('notary_letters', 'public');
        }

        $data['notaris_id'] = auth()->user()->notaris_id;

        $this->service->update($id, $data);
        notyf()->position('x', 'right')->position('y', 'top')->success('Surat berhasil diubah.');

        $redirectRoute = $letter->letter_type === 'surat_masuk'
            ? 'notary-letters.incoming.index'
            : 'notary-letters.index';

        return redirect()->route($redirectRoute);
    }

    public function destroy($id)
    {
        $letter = $this->service->getById($id);

        if ($letter->file_path && Storage::disk('public')->exists($letter->file_path)) {
            Storage::disk('public')->delete($letter->file_path);
        }

        $this->service->delete($id);
        notyf()->position('x', 'right')->position('y', 'top')->success('Surat berhasil dihapus.');

        return redirect()->back();
    }
}
