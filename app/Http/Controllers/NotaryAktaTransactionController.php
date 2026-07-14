<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\NotaryAktaTransaction;
use App\Models\NotaryAktaTypes;
use App\Services\NotaryAktaTransactionService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class NotaryAktaTransactionController extends Controller
{
    protected $service;

    public function __construct(NotaryAktaTransactionService $service)
    {
        $this->service = $service;
    }

    public function selectClient(Request $request)
    {
        $notarisId = auth()->user()->notaris_id;

        $clients = Client::where('notaris_id', $notarisId)
            ->when($request->search, function ($query, $search) {
                $query->where('fullname', 'like', '%'.$search.'%')->orWhere('client_code', 'like', '%'.$search.'%');
            })
            ->where('deleted_at', null)
            ->withCount('aktaTransactions')
            ->paginate(10);

        return view('pages.BackOffice.AktaTransaction.selectClient', [
            'clients' => $clients,
        ]);
    }

    public function index(Request $request)
    {
        $filters = $request->only(['status', 'transaction_code', 'client_code']);
        $transactions = $this->service->list($filters);

        return view('pages.BackOffice.AktaTransaction.index', compact('transactions', 'filters'));
    }

    public function create(Request $request)
    {
        $clientCode = $request->query('client_code');

        $client = Client::where('client_code', $clientCode)->firstOrFail();

        $aktaTypes = NotaryAktaTypes::where('deleted_at', null)->where('notaris_id', auth()->user()->notaris_id)->get();

        return view('pages.BackOffice.AktaTransaction.form', compact('clientCode', 'aktaTypes'));
    }

    // public function generateTransactionCode(int $notarisId, string $clientId): string
    // {
    //     $today = Carbon::now()->format('Ymd');

    //     $countToday = NotaryAktaTransaction::where('notaris_id', $notarisId)
    //         // ->where('client_code', $clientId)
    //         ->whereDate('created_at', Carbon::today())
    //         ->where('deleted_at', null)
    //         ->count();

    //     $countToday += 1;

    //     return 'T' . '-' . 'A' . '-'  . $today . '-' . $notarisId . '-'   . $countToday;
    // }

    // public function generateTransactionCode(int $notarisId, string $clientCode): string
    // {
    //     $today = now()->format('Ymd');

    //     $countToday = NotaryAktaTransaction::where('notaris_id', $notarisId)
    //         ->where('client_code', $clientCode)
    //         ->whereDate('created_at', today())
    //         ->count() + 1;

    //     return 'T-' . $clientCode . '-' . $today . '-' . $countToday;
    // }

    public function generateTransactionCode(int $notarisId, string $clientId): string
    {
        $now = Carbon::now();

        $date = $now->format('Ymd');
        $yearMonth = $now->format('Ym');

        $count = NotaryAktaTransaction::where('notaris_id', $notarisId)
            ->whereYear('created_at', $now->year)
            ->whereMonth('created_at', $now->month)
            ->whereNull('deleted_at')
            ->count();

        $count += 1;

        return 'T-A-'.$date.'-'.$notarisId.'-'.$count;
    }

    public function store(Request $request)
    {
        $data = $request->validate(
            [
                'akta_type_id' => 'required',
                'date_submission' => 'nullable|date',
                'date_finished' => 'nullable|date',
                'note' => 'nullable|string',
            ],
            [
                'akta_type_id.required' => 'Jenis akta harus dipilih.',
            ]
        );

        $clientCode = $request->client_code;

        $data['client_code'] = $clientCode;

        // $data['transaction_code'] = $clientCode;
        $notarisId = auth()->user()->notaris_id;
        $data['transaction_code'] = $this->generateTransactionCode($notarisId, $clientCode);

        $data['status'] = 'draft';
        $data['year'] = null;
        $data['akta_number'] = null;
        $data['akta_number_created_at'] = null;
        $data['notaris_id'] = auth()->user()->notaris_id;

        $this->service->create($data);

        notyf()->position('x', 'right')->position('y', 'top')->success('Berhasil menambahkan transaksi akta notaris.');

        return redirect()->route('akta-transactions.index', ['client_code' => $clientCode]);
    }

    public function edit($id)
    {
        $transaction = $this->service->get($id);
        $clientCode = $transaction->client_code;
        $aktaTypes = NotaryAktaTypes::where('deleted_at', null)->where('notaris_id', auth()->user()->notaris_id)->get();

        return view('pages.BackOffice.AktaTransaction.form', compact('transaction', 'clientCode', 'aktaTypes'));
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate(
            [
                'akta_type_id' => 'required',
                'date_submission' => 'required|date',
                'date_finished' => 'required|date',
                'status' => 'required',
                'note' => 'nullable|string',
            ],
            [
                'akta_type_id.required' => 'Jenis akta harus dipilih.',
                'date_submission.required' => 'Tanggal pengajuan harus diisi.',
                'date_finished.required' => 'Tanggal selesai harus diisi.',
                'status.required' => 'Status harus diisi.',
            ]
        );

        $data['notaris_id'] = auth()->user()->notaris_id;

        $this->service->update($id, $data);

        notyf()->position('x', 'right')->position('y', 'top')->success('Berhasil memperbarui transaksi akta notaris.');

        return redirect()->route('akta-transactions.index', ['client_code' => $request->client_code]);
    }

    public function destroy($id, Request $request)
    {
        $clientCode = $request->client_code;
        $this->service->delete($id);

        notyf()->position('x', 'right')->position('y', 'top')->success('Berhasil menghapus transaksi akta notaris.');

        return redirect()->route('akta-transactions.index', [
            'client_code' => $request->client_code,
        ]);
    }

    // Penomoran Akta
    public function indexNumber(Request $request)
    {
        $notarisId = auth()->user()->notaris_id;

        $lastAkta = NotaryAktaTransaction::orderBy('created_at', 'desc')
            ->where('notaris_id', $notarisId)
            ->first();

        $aktaInfo = null;
        $transactions = null;

        if ($request->filled('search')) {
            $search = $request->search;

            $aktaInfo = NotaryAktaTransaction::with(['client', 'akta_type', 'notaris'])
                ->where('notaris_id', $notarisId)
                ->where(function ($query) use ($search) {
                    $query->where('transaction_code', $search)
                        ->orWhere('akta_number', $search);
                })->first();

            if (! $aktaInfo) {
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
                        ->warning('Data transaksi tidak ditemukan');
                }
            }
        }

        return view('pages.BackOffice.AktaNumber.index', compact('lastAkta', 'aktaInfo', 'transactions'));
    }

    public function storeNumber(Request $request)
    {
        $request->validate([
            'akta_number' => 'required',
            'year' => 'required|integer',
        ]);

        $akta = NotaryAktaTransaction::findOrFail($request->transaction_id);

        $isEdit = ! is_null($akta->akta_number);

        $akta->update([
            'akta_number' => $request->akta_number,
            'year' => $request->year,
            'akta_number_created_at' => now(),
        ]);

        if ($isEdit) {
            notyf()->position('x', 'right')->position('y', 'top')
                ->success('Nomor akta berhasil diperbarui.');
        } else {
            notyf()->position('x', 'right')->position('y', 'top')
                ->success('Nomor akta berhasil ditambahkan.');
        }

        return redirect()->route('akta_number.index', ['search' => $akta->transaction_code]);
    }

    public function updateNumber(Request $request, $id)
    {
        $request->validate(
            [
                'akta_number' => 'required',
                'year' => 'required',
            ],
            [
                'akta_number.required' => 'Nomor akta harus diisi.',
                'year.required' => 'Tahun harus diisi.',
            ]
        );

        $akta = NotaryAktaTransaction::findOrFail($id);
        $akta->update([
            'akta_number' => $request->akta_number,
            'year' => $request->year,
        ]);

        notyf()->position('x', 'right')->position('y', 'top')->success('Nomor akta berhasil diperbarui.');

        return redirect()->route('akta_number.index', ['search' => $akta->transaction_code]);
    }
}
