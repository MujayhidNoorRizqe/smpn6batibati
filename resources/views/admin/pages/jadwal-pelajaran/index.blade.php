{{-- penjelasan: File ini adalah halaman daftar jadwal pelajaran. --}}
{{-- penjelasan: File ini dipanggil oleh JadwalPelajaranController method index(). --}}
{{-- penjelasan: Halaman ini bisa diakses oleh Super Admin dan Admin. --}}

@extends('admin.layouts.app')

@section('title', 'Jadwal Pelajaran')

@section('content')

    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                    <div>
                        <h4 class="fw-bold mb-1">Jadwal Pelajaran</h4>
                        <p class="text-muted mb-0">
                            Kelola jadwal pelajaran berdasarkan kelas, guru, mata pelajaran, tahun ajaran, dan semester.
                        </p>
                    </div>

                    <a href="{{ route($routePrefix . '.jadwal-pelajaran.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i>
                        Tambah Jadwal
                    </a>
                </div>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route($routePrefix . '.jadwal-pelajaran.index') }}" method="GET" class="row g-3">

                <div class="col-md-3">
                    <label class="form-label">Tahun Ajaran</label>
                    <select name="tahun_ajaran_id" class="form-select">
                        <option value="">Semua Tahun Ajaran</option>
                        @foreach ($tahunAjarans as $tahunAjaran)
                            <option value="{{ $tahunAjaran->id }}" {{ request('tahun_ajaran_id') == $tahunAjaran->id ? 'selected' : '' }}>
                                {{ $tahunAjaran->nama_tahun_ajaran }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Semester</label>
                    <select name="semester_id" class="form-select">
                        <option value="">Semua Semester</option>
                        @foreach ($semesters as $semester)
                            <option value="{{ $semester->id }}" {{ request('semester_id') == $semester->id ? 'selected' : '' }}>
                                {{ $semester->nama_semester_label }} - {{ $semester->tahunAjaran?->nama_tahun_ajaran ?? '-' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Kelas</label>
                    <select name="kelas_id" class="form-select">
                        <option value="">Semua Kelas</option>
                        @foreach ($kelasList as $kelas)
                            <option value="{{ $kelas->id }}" {{ request('kelas_id') == $kelas->id ? 'selected' : '' }}>
                                {{ $kelas->nama_kelas }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Guru</label>
                    <select name="guru_id" class="form-select">
                        <option value="">Semua Guru</option>
                        @foreach ($gurus as $guru)
                            <option value="{{ $guru->id }}" {{ request('guru_id') == $guru->id ? 'selected' : '' }}>
                                {{ $guru->nama_pegawai }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Hari</label>
                    <select name="hari" class="form-select">
                        <option value="">Semua Hari</option>
                        <option value="senin" {{ request('hari') === 'senin' ? 'selected' : '' }}>Senin</option>
                        <option value="selasa" {{ request('hari') === 'selasa' ? 'selected' : '' }}>Selasa</option>
                        <option value="rabu" {{ request('hari') === 'rabu' ? 'selected' : '' }}>Rabu</option>
                        <option value="kamis" {{ request('hari') === 'kamis' ? 'selected' : '' }}>Kamis</option>
                        <option value="jumat" {{ request('hari') === 'jumat' ? 'selected' : '' }}>Jumat</option>
                        <option value="sabtu" {{ request('hari') === 'sabtu' ? 'selected' : '' }}>Sabtu</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="aktif" {{ request('status') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="nonaktif" {{ request('status') === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>

                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search me-1"></i>
                        Filter
                    </button>
                </div>

                <div class="col-md-3 d-flex align-items-end">
                    <a href="{{ route($routePrefix . '.jadwal-pelajaran.index') }}" class="btn btn-outline-secondary w-100">
                        Reset
                    </a>
                </div>

            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
            <div>
                <h6 class="fw-bold mb-0">Daftar Jadwal Pelajaran</h6>
                <small class="text-muted">Jadwal aktif dan nonaktif yang terdaftar</small>
            </div>

            <span class="badge bg-primary-subtle text-primary">
                {{ $jadwalPelajarans->count() }} data tampil
            </span>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle admin-table">
                    <thead class="table-light">
                        <tr>
                            <th>Hari & Jam</th>
                            <th>Kelas</th>
                            <th>Mata Pelajaran</th>
                            <th>Guru</th>
                            <th>Periode</th>
                            <th>Status</th>
                            <th class="text-end table-action-column">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($jadwalPelajarans as $jadwalPelajaran)
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $jadwalPelajaran->hari_label }}</div>
                                    <small class="text-muted">{{ $jadwalPelajaran->jam_pelajaran }}</small>
                                </td>

                                <td>
                                    <span class="badge bg-primary-subtle text-primary">
                                        {{ $jadwalPelajaran->kelas?->nama_kelas ?? '-' }}
                                    </span>
                                </td>

                                <td>
                                    <div class="fw-semibold">{{ $jadwalPelajaran->mataPelajaran?->nama_mapel ?? '-' }}</div>
                                    <small class="text-muted">{{ $jadwalPelajaran->mataPelajaran?->kode_mapel ?? '-' }}</small>
                                </td>

                                <td>{{ $jadwalPelajaran->guru?->nama_pegawai ?? '-' }}</td>

                                <td>
                                    <div>{{ $jadwalPelajaran->tahunAjaran?->nama_tahun_ajaran ?? '-' }}</div>
                                    <small class="text-muted">{{ $jadwalPelajaran->semester?->nama_semester_label ?? '-' }}</small>
                                </td>

                                <td>
                                    <span class="badge {{ $jadwalPelajaran->status === 'aktif' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }}">
                                        {{ ucfirst($jadwalPelajaran->status) }}
                                    </span>
                                </td>

                                <td class="text-end">
                                    <div class="action-buttons">
                                        <a href="{{ route($routePrefix . '.jadwal-pelajaran.show', $jadwalPelajaran) }}" class="btn btn-sm btn-outline-secondary action-btn">
                                            <i class="bi bi-eye"></i>
                                            <span>Detail</span>
                                        </a>

                                        <a href="{{ route($routePrefix . '.jadwal-pelajaran.edit', $jadwalPelajaran) }}" class="btn btn-sm btn-outline-primary action-btn">
                                            <i class="bi bi-pencil-square"></i>
                                            <span>Edit</span>
                                        </a>

                                        <form action="{{ route($routePrefix . '.jadwal-pelajaran.toggle-status', $jadwalPelajaran) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')

                                            <button
                                                type="submit"
                                                class="btn btn-sm action-btn {{ $jadwalPelajaran->status === 'aktif' ? 'btn-outline-danger' : 'btn-outline-success' }}"
                                                onclick="return confirm('Yakin ingin mengubah status jadwal ini?')"
                                            >
                                                @if ($jadwalPelajaran->status === 'aktif')
                                                    <i class="bi bi-x-circle"></i>
                                                    <span>Nonaktif</span>
                                                @else
                                                    <i class="bi bi-check-circle"></i>
                                                    <span>Aktifkan</span>
                                                @endif
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    Data jadwal pelajaran belum tersedia.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $jadwalPelajarans->links() }}
            </div>
        </div>
    </div>

@endsection
