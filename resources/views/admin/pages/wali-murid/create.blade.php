{{-- penjelasan: File ini adalah halaman tambah wali murid. --}}
{{-- penjelasan: File ini dipanggil oleh WaliMuridController method create(). --}}
{{-- penjelasan: Form pada file ini dikirim ke WaliMuridController method store(). --}}
{{-- penjelasan: Halaman ini bisa dipakai oleh Super Admin dan Admin karena route menggunakan routePrefix. --}}
{{-- penjelasan: Alert validasi tidak ditulis lokal karena sudah ditampilkan global dari admin.components.alert. --}}
{{-- penjelasan: Nama wali, hubungan, nomor WhatsApp, alamat, dan status wajib diisi. --}}
{{-- penjelasan: NIK, pekerjaan, dan nomor HP bersifat opsional. --}}
{{-- penjelasan: Nomor WhatsApp langsung diarahkan memakai format +62. --}}
{{-- penjelasan: Tombol Simpan memakai modal konfirmasi global agar user memilih Ya atau Batal sebelum data disimpan. --}}

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

                    <div class="alert alert-info border-0 shadow-sm rounded-3">
                        <i class="bi bi-info-circle me-1"></i>
                        Field bertanda <span class="text-danger">*</span> wajib diisi.
                    </div>

                    <form action="{{ route($routePrefix . '.wali-murid.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">
                                Nama Wali <span class="text-danger">*</span>
                            </label>

                            <input
                                type="text"
                                name="nama_wali"
                                class="form-control @error('nama_wali') is-invalid @enderror"
                                value="{{ old('nama_wali') }}"
                                placeholder="Masukkan nama wali murid"
                                required
                            >

                            @error('nama_wali')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                Hubungan <span class="text-danger">*</span>
                            </label>

                            <select name="hubungan" class="form-select @error('hubungan') is-invalid @enderror" required>
                                <option value="">Pilih Hubungan</option>
                                <option value="ayah" {{ old('hubungan') === 'ayah' ? 'selected' : '' }}>Ayah</option>
                                <option value="ibu" {{ old('hubungan') === 'ibu' ? 'selected' : '' }}>Ibu</option>
                                <option value="wali" {{ old('hubungan') === 'wali' ? 'selected' : '' }}>Wali</option>
                            </select>

                            @error('hubungan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                NIK <span class="text-muted">(Opsional)</span>
                            </label>

                            <input
                                type="text"
                                name="nik"
                                class="form-control @error('nik') is-invalid @enderror"
                                value="{{ old('nik') }}"
                                placeholder="Masukkan NIK jika ada"
                            >

                            @error('nik')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror

                            <small class="text-muted">
                                NIK boleh dikosongkan, tetapi jika diisi tidak boleh sama dengan wali lain.
                            </small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                Pekerjaan <span class="text-muted">(Opsional)</span>
                            </label>

                            <input
                                type="text"
                                name="pekerjaan"
                                class="form-control @error('pekerjaan') is-invalid @enderror"
                                value="{{ old('pekerjaan') }}"
                                placeholder="Contoh: Petani, PNS, Wiraswasta"
                            >

                            @error('pekerjaan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                Nomor HP <span class="text-muted">(Opsional)</span>
                            </label>

                            <input
                                type="text"
                                name="no_hp"
                                class="form-control @error('no_hp') is-invalid @enderror"
                                value="{{ old('no_hp') }}"
                                placeholder="Masukkan nomor HP jika ada"
                            >

                            @error('no_hp')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                Nomor WhatsApp <span class="text-danger">*</span>
                            </label>

                            <input
                                type="text"
                                name="no_whatsapp"
                                class="form-control @error('no_whatsapp') is-invalid @enderror"
                                value="{{ old('no_whatsapp', '+62') }}"
                                placeholder="+6281234567890"
                                required
                            >

                            @error('no_whatsapp')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                Alamat <span class="text-danger">*</span>
                            </label>

                            <textarea
                                name="alamat"
                                class="form-control @error('alamat') is-invalid @enderror"
                                rows="3"
                                placeholder="Masukkan alamat wali murid"
                                required
                            >{{ old('alamat') }}</textarea>

                            @error('alamat')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label">
                                Status <span class="text-danger">*</span>
                            </label>

                            <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                <option value="aktif" {{ old('status', 'aktif') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="nonaktif" {{ old('status') === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                            </select>

                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a
                                href="{{ route($routePrefix . '.wali-murid.index') }}"
                                class="btn btn-outline-secondary"
                                data-confirm="true"
                                data-confirm-message="Batalkan tambah wali murid? Data yang belum disimpan akan hilang."
                                data-confirm-yes="Ya, Batalkan"
                                data-confirm-yes-class="btn-danger"
                            >
                                Batal
                            </a>

                            <button
                                type="submit"
                                class="btn btn-primary"
                                data-confirm="true"
                                data-confirm-message="Apakah Anda yakin ingin menyimpan data wali murid ini?"
                                data-confirm-yes="Ya, Simpan"
                                data-confirm-yes-class="btn-primary"
                            >
                                <i class="bi bi-save me-1"></i>
                                Simpan Wali Murid
                            </button>
                        </div>
                    </form>

                </div>
            </div>

        </div>
    </div>

@endsection
