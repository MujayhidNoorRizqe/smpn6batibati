{{-- penjelasan: File ini adalah halaman edit semester. --}}
{{-- penjelasan: File ini dipanggil oleh SemesterController method edit(). --}}
{{-- penjelasan: Form pada file ini dikirim ke SemesterController method update(). --}}
{{-- penjelasan: Alert validasi tidak ditulis lokal karena sudah ditampilkan global dari admin.components.alert. --}}
{{-- penjelasan: Tahun ajaran, semester, tanggal mulai, tanggal selesai, dan status wajib diisi. --}}
{{-- penjelasan: Tanggal mulai dan tanggal selesai dipilih manual sesuai kalender akademik. --}}

@extends('admin.layouts.app')

@section('title', 'Edit Semester')

@section('content')

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <h5 class="fw-bold mb-0">Edit Semester</h5>
                    <small class="text-muted">Ubah data semester.</small>
                </div>

                <div class="card-body">

                    <div class="alert alert-info border-0 shadow-sm rounded-3">
                        <i class="bi bi-info-circle me-1"></i>
                        Field bertanda <span class="text-danger">*</span> wajib diisi.
                    </div>

                    <form action="{{ route($routePrefix . '.semester.update', $semester) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label">
                                Tahun Ajaran <span class="text-danger">*</span>
                            </label>

                            <select name="tahun_ajaran_id" class="form-select @error('tahun_ajaran_id') is-invalid @enderror" required>
                                <option value="">Pilih Tahun Ajaran</option>

                                @foreach ($tahunAjarans as $tahunAjaran)
                                    <option value="{{ $tahunAjaran->id }}" {{ old('tahun_ajaran_id', $semester->tahun_ajaran_id) == $tahunAjaran->id ? 'selected' : '' }}>
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
                                <option value="ganjil" {{ old('nama_semester', $semester->nama_semester) === 'ganjil' ? 'selected' : '' }}>Ganjil</option>
                                <option value="genap" {{ old('nama_semester', $semester->nama_semester) === 'genap' ? 'selected' : '' }}>Genap</option>
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
                                value="{{ old('tanggal_mulai', $semester->tanggal_mulai ? $semester->tanggal_mulai->format('Y-m-d') : '') }}"
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
                                value="{{ old('tanggal_selesai', $semester->tanggal_selesai ? $semester->tanggal_selesai->format('Y-m-d') : '') }}"
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
                                <option value="nonaktif" {{ old('status', $semester->status) === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                                <option value="aktif" {{ old('status', $semester->status) === 'aktif' ? 'selected' : '' }}>Aktif</option>
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
                                data-confirm-message="Batalkan edit semester? Perubahan yang belum disimpan akan hilang."
                                data-confirm-yes="Ya, Batalkan"
                                data-confirm-yes-class="btn-danger"
                            >
                                Batal
                            </a>

                            <button
                                type="submit"
                                class="btn btn-primary"
                                data-confirm="true"
                                data-confirm-message="Apakah Anda yakin ingin menyimpan perubahan data semester ini?"
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
