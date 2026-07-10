<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\NotaryRelaasAkta;
use App\Services\RelaasPartiesService;
use Illuminate\Http\Request;

class NotaryRelaasPartiesController extends Controller
{
    protected $service;

    public function __construct(RelaasPartiesService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $notarisId = auth()->user()->notaris_id;
        $search = $request->input('search');

        $relaasInfo = null;
        $transactions = null;
        $parties = collect();

        if (
            $request->filled('search')) {

            $relaasInfo = NotaryRelaasAkta::with(['client', 'akta_type'])
                ->where('notaris_id', $notarisId)
                ->where(function ($query) use ($search) {
                    $query->where('transaction_code', $search)
                        ->orWhere('relaas_number', $search);
                })->first();

            if ($relaasInfo) {
                $parties = $this->service->getParties($relaasInfo->id);
            } else {
                $transactions = NotaryRelaasAkta::with(['client', 'akta_type'])
                    ->where('notaris_id', $notarisId)
                    ->where(function ($query) use ($search) {
                        $query->where('relaas_number', 'like', '%'.$search.'%')
                            ->orWhereHas('client', function ($clientQuery) use ($search) {
                                $clientQuery->where('fullname', 'like', '%'.$search.'%');
                            });
                    })
                    ->orderBy('created_at', 'desc')
                    ->paginate()
                    ->withQueryString();

                if ($transactions->isEmpty()) {
                    notyf()
                        ->position('x', 'right')
                        ->position('y', 'top')
                        ->warning('Data pihak akta tidak ditemukan');
                }
            }
        }

        return view(
            'pages.BackOffice.RelaasAkta.AktaParties.index',
            compact('relaasInfo', 'parties', 'transactions', 'search')
        );
    }

    public function create($relaasId)
    {
        // relaas untuk header + back-link + action form
        $relaas = NotaryRelaasAkta::findOrFail($relaasId);
        $party = null;
        $clients = Client::where('deleted_at', null)->get();

        return view('pages.BackOffice.RelaasAkta.AktaParties.form', compact('relaas', 'party', 'clients'));
    }

    public function edit($relaasId, $id)
    {
        $party = $this->service->findById($id);

        // pastikan relaas mengikuti data party (lebih aman)
        $relaas = NotaryRelaasAkta::findOrFail($party->relaas_id);
        $clients = Client::where('deleted_at', null)->get();

        return view('pages.BackOffice.RelaasAkta.AktaParties.form', compact('relaas', 'party', 'clients'));
    }

    public function store(Request $request, $relaasId)
    {
        $relaas = NotaryRelaasAkta::findOrFail($relaasId);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'role' => 'required|string|max:255',
            'address' => 'required|string',
            'id_number' => 'required',
            'id_type' => 'nullable',
        ], [
            'name.required' => 'Nama harus diisi.',
            'role.required' => 'Peran harus diisi.',
            'address.required' => 'Alamat harus diisi.',
            'id_number.required' => 'Nomor identitas harus diisi.',
        ]);

        $validated['relaas_id'] = $relaas->id;
        // $validated['registration_code'] = $relaas->registration_code;
        $validated['client_code'] = $relaas->client_code;
        $validated['notaris_id'] = $relaas->notaris_id;

        $this->service->store($validated);

        notyf()->position('x', 'right')->position('y', 'top')->success('Pihak Akta berhasil ditambahkan.');

        // pakai registration_code yang dikirim hidden dari form
        return redirect()->route('relaas-parties.index', [
            'search' => $relaas->transaction_code,
        ]);
    }

    public function update(Request $request, $relaasId, $id)
    {

        $relaas = NotaryRelaasAkta::findOrFail($relaasId);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'role' => 'required|string|max:255',
            'address' => 'nullable|string',
            'id_number' => 'required',
            'id_type' => 'nullable',
        ], [
            'name.required' => 'Nama harus diisi.',
            'role.required' => 'Peran harus diisi.',
            'id_number.required' => 'Nomor identitas harus diisi.',
            'id_type.required' => 'Jenis identitas harus diisi.',
        ]);

        $validated['relaas_id'] = $relaas->id;
        // $validated['registration_code'] = $relaas->registration_code;
        $validated['client_code'] = $relaas->client_code;
        $validated['notaris_id'] = $relaas->notaris_id;

        $this->service->update($id, $validated);

        notyf()->position('x', 'right')->position('y', 'top')->success('Pihak Akta berhasil diperbarui.');

        return redirect()->route('relaas-parties.index', [
            'search' => $relaas->transaction_code,
        ]);
    }

    public function destroy($id)
    {
        $this->service->destroy($id);

        notyf()->position('x', 'right')->position('y', 'top')->success('Pihak Akta berhasil dihapus.');

        return redirect()->back();
    }
}
