<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\PicDocuments;
use App\Models\ProsesLain;
use Illuminate\Http\Request;

class ProsesLainController extends Controller
{
    public function index(Request $request)
    {
        $notarisId = auth()->user()->notaris_id;
        $query = ProsesLain::query()->where('notaris_id', $notarisId);

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%'.$request->search.'%')
                    ->orWhere('client_code', 'like', '%'.$request->search.'%');
            });
        }

        $prosesLain = $query->latest()->paginate(10);

        return view('pages.ProsesLain.Transaksi.index', compact('prosesLain'));
    }

    public function create()
    {
        $clients = Client::where('notaris_id', auth()->user()->notaris_id)->where('deleted_at', null)->get();

        return view('pages.ProsesLain.Transaksi.form', compact('clients'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'client_code' => 'required',
            'name' => 'required',
            'time_estimation' => 'required|integer',
        ]);

        $statusBaru = 'Baru';

        $prefix = 'T-'.strtoupper(substr($request->client_code, 0, 3)).'-';

        $lastCode = ProsesLain::where('notaris_id', auth()->user()->notaris_id)
            ->where('transaction_code', 'like', $prefix.'%')
            ->orderByDesc('id')
            ->value('transaction_code');

        $nextNumber = $lastCode ? (int) substr($lastCode, -4) + 1 : 1;
        $paymentCode = $prefix.str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        ProsesLain::create([
            'client_code' => $request->client_code,
            'notaris_id' => auth()->user()->notaris_id,
            'name' => $request->name,
            'time_estimation' => $request->time_estimation,
            'status' => $statusBaru,
            'transaction_code' => $paymentCode,
        ]);

        notyf()->position('x', 'right')->position('y', 'top')->success('Data berhasil disimpan.');

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

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Baru,Proses,Selesai',
        ]);

        $data = ProsesLain::where('id', $id)
            ->where('notaris_id', auth()->user()->notaris_id)
            ->firstOrFail();

        $data->update(['status' => $request->status]);

        notyf()->position('x', 'right')->position('y', 'top')->success('Status berhasil diubah.');

        return back();
    }

    public function destroy(Request $request, $id)
    {
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
        $picDocuments = PicDocuments::where('notaris_id', auth()->user()->notaris_id)
            ->whereNull('deleted_at')
            ->where('transaction_type', 'Proses_lain')
            ->get();

        return view('pages.ProsesLain.PIC.form', compact('clients', 'picDocuments'));
    }

    public function storePic(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'client_code' => 'required',
            'proses_lain_id' => 'required',
            'pic_id' => 'required',
        ], [
            'client_code.required' => 'Klien wajib dipilih.',
            'proses_lain_id.required' => 'Transaksi wajib dipilih.',
            'pic_id.required' => 'PIC wajib dipilih.',
        ]);

        $prosesLain = ProsesLain::where('id', $request->proses_lain_id)
            ->where('client_code', $request->client_code)
            ->where('notaris_id', auth()->user()->notaris_id)
            ->first();

        if (! $prosesLain) {
            return back()->with('error', 'Data Transaksi tidak ditemukan.');
        }

        // Menyimpan pic_id ke data ProsesLain terkait
        $prosesLain->update(['pic_id' => $request->pic_id]);

        notyf()->position('x', 'right')->position('y', 'top')->success('PIC berhasil disimpan.');

        return redirect()->route('proses-lain-pic.index');
    }

    public function indexProgress(Request $request)
    {
        $prosesLain = collect();

        if ($request->filled('search')) {
            $prosesLain = ProsesLain::with('picDocument.pic')
                ->where('notaris_id', auth()->user()->notaris_id)
                ->where('transaction_code', 'like', '%'.$request->search.'%')
                ->whereNotNull('pic_id')
                ->latest()
                ->paginate(10);
        }

        return view('pages.ProsesLain.Progress.index', compact('prosesLain'));
    }
}
