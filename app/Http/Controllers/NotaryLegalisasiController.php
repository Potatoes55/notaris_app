<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\NotaryLegalisasi;
use App\Services\NotaryLegalisasiService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class NotaryLegalisasiController extends Controller
{
    protected $service;

    public function __construct(NotaryLegalisasiService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['legalisasi_number', 'sort']);
        $perPage = $request->get('perPage', 10);

        $data = $this->service->list($filters, $perPage);

        return view('pages.BackOffice.Legalisasi.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $clients = Client::all()->where('notaris_id', auth()->user()->notaris_id);

        return view('pages.BackOffice.Legalisasi.form', compact('clients'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //    dd($request->all());
        // Validasi input
        $validated = $request->validate(
            [
                'client_code' => 'required',
                'legalisasi_number' => 'required|string|max:255',
                'applicant_name' => 'required|string|max:255',
                'officer_name' => 'required|string|max:255',
                'document_type' => 'nullable|string|max:255',
                'document_number' => 'nullable|string|max:255',
                'request_date' => 'nullable|date',
                'release_date' => 'nullable|date',
                'notes' => 'nullable|string',
                'file_path' => 'nullable|mimes:pdf,jpg,jpeg,png|max:2048',
            ],
            [
                'client_code.required' => 'Klien harus dipilih.',
                'legalisasi_number.required' => 'Nomor Legalisasi harus diisi.',
                'legalisasi_number.unique' => 'Nomor Legalisasi sudah ada.',
                'applicant_name.required' => 'Nama Pemohon harus diisi.',
                'officer_name.required' => 'Nama Petugas harus diisi.',
                'file_path.max' => 'Ukuran file maksimal 2 MB.',
                'file_path.mimes' => 'Format file harus PDF, JPG, JPEG, atau PNG.',
            ]
        );

        // Handle file upload jika ada
        if ($request->hasFile('file_path')) {
            $validated['file_path'] = $request->file('file_path')->storeAs('documents', $request->file('file_path')->getClientOriginalName());
        }

        $validated['notaris_id'] = auth()->user()->notaris_id;
        // dd($validated);

        // Simpan ke database
        $this->service->create($validated);

        // Tambahkan pesan sukses
        notyf()->position('x', 'right')->position('y', 'top')->success('Legalisasi berhasil ditambahkan.');

        // Redirect dengan pesan sukses
        return redirect()->route('notary-legalisasi.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(NotaryLegalisasi $notaryLegalisasi)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $clients = Client::all();
        $data = $this->service->findById($id);

        return view('pages.BackOffice.Legalisasi.form', compact('data', 'clients'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate(
            [
                'client_code' => 'required',
                'legalisasi_number' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('notary_legalisasis', 'legalisasi_number')->ignore($id),
                ],
                'applicant_name' => 'nullable|string|max:255',
                'officer_name' => 'nullable|string|max:255',
                'document_type' => 'nullable|string|max:255',
                'document_number' => 'nullable|string|max:255',
                'request_date' => 'nullable|date',
                'release_date' => 'nullable|date',
                'notes' => 'nullable|string',
                'file_path' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            ],
            [
                'client_code.required' => 'Klien harus dipilih.',
                'legalisasi_number.required' => 'Nomor Legalisasi harus diisi.',
                'legalisasi_number.unique' => 'Nomor Legalisasi sudah ada.',
                'applicant_name.required' => 'Nama Pemohon harus diisi.',
                'file_path.max' => 'Ukuran file maksimal 10 MB.',
                'file_path.mimes' => 'Format file harus PDF, JPG, JPEG, atau PNG.',
            ]
        );

        if ($request->hasFile('file_path')) {
            $validated['file_path'] = $request->file('file_path')->storeAs(
                'documents',
                $request->file('file_path')->getClientOriginalName()
            );
        }

        $validated['notaris_id'] = auth()->user()->notaris_id;

        $this->service->update($id, $validated);

        notyf()->position('x', 'right')->position('y', 'top')->success('Legalisasi berhasil diubah.');

        return redirect()->route('notary-legalisasi.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $this->service->delete($id);
        notyf()->position('x', 'right')->position('y', 'top')->success('Legalisasi berhasil dihapus.');

        return redirect()->route('notary-legalisasi.index');
    }
}
