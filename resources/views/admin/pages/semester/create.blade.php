{{-- penjelasan: File ini adalah halaman tambah semester. --}}
{{-- penjelasan: File ini dipanggil oleh SemesterController method create(). --}}
{{-- penjelasan: Form pada file ini dikirim ke SemesterController method store(). --}}
{{-- penjelasan: Alert validasi tidak ditulis lokal karena sudah ditampilkan global dari admin.components.alert. --}}
{{-- penjelasan: Tahun ajaran, semester, tanggal mulai, tanggal selesai, dan status wajib diisi. --}}
{{-- penjelasan: Tanggal mulai dan tanggal selesai dipilih manual sesuai kalender akademik. --}}

@extends('admin.layouts.app')

@section('title', 'Tambah Semester')

@section('content')

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <h5 class="fw-bold mb-0">Tambah Semester</h5>
                    <small class="text-muted">Tambahkan semester pada tahun ajaran.</small>
                </div>

                <div class="card-body">

                    <div class="alert alert-info border-0 shadow-sm rounded-3">
                        <i class="bi bi-info-circle me-1"></i>
                        Field bertanda <span class="text-danger">*</span> wajib diisi.
                    </div>

                    @if ($tahunAjarans->isEmpty())
                        <div class="alert alert-warning border-0 shadow-sm rounded-3">
                            <i class="bi bi-exclamation-triangle me-1"></i>
                            Belum ada tahun ajaran. Tambahkan data tahun ajaran terlebih dahulu.
                        </div>
                    @endif

                    <form action="{{ route($routePrefix . '.semester.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">
                                Tahun Ajaran <span class="text-danger">*</span>
                            </label>

                            <select name="tahun_ajaran_id" class="form-select @error('tahun_ajaran_id') is-invalid @enderror" required>
                                <option value="">Pilih Tahun Ajaran</option>

                                @foreach ($tahunAjarans as $tahunAjaran)
                                    <option value="{{ $tahunAjaran->id }}" {{ old('tahun_ajaran_id') == $tahunAjaran->id ? 'selected' : '' }}>
                                        {{ $tahunAjaran->nama_tahun_ajaran }} - {{ ucfirst($tahunAjaran->status) }}
                                    </option>
                                @endforeach
                            </select>

                            @error('tahun_ajaran_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror

                            <small class="text-muted">
                                Semester aktif harus berada pada tahun ajaran yang aktif.
                            </small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                Semester <span class="text-danger">*</span>
                            </label>

                            <select name="nama_semester" class="form-select @error('nama_semester') is-invalid @enderror" required>
                                <option value="">Pilih Semester</option>
                                <option value="ganjil" {{ old('nama_semester') === 'ganjil' ? 'selected' : '' }}>Ganjil</option>
                                <option value="genap" {{ old('nama_semester') === 'genap' ? 'selected' : '' }}>Genap</option>
                            </select>

                            @error('nama_semester')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                Tanggal Mulai <span class="text-danger">*</span>
                            </label>

                            <input
                                type="date"
                                name="tanggal_mulai"
                                class="form-control @error('tanggal_mulai') is-invalid @enderror"
                                value="{{ old('tanggal_mulai') }}"
                                required
                            >

                            @error('tanggal_mulai')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror

                            <small class="text-muted">
                                Tanggal mulai wajib dipilih manual sesuai kalender akademik.
                            </small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                Tanggal Selesai <span class="text-danger">*</span>
                            </label>

                            <input
                                type="date"
                                name="tanggal_selesai"
                                class="form-control @error('tanggal_selesai') is-invalid @enderror"
                                value="{{ old('tanggal_selesai') }}"
                                required
                            >

                            @error('tanggal_selesai')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror

                            <small class="text-muted">
                                Tanggal selesai wajib diisi dan tidak boleh lebih kecil dari tanggal mulai.
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
                                Jika dibuat aktif, semester aktif lainnya otomatis menjadi nonaktif.
                            </small>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a
                                href="{{ route($routePrefix . '.semester.index') }}"
                                class="btn btn-outline-secondary"
                                data-confirm="true"
                                data-confirm-message="Batalkan tambah semester? Data yang belum disimpan akan hilang."
                                data-confirm-yes="Ya, Batalkan"
                                data-confirm-yes-class="btn-danger"
                            >
                                Batal
                            </a>

                            <button
                                type="submit"
                                class="btn btn-primary"
                                data-confirm="true"
                                data-confirm-message="Apakah Anda yakin ingin menyimpan data semester ini?"
                                data-confirm-yes="Ya, Simpan"
                                data-confirm-yes-class="btn-primary"
                                {{ $tahunAjarans->isEmpty() ? 'disabled' : '' }}
                            >
                                <i class="bi bi-save me-1"></i>
                                Simpan Semester
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection
