<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\NotaryAktaTransaction;
use App\Models\NotaryCost;
use App\Models\NotaryRelaasAkta;
use App\Models\PicDocuments;
use App\Services\NotaryCostService;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class NotaryCostController extends Controller
{
    protected $service;

    public function __construct(NotaryCostService $service)
    {
        $this->service = $service;
    }

    private function getNotarisId()
    {
        return auth()->user()->notaris_id;
    }

    public function index(Request $request)
    {
        $search = $request->get('search');
        $costs = $this->service->list(['search' => $search]);

        return view('pages.Biaya.TotalBiaya.index', compact('costs', 'search'));
    }

    public function create()
    {
        $clients = Client::where('notaris_id', $this->getNotarisId())
            ->whereNull('deleted_at')
            ->get();

        $picDocuments = PicDocuments::where('notaris_id', $this->getNotarisId())
            ->whereHas('client', function ($q) {
                $q->whereNull('deleted_at');
            })
            ->get();
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
            ]
        );

        $productCost = (int) str_replace('.', '', $request->product_cost);
        $adminCost = (int) str_replace('.', '', $request->admin_cost ?? 0);
        $otherCost = (int) str_replace('.', '', $request->other_cost ?? 0);
        $amountPaid = (int) str_replace('.', '', $request->amount_paid ?? 0);
        $totalCost = $productCost + $adminCost + $otherCost;

        if ($amountPaid > $totalCost) {
            notyf()->position('x', 'right')->position('y', 'top')->error('Jumlah Pembayaran melebihi dari total biaya.');
            return back()->withInput();
        }

        $today = now()->format('Ymd');
        $countToday = NotaryCost::whereDate('created_at', now())->count() + 1;
        $paymentCode = 'N-'.$today.'-'.str_pad($countToday, 3, '0', STR_PAD_LEFT);

        $validated['payment_code'] = $paymentCode;
        $validated['notaris_id'] = $this->getNotarisId();

        $this->service->create($validated);
        notyf()->position('x', 'right')->position('y', 'top')->success('Biaya berhasil ditambahkan.');

        return redirect()->route('notary_costs.index');
    }

    public function edit($id)
    {
        $cost = $this->service->detail($id);
        $clients = Client::where('notaris_id', $this->getNotarisId())
            ->whereNull('deleted_at')
            ->get();

        $picDocuments = PicDocuments::where('notaris_id', $this->getNotarisId())
            ->whereHas('client', function ($q) {
                $q->whereNull('deleted_at');
            })
            ->get();

        $aktaTransaction = NotaryAktaTransaction::where('notaris_id', $this->getNotarisId())
            ->where('status', 'draft')
            ->whereHas('client', function ($q) {
                $q->whereNull('deleted_at');
            })
            ->with('client')
            ->get();

        $relaasTransaction = NotaryRelaasAkta::where('notaris_id', $this->getNotarisId())
            ->where('status', 'draft')
            ->whereHas('client', function ($q) {
                $q->whereNull('deleted_at');
            })
            ->with('client')
            ->get();

        return view('pages.Biaya.TotalBiaya.form', compact('cost', 'clients', 'picDocuments', 'aktaTransaction', 'relaasTransaction'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'client_code' => 'required',
            'pic_document_id' => 'required',
            'product_cost' => 'required',
            'payment_status' => 'required',
            'paid_date' => 'required|date',
            'due_date' => 'required|date',
        ]);

        $validated['notaris_id'] = $this->getNotarisId();
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

        $notarisData = $costs->notaris->notaris_code ?? 'DATA-TIDAK-DITEMUKAN';
        $qrCode = base64_encode(QrCode::format('svg')->size(130)->margin(1)->generate($notarisData));

        $mpdf = new \Mpdf\Mpdf([
            'format' => 'A4',
            'margin_left' => 15,
            'margin_right' => 15,
            'margin_top' => 10,
            'margin_bottom' => 0,
            'tempDir' => storage_path('app/mpdf-temp'),
        ]);

        $html = view('pages.Biaya.TotalBiaya.print', compact('costs', 'qrCode'))->render();
        
        $mpdf->WriteHTML($html);
        $mpdf->Output("notary_cost_$id.pdf", 'I');
    }
}