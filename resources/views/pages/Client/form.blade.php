@extends('layouts.app')

@section('title', 'Klien')

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => isset($client) ? 'Edit Klien' : 'Tambah Klien'])
    @php
        $type = old('type', $client->type ?? request('type'));
    @endphp
    <div class="row mt-4 mx-4">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0">
                    <h6>{{ isset($client) ? 'Edit' : 'Tambah' }} Klien</h6>
                </div>
                <hr>
                <div class="card-body px-4 pt-0 pb-2">
                    <form action="{{ isset($client) ? route('clients.update', $client->id) : route('clients.store') }}"
                        method="POST">
                        @csrf
                        @if (isset($client))
                            @method('PUT')
                        @endif

                        <div class="row">
                            <input type="hidden" name="notaris_id" value="{{ auth()->user()->notaris_id }}">


                            {{--  --}}
                            {{-- <input type="text" name="tipe_klien" value="{{ request('type') }}" readonly> --}}
                            {{-- <input type="hidden" name="type" value="{{ $type ?? request('type') }}" readonly> --}}
                            {{-- <input type="hidden" name="type" value="{{ old('type', request('type')) }}"> --}}
                            <input type="hidden" name="type" value="{{ $type }}">

                            {{--  --}}
                            {{--  --}}
                            {{-- @if (request('type') == 'personal') --}}
                            @if ($type == 'personal')
                                {{--  --}}
                                {{-- @if ($errors->any())
                                    <div class="alert alert-danger">
                                        <ul class="mb-0">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif --}}

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
                                    <select name="status" class="form-select">
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
                            @endif
                            {{-- FORM COMPANY --}}
                            {{-- @elseif(request('type') == 'company') --}}
                            @if ($type == 'company')
                                {{-- @if ($errors->any())
                                    <div class="alert alert-danger">
                                        <ul class="mb-0">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif --}}
                                <div class="col-md-6 mb-3">
                                    <label for="fullname" class="form-label text-sm">Nama Badan Usaha/Badan
                                        Hukum</label>
                                    <input type="text" name="fullname" class="form-control"
                                        placeholder="Masukkan nama "
                                        value="{{ old('fullname', $client->fullname ?? '') }}" required>
                                    @error('fullname')
                                        <div class="text-danger text-sm mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="legal_status" class="form-label text-sm">Status Legal</label>
                                    <select name="legal_status" class="form-select" required>
                                        <option value="" hidden>Pilih</option>
                                        <option value="legal_entity"
                                            {{ old('legal_status', $client->legal_status ?? '') == 'legal_entity' ? 'selected' : '' }}>
                                            Badan Hukum</option>
                                        <option value="non_legal_entity"
                                            {{ old('legal_status', $client->legal_status ?? '') == 'non_legal_entity' ? 'selected' : '' }}>
                                            Non Badan Hukum</option>
                                    </select>
                                </div>

                                {{-- <div class="col-md-6 mb-3">
                                        <label for="business_form" class="form-label">Business Form</label>
                                        <input type="text" class="form-control" id="business_form"
                                            name="business_form">
                                    </div> --}}

                                <div class="col-md-6 mb-3">
                                    <label for="business_form" class="form-label text-sm">Bentuk Usaha</label>
                                    <select name="business_form" class="form-select" required>
                                        <option value="" hidden>Pilih</option>
                                        <option value="CV"
                                            {{ old('business_form', $client->business_form ?? '') == 'CV' ? 'selected' : '' }}>
                                            CV</option>
                                        <option value="PT perorangan"
                                            {{ old('business_form', $client->business_form ?? '') == 'PT perorangan' ? 'selected' : '' }}>
                                            PT perorangan</option>
                                        <option value="PT Persekutuan Modal"
                                            {{ old('business_form', $client->business_form ?? '') == 'PT Persekutuan Modal' ? 'selected' : '' }}>
                                            PT Persekutuan Modal</option>
                                        <option value="Yayasan"
                                            {{ old('business_form', $client->business_form ?? '') == 'Yayasan' ? 'selected' : '' }}>
                                            Yayasan</option>
                                        <option value="Perkumpulan"
                                            {{ old('business_form', $client->business_form ?? '') == 'Perkumpulan' ? 'selected' : '' }}>
                                            Perkumpulan</option>
                                        <option value="Koperasi"
                                            {{ old('business_form', $client->business_form ?? '') == 'Koperasi' ? 'selected' : '' }}>
                                            Koperasi</option>
                                        <option value="Usaha Dagang"
                                            {{ old('business_form', $client->business_form ?? '') == 'Firma' ? 'selected' : '' }}>
                                            Firma</option>
                                        <option value="Usaha Dagang"
                                            {{ old('business_form', $client->business_form ?? '') == 'Usaha Dagang' ? 'selected' : '' }}>
                                            Usaha Dagang</option>
                                        <option value="Lainnya"
                                            {{ old('business_form', $client->business_form ?? '') == 'Lainnya' ? 'selected' : '' }}>
                                            Lainnya</option>
                                    </select>
                                    @error('business_form')
                                        <div class="text-danger text-sm mt-1">{{ $message }}</div>
                                    @enderror
                                </div>


                                <div class="col-md-6 mb-3">
                                    <label for="deed_number" class="form-label text-sm">Nomor Akta Pendirian</label>
                                    <input type="text" name="deed_number" class="form-control"
                                        placeholder="Masukkan nomor akta pendirian"
                                        value="{{ old('deed_number', $client->deed_number ?? '') }}" required>
                                    {{-- @error('deed_number')
                                            <div class="text-danger text-sm mt-1">{{ $message }}</div>
                                        @enderror --}}
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="deed_date" class="form-label text-sm">Tanggal Akta Pendirian</label>
                                    <input type="date" name="deed_date" class="form-control"
                                        placeholder="Masukkan tanggal akta pendirian"
                                        value="{{ old('deed_date', $client->deed_date ?? '') }}" required>
                                    {{-- @error('deed_date')
                                            <div class="text-danger text-sm mt-1">{{ $message }}</div>
                                        @enderror --}}
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="nib" class="form-label text-sm">NIB</label>
                                    <input type="text" class="form-control" id="nib" name="nib"
                                        placeholder="Masukkan nomor NIB" value="{{ old('nib', $client->nib ?? '') }}" required>
                                    {{-- @error('nib')
                                            <div class="text-danger text-sm mt-1">{{ $message }}</div>
                                        @enderror --}}
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="npwp" class="form-label text-sm">NPWP</label>
                                    <input type="text" name="npwp" class="form-control"
                                        placeholder="Masukkan nomor NPWP" value="{{ old('npwp', $client->npwp ?? '') }}">
                                    @error('npwp')
                                        <div class="text-danger text-sm mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="pic_name" class="form-label">PIC Name</label>
                                    <input type="text" class="form-control" id="pic_name" name="pic_name"
                                        placeholder="Masukkan Nama PIC"
                                        value="{{ old('pic_name', $client->pic_name ?? '') }}" required>
                                    {{-- @error('pic_name')
                                            <div class="text-danger text-sm mt-1">{{ $message }}</div>
                                        @enderror --}}
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="pic_position" class="form-label">PIC Position</label>
                                    <input type="text" class="form-control" id="pic_position" name="pic_position"
                                        placeholder="Masukkan Jabatan"
                                        value="{{ old('pic_position', $client->pic_position ?? '') }}" required>
                                    {{-- @error('pic_position')
                                            <div class="text-danger text-sm mt-1">{{ $message }}</div>
                                        @enderror --}}
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="pic_phone" class="form-label">PIC Phone</label>
                                    <input type="text" class="form-control" id="pic_phone" name="pic_phone"
                                        placeholder="Masukkan no hp PIC"
                                        value="{{ old('pic_phone', $client->pic_phone ?? '') }}" required>
                                    {{-- @error('pic_phone')
                                        <div class="text-danger text-sm mt-1">{{ $message }}</div>
                                    @enderror --}}
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="pic_email" class="form-label">PIC Email</label>
                                    <input type="email" class="form-control" id="pic_email" name="pic_email"
                                        placeholder="Masukkan alamat email PIC"
                                        value="{{ old('pic_email', $client->pic_email ?? '') }}" required>
                                    {{-- @error('pic_email')
                                        <div class="text-danger text-sm mt-1">{{ $message }}</div>
                                    @enderror --}}
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="address" class="form-label text-sm">Alamat Badan</label>
                                    <input type="text" name="address" class="form-control"
                                        placeholder="Masukkan alamat lengkap"
                                        value="{{ old('address', $client->address ?? '') }}" required>
                                    @error('address')
                                        <div class="text-danger text-sm mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="city" class="form-label text-sm">Kota Badan</label>
                                    <input type="text" name="city" class="form-control"
                                        placeholder="Masukkan nama kota" value="{{ old('city', $client->city ?? '') }}" required>
                                    @error('city')
                                        <div class="text-danger text-sm mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="province" class="form-label text-sm">Provinsi Badan</label>
                                    <input type="text" name="province" class="form-control"
                                        placeholder="Masukkan provinsi"
                                        value="{{ old('province', $client->province ?? '') }}" required>
                                    @error('province')
                                        <div class="text-danger text-sm mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="postcode" class="form-label text-sm">Kode Pos</label>
                                    <input type="text" name="postcode" class="form-control"
                                        placeholder="Masukkan kode pos"
                                        value="{{ old('postcode', $client->postcode ?? '') }}" required>
                                    @error('postcode')
                                        <div class="text-danger text-sm mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- <div class="col-md-6 mb-3">
                                    <label for="company_phone" class="form-label">Company Phone</label>
                                    <input type="tel" class="form-control" id="company_phone" name="company_phone">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="company_email" class="form-label">Company Email</label>
                                    <input type="email" class="form-control" id="company_email" name="company_email">
                                </div> --}}

                                <div class="col-md-6 mb-3">
                                    <label for="phone" class="form-label text-sm">Telepon Perusahaan</label>
                                    <input type="text" name="phone" class="form-control"
                                        placeholder="Masukkan nomor telepon perusahaan"
                                        value="{{ old('phone', $client->phone ?? '') }}" required>
                                    @error('phone')
                                        <div class="text-danger text-sm mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label text-sm">Email Perusahaan</label>
                                    <input type="email" name="email" class="form-control"
                                        placeholder="Masukkan alamat email perusahaan"
                                        value="{{ old('email', $client->email ?? '') }}" required>
                                    @error('email')
                                        <div class="text-danger text-sm mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="status" class="form-label text-sm">Status</label>
                                    <select name="status" class="form-select">
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

                        {{--  --}}
                        @endif
                </div>

                {{-- @endif --}}

                <div class="mt-4">
                    <a href="{{ route('clients.index') }}" class="btn btn-secondary">Kembali</a>
                    <button type="submit" class="btn btn-primary">{{ isset($client) ? 'Ubah' : 'Simpan' }}</button>
                </div>
                </form>
            </div>
        </div>
    </div>
    </div>
@endsection
