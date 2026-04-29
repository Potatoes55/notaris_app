<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Notaris;
use App\Models\NotaryRelaasAkta;
use App\Models\RelaasType;
use App\Services\NotaryRelaasAktaService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class NotaryRelaasAktaController extends Controller
{
    protected $service;

    public function __construct(NotaryRelaasAktaService $service)
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
            ->withCount('aktaTransactionsRelaas')
            ->paginate(10);

        return view('pages.BackOffice.RelaasAkta.AktaTransaction.selectClient', [
            'clients' => $clients,
        ]);
    }

    public function index(Request $request)
    {
        $perPage = 10;

        if ($request->filled('client_code')) {
            $data = $this->service->searchByRegistrationCode($request->client_code, $perPage);
        } elseif ($request->filled('status')) {
            $data = $this->service->filterByStatus($request->status, $perPage);
        } else {
            $data = $this->service->getAll($perPage);
        }

        return view('pages.BackOffice.RelaasAkta.AktaTransaction.index', compact('data'));
    }

    // public function create(Request $request)
    // {
    //     $clients = Client::where('deleted_at', null)->get();
    //     $notaris = Notaris::where('deleted_at', null)->get();
    //     $relaasType = RelaasType::where('deleted_at', null)->get();
    //     return view('pages.BackOffice.RelaasAkta.AktaTransaction.form', compact('clients', 'notaris', 'relaasType'));
    // }

    public function generateTransactionCode(int $notarisId, string $clientId): string
    {
        $now = Carbon::now();

        $year = $now->format('Y');
        $date = $now->format('Ymd');

        $count = NotaryRelaasAkta::where('notaris_id', $notarisId)
            ->whereYear('created_at', $year)
            ->whereNull('deleted_at')
            ->count();

        $count += 1;

        return 'T-P-'.$date.'-'.$notarisId.'-'.$count;
    }

    // public function generateTransactionCode(int $notarisId, string $clientCode): string
    // {
    //     $today = now()->format('Ymd');

    //     $countToday = NotaryRelaasAkta::where('notaris_id', $notarisId)
    //         ->where('client_code', $clientCode)
    //         ->whereDate('created_at', today())
    //         ->count() + 1;

    //     return 'T-' . $clientCode . '-' . $today . '-' . $countToday;
    // }

    public function create(Request $request)
    {
        $clientCode = $request->query('client_code');
        $client = Client::where('client_code', $clientCode)->firstOrFail();
        $notaris = Notaris::whereNull('deleted_at')->get();
        $relaasType = RelaasType::whereNull('deleted_at')->where('notaris_id', auth()->user()->notaris_id)->get();

        return view('pages.BackOffice.RelaasAkta.AktaTransaction.form', compact(
            'client',
            'clientCode',
            'notaris',
            'relaasType'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            // 'client_code' => 'required',
            'year' => 'nullable|digits:4|integer',
            'relaas_number' => 'nullable',
            'relaas_number_created_at' => 'nullable',
            'title' => 'required|string',
            'relaas_type_id' => 'required',
            'story' => 'required|string',
            'story_date' => 'required|date',
            'story_location' => 'required|string',
            'status' => 'required|string',
            'note' => 'nullable|string',
        ], [
            // 'client_code.required' => 'Klien harus dipilih.',
            'title.required' => 'Judul harus diisi.',
            'story.required' => 'Cerita harus diisi.',
            'story_date.required' => 'Tanggal cerita harus diisi.',
            'story_location.required' => 'Lokasi Story harus diisi.',
            'status.required' => 'Status harus diisi.',
        ]);

        $clientCode = $request->client_code;

        $validated['client_code'] = $clientCode;
        $notarisId = auth()->user()->notaris_id;
        // transaction_code sementara sama
        $validated['transaction_code'] = $this->generateTransactionCode($notarisId, $clientCode);

        $validated['notaris_id'] = auth()->user()->notaris_id;

        $this->service->create($validated);

        notyf()->position('x', 'right')->position('y', 'top')->success('Transaksi Akta berhasil ditambahkan.');

        return redirect()->route('relaas-aktas.index', ['client_code' => $clientCode]);
    }

    public function edit($id)
    {
        $data = $this->service->getById($id);
        $clientCode = $data->client_code;
        $relaasType = RelaasType::where('deleted_at', null)->where('notaris_id', auth()->user()->notaris_id)->get();

        return view('pages.BackOffice.RelaasAkta.AktaTransaction.form', compact('data', 'clientCode', 'relaasType'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            // 'client_code' => 'required',
            'year' => 'nullable|digits:4|integer',
            'relaas_type_id' => 'required',
            'relaas_number' => 'nullable',
            'relaas_number_created_at' => 'nullable',
            'title' => 'required|string',
            'story' => 'required|string',
            'story_date' => 'required|date',
            'story_location' => 'required|string',
            'status' => 'required|string',
            'note' => 'nullable|string',
        ]);

        $this->service->update($id, $validated);

        notyf()->position('x', 'right')->position('y', 'top')->success('Transaksi akta berhasil diperbarui.');

        return redirect()->route('relaas-aktas.index', ['client_code' => $request->client_code]);
    }

    public function destroy($id, Request $request)
    {
        $this->service->delete($id);

        notyf()->position('x', 'right')->position('y', 'top')->success('Transaksi akta berhasil dihapus.');

        return redirect()->route('relaas-aktas.index', ['client_code' => $request->client_code]);
    }

    // public function indexNumber(Request $request)
    // {

    //     $lastAkta = NotaryRelaasAkta::orderBy('relaas_number_created_at', 'desc')->first();
    //     $aktaInfo = null;

    //     if ($request->filled('search')) {
    //         $aktaInfo = NotaryRelaasAkta::where('relaas_number', 'like', '%' . $request->search . '%')
    //             ->orWhere('registration_code', 'like', '%' . $request->search . '%')
    //             ->first();
    //     }

    //     return view('pages.BackOffice.RelaasAkta.AktaNumber.index', compact('lastAkta', 'aktaInfo'));
    // }

    // public function indexNumber(Request $request)
    // {
    //     $aktaInfo = null;
    //     $lastAkta = NotaryRelaasAkta::orderBy('relaas_number_created_at', 'desc')->first();

    //     // Kalau user melakukan pencarian
    //     if ($request->filled('search')) {
    //         $aktaInfo = NotaryRelaasAkta::where(function ($q) use ($request) {
    //             $q->where('client_code', $request->search)
    //                 ->orWhere('relaas_number', $request->search);
    //         })
    //             ->orderBy('relaas_number', 'desc') // ambil yang sudah ada nomornya
    //             ->first();
    //     } else {
    //         // Kalau tidak ada pencarian, tampilkan nomor akta terakhir
    //         $lastAkta = NotaryRelaasAkta::orderBy('relaas_number_created_at', 'desc')->first();
    //         dd($lastAkta);
    //     }

    //     return view('pages.BackOffice.RelaasAkta.AktaNumber.index', compact('lastAkta', 'aktaInfo'));
    // }

    public function indexNumber(Request $request)
    {
        // Ambil nomor akta terakhir (tetap ditampilkan)
        $lastAkta = NotaryRelaasAkta::orderBy('relaas_number_created_at', 'desc')->first();

        $aktaInfo = null;

        // Jika user melakukan pencarian
        if ($request->filled('search')) {

            $search = $request->search;

            $aktaInfo = NotaryRelaasAkta::where('notaris_id', auth()->user()->notaris_id)->where(function ($q) use ($search) {
                $q->where('transaction_code', 'like', "%$search%")
                    ->orWhere('relaas_number', 'like', "%$search%");
            })

                ->orderByRaw('CASE WHEN relaas_number IS NULL THEN 1 ELSE 0 END')
                ->orderBy('relaas_number', 'desc')
                ->first();

            if (! $aktaInfo) {
                notyf()
                    ->position('x', 'right')
                    ->position('y', 'top')
                    ->warning('Kode Transaksi atau Nomor Akta tidak ditemukan');
            }
        }

        return view('pages.BackOffice.RelaasAkta.AktaNumber.index', compact('lastAkta', 'aktaInfo'));
    }

    public function storeNumber(Request $request)
    {
        $request->validate(
            [
                'relaas_number' => 'required|integer',
                'year' => 'required',
            ],
            [
                'relaas_number.required' => 'Nomor akta harus diisi.',
                'relaas_number.integer' => 'Nomor akta harus berupa angka.',
                'year.required' => 'Tahun harus diisi.',
            ]
        );

        $akta = NotaryRelaasAkta::findOrFail($request->relaas_id);

        // Cek apakah ini edit atau create
        $isEdit = ! is_null($akta->relaas_number);

        // Update data
        $akta->update([
            'relaas_number' => $request->relaas_number,
            'year' => $request->year,
            'relaas_number_created_at' => now(),
        ]);

        // Alert berbeda
        if ($isEdit) {
            notyf()->position('x', 'right')->position('y', 'top')->success('Nomor Akta berhasil diperbarui.');
        } else {
            notyf()->position('x', 'right')->position('y', 'top')->success('Nomor Akta berhasil disimpan.');
        }

        return redirect()->route('relaas_akta.indexNumber', [
            'search' => $akta->transaction_code,
        ]);
    }

    public function updateNumber(Request $request, $id)
    {
        $request->validate(
            [
                'relaas_number' => 'required',
                'year' => 'required',
            ],
            [
                'relaas_number.required' => 'Nomor akta harus diisi.',
                'year.required' => 'Tahun harus diisi.',
            ]
        );

        $akta = NotaryRelaasAkta::findOrFail($id);
        $akta->update([
            'relaas_number' => $request->relaas_number,
            'year' => $request->year,
        ]);

        notyf()->position('x', 'right')->position('y', 'top')->success('Nomor akta berhasil diperbarui.');

        return redirect()->route('relaas_akta.indexNumber', ['search' => $akta->transaction_code]);
    }
}
