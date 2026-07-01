{{-- penjelasan: File ini adalah halaman edit jadwal pelajaran. --}}
{{-- penjelasan: File ini dipanggil oleh JadwalPelajaranController method edit(). --}}

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
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            @foreach ($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    @endif

                    <form action="{{ route($routePrefix . '.jadwal-pelajaran.update', $jadwalPelajaran) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label">Tahun Ajaran</label>
                            <select name="tahun_ajaran_id" class="form-select" required>
                                @foreach ($tahunAjarans as $tahunAjaran)
                                    <option value="{{ $tahunAjaran->id }}" {{ old('tahun_ajaran_id', $jadwalPelajaran->tahun_ajaran_id) == $tahunAjaran->id ? 'selected' : '' }}>
                                        {{ $tahunAjaran->nama_tahun_ajaran }} - {{ ucfirst($tahunAjaran->status) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Semester</label>
                            <select name="semester_id" class="form-select" required>
                                @foreach ($semesters as $semester)
                                    <option value="{{ $semester->id }}" {{ old('semester_id', $jadwalPelajaran->semester_id) == $semester->id ? 'selected' : '' }}>
                                        {{ $semester->nama_semester_label }} - {{ $semester->tahunAjaran?->nama_tahun_ajaran ?? '-' }} - {{ ucfirst($semester->status) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Kelas</label>
                            <select name="kelas_id" class="form-select" required>
                                @foreach ($kelasList as $kelas)
                                    <option value="{{ $kelas->id }}" {{ old('kelas_id', $jadwalPelajaran->kelas_id) == $kelas->id ? 'selected' : '' }}>
                                        {{ $kelas->nama_kelas }} - Tingkat {{ $kelas->tingkat }} - {{ ucfirst($kelas->status) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Mata Pelajaran</label>
                            <select name="mata_pelajaran_id" class="form-select" required>
                                @foreach ($mataPelajarans as $mataPelajaran)
                                    <option value="{{ $mataPelajaran->id }}" {{ old('mata_pelajaran_id', $jadwalPelajaran->mata_pelajaran_id) == $mataPelajaran->id ? 'selected' : '' }}>
                                        {{ $mataPelajaran->kode_mapel }} - {{ $mataPelajaran->nama_mapel }} - {{ ucfirst($mataPelajaran->status) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Guru Pengajar</label>
                            <select name="guru_id" class="form-select" required>
                                @foreach ($gurus as $guru)
                                    <option value="{{ $guru->id }}" {{ old('guru_id', $jadwalPelajaran->guru_id) == $guru->id ? 'selected' : '' }}>
                                        {{ $guru->nama_pegawai }} - {{ ucfirst($guru->status) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Hari</label>
                            <select name="hari" class="form-select" required>
                                <option value="senin" {{ old('hari', $jadwalPelajaran->hari) === 'senin' ? 'selected' : '' }}>Senin</option>
                                <option value="selasa" {{ old('hari', $jadwalPelajaran->hari) === 'selasa' ? 'selected' : '' }}>Selasa</option>
                                <option value="rabu" {{ old('hari', $jadwalPelajaran->hari) === 'rabu' ? 'selected' : '' }}>Rabu</option>
                                <option value="kamis" {{ old('hari', $jadwalPelajaran->hari) === 'kamis' ? 'selected' : '' }}>Kamis</option>
                                <option value="jumat" {{ old('hari', $jadwalPelajaran->hari) === 'jumat' ? 'selected' : '' }}>Jumat</option>
                                <option value="sabtu" {{ old('hari', $jadwalPelajaran->hari) === 'sabtu' ? 'selected' : '' }}>Sabtu</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Jam Mulai</label>
                            <input
                                type="time"
                                name="jam_mulai"
                                class="form-control"
                                value="{{ old('jam_mulai', $jadwalPelajaran->jam_mulai_format) }}"
                                required
                            >
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Jam Selesai</label>
                            <input
                                type="time"
                                name="jam_selesai"
                                class="form-control"
                                value="{{ old('jam_selesai', $jadwalPelajaran->jam_selesai_format) }}"
                                required
                            >
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select" required>
                                <option value="aktif" {{ old('status', $jadwalPelajaran->status) === 'aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="nonaktif" {{ old('status', $jadwalPelajaran->status) === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                            </select>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route($routePrefix . '.jadwal-pelajaran.index') }}" class="btn btn-outline-secondary">
                                Batal
                            </a>

                            <button type="submit" class="btn btn-primary">
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection
