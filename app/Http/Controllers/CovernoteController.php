<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Covernote;
use App\Models\Notaris;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Mpdf\Mpdf;

class CovernoteController extends Controller
{
    // Helper untuk mengambil ID Notaris yang sedang login
    private function getNotarisId()
    {
        return Auth::user()->notaris_id;
    }

    public function index(Request $request)
    {
        $search = $request->input('search');
        
        $covernotes = Covernote::with('client')
            ->where('notaris_id', $this->getNotarisId()) // Filter data milik notaris saja
            ->when($search, function ($query, $search) {
                return $query->where(function($q) use ($search) {
                    $q->where('covernote_number', 'like', "%{$search}%")
                    ->orWhere('subject', 'like', "%{$search}%")
                    ->orWhere('recipient', 'like', "%{$search}%")
                    ->orWhereHas('client', function ($q) use ($search) {
                        $q->where('fullname', 'like', "%{$search}%");
                    });
                });
            })
            ->latest()
            ->paginate(10);

        return view('pages.BackOffice.Covernote.index', compact('covernotes'));
    }

    public function print(Request $request)
    {
        $search = $request->input('search');

        $covernotes = Covernote::with('client')
            ->where('notaris_id', $this->getNotarisId()) // Filter data print milik notaris saja
            ->when($search, function ($query, $search) {
                return $query->where('covernote_number', 'like', "%{$search}%")
                            ->orWhere('subject', 'like', "%{$search}%")
                            ->orWhere('recipient', 'like', "%{$search}%");
            })
            ->latest()
            ->get();

        $notaris = Notaris::where('id', $this->getNotarisId())->first();

        $html = view('pages.BackOffice.Covernote.print', compact('covernotes', 'notaris'))->render();

        $mpdf = new Mpdf([
            'default_font'  => 'dejavusans',
            'format'        => 'A4-L',
            'tempDir'       => storage_path('app/mpdf-temp'),
        ]);

        $mpdf->WriteHTML($html);
        return $mpdf->Output('Laporan_Covernote_' . now()->format('YmdHis') . '.pdf', 'I');
    }

    public function create()
    {
        // Hanya tampilkan klien milik notaris ini di dropdown
        $clients = Client::where('notaris_id', $this->getNotarisId())->get();
        return view('pages.BackOffice.Covernote.form', compact('clients'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'client_id'        => 'required',
            'covernote_number' => 'required|string|unique:covernotes,covernote_number',
        ]);

        // Verifikasi kepemilikan klien
        $client = Client::where('id', $request->client_id)
                        ->where('notaris_id', $this->getNotarisId())
                        ->firstOrFail();

        $data = $request->all();
        $data['notaris_id'] = $this->getNotarisId(); // Simpan notaris_id
        $data['client_code'] = $client->client_code;

        if ($request->hasFile('file')) {
            $data['file_path'] = $request->file('file')->store('covernotes', 'public');
        }

        Covernote::create($data);

        return redirect()->route('covernotes.index')->with('success', 'Data berhasil ditambahkan.');
    }

    public function show(string $id)
    {
        $covernote = Covernote::where('id', $id)
                            ->where('notaris_id', $this->getNotarisId())
                            ->firstOrFail();
        return view('pages.BackOffice.Covernote.show', compact('covernote'));
    }

    public function edit(string $id)
    {
        $data = Covernote::where('id', $id)
                        ->where('notaris_id', $this->getNotarisId())
                        ->firstOrFail();
        
        $clients = Client::where('notaris_id', $this->getNotarisId())->get();
        return view('pages.BackOffice.Covernote.form', compact('data', 'clients'));
    }

    public function update(Request $request, string $id)
    {
        $covernote = Covernote::where('id', $id)
                            ->where('notaris_id', $this->getNotarisId())
                            ->firstOrFail();

        $request->validate([
            'client_id' => 'required',
            'covernote_number' => 'required|unique:covernotes,covernote_number,' . $id,
        ]);

        $data = $request->all();
        $client = Client::where('id', $request->client_id)
                        ->where('notaris_id', $this->getNotarisId())
                        ->firstOrFail();
        
        $data['client_code'] = $client->client_code;

        if ($request->hasFile('file')) {
            if ($covernote->file_path) Storage::disk('public')->delete($covernote->file_path);
            $data['file_path'] = $request->file('file')->store('covernotes', 'public');
        }

        $covernote->update($data);
        return redirect()->route('covernotes.index')->with('success', 'Data berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $covernote = Covernote::where('id', $id)
                            ->where('notaris_id', $this->getNotarisId())
                            ->firstOrFail();

        if ($covernote->file_path) Storage::disk('public')->delete($covernote->file_path);
        $covernote->delete();

        return redirect()->route('covernotes.index')->with('success', 'Data berhasil dihapus.');
    }
}