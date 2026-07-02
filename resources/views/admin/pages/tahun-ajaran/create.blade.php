{{-- penjelasan: File ini adalah halaman tambah tahun ajaran. --}}
{{-- penjelasan: File ini dipanggil oleh TahunAjaranController method create(). --}}
{{-- penjelasan: Form pada file ini dikirim ke TahunAjaranController method store(). --}}
{{-- penjelasan: Alert validasi tidak ditulis lokal karena sudah ditampilkan global dari admin.components.alert. --}}
{{-- penjelasan: Nama tahun ajaran dan status wajib diisi. --}}
{{-- penjelasan: Tanggal mulai dan tanggal selesai bersifat opsional, tetapi tetap dipilih manual jika diisi. --}}

@extends('admin.layouts.app')

@section('title', 'Tambah Tahun Ajaran')

@section('content')

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <h5 class="fw-bold mb-0">Tambah Tahun Ajaran</h5>
                    <small class="text-muted">Tambahkan periode tahun ajaran baru.</small>
                </div>

                <div class="card-body">

                    <div class="alert alert-info border-0 shadow-sm rounded-3">
                        <i class="bi bi-info-circle me-1"></i>
                        Field bertanda <span class="text-danger">*</span> wajib diisi.
                    </div>

                    <form action="{{ route($routePrefix . '.tahun-ajaran.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">
                                Nama Tahun Ajaran <span class="text-danger">*</span>
                            </label>

                            <input
                                type="text"
                                name="nama_tahun_ajaran"
                                class="form-control @error('nama_tahun_ajaran') is-invalid @enderror"
                                value="{{ old('nama_tahun_ajaran') }}"
                                placeholder="Contoh: 2025/2026"
                                required
                            >

                            @error('nama_tahun_ajaran')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                Tanggal Mulai <span class="text-muted">(Opsional)</span>
                            </label>

                            <input
                                type="date"
                                name="tanggal_mulai"
                                class="form-control @error('tanggal_mulai') is-invalid @enderror"
                                value="{{ old('tanggal_mulai') }}"
                            >

                            @error('tanggal_mulai')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror

                            <small class="text-muted">
                                Tanggal dipilih manual sesuai kalender akademik.
                            </small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                Tanggal Selesai <span class="text-muted">(Opsional)</span>
                            </label>

                            <input
                                type="date"
                                name="tanggal_selesai"
                                class="form-control @error('tanggal_selesai') is-invalid @enderror"
                                value="{{ old('tanggal_selesai') }}"
                            >

                            @error('tanggal_selesai')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror

                            <small class="text-muted">
                                Jika diisi, tanggal selesai tidak boleh lebih kecil dari tanggal mulai.
                            </small>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">
                                Status <span class="text-danger">*</span>
                            </label>

                            <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                <option value="">Pilih Status</option>
                                <option value="nonaktif" {{ old('status', 'nonaktif') === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                                <option value="aktif" {{ old('status') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                            </select>

                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror

                            <small class="text-muted">
                                Jika dibuat aktif, tahun ajaran aktif lainnya otomatis menjadi nonaktif.
                            </small>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a
                                href="{{ route($routePrefix . '.tahun-ajaran.index') }}"
                                class="btn btn-outline-secondary"
                                data-confirm="true"
                                data-confirm-message="Batalkan tambah tahun ajaran? Data yang belum disimpan akan hilang."
                                data-confirm-yes="Ya, Batalkan"
                                data-confirm-yes-class="btn-danger"
                            >
                                Batal
                            </a>

                            <button
                                type="submit"
                                class="btn btn-primary"
                                data-confirm="true"
                                data-confirm-message="Apakah Anda yakin ingin menyimpan data tahun ajaran ini?"
                                data-confirm-yes="Ya, Simpan"
                                data-confirm-yes-class="btn-primary"
                            >
                                <i class="bi bi-save me-1"></i>
                                Simpan Tahun Ajaran
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection
