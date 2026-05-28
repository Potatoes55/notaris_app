<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Notaris;
use App\Models\NotaryAktaLogs;
use App\Models\NotaryAktaTransaction;
use App\Services\NotaryAktaLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class NotaryAktaLogsController extends Controller
{
    protected $service;

    public function __construct(NotaryAktaLogService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['client_code', 'step']);
        $logs = $this->service->list($filters);

        return view('pages.BackOffice.AktaLogs.index', compact('logs', 'filters'));
    }

    public function create()
    {
        $notaris = Notaris::all();
        $clients = Client::all();
        $transactions = NotaryAktaTransaction::all();

        // dd($notaris, $clients, $transactions);

        return view('pages.BackOffice.AktaLogs.form', compact('notaris', 'clients', 'transactions'));
    }

    // public function generateRegistrationCode(int $notarisId, int $clientId): string
    // {
    //     $today = Carbon::now()->format('Ymd');

    //     // Hitung jumlah konsultasi notaris ini hari ini
    //     $countToday = NotaryAktaLogs::where('notaris_id', $notarisId)
    //         ->where('client_code', $clientId)
    //         ->whereDate('created_at', Carbon::today())
    //         ->count();

    //     $countToday += 1; // untuk konsultasi baru ini

    //     return 'N' . '-' . $today . '-' . $notarisId . '-' . $clientId . '-' . $countToday;
    // }

    public function store(Request $request)
    {
        $data = $request->validate([
            // 'notaris_id' => 'required|exists:notaris,id',

            'client_code' => 'required',
            'akta_transaction_id' => 'required',
            'step' => 'required|string',
            'note' => 'nullable|string',
        ], [
            'client_code.required' => 'Klien harus dipilih.',
            'step.required' => 'Step harus diisi.',
            'akta_transaction_id.required' => 'Transaksi akta harus dipilih.',
        ]);

        $data['notaris_id'] = auth()->user()->notaris_id;

        $this->service->create($data);

        notyf()->position('x', 'right')->position('y', 'top')->success('Berhasil menambahkan log.');

        return redirect()->route('akta-logs.index');
    }

    public function edit($id)
    {
        $log = $this->service->get($id);
        $notaris = Notaris::all();
        $clients = Client::all();
        $transactions = NotaryAktaTransaction::all();

        return view('pages.BackOffice.AktaLogs.form', compact('log', 'notaris', 'clients', 'transactions'));
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            // 'notaris_id' => 'required|exists:notaris,id',
            'client_code' => 'required',
            'akta_transaction_id' => 'required',
            'step' => 'required|string',
            'note' => 'nullable|string',
        ], [
            'client_code.required' => 'Klien harus dipilih.',
            'step.required' => 'Step harus diisi.',
            'akta_transaction_id.required' => 'Transaksi akta harus dipilih.',
        ]);

        $this->service->update($id, $data);

        notyf()->position('x', 'right')->position('y', 'top')->success('Berhasil memperbarui log.');

        return redirect()->route('akta-logs.index');
    }

    public function destroy($id)
    {
        $this->service->delete($id);

        notyf()->position('x', 'right')->position('y', 'top')->success('Berhasil menghapus log.');

        return redirect()->route('akta-logs.index');
    }
}
