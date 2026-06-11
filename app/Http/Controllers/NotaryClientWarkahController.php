<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Documents;
use App\Models\NotaryClientWarkah;
use Illuminate\Http\Request;

class NotaryClientWarkahController extends Controller
{
    public function selectClient(Request $request)
    {
        $notarisId = auth()->user()->notaris_id;

        $clients = Client::where('notaris_id', $notarisId)
            ->when($request->search, function ($query, $search) {
                $query->where('fullname', 'like', '%'.$search.'%')->orWhere('client_code', 'like', '%'.$search.'%');
            })
            ->where('deleted_at', null)
            ->paginate(10);

        return view('pages.BackOffice.Warkah.selectClient', [
            'clients' => $clients,
        ]);
    }

    public function index(Request $request, $clientId)
    {
        $client = Client::findOrFail($clientId);
        $query = NotaryClientWarkah::with('client');
        if ($request->filled('client_code')) {
            $query->where('client_code', 'like', '%'.$request->client_code.'%');
        }

        if ($request->filled('fullname')) {
            $query->whereHas('client', function ($q) use ($request) {
                $q->where('fullname', 'like', '%'.$request->client_name.'%');
            });
        }

        $notarisId = auth()->user()->notaris_id;

        $documents = $query->where('notaris_id', $notarisId)->where('client_code', $client->client_code)->where('client_code', $client->client_code)->orderBy('created_at', 'desc')->paginate(10);
        $clients = Client::where('notaris_id', $notarisId)->where('deleted_at', null)->get();

        return view('pages.BackOffice.Warkah.index', [
            'clients' => $clients,
            'documents' => $documents,
            'client' => $client,
        ]);
    }

    public function create($id)
    {
        $notarisId = auth()->user()->notaris_id;

        $client = Client::where('notaris_id', $notarisId)
            ->where('id', $id)
            ->where('deleted_at', null)
            ->firstOrFail();

        $documents = Documents::where('notaris_id', $notarisId)->where('status', 1)->get();

        return view('pages.BackOffice.Warkah.form', [
            'documents' => $documents,
            'client' => $client,
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'client_code' => 'required',
            'status' => 'required',
        ]);

        $warkah = NotaryClientWarkah::findOrFail($id);

        $warkah->update([
            'status' => $request->status,
        ]);

        $msg = $request->status === 'valid'
            ? 'Dokumen berhasil divalidasi'
            : 'Dokumen ditandai tidak valid';

        notyf()->position('x', 'right')->position('y', 'top')->success($msg);

        return back();
    }

    public function store(Request $request)
    {
        $notarisId = auth()->user()->notaris_id;

        $validated = $request->validate([
            'client_code' => 'required',
            'warkah_code' => 'required',
            'city'        => 'required|string|max:255',
            'warkah_link' => 'required|mimes:jpg,jpeg,png,pdf|max:15240',
            'note'        => 'nullable',
            'uploaded_at' => 'required|date',
        ]);

        $path = null;

        if ($request->hasFile('warkah_link')) {
            $path = $request->file('warkah_link')
                ->storeAs(
                    'documents',
                    time() . '_' . $request->file('warkah_link')->getClientOriginalName()
                );
        }

        $client = Client::where('client_code', $request->client_code)->firstOrFail();

        NotaryClientWarkah::create([
            'client_code' => $validated['client_code'],
            'notaris_id'  => $notarisId,
            'warkah_code' => $validated['warkah_code'],
            'warkah_name' => $validated['warkah_code'],
            'city'        => $validated['city'],
            'warkah_link' => $path,
            'note'        => $validated['note'] ?? null,
            'status'      => 'new',
            'uploaded_at' => $validated['uploaded_at'],
        ]);

        notyf()->position('x', 'right')->position('y', 'top')
            ->success('Warkah berhasil ditambahkan');

        return redirect()->route('warkah.index', ['id' => $client->id]);
    }
}
