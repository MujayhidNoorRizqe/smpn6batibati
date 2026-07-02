{{-- penjelasan: File ini adalah halaman detail jadwal pelajaran. --}}
{{-- penjelasan: File ini dipanggil oleh JadwalPelajaranController method show(). --}}
{{-- penjelasan: File ini menampilkan informasi lengkap jadwal pelajaran. --}}

@extends('admin.layouts.app')

@section('title', 'Detail Jadwal Pelajaran')

@section('content')

    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
                    <div>
                        <h5 class="fw-bold mb-0">Detail Jadwal Pelajaran</h5>
                        <small class="text-muted">Informasi lengkap jadwal mengajar</small>
                    </div>

                    @if ($jadwalPelajaran->status === 'aktif')
                        <span class="badge bg-success-subtle text-success">
                            Aktif
                        </span>
                    @else
                        <span class="badge bg-danger-subtle text-danger">
                            Nonaktif
                        </span>
                    @endif
                </div>

                <div class="card-body">

                    <div class="row mb-3">
                        <div class="col-md-4 text-muted">Tahun Ajaran</div>
                        <div class="col-md-8 fw-semibold">
                            {{ $jadwalPelajaran->tahunAjaran?->nama_tahun_ajaran ?? '-' }}
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4 text-muted">Semester</div>
                        <div class="col-md-8 fw-semibold">
                            {{ $jadwalPelajaran->semester?->nama_semester_label ?? '-' }}
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4 text-muted">Kelas</div>
                        <div class="col-md-8 fw-semibold">
                            {{ $jadwalPelajaran->kelas?->nama_kelas ?? '-' }}
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4 text-muted">Mata Pelajaran</div>
                        <div class="col-md-8 fw-semibold">
                            {{ $jadwalPelajaran->mataPelajaran?->kode_mapel ?? '-' }}
                            -
                            {{ $jadwalPelajaran->mataPelajaran?->nama_mapel ?? '-' }}
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4 text-muted">Guru Pengajar</div>
                        <div class="col-md-8 fw-semibold">
                            {{ $jadwalPelajaran->guru?->nama_pegawai ?? '-' }}
                        </div>
                    </div>

                    <hr>

                    <div class="row mb-3">
                        <div class="col-md-4 text-muted">Hari</div>
                        <div class="col-md-8 fw-semibold">
                            {{ $jadwalPelajaran->hari_label }}
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4 text-muted">Jam Pelajaran</div>
                        <div class="col-md-8 fw-semibold">
                            {{ $jadwalPelajaran->jam_pelajaran }}
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-4 text-muted">Dibuat / Diperbarui</div>
                        <div class="col-md-8 fw-semibold">
                            {{ $jadwalPelajaran->created_at ? $jadwalPelajaran->created_at->format('d-m-Y H:i') : '-' }}
                            /
                            {{ $jadwalPelajaran->updated_at ? $jadwalPelajaran->updated_at->format('d-m-Y H:i') : '-' }}
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route($routePrefix . '.jadwal-pelajaran.index') }}" class="btn btn-outline-secondary">
                            Kembali
                        </a>

                        <a href="{{ route($routePrefix . '.jadwal-pelajaran.edit', $jadwalPelajaran) }}" class="btn btn-primary">
                            <i class="bi bi-pencil-square me-1"></i>
                            Edit Jadwal
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>

@endsection
