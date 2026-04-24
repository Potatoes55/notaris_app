<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\NotaryLetters;
use App\Services\NotaryLetterService;
use Illuminate\Http\Request;

class NotaryLettersController extends Controller
{
    protected $service;

    public function __construct(NotaryLetterService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $search = $request->query('search');
        $notaryLetters = $this->service->getAll($search);

        return view('pages.BackOffice.Letters.index', compact('notaryLetters'));
    }

    public function create()
    {
        $clients = Client::all()->where('notaris_id', auth()->user()->notaris_id);

        return view('pages.BackOffice.Letters.form', compact('clients'));
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

        $data['notaris_id'] = auth()->user()->notaris_id;

        $this->service->create($data);

        notyf()->position('x', 'right')->position('y', 'top')->success('Surat berhasil ditambahkan.');

        return redirect()->route('notary-letters.index');
    }

    public function show(NotaryLetters $notaryLetters) {}

    public function edit($id)
    {
        $data = $this->service->getById($id);
        $clients = Client::all();

        return view('pages.BackOffice.Letters.form', compact('data', 'clients'));
    }

    public function update(Request $request, $id)
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

        $data['notaris_id'] = auth()->user()->notaris_id;

        $this->service->update($id, $data);
        notyf()->position('x', 'right')->position('y', 'top')->success('Surat berhasil diubah.');

        return redirect()->route('notary-letters.index');
    }

    public function destroy($id)
    {
        $this->service->delete($id);
        notyf()->position('x', 'right')->position('y', 'top')->success('Surat berhasil dihapus.');

        return redirect()->route('notary-letters.index');
    }
}
