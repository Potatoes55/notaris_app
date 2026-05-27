<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Covernote;
use App\Models\Notaris;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
// PENTING: Gunakan namespace mPDF sesuai dengan package project lu
use Mpdf\Mpdf; 

class CovernoteController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $covernotes = Covernote::with('client')
            ->when($search, function ($query, $search) {
                return $query->where('covernote_number', 'like', "%{$search}%")
                            ->orWhere('subject', 'like', "%{$search}%")
                            ->orWhere('recipient', 'like', "%{$search}%")
                            ->orWhereHas('client', function ($q) use ($search) {
                                $q->where('fullname', 'like', "%{$search}%");
                            });
            })
            ->latest()
            ->paginate(10);

        return view('pages.BackOffice.Covernote.index', compact('covernotes'));
    }

    public function print(Request $request)
    {
        $search = $request->input('search');

        // Ambil data covernote sesuai filter search tanpa pagination
        $covernotes = Covernote::with('client')
            ->when($search, function ($query, $search) {
                return $query->where('covernote_number', 'like', "%{$search}%")
                            ->orWhere('subject', 'like', "%{$search}%")
                            ->orWhere('recipient', 'like', "%{$search}%")
                            ->orWhereHas('client', function ($q) use ($search) {
                                $q->where('fullname', 'like', "%{$search}%");
                            });
            })
            ->latest()
            ->get();

        // Ambil profile notaris user login
        $notaris = Notaris::where('id', auth()->user()->notaris_id)->first() ?? Notaris::first();

        // PENTING: Render view blade menjadi format HTML string biasa untuk mPDF
        $html = view('pages.BackOffice.Covernote.print', compact('covernotes', 'notaris'))->render();

        // PENTING: Setup & Inisialisasi mPDF dengan format A4 Landscape
        $mpdf = new Mpdf([
            'default_font'  => 'dejavusans',
            'format'        => 'A4-L', // Akhiran -L artinya Landscape
            'margin_top'    => 10,
            'margin_bottom' => 10,
            'margin_left'   => 15,
            'margin_right'  => 15,
            'tempDir'       => storage_path('app/mpdf-temp'),
        ]);

        // Tulis HTML tadi ke generator mPDF
        $mpdf->WriteHTML($html);

        // Lempar langsung ke browser sebagai PDF Inline stream
        return response($mpdf->Output('Laporan_Covernote_' . now()->format('YmdHis') . '.pdf', 'I'))
            ->header('Content-Type', 'application/pdf');
    }

    public function create()
    {
        $clients = Client::all();
        return view('pages.BackOffice.Covernote.form', compact('clients'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'client_id'        => 'required|exists:clients,id',
            'covernote_number' => 'required|string|unique:covernotes,covernote_number',
            'recipient'        => 'nullable|string',
            'subject'          => 'nullable|string',
            'date'             => 'nullable|date',
            'expiry_date'      => 'nullable|date',
            'attachment'       => 'nullable|string',
            'file'             => 'nullable|file|mimes:pdf,jpg,jpeg,png,svg,webp|max:2048',
        ]);

        $data = $request->all();

        $client = Client::find($request->client_id);
        $data['client_code'] = $client->client_code ?? null;

        if ($request->hasFile('file')) {
            $data['file_path'] = $request->file('file')->store('covernotes', 'public');
        }

        Covernote::create($data);

        return redirect()->route('covernotes.index')->with('success', 'Data Covernote berhasil ditambahkan.');
    }

    public function show(string $id)
    {
        $covernote = Covernote::with('client')->findOrFail($id);
        return view('pages.BackOffice.Covernote.show', compact('covernote'));
    }

    public function edit(string $id)
    {
        $data = Covernote::findOrFail($id);
        $clients = Client::all();
        return view('pages.BackOffice.Covernote.form', compact('data', 'clients'));
    }

    public function update(Request $request, string $id)
    {
        $covernote = Covernote::findOrFail($id);

        $request->validate([
            'client_id'        => 'required|exists:clients,id',
            'covernote_number' => 'required|string|unique:covernotes,covernote_number,' . $id,
            'recipient'        => 'nullable|string',
            'subject'          => 'nullable|string',
            'date'             => 'nullable|date',
            'expiry_date'      => 'nullable|date',
            'attachment'       => 'nullable|string|max:65000',
            'file'             => 'nullable|file|mimes:pdf,jpg,jpeg,png,svg,webp|max:10240',
        ]);

        $data = $request->all();

        $client = Client::find($request->client_id);
        $data['client_code'] = $client->client_code ?? null;

        if ($request->hasFile('file')) {
            if ($covernote->file_path && Storage::disk('public')->exists($covernote->file_path)) {
                Storage::disk('public')->delete($covernote->file_path);
            }
            $data['file_path'] = $request->file('file')->store('covernotes', 'public');
        }

        $covernote->update($data);

        return redirect()->route('covernotes.index')->with('success', 'Data Covernote berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $covernote = Covernote::findOrFail($id);

        if ($covernote->file_path && Storage::disk('public')->exists($covernote->file_path)) {
            Storage::disk('public')->delete($covernote->file_path);
        }

        $covernote->delete();

        return redirect()->route('covernotes.index')->with('success', 'Data Covernote berhasil dihapus.');
    }
}