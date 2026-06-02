@extends('layouts.app')

@section('title', 'Jenis Akta')

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Akta Notaris / Jenis Akta'])

    <div class="row mt-4 mx-4">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0">
                    <h6 class="mb-0">{{ isset($aktaType) ? 'Edit Jenis Akta' : 'Tambah Jenis Akta' }}</h6>
                </div>
                <hr>
                <div class="card-body px-4 pt-0 pb-2">
                    <form
                        action="{{ isset($aktaType) ? route('akta-types.update', $aktaType->id) : route('akta-types.store') }}"
                        method="POST">
                        @csrf
                        @if (isset($aktaType))
                            @method('PUT')
                        @endif
                        <div class="mb-3">
                            <label for="category" class="form-label text-sm">Kategori Akta</label>

                            @php
                                // Daftar kategori standar
                                $defaultCategories = ['pendirian', 'perubahan', 'pembubaran'];

                                // Ambil kategori dari database (saat edit)
                                $selectedCategory = old('category', $aktaType->category ?? '');

                                // Cek apakah kategori bukan dari daftar standar
                                $isOther = $selectedCategory && !in_array($selectedCategory, $defaultCategories);
                            @endphp

                            <select name="category" id="category"
                                class="form-select @error('category') is-invalid @enderror">
                                <option value="" hidden>Pilih Kategori Akta</option>
                                @foreach ($defaultCategories as $cat)
                                    <option value="{{ $cat }}" {{ $selectedCategory === $cat ? 'selected' : '' }}>
                                        {{ ucfirst($cat) }}
                                    </option>
                                @endforeach
                                <option value="lainnya" {{ $isOther ? 'selected' : '' }}>Lainnya</option>
                            </select>

                            {{-- Input tambahan hanya muncul jika pilih "lainnya" --}}
                            <input type="text" name="other_category" id="other_category"
                                class="form-control mt-2 @error('other_category') is-invalid @enderror"
                                placeholder="Isi kategori lain..."
                                value="{{ old('other_category', $isOther ? $selectedCategory : '') }}"
                                style="{{ $isOther ? '' : 'display:none;' }}">

                            @error('category')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>


                        <div class="mb-3">
                            <label for="type" class="form-label text-sm">Tipe</label>
                            <input type="text" name="type" id="type"
                                class="form-control @error('type') is-invalid @enderror"
                                value="{{ old('type', $aktaType->type ?? '') }}" placeholder="Contoh: Akta Pendirian PT">
                            {{-- <div class="form-text text-secondary">Contoh: Akta Pendirian PT</div> --}}
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label text-sm">Deskripsi</label>
                            <textarea name="description" id="description" class="form-control">{{ $aktaType->description ?? old('description') }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label for="documents" class="form-label text-sm">Kebutuhan Dokumen</label>
                            <textarea name="documents" id="documents" class="form-control"
                                placeholder="Contoh: Fotokopi KTP, NPWP, Fotokopi Akta Pendirian">{{ $aktaType->documents ?? old('documents') }}</textarea>
                            {{-- <div class="form-text">Contoh: Fotokopi KTP, NPWP, Fotokopi Akta Pendirian</div> --}}
                        </div>


                        <a href="{{ route('akta-types.index') }}" class="btn btn-secondary">Kembali</a>
                        <button type="submit" class="btn btn-primary">
                            {{ isset($aktaType) ? 'Ubah' : 'Simpan' }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('js')
    <script>
        function toggleOtherCategory(select) {
            const otherInput = document.getElementById('other_category');
            if (select.value === 'lainnya') {
                otherInput.style.display = 'block';
            } else {
                otherInput.style.display = 'none';
                otherInput.value = '';
            }
        }

        // Biar tetap tampil kalau sebelumnya pilih "lainnya"
        document.addEventListener('DOMContentLoaded', function() {
            const select = document.getElementById('category');
            toggleOtherCategory(select);
        });
        document.addEventListener('DOMContentLoaded', function() {
            const select = document.getElementById('category');
            const otherInput = document.getElementById('other_category');

            select.addEventListener('change', function() {
                if (this.value === 'lainnya') {
                    otherInput.style.display = 'block';
                    otherInput.focus();
                } else {
                    otherInput.style.display = 'none';
                    otherInput.value = '';
                }
            });
        });
    </script>
@endpush
