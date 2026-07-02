{{-- penjelasan: File ini adalah halaman edit jadwal pelajaran. --}}
{{-- penjelasan: File ini dipanggil oleh JadwalPelajaranController method edit(). --}}
{{-- penjelasan: Form pada file ini dikirim ke JadwalPelajaranController method update(). --}}
{{-- penjelasan: Alert validasi tidak ditulis lokal karena sudah ditampilkan global dari admin.components.alert. --}}
{{-- penjelasan: Semua field pada jadwal pelajaran wajib diisi. --}}
{{-- penjelasan: Jadwal aktif akan divalidasi agar memakai data aktif dan tidak bentrok. --}}

@extends('admin.layouts.app')

@section('title', 'Edit Jadwal Pelajaran')

@section('content')

    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <h5 class="fw-bold mb-0">Edit Jadwal Pelajaran</h5>
                    <small class="text-muted">Ubah data jadwal pelajaran.</small>
                </div>

                <div class="card-body">

                    <div class="alert alert-info border-0 shadow-sm rounded-3">
                        <i class="bi bi-info-circle me-1"></i>
                        Field bertanda <span class="text-danger">*</span> wajib diisi.
                    </div>

                    <form action="{{ route($routePrefix . '.jadwal-pelajaran.update', $jadwalPelajaran) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label">
                                Tahun Ajaran <span class="text-danger">*</span>
                            </label>

                            <select name="tahun_ajaran_id" class="form-select @error('tahun_ajaran_id') is-invalid @enderror" required>
                                <option value="">Pilih Tahun Ajaran</option>

                                @foreach ($tahunAjarans as $tahunAjaran)
                                    <option value="{{ $tahunAjaran->id }}" {{ old('tahun_ajaran_id', $jadwalPelajaran->tahun_ajaran_id) == $tahunAjaran->id ? 'selected' : '' }}>
                                        {{ $tahunAjaran->nama_tahun_ajaran }} - {{ ucfirst($tahunAjaran->status) }}
                                    </option>
                                @endforeach
                            </select>

                            @error('tahun_ajaran_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror

                            <small class="text-muted">
                                Jadwal aktif hanya boleh memakai tahun ajaran aktif.
                            </small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                Semester <span class="text-danger">*</span>
                            </label>

                            <select name="semester_id" class="form-select @error('semester_id') is-invalid @enderror" required>
                                <option value="">Pilih Semester</option>

                                @foreach ($semesters as $semester)
                                    <option value="{{ $semester->id }}" {{ old('semester_id', $jadwalPelajaran->semester_id) == $semester->id ? 'selected' : '' }}>
                                        {{ $semester->nama_semester_label }} - {{ $semester->tahunAjaran?->nama_tahun_ajaran ?? '-' }} - {{ ucfirst($semester->status) }}
                                    </option>
                                @endforeach
                            </select>

                            @error('semester_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror

                            <small class="text-muted">
                                Semester harus sesuai dengan tahun ajaran yang dipilih.
                            </small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                Kelas <span class="text-danger">*</span>
                            </label>

                            <select name="kelas_id" class="form-select @error('kelas_id') is-invalid @enderror" required>
                                <option value="">Pilih Kelas</option>

                                @foreach ($kelasList as $kelas)
                                    <option value="{{ $kelas->id }}" {{ old('kelas_id', $jadwalPelajaran->kelas_id) == $kelas->id ? 'selected' : '' }}>
                                        {{ $kelas->nama_kelas }} - Tingkat {{ $kelas->tingkat }} - {{ ucfirst($kelas->status) }}
                                    </option>
                                @endforeach
                            </select>

                            @error('kelas_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                Mata Pelajaran <span class="text-danger">*</span>
                            </label>

                            <select name="mata_pelajaran_id" class="form-select @error('mata_pelajaran_id') is-invalid @enderror" required>
                                <option value="">Pilih Mata Pelajaran</option>

                                @foreach ($mataPelajarans as $mataPelajaran)
                                    <option value="{{ $mataPelajaran->id }}" {{ old('mata_pelajaran_id', $jadwalPelajaran->mata_pelajaran_id) == $mataPelajaran->id ? 'selected' : '' }}>
                                        {{ $mataPelajaran->kode_mapel }} - {{ $mataPelajaran->nama_mapel }} - {{ ucfirst($mataPelajaran->status) }}
                                    </option>
                                @endforeach
                            </select>

                            @error('mata_pelajaran_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                Guru Pengajar <span class="text-danger">*</span>
                            </label>

                            <select name="guru_id" class="form-select @error('guru_id') is-invalid @enderror" required>
                                <option value="">Pilih Guru</option>

                                @foreach ($gurus as $guru)
                                    <option value="{{ $guru->id }}" {{ old('guru_id', $jadwalPelajaran->guru_id) == $guru->id ? 'selected' : '' }}>
                                        {{ $guru->nama_pegawai }} - {{ ucfirst($guru->status) }}
                                    </option>
                                @endforeach
                            </select>

                            @error('guru_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror

                            <small class="text-muted">
                                Jadwal aktif hanya boleh memakai pegawai jenis guru yang aktif.
                            </small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                Hari <span class="text-danger">*</span>
                            </label>

                            <select name="hari" class="form-select @error('hari') is-invalid @enderror" required>
                                <option value="">Pilih Hari</option>
                                <option value="senin" {{ old('hari', $jadwalPelajaran->hari) === 'senin' ? 'selected' : '' }}>Senin</option>
                                <option value="selasa" {{ old('hari', $jadwalPelajaran->hari) === 'selasa' ? 'selected' : '' }}>Selasa</option>
                                <option value="rabu" {{ old('hari', $jadwalPelajaran->hari) === 'rabu' ? 'selected' : '' }}>Rabu</option>
                                <option value="kamis" {{ old('hari', $jadwalPelajaran->hari) === 'kamis' ? 'selected' : '' }}>Kamis</option>
                                <option value="jumat" {{ old('hari', $jadwalPelajaran->hari) === 'jumat' ? 'selected' : '' }}>Jumat</option>
                                <option value="sabtu" {{ old('hari', $jadwalPelajaran->hari) === 'sabtu' ? 'selected' : '' }}>Sabtu</option>
                            </select>

                            @error('hari')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    Jam Mulai <span class="text-danger">*</span>
                                </label>

                                <input
                                    type="time"
                                    name="jam_mulai"
                                    class="form-control @error('jam_mulai') is-invalid @enderror"
                                    value="{{ old('jam_mulai', $jadwalPelajaran->jam_mulai_format) }}"
                                    required
                                >

                                @error('jam_mulai')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    Jam Selesai <span class="text-danger">*</span>
                                </label>

                                <input
                                    type="time"
                                    name="jam_selesai"
                                    class="form-control @error('jam_selesai') is-invalid @enderror"
                                    value="{{ old('jam_selesai', $jadwalPelajaran->jam_selesai_format) }}"
                                    required
                                >

                                @error('jam_selesai')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror

                                <small class="text-muted">
                                    Jam selesai harus lebih besar dari jam mulai.
                                </small>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">
                                Status <span class="text-danger">*</span>
                            </label>

                            <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                <option value="">Pilih Status</option>
                                <option value="aktif" {{ old('status', $jadwalPelajaran->status) === 'aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="nonaktif" {{ old('status', $jadwalPelajaran->status) === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                            </select>

                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror

                            <small class="text-muted">
                                Jadwal aktif akan dicek agar tidak bentrok dengan jadwal kelas dan guru.
                            </small>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a
                                href="{{ route($routePrefix . '.jadwal-pelajaran.index') }}"
                                class="btn btn-outline-secondary"
                                data-confirm="true"
                                data-confirm-message="Batalkan edit jadwal pelajaran? Perubahan yang belum disimpan akan hilang."
                                data-confirm-yes="Ya, Batalkan"
                                data-confirm-yes-class="btn-danger"
                            >
                                Batal
                            </a>

                            <button
                                type="submit"
                                class="btn btn-primary"
                                data-confirm="true"
                                data-confirm-message="Apakah Anda yakin ingin menyimpan perubahan data jadwal pelajaran ini?"
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
