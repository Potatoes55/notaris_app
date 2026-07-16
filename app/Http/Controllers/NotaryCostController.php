<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\NotaryAktaTransaction;
use App\Models\NotaryCost;
use App\Models\NotaryRelaasAkta;
use App\Models\PicDocuments;
use App\Services\NotaryCostService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
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

    private function routeName()
    {
        return match (request()->segment(1)) {
            'notaris' => 'notaris.costs',
            'ppat' => 'ppat.costs',
            'proses-lain' => 'proses-lain.biaya.total',
            default => 'notaris.costs',
        };
    }

    public function index(Request $request)
    {
        $search = $request->get('search');
        $costs = $this->service->list(['search' => $search]);

        $module = request()->segment(1) === 'ppat' ? 'PPAT' : 'Notaris';

        return view('pages.Biaya.TotalBiaya.index', compact('costs', 'search', 'module'));
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
                'pph' => 'nullable',
                'bphtb' => 'nullable',
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
        $pph = (int) str_replace('.', '', $request->pph ?? 0);
        $bphtb = (int) str_replace('.', '', $request->bphtb ?? 0);
        $totalCost = $productCost + $adminCost + $otherCost + $pph + $bphtb;

        if ($amountPaid > $totalCost) {
            notyf()->position('x', 'right')->position('y', 'top')->error('Jumlah Pembayaran melebihi dari total biaya.');

            return back()->withInput();
        }

        $today = now()->format('Ymd');
        $countToday = NotaryCost::whereDate('created_at', now())->count() + 1;
        $paymentCode = 'N-' . $today . '-' . str_pad($countToday, 3, '0', STR_PAD_LEFT);

        $validated['payment_code'] = $paymentCode;
        $validated['notaris_id'] = $this->getNotarisId();

        $validated['product_cost'] = $productCost;
        $validated['admin_cost'] = $adminCost;
        $validated['other_cost'] = $otherCost;
        $validated['amount_paid'] = $amountPaid;
        $validated['pph'] = $pph;
        $validated['bphtb'] = $bphtb;
        $validated['total_cost'] = $totalCost;

        $this->service->create($validated);

        notyf()->position('x', 'right')->position('y', 'top')->success('Biaya berhasil ditambahkan.');

        return redirect()->route($this->routeName());
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
            'admin_cost' => 'nullable',
            'other_cost' => 'nullable',
            'amount_paid' => 'nullable',
            'pph' => 'nullable',
            'bphtb' => 'nullable',
            'payment_status' => 'required',
            'paid_date' => 'required|date',
            'due_date' => 'required|date',
            'note' => 'nullable',
        ]);

        $productCost = (int) str_replace('.', '', $request->product_cost);
        $adminCost = (int) str_replace('.', '', $request->admin_cost ?? 0);
        $otherCost = (int) str_replace('.', '', $request->other_cost ?? 0);
        $amountPaid = (int) str_replace('.', '', $request->amount_paid ?? 0);
        $pph = (int) str_replace('.', '', $request->pph ?? 0);
        $bphtb = (int) str_replace('.', '', $request->bphtb ?? 0);

        $validated['notaris_id'] = $this->getNotarisId();
        $validated['product_cost'] = $productCost;
        $validated['admin_cost'] = $adminCost;
        $validated['other_cost'] = $otherCost;
        $validated['amount_paid'] = $amountPaid;
        $validated['pph'] = $pph;
        $validated['bphtb'] = $bphtb;
        $validated['total_cost'] = $productCost + $adminCost + $otherCost + $pph + $bphtb;

        $this->service->update($id, $validated);

        notyf()->position('x', 'right')->position('y', 'top')->success('Biaya berhasil diubah.');

        return redirect()->route($this->routeName());
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

        $hash = Crypt::encryptString($costs->notaris->id);

        $notarisData = route('profileNotaris', ['hash' => $hash]);
        $qrCode = base64_encode(QrCode::format('svg')->size(175)->margin(1)->generate($notarisData));

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

    public function payments(Request $request)
    {
        $search = $request->get('search');
        $costs = $this->service->list(['search' => $search]);

        $module = request()->segment(1) === 'ppat' ? 'PPAT' : 'Notaris';

        return view('pages.Biaya.Pembayaran.index', compact('costs', 'search', 'module'));
    }
}
