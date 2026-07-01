{{-- penjelasan: File ini adalah halaman tambah jadwal pelajaran. --}}
{{-- penjelasan: File ini dipanggil oleh JadwalPelajaranController method create(). --}}

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
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            @foreach ($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    @endif

                    <form action="{{ route($routePrefix . '.jadwal-pelajaran.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Tahun Ajaran</label>
                            <select name="tahun_ajaran_id" class="form-select" required>
                                <option value="">Pilih Tahun Ajaran Aktif</option>
                                @foreach ($tahunAjarans as $tahunAjaran)
                                    <option value="{{ $tahunAjaran->id }}" {{ old('tahun_ajaran_id') == $tahunAjaran->id ? 'selected' : '' }}>
                                        {{ $tahunAjaran->nama_tahun_ajaran }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Jadwal aktif hanya boleh memakai tahun ajaran aktif.</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Semester</label>
                            <select name="semester_id" class="form-select" required>
                                <option value="">Pilih Semester Aktif</option>
                                @foreach ($semesters as $semester)
                                    <option value="{{ $semester->id }}" {{ old('semester_id') == $semester->id ? 'selected' : '' }}>
                                        {{ $semester->nama_semester_label }} - {{ $semester->tahunAjaran?->nama_tahun_ajaran ?? '-' }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Semester harus sesuai dengan tahun ajaran yang dipilih.</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Kelas</label>
                            <select name="kelas_id" class="form-select" required>
                                <option value="">Pilih Kelas</option>
                                @foreach ($kelasList as $kelas)
                                    <option value="{{ $kelas->id }}" {{ old('kelas_id') == $kelas->id ? 'selected' : '' }}>
                                        {{ $kelas->nama_kelas }} - Tingkat {{ $kelas->tingkat }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Mata Pelajaran</label>
                            <select name="mata_pelajaran_id" class="form-select" required>
                                <option value="">Pilih Mata Pelajaran</option>
                                @foreach ($mataPelajarans as $mataPelajaran)
                                    <option value="{{ $mataPelajaran->id }}" {{ old('mata_pelajaran_id') == $mataPelajaran->id ? 'selected' : '' }}>
                                        {{ $mataPelajaran->kode_mapel }} - {{ $mataPelajaran->nama_mapel }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Guru Pengajar</label>
                            <select name="guru_id" class="form-select" required>
                                <option value="">Pilih Guru</option>
                                @foreach ($gurus as $guru)
                                    <option value="{{ $guru->id }}" {{ old('guru_id') == $guru->id ? 'selected' : '' }}>
                                        {{ $guru->nama_pegawai }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Guru yang tampil hanya pegawai jenis guru dan status aktif.</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Hari</label>
                            <select name="hari" class="form-select" required>
                                <option value="">Pilih Hari</option>
                                <option value="senin" {{ old('hari') === 'senin' ? 'selected' : '' }}>Senin</option>
                                <option value="selasa" {{ old('hari') === 'selasa' ? 'selected' : '' }}>Selasa</option>
                                <option value="rabu" {{ old('hari') === 'rabu' ? 'selected' : '' }}>Rabu</option>
                                <option value="kamis" {{ old('hari') === 'kamis' ? 'selected' : '' }}>Kamis</option>
                                <option value="jumat" {{ old('hari') === 'jumat' ? 'selected' : '' }}>Jumat</option>
                                <option value="sabtu" {{ old('hari') === 'sabtu' ? 'selected' : '' }}>Sabtu</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Jam Mulai</label>
                            <input type="time" name="jam_mulai" class="form-control" value="{{ old('jam_mulai') }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Jam Selesai</label>
                            <input type="time" name="jam_selesai" class="form-control" value="{{ old('jam_selesai') }}" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select" required>
                                <option value="aktif" {{ old('status') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="nonaktif" {{ old('status') === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                            </select>
                            <small class="text-muted">Jadwal aktif akan dicek agar tidak bentrok dengan jadwal kelas dan guru.</small>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route($routePrefix . '.jadwal-pelajaran.index') }}" class="btn btn-outline-secondary">
                                Batal
                            </a>

                            <button type="submit" class="btn btn-primary">
                                Simpan Jadwal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection
