<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\PicDocuments;
use App\Models\ProsesLain;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

use function Flasher\Notyf\Prime\notyf;

class ProsesLainController extends Controller
{
    public function index(Request $request)
    {
        $notarisId = auth()->user()->notaris_id;
        $query = ProsesLain::query()->where('notaris_id', $notarisId);

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('client_code', 'like', '%' . $request->search . '%');
            });
        }

        $prosesLain = $query->latest()->paginate(10);

        return view('pages.ProsesLain.Transaksi.index', compact('prosesLain'));
        $check = ProsesLain::with(['client', 'picStaff'])->first();
        dd($check);
    }

    public function create()
    {
        $clients = Client::where('notaris_id', auth()->user()->notaris_id)->where('deleted_at', null)->get();

        return view('pages.ProsesLain.Transaksi.form', compact('clients'));
    }

    // // Urutan status tetap
    // $urutan = ['Baru', 'Proses', 'Selesai'];

    // // Ambil data terakhir berdasarkan client_code
    // $last = ProsesLain::where('client_code', $request->client_code)
    //     ->latest()
    //     ->first();

    // if (!$last) {
    //     $statusBaru = 'Baru';
    // } else {
    //     $index = array_search($last->status, $urutan);

    //     // Kalau sudah selesai, tetap selesai
    //     if ($index === false || $index == count($urutan) - 1) {
    //         $statusBaru = 'selesai';
    //     } else {
    //         $statusBaru = $urutan[$index + 1];
    //     }
    // }
    // $prefix = 'T-' . strtoupper(substr($request->client_code, 0, 3)) . '-';

    // // Ambil kode terakhir untuk client tersebut
    // $lastCode = ProsesLain::where('client_code', $request->client_code)
    //     ->where('transaction_code', 'like', $prefix . '%')
    //     ->orderByDesc('transaction_code')
    //     ->value('transaction_code');

    // if ($lastCode) {
    //     // Ambil angka terakhir
    //     $lastNumber = (int) substr($lastCode, -4);
    //     $nextNumber = $lastNumber + 1;
    // } else {
    //     $nextNumber = 1;
    // }

    // // Format jadi 4 digit
    // $paymentCode = $prefix . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

    public function generateTransactionCode(int $notarisId): string
    {
        $now = Carbon::now();
        $date = $now->format('Ymd'); // 20260602

        // Count how many transactions already exist for THIS notary TODAY
        $count = ProsesLain::where('notaris_id', $notarisId)
            ->whereDate('created_at', $now->toDateString())
            ->count();

        $nextNumber = $count + 1;

        // Pad the number with leading zeros (e.g., 1 becomes 0001)
        $paddedNumber = str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        // Yields something short and clean like: T-PL-20260602-0001
        return 'T-PL-'.$date.'-'.$paddedNumber;
    }

    public function store(Request $request)
    {
        $request->validate([
            'client_code' => 'required',
            'name' => 'required',
            'time_estimation' => 'required|integer',
        ], [
            'client_code.required' => 'Kode klien wajib diisi.',
            'name.required' => 'Nama proses wajib diisi.',
            'time_estimation.required' => 'Estimasi waktu wajib diisi.',
            'time_estimation.integer' => 'Estimasi waktu harus berupa angka.',
        ]);

        // Generate the code using just the notary ID to fetch today's count
        $transactionCode = $this->generateTransactionCode(auth()->user()->notaris_id);

        ProsesLain::create([
            'client_code' => $request->client_code,
            'notaris_id' => auth()->user()->notaris_id,
            'name' => $request->name,
            'time_estimation' => $request->time_estimation,
            'status' => $request->status,
            'transaction_code' => $transactionCode,
        ]);

        notyf()->position('x', 'right')
            ->position('y', 'top')
            ->success('Data berhasil disimpan.');

        return redirect()->route('proses-lain-transaksi.index');
    }

    public function edit($id)
    {
        $data = ProsesLain::findOrFail($id);
        $clients = Client::where('notaris_id', auth()->user()->notaris_id)->where('deleted_at', null)->get();

        return view('pages.ProsesLain.Transaksi.form', compact('data', 'clients'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'client_code' => 'required',
            'name' => 'required',
            'time_estimation' => 'required|integer',
        ]);

        $data = ProsesLain::findOrFail($id);
        $data->update([
            'client_code' => $request->client_code,
            'name' => $request->name,
            'time_estimation' => $request->time_estimation,
        ]);

        notyf()->position('x', 'right')->position('y', 'top')->success('Data berhasil diubah.');
        return redirect()->route('proses-lain-transaksi.index');
    }

    public function destroy(Request $request, $id)
    {
        $data = ProsesLain::findOrFail($id);
        $data = ProsesLain::where('id', $id)
                    ->where('notaris_id', auth()->user()->notaris_id)
                    ->firstOrFail();

        if (str_contains($request->headers->get('referer'), 'proses-lain-pic')) {
            $data->update(['pic_id' => null]);
            notyf()->position('x', 'right')->position('y', 'top')->success('Data PIC berhasil dihapus.');
            return redirect()->route('proses-lain-pic.index');
        }

        $data->delete();
        notyf()->position('x', 'right')->position('y', 'top')->success('Data transaksi berhasil dihapus.');
        return redirect()->route('proses-lain-transaksi.index');
    }

    public function indexPic(Request $request)
    {
        $query = ProsesLain::with('picDocument.pic')->where('notaris_id', auth()->user()->notaris_id);

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%'.$request->search.'%')
                    ->orWhere('client_code', 'like', '%'.$request->search.'%')
                    ->orWhereHas('picDocument.pic', function ($pic) use ($request) {
                        $pic->where('full_name', 'like', '%'.$request->search.'%');
                    });
            });
        }

        $prosesLain = $query->whereNotNull('pic_id')->latest()->paginate(10);
        return view('pages.ProsesLain.PIC.index', compact('prosesLain'));
    }

    public function createPic()
    {
        $clients = Client::where('notaris_id', auth()->user()->notaris_id)->where('deleted_at', null)->get();
        $picDocuments = PicDocuments::where('notaris_id', auth()->user()->notaris_id)->where('deleted_at', null)->get();

        return view('pages.ProsesLain.PIC.form', compact('clients', 'picDocuments'));
    }

    public function storePic(Request $request)
    {
        $request->validate([
            'client_code' => 'required',
            'pic_id' => 'required',
        ], [
            'client_code.required' => 'Klien wajib dipilih.',
            'pic_id.required' => 'PIC wajib dipilih.',
        ]);

        $prosesLain = ProsesLain::where('client_code', $request->client_code)
            ->where('notaris_id', auth()->user()->notaris_id)
            ->first();

        if (! $prosesLain) {
            return back()->with('error', 'Data Proses Lain tidak ditemukan.');
        }
    }
    return response()->json($output);
}

public function storePic(Request $request)
{
    $request->validate([
        'client_code'    => 'required',
        'proses_lain_id' => 'required',
    ]);

    $prosesLain = ProsesLain::where('id', $request->proses_lain_id)
        ->where('notaris_id', auth()->user()->notaris_id)
        ->first();

    if (!$prosesLain) {
        return back()->with('error', 'Data Transaksi tidak ditemukan.');
    }

    public function indexProgress(Request $request)
    {
        $prosesLain = collect();

    if ($doc) {
        $prosesLain->update(['pic_id' => $doc->id]);
        notyf()->position('x', 'right')->position('y', 'top')->success('PIC berhasil disimpan.');
    } else {
        notyf()->position('x', 'right')->position('y', 'top')->error('Data PIC tidak ditemukan untuk transaksi ini.');
    }

            $prosesLain = ProsesLain::with('picDocument.pic')
                ->where('transaction_code', 'like', '%'.$request->search.'%')
                ->whereNotNull('pic_id')
                ->latest()
                ->paginate(10);
        }

public function indexProgress(Request $request)
{
    $prosesLain = collect(); 

    if ($request->filled('search')) {
        $prosesLain = ProsesLain::with('picDocument.pic')
            ->where('notaris_id', auth()->user()->notaris_id)
            ->where('transaction_code', 'like', '%' . $request->search . '%')
            ->whereNotNull('pic_id')
            ->latest()
            ->paginate(10);
    }

    return view('pages.ProsesLain.Progress.index', compact('prosesLain'));
}
}