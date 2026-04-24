<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\NotaryAktaTransaction;
use App\Models\NotaryRelaasAkta;
use App\Models\PicDocuments;
use App\Models\PicStaff;
use App\Services\PicHandoverService;
use Illuminate\Http\Request;

// gunakan dompdf / barryvdh

class PicHandoverController extends Controller
{
    protected $service;

    public function __construct(PicHandoverService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $handovers = $this->service->listHandovers([
            'search' => $request->get('search'),
        ]);

        return view('pages.PIC.PicHandovers.index', compact('handovers'));
    }

    public function create()
    {
        $picDocuments = PicDocuments::where('deleted_at', null)->latest()->get();
        $clients = Client::where('deleted_at', null)->where('notaris_id', auth()->user()->notaris_id)->get();
        $picStaffList = PicStaff::where('deleted_at', null)->where('notaris_id', auth()->user()->notaris_id)->get();
        $aktaTransaction = NotaryAktaTransaction::where('deleted_at', null)->where('notaris_id', auth()->user()->notaris_id)->where('status', 'draft')->get();
        $relaasTransaction = NotaryRelaasAkta::where('deleted_at', null)->where('notaris_id', auth()->user()->notaris_id)->where('status', 'draft')->get();

        return view('pages.PIC.PicHandovers.form', compact('picDocuments', 'clients', 'picStaffList', 'aktaTransaction', 'relaasTransaction'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'pic_document_id' => 'required',
            'handover_date' => 'required|date',
            'recipient_name' => 'required|string',
            'recipient_contact' => 'required|string',
            'note' => 'nullable|string',
            'file_path' => 'nullable|file|mimes:jpg,png,png,pdf|max:10240',
        ], [
            'pic_document_id.required' => 'Dokumen PIC harus dipilih.',
            'handover_date.required' => 'Tanggal serah terima harus diisi.',
            'recipient_name.required' => 'Nama penerima harus diisi.',
            'recipient_contact.required' => 'Kontak penerima harus diisi.',
            'file_path.max' => 'Ukuran file maksimal 10 MB.',
            'file_path.mimes' => 'Format file harus PDF, JPG, JPEG, atau PNG.',
        ]);

        $validated['notaris_id'] = auth()->user()->notaris_id;

        if ($request->hasFile('file_path')) {
            $validated['file_path'] = $request->file('file_path')->storeAs('documents', $request->file('file_path')->getClientOriginalName());
        }

        $this->service->createHandover($validated);

        notyf()->position('x', 'right')->position('y', 'top')->success('Serah terima berhasil ditambahkan.');

        return redirect()->route('pic_handovers.index');
    }

    public function destroy($id)
    {
        $this->service->deleteHandover($id);
        notyf()->success('Data serah terima dihapus.');

        return back();
    }

    public function print($id)
    {
        $handover = $this->service->listHandovers([])->firstWhere('id', $id);

        $notaris = auth()->user()->notaris;

        if (! $handover) {
            abort(404, 'Data serah terima tidak ditemukan');
        }

        $html = view('pages.PIC.PicHandovers.print', compact('handover', 'notaris'))->render();

        $mpdf = new \Mpdf\Mpdf([
            'tempDir' => storage_path('app/mpdf'),
            'format' => 'A4',
            'mode' => 'utf-8',
        ]);

        $mpdf->WriteHTML($html);

        return response(
            $mpdf->Output("handover-{$handover->id}.pdf", \Mpdf\Output\Destination::STRING_RETURN)
        )
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="handover-'.$handover->id.'.pdf"');
    }
}
