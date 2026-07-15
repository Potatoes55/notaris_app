<?php

namespace App\Services;

use App\Models\Client;
use App\Repositories\Interfaces\ClientRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ClientService
{
    protected $clientRepository;

    public function __construct(ClientRepositoryInterface $clientRepository)
    {
        $this->clientRepository = $clientRepository;
    }

    public function getAll()
    {
        return $this->clientRepository->all();
    }

    public function search(array $filters)
    {
        $filters['notaris_id'] = auth()->user()->notaris_id;

        return $this->clientRepository->search($filters);
    }

    public function getById($id)
    {
        return $this->clientRepository->findById($id);
    }

    public function create(array $data)
    {
        $validated = $this->validate($data, $id = null);
        $validated['notaris_id'] = auth()->user()->notaris_id;

        if (
            (($validated['status'] ?? null) === 'valid') &&
            (empty($validated['uuid']))
        ) {
            $validated['uuid'] = Str::uuid();
        }

        // 1️⃣ Hitung jumlah client hari ini SEBELUM insert
        $countToday = Client::where('notaris_id', $validated['notaris_id'])
            ->whereDate('created_at', Carbon::today())
            ->count();
        $sequence = $countToday + 1;

        // 2️⃣ Simpan client
        $client = $this->clientRepository->create($validated);

        // 3️⃣ Generate client_code
        $clientCode = 'N'.Carbon::now()->format('ymd').'-'.$client->notaris_id.'-'.$client->id.'-'.$sequence;

        // 4️⃣ Update client dengan client_code
        $client = $this->clientRepository->update($client->id, ['client_code' => $clientCode]);

        return $client;
    }

    // private function generateClientCode(int $notarisId, int $clientId): string
    // {
    //     $today = Carbon::now()->format('ymd');

    //     // Hitung jumlah client yang sudah ada hari ini SEBELUM client ini dibuat
    //     $countToday = Client::where('notaris_id', $notarisId)
    //         ->whereDate('created_at', Carbon::today())
    //         ->count();

    //     $sequence = $countToday + 1; // urutan client baru hari ini

    //     return 'N' . '-' . $today . '-' . $notarisId . '-' . $clientId . '-' . $sequence;
    // }

    public function update($id, array $data)
    {
        $validated = $this->validate($data, $id);
        $validated['notaris_id'] = auth()->user()->notaris_id ?? null;

        return $this->clientRepository->update($id, $validated);
    }

    public function delete($id)
    {
        return $this->clientRepository->delete($id);
    }

    protected function validate(array $data, $id = null)
    {
        $rules = [

            'fullname' => 'required|string|max:255',
            'nik' => 'required_if:type,personal',
            'birth_place' => 'required_if:type,personal|string|max:255',
            'gender' => 'required_if:type,personal',
            'marital_status' => 'required_if:type,personal|string',
            'job' => 'required_if:type,personal|string',
            'address' => 'required|string',
            'kota_name' => 'required|string',
            'provinsi_name' => 'required|string',
            'kecamatan_name' => 'required|string',
            'kelurahan_name' => 'required|string',
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

        ];

        $messages = [
            'fullname.required' => 'Nama lengkap wajib diisi.',
            'fullname.max' => 'Nama lengkap maksimal 255 karakter.',
            'nik.required' => 'NIK wajib diisi.',
            'birth_place.required' => 'Tempat lahir wajib diisi.',
            'gender.required' => 'Jenis kelamin wajib dipilih.',
            'marital_status.required' => 'Status pernikahan wajib dipilih.',
            'job.required' => 'Pekerjaan wajib diisi.',
            'address.required' => 'Alamat wajib diisi.',
            'kota.required' => 'Kota wajib diisi.',
            'provinsi.required' => 'Provinsi wajib diisi.',
            'Kecamatan.required' => 'Kecamatan wajib diisi.',
            'Kelurahan.required' => 'Kelurahan wajib diisi.',
            'postcode.required' => 'Kode pos wajib diisi.',
            'phone.required' => 'Nomor telepon wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'npwp.required' => 'NPWP wajib diisi.',
            'type.required' => 'Tipe klien wajib dipilih.',
            'type.in' => 'Tipe klien hanya boleh "personal" atau "company".',
            'status.required' => 'Status wajib diisi.',
        ];

        $validator = Validator::make($data, $rules, $messages);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }
}
