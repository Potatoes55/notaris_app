<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\NotaryAktaTransaction;
use App\Models\NotaryCost;
use App\Models\NotaryRelaasAkta;
use App\Models\PicDocuments;
use App\Services\NotaryCostService;
use Illuminate\Http\Request;

class NotaryCostController extends Controller
{
    protected $service;

    public function __construct(NotaryCostService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $search = $request->get('search');
        $costs = $this->service->list(['search' => $search]);

        return view('pages.Biaya.TotalBiaya.index', compact('costs', 'search'));
    }

    public function create()
    {
        $clients = Client::where('deleted_at', null)->where('notaris_id', auth()->user()->notaris_id)->get();
        $picDocuments = PicDocuments::where('deleted_at', null)->where('notaris_id', auth()->user()->notaris_id)->get();
        $cost = null;

        return view('pages.Biaya.TotalBiaya.form', compact('clients', 'picDocuments', 'cost'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate(
            [
                'client_code' => 'required',
                'pic_document_id' => 'required',
                'product_cost' => 'required',
                'admin_cost' => 'nullable',
                'other_cost' => 'nullable',
                'amount_paid' => 'nullable',
                'payment_status' => 'required|string',
                'paid_date' => 'nullable|date',
                'due_date' => 'nullable|date',
                'note' => 'nullable',
            ],
            [
                'client_code.required' => 'Klien harus diisi.',
                'pic_document_id.required' => 'Dokumen harus diisi.',
                'payment_status.required' => 'Status Pembayaran harus diisi.',
                'product_cost.required' => 'Biaya Produk harus diisi.',
                'product_cost.numeric' => 'Biaya Produk harus berupa angka.',
                'product_cost.min' => 'Biaya Produk harus lebih dari 0.',
            ]
        );
        $productCost = (int) str_replace('.', '', $request->product_cost);
        $adminCost = (int) str_replace('.', '', $request->admin_cost ?? 0);
        $otherCost = (int) str_replace('.', '', $request->other_cost ?? 0);
        $amountPaid = (int) str_replace('.', '', $request->amount_paid ?? 0);
        $totalCost = $productCost + $adminCost + $otherCost;
        if ($amountPaid > $totalCost) {
            notyf()
                ->position('x', 'right')
                ->position('y', 'top')
                ->error('Jumlah Pembayaran melebihi dari total biaya.');

            return back()->withInput();
        }

        // Ambil tanggal hari ini (format: 20251112)
        $today = now()->format('Ymd');

        // Hitung berapa banyak kode di tanggal ini
        $countToday = NotaryCost::whereDate('created_at', now())->count() + 1;

        // Generate kode dengan padding 3 digit
        $paymentCode = 'N-'.$today.'-'.str_pad($countToday, 3, '0', STR_PAD_LEFT);

        $validated['payment_code'] = $paymentCode;
        $validated['notaris_id'] = auth()->user()->notaris_id;

        $this->service->create($validated);

        notyf()->position('x', 'right')->position('y', 'top')->success('Biaya berhasil ditambahkan.');

        return redirect()->route('notary_costs.index');
    }

    public function edit($id)
    {
        $cost = $this->service->detail($id);
        $clients = Client::where('deleted_at', null)->get();
        $picDocuments = PicDocuments::where('deleted_at', null)->get();
        $aktaTransaction = NotaryAktaTransaction::where('deleted_at', null)->where('notaris_id', auth()->user()->notaris_id)->where('status', 'draft')->get();
        $relaasTransaction = NotaryRelaasAkta::where('deleted_at', null)->where('notaris_id', auth()->user()->notaris_id)->where('status', 'draft')->get();

        return view('pages.Biaya.TotalBiaya.form', compact('cost', 'clients', 'picDocuments', 'aktaTransaction', 'relaasTransaction'));
    }

    // public function show($id)
    // {
    //     $cost = $this->service->detail($id);
    //     $clients = Client::where('deleted_at', null)->get();
    //     return view('pages.Biaya.TotalBiaya.show', compact('cost', 'clients'));
    // }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'client_code' => 'required',
            'pic_document_id' => 'required',
            'payment_code' => 'nullable',
            'product_cost' => 'required',
            'admin_cost' => 'nullable',
            'other_cost' => 'nullable',
            'amount_paid' => 'nullable',
            'payment_status' => 'required',
            'paid_date' => 'required|date',
            'due_date' => 'required|date',
            'note' => 'nullable',
        ], [
            'client_code.required' => 'Kode Klien harus diisi.',
            'pic_document_id.required' => 'PIC Dokumen harus diisi.',
            'product_cost.required' => 'Biaya Produk harus diisi.',
            'payment_status.required' => 'Status Pembayaran harus diisi.',
            'paid_date.required' => 'Tanggal Bayar harus diisi.',
            'paid_date.date' => 'Format Tanggal Bayar tidak valid.',
            'due_date.required' => 'Tanggal Jatuh Tempo harus diisi.',
            'due_date.date' => 'Format Tanggal Jatuh Tempo tidak valid.',
        ]);

        $validated['notaris_id'] = auth()->user()->notaris_id;

        $this->service->update($id, $validated);
        notyf()->position('x', 'right')->position('y', 'top')->success('Biaya berhasil diubah.');

        return redirect()->route('notary_costs.index');
    }

    public function destroy($id)
    {
        $this->service->delete($id);
        notyf()->position('x', 'right')->position('y', 'top')->success('Biaya berhasil dihapus.');

        return back();
    }

    public function print($id)
    {
        $costs = $this->service->detail($id);

        // ✅ Gunakan format A4 (bisa portrait atau landscape)
        $mpdf = new \Mpdf\Mpdf([
            'format' => 'A4', // A4 portrait
            // 'format' => 'A4-L', // jika ingin landscape
            'margin_left' => 15,
            'margin_right' => 15,
            'margin_top' => 10,
            'margin_bottom' => 0,
            'tempDir' => storage_path('app/mpdf-temp'),
        ]);

        $html = view('pages.Biaya.TotalBiaya.print', compact('costs'))->render();
        $mpdf->WriteHTML($html);
        $mpdf->Output("notary_cost_$id.pdf", 'I');
    }
}
