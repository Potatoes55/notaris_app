<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClientRequest;
use App\Models\Client;
use App\Models\Documents;
use App\Models\Notaris;
use App\Models\NotaryClientDocument;
use App\Models\NotaryClientWarkah;
use App\Models\NotaryCost;
use App\Models\NotaryPayment;
use App\Models\PicDocuments;
use App\Models\PicProcess;
use App\Services\ClientService;
use DNS2D;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use Milon\Barcode\Facades\DNS2DFacade;


class ClientController extends Controller
{
    protected $clientService;

    public function __construct(ClientService $clientService)
    {
        $this->clientService = $clientService;
    }


    public function index(Request $request)
    {
        $clients = $this->clientService->search($request->all());
        return view('pages.Client.index', compact('clients'));
    }


    public function create(Request $request)
    {
        $type = $request->get('type', 'personal');

        return view('pages.Client.form', compact('type'));
    }

    public function store(Request $request)
    {
        $this->clientService->create($request->all());
        notyf()->position('x', 'right')->position('y', 'top')->success('Klien berhasil ditambahkan');
        return redirect()->route('clients.index');
    }

    public function edit($id)
    {
        $client = $this->clientService->getById($id);

        return view('pages.Client.form', compact('client'));
    }

    public function update(Request $request, $id)
    {
        $this->clientService->update($id, $request->all());
        notyf()->position('x', 'right')->position('y', 'top')->success('Klien berhasil diperbarui');
        return redirect()->route('clients.index');
    }

    public function destroy($id)
    {
        $this->clientService->delete($id);
        notyf()->position('x', 'right')->position('y', 'top')->success('Klien berhasil dihapus');
        return redirect()->back();
    }

    public function publicForm($encryptedId, Request $request)
    {
        try {
            $decrypted = Crypt::decrypt($encryptedId);
        } catch (DecryptException $e) {
            abort(404, 'Link tidak valid atau sudah kadaluarsa.');
        }

        if ($request->has('mode') && $request->mode === 'revision') {
            $client = Client::findOrFail($decrypted);

            if ($client->type == 'company') {
                return view('pages.Public.company', compact(
                    'client',
                    'encryptedId'
                ));
            }

            return view('pages.Public.personal', compact(
                'client',
                'encryptedId'
            ));
        }

        $notaris_id = $decrypted;
        $type = $request->get('type', 'personal');

        if ($type == 'company') {
            return view('pages.Public.company', compact(
                'encryptedId',
                'notaris_id'
            ));
        }

        return view('pages.Public.personal', compact(
            'encryptedId',
            'notaris_id'
        ));
    }

    public function editClient($encryptedClientId)
    {
        $clientId = Crypt::decrypt($encryptedClientId);
        $client = Client::findOrFail($clientId);

        if ($client->type == 'company') {
            return view('pages.Public.company', compact(
                'client',
                'encryptedClientId'
            ));
        }

        return view('pages.Public.personal', compact(
            'client',
            'encryptedClientId'
        ));
    }

    public function updateClient(Request $request, $encryptedClientId)
    {
        try {
            $clientId = Crypt::decrypt($encryptedClientId);
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            abort(404, 'Link tidak valid.');
        }

        $client = Client::findOrFail($clientId);

        $validated = $request->validate([
            'fullname' => 'required|string|max:255',
            'nik' => 'required_if:type,personal|string|max:20|unique:clients,nik,' . $client->id,
            'birth_place' => 'required_if:type,personal|string|max:255',
            'gender' => 'required_if:type,personal',
            'marital_status' => 'required_if:type,personal|string',
            'job' => 'required_if:type,personal|string',
            'address' => 'required|string',
            'city' => 'required|string',
            'province' => 'required|string',
            'postcode' => 'nullable|string',
            'phone' => 'required|string',
            'email' => 'nullable|email',
            'npwp' => 'nullable|string',
            'type' => 'nullable|in:personal,company',
            'company_name' => 'nullable|string',
            'note' => 'nullable|string',
            'status' => 'nullable|string',

            'legal_status' => 'required_if:type,company|string|max:255',
            'business_form' => 'required_if:type,company|string|max:255',
            'deed_number' => 'required_if:type,company|string|max:255',
            'deed_date' => 'required_if:type,company|date',
            'nib' => 'required_if:type,company|string|max:255',
            'pic_name' => 'required_if:type,company|string|max:255',
            'pic_position' => 'required_if:type,company|string|max:255',
            'pic_phone' => 'required_if:type,company|string|max:20',
            'pic_email' => 'required_if:type,company|email|max:255',
        ]);

        $client->update($validated);

        notyf()->position('x', 'right')->position('y', 'top')->success('Data klien berhasil diperbarui.');
        return redirect()->back();
    }


    public function storeClient(ClientRequest $request, $encryptedNotarisId)
    {
        try {
            $notaris_id = Crypt::decrypt($encryptedNotarisId);
        } catch (DecryptException $e) {
            abort(403, 'Invalid Notaris ID.');
        }

        $validated = $request->validated();

        $validated['notaris_id'] = $notaris_id;
        $validated['status'] = 'pending';

        $countToday = Client::where('notaris_id', $notaris_id)
            ->whereDate('created_at', now()->toDateString())
            ->count();

        $sequence = $countToday + 1;

        $client = Client::create($validated);

        $client->update([
            'client_code' => 'N' . now()->format('ymd') . '-' . $notaris_id . '-' . $client->id . '-' . $sequence
        ]);

        notyf()->position('x', 'right')->position('y', 'top')
            ->success('Berhasil mengirim data klien. Silakan konfirmasi ke notaris.');

        return redirect()->back();
    }

    public function markAsValid($id)
    {
        $client = Client::findOrFail($id);
        $client->status = 'valid';
        if (empty($client->uuid)) {
            $client->uuid = Str::uuid();
        }
        $client->save();

        notyf()->position('x', 'right')->position('y', 'top')->success('Status Klien Valid atas nama ' . $client->fullname . ' berhasil diubah');
        return redirect()->back();
    }

    public function showQrCode($uuid)
    {
        $client = Client::where('uuid', $uuid)->firstOrFail();

        $link = url("/clients/{$client->uuid}");

        $qrCode = base64_encode(\Milon\Barcode\Facades\DNS2DFacade::getBarcodePNG($link, 'QRCODE', 10, 10));

        return view('pages.Client.modal.qr-code', compact('client', 'link', 'qrCode'));
    }


    public function showByUuid(Request $request, $uuid)
    {
        $client = Client::where('uuid', $uuid)->firstOrFail();

        $notaryCost     = NotaryCost::where('client_code', $client->client_code)->get();
        $notaryPayment  = NotaryPayment::where('client_code', $client->client_code)->get();

        $picDocuments = collect();

        if ($request->filled('client_code')) {
            $picDocuments = PicDocuments::with('processes')
                ->where('client_code', $client->client_code)
                // ->where('pic_document_code', $request->registration_code)
                ->get();
        }

        $clientDocuments = NotaryClientWarkah::where('client_code', $client->client_code)->get();

        $validUploadedCodes = $clientDocuments
            ->where('status', 'valid')
            ->pluck('warkah_code')
            ->toArray();

        $documents = Documents::where('notaris_id', $client->notaris_id)
            ->whereNotIn('code', $validUploadedCodes)
            ->get();

        return view('pages.Client.detail', compact(
            'client',
            'notaryCost',
            'notaryPayment',
            'picDocuments',
            'clientDocuments',
            'documents'
        ));
    }


    public function searchByRegistrationCode(Request $request)
    {
        $request->validate([
            'registration_code' => 'required|string'
        ]);

        $picDocuments = PicDocuments::with('processes')
            ->where('pic_document_code', $request->registration_code)
            ->get();


        return view('pages.Client.detail')->with('registration_code', $request->registration_code);
    }

    public function generateRegistrationCode(int $notarisId, int $clientId): string
    {
        $today = Carbon::now()->format('Ymd');

        $countToday = NotaryClientDocument::where('notaris_id', $notarisId)
            ->where('client_id', $clientId)
            ->whereDate('created_at', Carbon::today())
            ->count();

        $countToday += 1;

        return 'N' . '-' . $today . '-' . $notarisId . '-' . $clientId . '-' . $countToday;
    }


    public function uploadDocument(Request $request, $uuid)
    {
        $client = Client::where('uuid', $uuid)->firstOrFail();

        $validated = $request->validate([
            'document_code' => 'required|exists:documents,code',
            'document_link' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5000',
            'note' => 'nullable|string',
        ]);

        $document = Documents::where('code', $validated['document_code'])->firstOrFail();

        $fileName = time() . '_' . $request->file('document_link')->getClientOriginalName();
        $filePath = $request->file('document_link')->storeAs('documents', $fileName, 'public');

        NotaryClientDocument::create([
            'notaris_id'       => $client->notaris_id,
            'client_id'        => $client->id,
            'registration_code' => $this->generateRegistrationCode($client->notaris_id, $client->id),
            'document_code'    => $document->code,
            'document_name'    => $document->name,
            'note'             => $validated['note'] ?? null,
            'document_link'    => $filePath,
            'uploaded_at'      => now(),
            'status'           => 'new',
        ]);

        notyf()->position('x', 'right')->position('y', 'top')->success('Dokumen berhasil diupload');
        return redirect()->back()->with('active_tab', 'dokumen');
    }

    public function setRevision($id)
    {
        $client = Client::findOrFail($id);

        $client->update(['status' => 'revisi']);

        $encryptedId = Crypt::encrypt($client->notaris_id);

        $revisionLink = route('client.public.create', ['encryptedNotarisId' => $encryptedId]);

        return redirect()->route('clients.index')->with('revisionLink', $revisionLink);
    }


    public function showRevisionForm($encryptedClientId)
    {
        try {
            $clientId = decrypt($encryptedClientId);
        } catch (\Exception $e) {
            abort(404, 'Link tidak valid.');
        }

        $client = Client::findOrFail($clientId);

        if ($client->type == 'company') {
            return view('pages.Public.company', compact(
                'client',
                'encryptedClientId'
            ));
        }

        return view('pages.Public.personal', compact(
            'client',
            'encryptedClientId'
        ));
    }

    public function submitRevision(Request $request, $encryptedClientId)
    {
        $clientId = decrypt($encryptedClientId);
        $client = Client::findOrFail($clientId);

        $validated = $request->validate([
            'fullname' => 'required|string|max:255',
            'nik' => 'required|string|max:50',
            'birth_place' => 'nullable|string|max:255',
            'gender' => 'nullable|string|max:20',
            'marital_status' => 'nullable|string|max:50',
            'job' => 'nullable|string|max:100',
            'company_name' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'province' => 'nullable|string|max:255',
            'postcode' => 'nullable|string|max:20',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'npwp' => 'nullable|string|max:100',
            'type' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'note' => 'nullable|string',
            // 
            'legal_status' => 'nullable|string|max:255',
            'business_form' => 'nullable|string|max:255',
            'deed_number' => 'nullable|string|max:255',
            'deed_date' => 'nullable|date',
            'nib' => 'nullable|string|max:255',
            'pic_name' => 'nullable|string|max:255',
            'pic_position' => 'nullable|string|max:255',
            'pic_phone' => 'nullable|string|max:20',
            'pic_email' => 'nullable|email|max:255',
        ]);

        $client->update($validated);

        $client->status = 'revisi';
        $client->save();

        return redirect()->route('clients.index')
            ->with('success', 'Data revisi berhasil dikirim kembali.');
    }
    
}
