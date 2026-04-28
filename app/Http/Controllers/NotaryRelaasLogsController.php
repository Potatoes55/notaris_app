<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\NotaryRelaasAkta;
use App\Services\NotaryRelaasLogsService;
use Illuminate\Http\Request;

class NotaryRelaasLogsController extends Controller
{
    protected $service;

    public function __construct(NotaryRelaasLogsService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $filters = request()->only(['client_code', 'step']);
        $logs = $this->service->getAll();

        return view('pages.BackOffice.RelaasAkta.AktaLogs.index', compact('logs'));
    }

    public function create()
    {
        // Tambahkan with('client') agar data klien ikut terbawa ke View & JS
        $relaasAktas = NotaryRelaasAkta::with('client')->get();
        $clients = Client::whereNull('deleted_at')->get();

        // dd($relaasAktas, $clients);

        return view('pages.BackOffice.RelaasAkta.AktaLogs.form', compact('relaasAktas', 'clients'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'relaas_id' => 'required|integer',
            'step' => 'required|string',
            'note' => 'nullable|string',
        ], [
            'relaas_id.required' => 'Relaas Akta harus dipilih.',
            'step.required' => 'Langkah harus diisi.',
        ]);

        $relaas = NotaryRelaasAkta::find(
            $validated['relaas_id']
        );

        $this->service->create([
            'notaris_id' => $relaas->notaris_id,
            'client_code' => $relaas->client_code,
            // 'registration_code' => $relaas->registration_code,
            'relaas_id' => $validated['relaas_id'],
            'step' => $validated['step'],
            'note' => $validated['note'],
        ]);
        notyf()->position('x', 'right')->position('y', 'top')->success('Log berhasil ditambahkan.');

        return redirect()->route('relaas-logs.index');
    }

    public function edit($id)
    {
        $data = $this->service->findById($id);
        $relaasAktas = NotaryRelaasAkta::with('client')->get();
        $clients = Client::whereNull('deleted_at')->get();

        return view('pages.BackOffice.RelaasAkta.AktaLogs.form', compact('data', 'relaasAktas', 'clients'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'client_code' => 'required',
            'relaas_id' => 'required|integer',
            'step' => 'required|string',
            'note' => 'nullable|string',
        ], [
            'client_code.required' => 'Klien harus dipilih.',
            'relaas_id.required' => 'Relaas Akta harus dipilih.',
            'step.required' => 'Langkah harus diisi.',
        ]);

        $this->service->update($id, $validated);
        notyf()->position('x', 'right')->position('y', 'top')->success('Log berhasil diperbarui.');

        return redirect()->route('relaas-logs.index');
    }

    public function destroy($id)
    {
        $this->service->delete($id);
        notyf()->position('x', 'right')->position('y', 'top')->success('Log berhasil dihapus.');

        return redirect()->route('relaas-logs.index');
    }
}
