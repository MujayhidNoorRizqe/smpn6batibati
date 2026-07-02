{{-- penjelasan: File ini adalah halaman tambah kelas. --}}
{{-- penjelasan: File ini dipanggil oleh KelasController method create(). --}}
{{-- penjelasan: Form ini dikirim ke KelasController method store(). --}}
{{-- penjelasan: Data $gurus berisi pegawai jenis guru aktif untuk pilihan wali kelas. --}}
{{-- penjelasan: Alert validasi tidak ditulis lokal karena sudah ditampilkan global dari admin.components.alert. --}}
{{-- penjelasan: Nama kelas, tingkat, wali kelas, dan status kelas wajib diisi. --}}

@extends('admin.layouts.app')

@section('title', 'Tambah Kelas')

@section('content')

    <div class="row justify-content-center">
        <div class="col-lg-8">

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <h5 class="fw-bold mb-0">Tambah Kelas</h5>
                    <small class="text-muted">Tambahkan data kelas baru.</small>
                </div>

                <div class="card-body">

                    <div class="alert alert-info border-0 shadow-sm rounded-3">
                        <i class="bi bi-info-circle me-1"></i>
                        Field bertanda <span class="text-danger">*</span> wajib diisi.
                    </div>

                    @if ($gurus->isEmpty())
                        <div class="alert alert-warning border-0 shadow-sm rounded-3">
                            <i class="bi bi-exclamation-triangle me-1"></i>
                            Belum ada guru aktif yang bisa dipilih sebagai wali kelas. Tambahkan data pegawai guru aktif terlebih dahulu.
                        </div>
                    @endif

                    <form action="{{ route($routePrefix . '.kelas.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">
                                Nama Kelas <span class="text-danger">*</span>
                            </label>

                            <input
                                type="text"
                                name="nama_kelas"
                                class="form-control @error('nama_kelas') is-invalid @enderror"
                                value="{{ old('nama_kelas') }}"
                                placeholder="Contoh: 7A"
                                required
                            >

                            @error('nama_kelas')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                Tingkat <span class="text-danger">*</span>
                            </label>

                            <select name="tingkat" class="form-select @error('tingkat') is-invalid @enderror" required>
                                <option value="">Pilih Tingkat</option>
                                <option value="7" {{ old('tingkat') === '7' ? 'selected' : '' }}>7</option>
                                <option value="8" {{ old('tingkat') === '8' ? 'selected' : '' }}>8</option>
                                <option value="9" {{ old('tingkat') === '9' ? 'selected' : '' }}>9</option>
                            </select>

                            @error('tingkat')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                Wali Kelas <span class="text-danger">*</span>
                            </label>

                            <select name="wali_kelas_id" class="form-select @error('wali_kelas_id') is-invalid @enderror" required>
                                <option value="">Pilih Wali Kelas</option>

                                @foreach ($gurus as $guru)
                                    <option value="{{ $guru->id }}" {{ old('wali_kelas_id') == $guru->id ? 'selected' : '' }}>
                                        {{ $guru->nama_pegawai }} - {{ $guru->jabatan ?? 'Guru' }}
                                    </option>
                                @endforeach
                            </select>

                            @error('wali_kelas_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror

                            <small class="text-muted">
                                Wali kelas wajib dipilih dari pegawai jenis guru yang aktif.
                            </small>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">
                                Status Kelas <span class="text-danger">*</span>
                            </label>

                            <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                <option value="">Pilih Status</option>
                                <option value="aktif" {{ old('status', 'aktif') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="nonaktif" {{ old('status') === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                            </select>

                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a
                                href="{{ route($routePrefix . '.kelas.index') }}"
                                class="btn btn-outline-secondary"
                                data-confirm="true"
                                data-confirm-message="Batalkan tambah kelas? Data yang belum disimpan akan hilang."
                                data-confirm-yes="Ya, Batalkan"
                                data-confirm-yes-class="btn-danger"
                            >
                                Batal
                            </a>

                            <button type="submit" class="btn btn-primary" {{ $gurus->isEmpty() ? 'disabled' : '' }}>
                                <i class="bi bi-save me-1"></i>
                                Simpan Kelas
                            </button>
                        </div>
                    </form>

                </div>
            </div>

        </div>
    </div>

@endsection
