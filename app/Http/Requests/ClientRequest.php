<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ClientRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'fullname' => 'required|string|max:255',
            'nik' => [
                'required_if:type,personal',
                'string',
                'max:20',
                Rule::unique('clients', 'nik')->ignore($this->client),
            ],
            'birth_place' => 'required_if:type,personal',
            'gender' => 'required_if:type,personal',
            'marital_status' => 'required_if:type,personal',
            'job' => 'required_if:type,personal',
            'address' => 'required',
            'city' => 'required',
            'province' => 'required',
            'postcode' => 'nullable',
            'phone' => 'required',
            'email' => 'nullable|email',
            'type' => 'required|in:personal,company',
            'status' => 'nullable|in:pending,valid,revisi',
            'note' => 'nullable',
            'company_name' => 'nullable',
            'npwp' => 'nullable',
            // 
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
    }

    public function messages()
    {
        return [
            'fullname.required' => 'Nama lengkap harus diisi.',
            'nik.required' => 'NIK harus diisi.',
            'nik.unique' => 'NIK sudah terdaftar.',
            'birth_place.required' => 'Tempat lahir harus diisi.',
            'gender.required' => 'Jenis kelamin harus diisi.',
            'marital_status.required' => 'Status perkawinan harus diisi.',
            'job.required' => 'Pekerjaan harus diisi.',
            'address.required' => 'Alamat harus diisi.',
            'city.required' => 'Kota harus diisi.',
            'province.required' => 'Provinsi harus diisi.',
            'postcode.required' => 'Kode pos harus diisi.',
            'phone.required' => 'Nomor telepon harus diisi.',
            'email.required' => 'Email harus diisi.',
            'type.required' => 'Tipe harus diisi.',
            'company_name.required' => 'Nama perusahaan harus diisi.',
            'npwp.required' => 'NPWP harus diisi.',
        ];
    }
}
