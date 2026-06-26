<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\NotaryAktaTransaction;
use App\Models\NotaryRelaasAkta;
use App\Models\ProsesLain;
use App\Models\PicDocuments;
use App\Models\PicStaff;
use App\Services\PicDocumentsService;
use Illuminate\Http\Request;
use Mpdf\Mpdf;

class PicDocumentsController extends Controller
{
    protected $service;

    public function __construct(PicDocumentsService $service)
    {
        $this->service = $service;
    }

    private function getNotarisId()
    {
        return auth()->user()->notaris_id;
    }

    private function getModule()
    {
        return request()->routeIs('ppat.*') ? 'PPAT' : 'Notaris';
    }

    private function getIndexRoute()
    {
        return request()->routeIs('ppat.*')
            ? 'ppat.pic.documents'
            : 'notaris.pic.documents';
    }

    public function index(Request $request)
    {
        $filters = [
            'search' => $request->search,
            'status' => $request->status,
        ];

        $module = $this->getModule();

        $picDocuments = $this->service->getAllDocuments($filters);

        return view(
            'pages.PIC.PicDocuments.index',
            compact('picDocuments', 'module')
        );
    }

    public function create()
    {
        $module = $this->getModule();
        $notarisId = $this->getNotarisId();

        $clients = Client::where('notaris_id', $notarisId)
            ->whereNull('deleted_at')
            ->get();

        $picStaffList = PicStaff::where('notaris_id', $notarisId)
            ->get();

        $aktaTransaction = NotaryAktaTransaction::where('notaris_id', $notarisId)
            ->whereHas('client', function ($q) {
                $q->whereNull('deleted_at');
            })
            ->with('client')
            ->get();

        $relaasTransaction = NotaryRelaasAkta::where('notaris_id', $notarisId)
            ->whereHas('client', function ($q) {
                $q->whereNull('deleted_at');
            })
            ->with('client')
            ->get();

        $prosesLainTransaction = ProsesLain::where('notaris_id', $notarisId)
            ->whereHas('client', function ($q) {
                $q->whereNull('deleted_at');
            })
            ->with('client')
            ->get();

        return view(
            'pages.PIC.PicDocuments.form',
            compact(
                'module',
                'clients',
                'picStaffList',
                'aktaTransaction',
                'relaasTransaction',
                'prosesLainTransaction'
            )
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'pic_id' => 'required',
            'transaction_id' => 'required',
            'transaction_type' => 'required',
            'received_date' => 'required|date',
            'status' => 'required',
            'note' => 'nullable',
        ]);

        $validated['notaris_id'] = $this->getNotarisId();

        if ($validated['transaction_type'] === 'akta') {
            $transaction = NotaryAktaTransaction::find($validated['transaction_id']);

            if ($transaction) {
                $validated['client_code'] = $transaction->client_code;
            }
        } elseif ($validated['transaction_type'] === 'ppat') {
            $transaction = NotaryRelaasAkta::find($validated['transaction_id']);

            if ($transaction) {
                $validated['client_code'] = $transaction->client_code;
            }
        } elseif ($validated['transaction_type'] === 'proses_lain') {
            $transaction = ProsesLain::find($validated['transaction_id']);

            if ($transaction) {
                $validated['client_code'] = $transaction->client_code;
            }
        }

        if (!isset($validated['client_code'])) {
            return back()->withErrors([
                'transaction_id' => 'Client untuk transaksi ini tidak ditemukan.'
            ]);
        }

        $this->service->createDocument($validated);

        notyf()
            ->position('x', 'right')
            ->position('y', 'top')
            ->success('PIC Dokumen berhasil ditambahkan.');

        return redirect()->route($this->getIndexRoute());
    }

    public function edit($id)
    {
        $module = $this->getModule();

        $clients = Client::where(
            'notaris_id',
            $this->getNotarisId()
        )->get();

        $picStaffList = PicStaff::where(
            'notaris_id',
            $this->getNotarisId()
        )->get();

        $picDocument = $this->service->getDocumentById($id);

        $aktaTransaction = NotaryAktaTransaction::where(
            'notaris_id',
            $this->getNotarisId()
        )
            ->whereHas('client', function ($q) {
                $q->whereNull('deleted_at');
            })
            ->with('client')
            ->get();

        $relaasTransaction = NotaryRelaasAkta::where(
            'notaris_id',
            $this->getNotarisId()
        )
            ->whereHas('client', function ($q) {
                $q->whereNull('deleted_at');
            })
            ->with('client')
            ->get();

        $prosesLainTransaction = ProsesLain::where(
            'notaris_id',
            $this->getNotarisId()
        )
            ->whereHas('client', function ($q) {
                $q->whereNull('deleted_at');
            })
            ->with('client')
            ->get();

        return view(
            'pages.PIC.PicDocuments.form',
            compact(
                'module',
                'picDocument',
                'clients',
                'picStaffList',
                'aktaTransaction',
                'relaasTransaction',
                'prosesLainTransaction'
            )
        );
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'pic_id' => 'required',
            'received_date' => 'required',
            'transaction_type' => 'required',
            'transaction_id' => 'nullable',
            'status' => 'required',
            'note' => 'nullable',
        ]);

        $validated['notaris_id'] = $this->getNotarisId();

        $this->service->updateDocument($id, $validated);

        notyf()
            ->position('x', 'right')
            ->position('y', 'top')
            ->success('PIC Dokumen berhasil diperbarui.');

        return redirect()->route($this->getIndexRoute());
    }

    public function destroy($id)
    {
        $this->service->deleteDocument($id);

        notyf()
            ->position('x', 'right')
            ->position('y', 'top')
            ->success('PIC Dokumen berhasil dihapus.');

        return redirect()->route($this->getIndexRoute());
    }

    public function print($id)
    {
        $picDocuments = PicDocuments::findOrFail($id);

        $html = view(
            'pages.PIC.PicDocuments.print',
            compact('picDocuments')
        )->render();

        $mpdf = new Mpdf([
            'default_font' => 'dejavusans',
            'format' => 'A4',
            'margin_top' => 10,
            'margin_bottom' => 0,
            'margin_left' => 15,
            'margin_right' => 15,
            'tempDir' => storage_path('app/mpdf-temp'),
        ]);

        $mpdf->WriteHTML($html);

        return response(
            $mpdf->Output('Pic Dokumen.pdf', 'I')
        )->header('Content-Type', 'application/pdf');
    }
}