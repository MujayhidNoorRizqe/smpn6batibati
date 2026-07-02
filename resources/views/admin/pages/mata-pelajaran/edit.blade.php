{{-- penjelasan: File ini adalah halaman edit mata pelajaran. --}}
{{-- penjelasan: File ini dipanggil oleh MataPelajaranController method edit(). --}}
{{-- penjelasan: Form pada file ini dikirim ke MataPelajaranController method update(). --}}
{{-- penjelasan: Data $mataPelajaran berisi data mata pelajaran yang sedang diedit. --}}
{{-- penjelasan: Alert validasi tidak ditulis lokal karena sudah ditampilkan global dari admin.components.alert. --}}
{{-- penjelasan: Kode mapel, nama mapel, kelompok, dan status wajib diisi. --}}
{{-- penjelasan: Deskripsi bersifat opsional. --}}

@extends('admin.layouts.app')

@section('title', 'Edit Mata Pelajaran')

@section('content')

    <div class="row justify-content-center">
        <div class="col-lg-8">

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <h5 class="fw-bold mb-0">Edit Mata Pelajaran</h5>
                    <small class="text-muted">Ubah data mata pelajaran.</small>
                </div>

                <div class="card-body">

                    <div class="alert alert-info border-0 shadow-sm rounded-3">
                        <i class="bi bi-info-circle me-1"></i>
                        Field bertanda <span class="text-danger">*</span> wajib diisi.
                    </div>

                    <form action="{{ route($routePrefix . '.mata-pelajaran.update', $mataPelajaran) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label">
                                Kode Mata Pelajaran <span class="text-danger">*</span>
                            </label>

                            <input
                                type="text"
                                name="kode_mapel"
                                class="form-control @error('kode_mapel') is-invalid @enderror"
                                value="{{ old('kode_mapel', $mataPelajaran->kode_mapel) }}"
                                placeholder="Contoh: MTK"
                                required
                            >

                            @error('kode_mapel')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror

                            <small class="text-muted">
                                Kode akan otomatis disimpan dalam huruf besar.
                            </small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                Nama Mata Pelajaran <span class="text-danger">*</span>
                            </label>

                            <input
                                type="text"
                                name="nama_mapel"
                                class="form-control @error('nama_mapel') is-invalid @enderror"
                                value="{{ old('nama_mapel', $mataPelajaran->nama_mapel) }}"
                                placeholder="Contoh: Matematika"
                                required
                            >

                            @error('nama_mapel')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                Kelompok <span class="text-danger">*</span>
                            </label>

                            <select name="kelompok" class="form-select @error('kelompok') is-invalid @enderror" required>
                                <option value="">Pilih Kelompok</option>
                                <option value="umum" {{ old('kelompok', $mataPelajaran->kelompok) === 'umum' ? 'selected' : '' }}>Umum</option>
                                <option value="muatan_lokal" {{ old('kelompok', $mataPelajaran->kelompok) === 'muatan_lokal' ? 'selected' : '' }}>Muatan Lokal</option>
                                <option value="ekstrakurikuler" {{ old('kelompok', $mataPelajaran->kelompok) === 'ekstrakurikuler' ? 'selected' : '' }}>Ekstrakurikuler</option>
                            </select>

                            @error('kelompok')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                Deskripsi <span class="text-muted">(Opsional)</span>
                            </label>

                            <textarea
                                name="deskripsi"
                                class="form-control @error('deskripsi') is-invalid @enderror"
                                rows="3"
                                placeholder="Masukkan deskripsi jika diperlukan"
                            >{{ old('deskripsi', $mataPelajaran->deskripsi) }}</textarea>

                            @error('deskripsi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label">
                                Status <span class="text-danger">*</span>
                            </label>

                            <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                <option value="">Pilih Status</option>
                                <option value="aktif" {{ old('status', $mataPelajaran->status) === 'aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="nonaktif" {{ old('status', $mataPelajaran->status) === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                            </select>

                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a
                                href="{{ route($routePrefix . '.mata-pelajaran.index') }}"
                                class="btn btn-outline-secondary"
                                data-confirm="true"
                                data-confirm-message="Batalkan edit mata pelajaran? Perubahan yang belum disimpan akan hilang."
                                data-confirm-yes="Ya, Batalkan"
                                data-confirm-yes-class="btn-danger"
                            >
                                Batal
                            </a>

                            <button
                                type="submit"
                                class="btn btn-primary"
                                data-confirm="true"
                                data-confirm-message="Apakah Anda yakin ingin menyimpan perubahan data mata pelajaran ini?"
                                data-confirm-yes="Ya, Simpan Perubahan"
                                data-confirm-yes-class="btn-primary"
                            >
                                <i class="bi bi-save me-1"></i>
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>

                </div>
            </div>

        </div>
    </div>

@endsection
