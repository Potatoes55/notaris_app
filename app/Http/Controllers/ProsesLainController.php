<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\PicDocuments;
use App\Models\ProsesLain;
use Illuminate\Http\Request;

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
    }

    public function create()
    {
        // FIX: Hapus ->where('deleted_at', null)
        $clients = Client::where('notaris_id', auth()->user()->notaris_id)->get();
        return view('pages.ProsesLain.Transaksi.form', compact('clients'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'client_code'     => 'required',
            'name'            => 'required',
            'time_estimation' => 'required|integer',
        ]);

        $urutan = ['Baru', 'Proses', 'Selesai'];

        $last = ProsesLain::where('client_code', $request->client_code)
            ->where('notaris_id', auth()->user()->notaris_id)
            ->latest('id')
            ->first();

        $statusBaru = !$last ? 'Baru' : (
            (array_search($last->status, $urutan) === false || array_search($last->status, $urutan) == count($urutan) - 1) 
            ? 'Selesai' : $urutan[array_search($last->status, $urutan) + 1]
        );
        
        $prefix = 'T-' . strtoupper(substr($request->client_code, 0, 3)) . '-';

        $lastCode = ProsesLain::where('client_code', $request->client_code)
            ->where('transaction_code', 'like', $prefix . '%')
            ->orderByDesc('id')
            ->value('transaction_code');

        $nextNumber = $lastCode ? (int) substr($lastCode, -4) + 1 : 1;
        $paymentCode = $prefix . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        ProsesLain::create([
            'client_code'      => $request->client_code,
            'notaris_id'       => auth()->user()->notaris_id,
            'name'             => $request->name,
            'time_estimation'  => $request->time_estimation,
            'status'           => $statusBaru,
            'transaction_code' => $paymentCode,
        ]);

        notyf()->position('x', 'right')->position('y', 'top')->success('Data berhasil disimpan.');
        return redirect()->route('proses-lain-transaksi.index');
    }

    public function edit($id)
    {
        $data = ProsesLain::findOrFail($id);
        // FIX: Hapus ->where('deleted_at', null)
        $clients = Client::where('notaris_id', auth()->user()->notaris_id)->get();
        return view('pages.ProsesLain.Transaksi.form', compact('data', 'clients'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'client_code'     => 'required',
            'name'            => 'required',
            'time_estimation' => 'required|integer',
        ]);

        $data = ProsesLain::findOrFail($id);
        $data->update([
            'client_code'     => $request->client_code,
            'name'            => $request->name,
            'time_estimation' => $request->time_estimation,
        ]);

        notyf()->position('x', 'right')->position('y', 'top')->success('Data berhasil diubah.');
        return redirect()->route('proses-lain-transaksi.index');
    }

    public function destroy(Request $request, $id)
    {
        $data = ProsesLain::findOrFail($id);

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
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('client_code', 'like', '%' . $request->search . '%')
                    ->orWhereHas('picDocument.pic', function ($pic) use ($request) {
                        $pic->where('full_name', 'like', '%' . $request->search . '%');
                    });
            });
        }

        $prosesLain = $query->whereNotNull('pic_id')->latest()->paginate(10);
        return view('pages.ProsesLain.PIC.index', compact('prosesLain'));
    }

    public function createPic()
    {
        $clients = Client::where('notaris_id', auth()->user()->notaris_id)->get();
        $picDocuments = PicDocuments::where('notaris_id', auth()->user()->notaris_id)->get();
        return view('pages.ProsesLain.PIC.form', compact('clients', 'picDocuments'));
    }

    public function getPicByClient($client_code)
    {
        $transaksi = ProsesLain::where('client_code', $client_code)
            ->where('notaris_id', auth()->user()->notaris_id)
            ->get();

        $documents = PicDocuments::where('client_code', $client_code)
            ->where('notaris_id', auth()->user()->notaris_id)
            ->with('pic')
            ->get();

        $output = [];
        foreach ($transaksi as $index => $t) {
            $doc = $documents->get($index) ?? $documents->first();
            if ($doc && $doc->pic) {
                $output[] = [
                    'proses_lain_id'   => $t->id, 
                    'pic_id'           => $doc->pic_id,
                    'full_name'        => $doc->pic->full_name,
                    'transaction_type' => ucwords(str_replace('_', ' ', $doc->transaction_type)),
                    'transaction_name' => $t->name 
                ];
            }
        }
        return response()->json($output);
    }

    public function storePic(Request $request)
    {
        $request->validate([
            'client_code'     => 'required',
            'proses_lain_id' => 'required',
        ]);

        $prosesLain = ProsesLain::where('id', $request->proses_lain_id)
            ->where('notaris_id', auth()->user()->notaris_id)
            ->first();

        if (!$prosesLain) {
            return back()->with('error', 'Data Transaksi tidak ditemukan.');
        }

        $allTransactions = ProsesLain::where('client_code', $request->client_code)
            ->where('notaris_id', auth()->user()->notaris_id)
            ->get();

        $indexDropdown = $allTransactions->pluck('id')->search($prosesLain->id);
        $documents = PicDocuments::where('client_code', $request->client_code)
            ->where('notaris_id', auth()->user()->notaris_id)
            ->get();

        $doc = $documents->get($indexDropdown) ?? $documents->first();
        if ($doc && \App\Models\PicDocuments::where('id', $doc->id)->exists()) {
            $prosesLain->update(['pic_id' => $doc->id]);
            notyf()->position('x', 'right')->position('y', 'top')->success('PIC berhasil disimpan.');
        } else {
            notyf()->position('x', 'right')->position('y', 'top')->error('Data PIC tidak ditemukan atau tidak valid.');
        }

        return redirect()->route('proses-lain-pic.index');
    }

    public function indexProgress(Request $request)
    {
        $query = ProsesLain::with('picDocument.pic')
            ->where('notaris_id', auth()->user()->notaris_id)
            ->whereNotNull('pic_id');

        if ($request->filled('search')) {
            $query->where('transaction_code', 'like', '%' . $request->search . '%');
        }

        $prosesLain = $query->latest()->paginate(10);
        return view('pages.ProsesLain.Progress.index', compact('prosesLain'));
    }
}