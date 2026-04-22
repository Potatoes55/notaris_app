<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\NotaryAktaTransaction;
use App\Models\NotaryRelaasAkta;
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

    public function index(Request $request)
    {
        $filters = [
            'search' => $request->search,
            'status' => $request->status,
        ];

        $picDocuments = $this->service->getAllDocuments($filters);

        return view('pages.PIC.PicDocuments.index', compact('picDocuments'));
    }

    public function create()
    {
        $clients = Client::where('deleted_at', null)->where('notaris_id', auth()->user()->notaris_id)->get();
        $picStaffList = PicStaff::where('deleted_at', null)->where('notaris_id', auth()->user()->notaris_id)->get();
        $aktaTransaction = NotaryAktaTransaction::where('deleted_at', null)->where('notaris_id', auth()->user()->notaris_id)->where('status', 'draft')->get();
        $relaasTransaction = NotaryRelaasAkta::where('deleted_at', null)->where('notaris_id', auth()->user()->notaris_id)->where('status', 'draft')->get();

        return view('pages.PIC.PicDocuments.form', compact('clients', 'picStaffList', 'aktaTransaction', 'relaasTransaction'));
    }

    public function store(Request $request)
    {
        // dd($request->input('pic_id'));

        // dd($request->all());
        $validated = $request->validate([
            'pic_id' => 'required',
            'transaction_id' => 'required',
            'transaction_type' => 'required',
            'received_date' => 'required|date',
            'status' => 'required',
            'note' => 'nullable',
        ], [
            'pic_id.required' => 'PIC harus dipilih.',
            'transaction_id.required' => 'Transaksi harus dipilih.',
            'transaction_type.required' => 'Tipe transaksi harus dipilih.',
            'received_date.required' => 'Tanggal diterima harus diisi.',
        ]);

        $validated['notaris_id'] = auth()->user()->notaris_id;

        // Ambil client_code berdasarkan tipe transaksi
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
        }

        if (! isset($validated['client_code'])) {
            return back()->withErrors(['transaction_id' => 'Client untuk transaksi ini tidak ditemukan.']);
        }

        $this->service->createDocument($validated);

        notyf()->position('x', 'right')->position('y', 'top')->success('PIC Dokumen berhasil ditambahkan.');

        return redirect()->route('pic_documents.index');
    }

    public function edit($id)
    {
        $clients = Client::where('deleted_at', null)->where('notaris_id', auth()->user()->notaris_id)->get();
        $picStaffList = PicStaff::where('deleted_at', null)->where('notaris_id', auth()->user()->notaris_id)->get();
        $picDocument = $this->service->getDocumentById($id);
        $aktaTransaction = NotaryAktaTransaction::where('deleted_at', null)->where('notaris_id', auth()->user()->notaris_id)->get();
        $relaasTransaction = NotaryRelaasAkta::where('deleted_at', null)->where('notaris_id', auth()->user()->notaris_id)->get();

        return view('pages.PIC.PicDocuments.form', compact('picDocument', 'clients', 'picStaffList', 'aktaTransaction', 'relaasTransaction'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'pic_id' => 'required',
            // 'client_code' => 'required',
            'received_date' => 'required',
            'transaction_type' => 'required',
            'transaction_id' => 'nullable',
            'status' => 'required',
            'note' => 'nullable',
        ]);

        $validated['notaris_id'] = auth()->user()->notaris_id;

        $this->service->updateDocument($id, $validated);

        notyf()->position('x', 'right')->position('y', 'top')->success('PIC Dokumen berhasil diperbarui.');

        return redirect()->route('pic_documents.index');
    }

    public function destroy($id)
    {
        $this->service->deleteDocument($id);

        notyf()->position('x', 'right')->position('y', 'top')->success('PIC Dokumen berhasil dihapus.');

        return redirect()->route('pic_documents.index');
    }

    public function print($id)
    {
        $picDocuments = PicDocuments::findOrFail($id);

        $html = view('pages.PIC.PicDocuments.print', compact('picDocuments'))->render();

        // Inisialisasi mPDF
        $mpdf = new Mpdf([
            'default_font' => 'dejavusans',
            'format' => 'A4',
            'margin_top' => 10,
            'margin_bottom' => 0,
            'margin_left' => 15,
            'margin_right' => 15,
            'tempDir' => storage_path('app/mpdf-temp'),
        ]);

        // Tulis HTML ke PDF
        $mpdf->WriteHTML($html);

        // Output langsung ke browser (inline)
        return response($mpdf->Output('Pic Dokumen.pdf', 'I'))
            ->header('Content-Type', 'application/pdf');
    }
}
