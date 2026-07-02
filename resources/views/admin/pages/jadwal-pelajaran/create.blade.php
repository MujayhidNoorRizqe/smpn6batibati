{{-- penjelasan: File ini adalah halaman tambah jadwal pelajaran. --}}
{{-- penjelasan: File ini dipanggil oleh JadwalPelajaranController method create(). --}}
{{-- penjelasan: Form pada file ini dikirim ke JadwalPelajaranController method store(). --}}
{{-- penjelasan: Alert validasi tidak ditulis lokal karena sudah ditampilkan global dari admin.components.alert. --}}
{{-- penjelasan: Semua field pada jadwal pelajaran wajib diisi. --}}
{{-- penjelasan: Jadwal aktif akan divalidasi agar memakai data aktif dan tidak bentrok. --}}

@extends('admin.layouts.app')

@section('title', 'Tambah Jadwal Pelajaran')

@section('content')

    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <h5 class="fw-bold mb-0">Tambah Jadwal Pelajaran</h5>
                    <small class="text-muted">Tambahkan jadwal pelajaran untuk kelas dan guru.</small>
                </div>

                <div class="card-body">

                    <div class="alert alert-info border-0 shadow-sm rounded-3">
                        <i class="bi bi-info-circle me-1"></i>
                        Field bertanda <span class="text-danger">*</span> wajib diisi.
                    </div>

                    @if ($tahunAjarans->isEmpty())
                        <div class="alert alert-warning border-0 shadow-sm rounded-3">
                            <i class="bi bi-exclamation-triangle me-1"></i>
                            Belum ada tahun ajaran aktif. Aktifkan tahun ajaran terlebih dahulu.
                        </div>
                    @endif

                    @if ($semesters->isEmpty())
                        <div class="alert alert-warning border-0 shadow-sm rounded-3">
                            <i class="bi bi-exclamation-triangle me-1"></i>
                            Belum ada semester aktif. Aktifkan semester terlebih dahulu.
                        </div>
                    @endif

                    @if ($kelasList->isEmpty())
                        <div class="alert alert-warning border-0 shadow-sm rounded-3">
                            <i class="bi bi-exclamation-triangle me-1"></i>
                            Belum ada kelas aktif. Tambahkan atau aktifkan data kelas terlebih dahulu.
                        </div>
                    @endif

                    @if ($mataPelajarans->isEmpty())
                        <div class="alert alert-warning border-0 shadow-sm rounded-3">
                            <i class="bi bi-exclamation-triangle me-1"></i>
                            Belum ada mata pelajaran aktif. Tambahkan atau aktifkan data mata pelajaran terlebih dahulu.
                        </div>
                    @endif

                    @if ($gurus->isEmpty())
                        <div class="alert alert-warning border-0 shadow-sm rounded-3">
                            <i class="bi bi-exclamation-triangle me-1"></i>
                            Belum ada guru aktif. Tambahkan atau aktifkan data pegawai guru terlebih dahulu.
                        </div>
                    @endif

                    <form action="{{ route($routePrefix . '.jadwal-pelajaran.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">
                                Tahun Ajaran <span class="text-danger">*</span>
                            </label>

                            <select name="tahun_ajaran_id" class="form-select @error('tahun_ajaran_id') is-invalid @enderror" required>
                                <option value="">Pilih Tahun Ajaran Aktif</option>

                                @foreach ($tahunAjarans as $tahunAjaran)
                                    <option value="{{ $tahunAjaran->id }}" {{ old('tahun_ajaran_id') == $tahunAjaran->id ? 'selected' : '' }}>
                                        {{ $tahunAjaran->nama_tahun_ajaran }}
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
                                <option value="">Pilih Semester Aktif</option>

                                @foreach ($semesters as $semester)
                                    <option value="{{ $semester->id }}" {{ old('semester_id') == $semester->id ? 'selected' : '' }}>
                                        {{ $semester->nama_semester_label }} - {{ $semester->tahunAjaran?->nama_tahun_ajaran ?? '-' }}
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
                                    <option value="{{ $kelas->id }}" {{ old('kelas_id') == $kelas->id ? 'selected' : '' }}>
                                        {{ $kelas->nama_kelas }} - Tingkat {{ $kelas->tingkat }}
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
                                    <option value="{{ $mataPelajaran->id }}" {{ old('mata_pelajaran_id') == $mataPelajaran->id ? 'selected' : '' }}>
                                        {{ $mataPelajaran->kode_mapel }} - {{ $mataPelajaran->nama_mapel }}
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
                                    <option value="{{ $guru->id }}" {{ old('guru_id') == $guru->id ? 'selected' : '' }}>
                                        {{ $guru->nama_pegawai }}
                                    </option>
                                @endforeach
                            </select>

                            @error('guru_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror

                            <small class="text-muted">
                                Guru yang tampil hanya pegawai jenis guru dan status aktif.
                            </small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                Hari <span class="text-danger">*</span>
                            </label>

                            <select name="hari" class="form-select @error('hari') is-invalid @enderror" required>
                                <option value="">Pilih Hari</option>
                                <option value="senin" {{ old('hari') === 'senin' ? 'selected' : '' }}>Senin</option>
                                <option value="selasa" {{ old('hari') === 'selasa' ? 'selected' : '' }}>Selasa</option>
                                <option value="rabu" {{ old('hari') === 'rabu' ? 'selected' : '' }}>Rabu</option>
                                <option value="kamis" {{ old('hari') === 'kamis' ? 'selected' : '' }}>Kamis</option>
                                <option value="jumat" {{ old('hari') === 'jumat' ? 'selected' : '' }}>Jumat</option>
                                <option value="sabtu" {{ old('hari') === 'sabtu' ? 'selected' : '' }}>Sabtu</option>
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
                                    value="{{ old('jam_mulai') }}"
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
                                    value="{{ old('jam_selesai') }}"
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
                                <option value="aktif" {{ old('status', 'aktif') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="nonaktif" {{ old('status') === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
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
                                data-confirm-message="Batalkan tambah jadwal pelajaran? Data yang belum disimpan akan hilang."
                                data-confirm-yes="Ya, Batalkan"
                                data-confirm-yes-class="btn-danger"
                            >
                                Batal
                            </a>

                            <button
                                type="submit"
                                class="btn btn-primary"
                                data-confirm="true"
                                data-confirm-message="Apakah Anda yakin ingin menyimpan data jadwal pelajaran ini?"
                                data-confirm-yes="Ya, Simpan"
                                data-confirm-yes-class="btn-primary"
                                {{ $tahunAjarans->isEmpty() || $semesters->isEmpty() || $kelasList->isEmpty() || $mataPelajarans->isEmpty() || $gurus->isEmpty() ? 'disabled' : '' }}
                            >
                                <i class="bi bi-save me-1"></i>
                                Simpan Jadwal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection
