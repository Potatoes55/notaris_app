<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\NotaryAktaTransaction;
use App\Services\NotaryAktaPartyService;
use Illuminate\Http\Request;

class NotaryAktaPartiesController extends Controller
{
    protected $service;

    public function __construct(NotaryAktaPartyService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $notarisId = auth()->user()->notaris_id;
        $search = $request->input('search');

        $aktaInfo = null;
        $transactions = null; // Untuk menampung hasil jika pencarian berupa list/partial
        $parties = collect();

        if ($request->filled('search')) {
            // 1. Cari exact match (Kode Transaksi atau Nomor Akta Tepat)
            $aktaInfo = NotaryAktaTransaction::with(['client', 'akta_type'])
                ->where('notaris_id', $notarisId)
                ->where(function ($query) use ($search) {
                    $query->where('transaction_code', $search)
                        ->orWhere('akta_number', $search);
                })->first();

            if ($aktaInfo) {
                // Jika langsung ketemu data pastinya, ambil data pihak (parties)
                $parties = $this->service->getPartiesByAkta($aktaInfo->id, true);
            } else {
                // 2. Jika tidak ada yang pas, cari partial (Nomor Akta mirip ATAU Nama Klien mirip)
                $transactions = NotaryAktaTransaction::with(['client', 'akta_type'])
                    ->where('notaris_id', $notarisId)
                    ->where(function ($query) use ($search) {
                        $query->where('akta_number', 'like', '%'.$search.'%')
                            ->orWhereHas('client', function ($q) use ($search) {
                                $q->where('fullname', 'like', '%'.$search.'%');
                            });
                    })
                    ->orderBy('created_at', 'desc')
                    ->paginate(10)
                    ->withQueryString();

                if ($transactions->isEmpty()) {
                    notyf()
                        ->position('x', 'right')
                        ->position('y', 'top')
                        ->warning('Data transaksi atau nama klien tidak ditemukan');
                }
            }
        }

        return view('pages.BackOffice.AktaParties.index', compact('aktaInfo', 'transactions', 'parties', 'search'));
    }

    public function createData($akta_transaction_id)
    {
        $transaction = NotaryAktaTransaction::with('akta_type', 'notaris', 'client')
            ->findOrFail($akta_transaction_id);
        $clients = Client::where('deleted_at', null)->get();

        return view('pages.BackOffice.AktaParties.form', [
            'transaction' => $transaction,
            'aktaParty' => null,
            'clients' => $clients,
        ]);
    }

    public function storeData(Request $request)
    {
        $request->validate(
            [
                // 'client_code' => 'required',
                'name' => 'required|string',
                'role' => 'required|string',
                'address' => 'required|string',
                'id_number' => 'required|string',
                'id_type' => 'nullable|string',
                'note' => 'nullable|string',
            ],
            [
                // 'client_code.required' => 'Nomor akta harus diisi.',
                'name.required' => 'Nama harus diisi.',
                'role.required' => 'Peran harus diisi.',
                'address.required' => 'Alamat harus diisi.',
                'id_number.required' => 'Nomor identitas harus diisi.',
            ]
        );

        $this->service->store($request->all());

        notyf()->position('x', 'right')->position('y', 'top')->success('Pihak akta berhasil ditambahkan.');

        return redirect()->route('akta-parties.index', [
            'transaction_code' => $request->transaction_code,
            'akta_number' => $request->akta_number ?? null,
        ]);
    }

    public function edit($id)
    {
        $aktaParty = $this->service->findParty($id);
        $transaction = $aktaParty->akta_transaction; // relasi belongsTo
        $clients = Client::where('deleted_at', null)->get();

        return view('pages.BackOffice.AktaParties.form', compact('transaction', 'aktaParty', 'clients'));
    }

    public function update(Request $request, $id)
    {
        $this->service->updateParty($id, $request->all());

        notyf()->position('x', 'right')->position('y', 'top')->success('Pihak akta berhasil diperbarui.');

        return redirect()->route('akta-parties.index', [
            'transaction_code' => $request->transaction_code,
            'akta_number' => $request->akta_number ?? null,
        ]);
    }

    public function destroy($id)
    {
        $this->service->deleteParty($id);

        notyf()->position('x', 'right')->position('y', 'top')->success('Pihak akta berhasil dihapus.');

        return back();
    }
}
