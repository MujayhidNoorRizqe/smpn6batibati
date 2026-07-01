{{-- penjelasan: File ini adalah halaman tambah wali murid. --}}
{{-- penjelasan: File ini dipanggil oleh WaliMuridController method create(). --}}
{{-- penjelasan: Form pada file ini dikirim ke WaliMuridController method store(). --}}
{{-- penjelasan: Halaman ini bisa dipakai oleh Super Admin dan Admin karena route menggunakan routePrefix. --}}

@extends('admin.layouts.app')

@section('title', 'Tambah Wali Murid')

@section('content')

    <div class="row justify-content-center">
        <div class="col-lg-8">

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <h5 class="fw-bold mb-0">Tambah Wali Murid</h5>
                    <small class="text-muted">Tambahkan data orang tua atau wali murid.</small>
                </div>

                <div class="card-body">

                    {{-- penjelasan: Bagian ini menampilkan error validasi dari controller. --}}
                    {{-- penjelasan: Error muncul jika input wajib kosong atau data tidak sesuai aturan. --}}
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            @foreach ($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    @endif

                    {{-- penjelasan: Form ini mengirim data wali murid baru ke route wali-murid.store. --}}
                    {{-- penjelasan: Method POST digunakan karena form ini membuat data baru. --}}
                    <form action="{{ route($routePrefix . '.wali-murid.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Nama Wali</label>
                            <input
                                type="text"
                                name="nama_wali"
                                class="form-control"
                                value="{{ old('nama_wali') }}"
                                placeholder="Masukkan nama wali murid"
                                required
                            >
                        </div>

                        <div class="mb-3">
                            <label class="form-label">NIK</label>
                            <input
                                type="text"
                                name="nik"
                                class="form-control"
                                value="{{ old('nik') }}"
                                placeholder="Masukkan NIK jika ada"
                            >
                            <small class="text-muted">NIK boleh dikosongkan, tetapi jika diisi tidak boleh sama dengan wali lain.</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Hubungan</label>
                            <select name="hubungan" class="form-select" required>
                                <option value="">Pilih Hubungan</option>
                                <option value="ayah" {{ old('hubungan') === 'ayah' ? 'selected' : '' }}>Ayah</option>
                                <option value="ibu" {{ old('hubungan') === 'ibu' ? 'selected' : '' }}>Ibu</option>
                                <option value="wali" {{ old('hubungan') === 'wali' ? 'selected' : '' }}>Wali</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Pekerjaan</label>
                            <input
                                type="text"
                                name="pekerjaan"
                                class="form-control"
                                value="{{ old('pekerjaan') }}"
                                placeholder="Contoh: Petani, PNS, Wiraswasta"
                            >
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nomor HP</label>
                            <input
                                type="text"
                                name="no_hp"
                                class="form-control"
                                value="{{ old('no_hp') }}"
                                placeholder="Masukkan nomor HP"
                            >
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nomor WhatsApp</label>
                            <input
                                type="text"
                                name="no_whatsapp"
                                class="form-control"
                                value="{{ old('no_whatsapp') }}"
                                placeholder="Contoh: 6281234567890"
                            >
                            <small class="text-muted">Nomor ini nanti digunakan untuk notifikasi Fonnte. Disarankan format 62.</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Alamat</label>
                            <textarea
                                name="alamat"
                                class="form-control"
                                rows="3"
                                placeholder="Masukkan alamat wali murid"
                            >{{ old('alamat') }}</textarea>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select" required>
                                <option value="aktif" {{ old('status') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="nonaktif" {{ old('status') === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                            </select>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route($routePrefix . '.wali-murid.index') }}" class="btn btn-outline-secondary">
                                Batal
                            </a>

                            <button type="submit" class="btn btn-primary">
                                Simpan Wali Murid
                            </button>
                        </div>
                    </form>

                </div>
            </div>

        </div>
    </div>

@endsection
