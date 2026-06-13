@extends('layouts.app')

@section('title', 'Klien Personal')

@section('content')
@include('layouts.navbars.auth.topnav', ['title' => isset($client) ? 'Edit Klien Personal' : 'Tambah Klien Personal'])

<div class="row mt-4 mx-4">
    <div class="col-12">
        <div class="card mb-4">
            <div class="card-header pb-0">
                <h6>{{ isset($client) ? 'Edit' : 'Tambah' }} Klien Personal</h6>
            </div>

            <hr>

            <div class="card-body px-4 pt-0 pb-2">
                <form action="{{ route('client.public.store', $encryptedId) }}" method="POST">
                    @csrf

                    @if(isset($client))
                        @method('PUT')
                    @endif

                    <input type="hidden" name="notaris_id"
                        value="{{ $notaris_id ?? ($client->notaris_id ?? '') }}">
                    <input type="hidden" name="type" value="personal">

                    <div class="row">

                                <div class="col-md-6 mb-3">
                                    <label for="fullname" class="form-label text-sm">Nama Lengkap</label>
                                    <input type="text" name="fullname" class="form-control"
                                        placeholder="Masukkan nama lengkap"
                                        value="{{ old('fullname', $client->fullname ?? '') }}">
                                    @error('fullname')
                                        <div class="text-danger text-sm mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="nik" class="form-label text-sm">NIK</label>
                                    <input type="text" name="nik" class="form-control"
                                        placeholder="Masukkan nomor NIK" value="{{ old('nik', $client->nik ?? '') }}">
                                    @error('nik')
                                        <div class="text-danger text-sm mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="birth_place" class="form-label text-sm">Tempat Lahir</label>
                                    <input type="text" name="birth_place" class="form-control"
                                        placeholder="Masukkan tempat lahir"
                                        value="{{ old('birth_place', $client->birth_place ?? '') }}">
                                    @error('birth_place')
                                        <div class="text-danger text-sm mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="gender" class="form-label text-sm">Jenis Kelamin</label>
                                    <select name="gender" class="form-select">
                                        <option value="" hidden>Pilih jenis kelamin</option>
                                        <option value="Laki-Laki"
                                            {{ old('gender', $client->gender ?? '') == 'Laki-Laki' ? 'selected' : '' }}>
                                            Laki-laki</option>
                                        <option value="Perempuan"
                                            {{ old('gender', $client->gender ?? '') == 'Perempuan' ? 'selected' : '' }}>
                                            Perempuan</option>
                                    </select>
                                    @error('gender')
                                        <div class="text-danger text-sm mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="marital_status" class="form-label text-sm">Status Perkawinan</label>
                                    <select name="marital_status" class="form-select">
                                        <option value="" hidden>Pilih status perkawinan</option>
                                        <option value="Belum Menikah"
                                            {{ old('marital_status', $client->marital_status ?? '') == 'Belum Menikah' ? 'selected' : '' }}>
                                            Belum Menikah</option>
                                        <option value="Menikah"
                                            {{ old('marital_status', $client->marital_status ?? '') == 'Menikah' ? 'selected' : '' }}>
                                            Menikah</option>
                                        <option value="Cerai"
                                            {{ old('marital_status', $client->marital_status ?? '') == 'Cerai' ? 'selected' : '' }}>
                                            Cerai</option>
                                        <option value="widow"
                                            {{ old('marital_status', $client->marital_status ?? '') == 'widow' ? 'selected' : '' }}>
                                            Janda/Duda</option>
                                    </select>
                                    @error('marital_status')
                                        <div class="text-danger text-sm mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="job" class="form-label text-sm">Pekerjaan</label>
                                    <input type="text" name="job" class="form-control"
                                        placeholder="Masukkan pekerjaan" value="{{ old('job', $client->job ?? '') }}">
                                    @error('job')
                                        <div class="text-danger text-sm mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="address" class="form-label text-sm">Alamat</label>
                                    <input type="text" name="address" class="form-control"
                                        placeholder="Masukkan alamat lengkap"
                                        value="{{ old('address', $client->address ?? '') }}">
                                    @error('address')
                                        <div class="text-danger text-sm mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="city" class="form-label text-sm">Kota</label>
                                    <input type="text" name="city" class="form-control"
                                        placeholder="Masukkan nama kota" value="{{ old('city', $client->city ?? '') }}">
                                    @error('city')
                                        <div class="text-danger text-sm mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="province" class="form-label text-sm">Provinsi</label>
                                    <input type="text" name="province" class="form-control"
                                        placeholder="Masukkan provinsi"
                                        value="{{ old('province', $client->province ?? '') }}">
                                    @error('province')
                                        <div class="text-danger text-sm mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="postcode" class="form-label text-sm">Kode Pos</label>
                                    <input type="text" name="postcode" class="form-control"
                                        placeholder="Masukkan kode pos"
                                        value="{{ old('postcode', $client->postcode ?? '') }}">
                                    @error('postcode')
                                        <div class="text-danger text-sm mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="phone" class="form-label text-sm">Telepon</label>
                                    <input type="text" name="phone" class="form-control"
                                        placeholder="Masukkan nomor telepon"
                                        value="{{ old('phone', $client->phone ?? '') }}">
                                    @error('phone')
                                        <div class="text-danger text-sm mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label text-sm">Email</label>
                                    <input type="email" name="email" class="form-control"
                                        placeholder="Masukkan alamat email"
                                        value="{{ old('email', $client->email ?? '') }}">
                                    @error('email')
                                        <div class="text-danger text-sm mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="npwp" class="form-label text-sm">NPWP</label>
                                    <input type="text" name="npwp" class="form-control"
                                        placeholder="Masukkan nomor NPWP" value="{{ old('npwp', $client->npwp ?? '') }}">
                                    @error('npwp')
                                        <div class="text-danger text-sm mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- <div class="col-md-6 mb-3">
                                    <label for="type" class="form-label text-sm">Tipe Klien</label>
                                    <
                                    select name="type" class="form-select">
                                        <option value="" hidden>Pilih tipe klien</option>
                                        <option value="personal"
                                            {{ old('type', $client->type ?? '') == 'personal' ? 'selected' : '' }}>Personal
                                        </option>
                                        <option value="company"
                                            {{ old('type', $client->type ?? '') == 'company' ? 'selected' : '' }}>
                                            Perusahaan
                                        </option>
                                    </>
                                    @error('type')
                                        <div class="text-danger text-sm mt-1">{{ $message }}</div>
                                    @enderror
                                </div> --}}

                                {{-- <div class="col-md-6 mb-3">
                                    <label for="company_name" class="form-label text-sm">Nama Perusahaan</label>
                                    <input type="text" name="company_name" class="form-control"
                                        placeholder="Masukkan nama perusahaan (jika ada)"
                                        value="{{ old('company_name', $client->company_name ?? '') }}">
                                    @error('company_name')
                                        <div class="text-danger text-sm mt-1">{{ $message }}</div>
                                    @enderror
                                </div> --}}

                                <div class="col-md-6 mb-3">
                                    <label for="status" class="form-label text-sm">Status</label>
                                    <select name="status" class="form-select" required>
                                        <option value="" hidden>Pilih status klien</option>
                                        <option value="pending"
                                            {{ old('status', $client->status ?? '') == 'pending' ? 'selected' : '' }}>
                                            Pending
                                        </option>
                                        <option value="valid"
                                            {{ old('status', $client->status ?? '') == 'valid' ? 'selected' : '' }}>Valid
                                        </option>
                                        <option value="revisi"
                                            {{ old('status', $client->status ?? '') == 'revisi' ? 'selected' : '' }}>Revisi
                                        </option>
                                    </select>
                                    @error('status')
                                        <div class="text-danger text-sm mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label for="note" class="form-label text-sm">Catatan</label>
                                    <textarea name="note" class="form-control" rows="3" placeholder="Tambahkan catatan jika diperlukan">{{ old('note', $client->note ?? '') }}</textarea>
                                </div>

                    </div>

                    <div class="mt-4">
                        <a href="{{ route('clients.index') }}" class="btn btn-secondary">Kembali</a>
                        <button type="submit" class="btn btn-primary">
                            {{ isset($client) ? 'Ubah' : 'Simpan' }}
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>
@endsection