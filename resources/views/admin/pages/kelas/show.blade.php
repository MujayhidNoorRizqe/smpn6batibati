{{-- penjelasan: File ini adalah halaman detail kelas. --}}
{{-- penjelasan: File ini dipanggil oleh KelasController method show(). --}}
{{-- penjelasan: Halaman ini menampilkan nama kelas, tingkat, wali kelas, total siswa, status, dan informasi dasar kelas. --}}

@extends('admin.layouts.app')

@section('title', 'Detail Kelas')

@section('content')

    <div class="row justify-content-center">
        <div class="col-lg-8">

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <h5 class="fw-bold mb-0">Detail Kelas</h5>
                    <small class="text-muted">Informasi lengkap data kelas</small>
                </div>

                <div class="card-body">

                    <div class="row mb-3">
                        <div class="col-md-4 text-muted">Nama Kelas</div>
                        <div class="col-md-8 fw-semibold">{{ $kelas->nama_kelas }}</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4 text-muted">Tingkat</div>
                        <div class="col-md-8 fw-semibold">Tingkat {{ $kelas->tingkat }}</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4 text-muted">Wali Kelas</div>
                        <div class="col-md-8 fw-semibold">
                            @if ($kelas->waliKelas)
                                {{ $kelas->waliKelas->nama_pegawai }}
                            @else
                                <span class="text-warning">Belum ditentukan</span>
                            @endif
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4 text-muted">Jumlah Siswa</div>
                        <div class="col-md-8 fw-semibold">
                            {{-- penjelasan: total_siswa berasal dari loadCount relasi murids di KelasController method show(). --}}
                            <span class="badge bg-info-subtle text-info">
                                {{ $kelas->total_siswa ?? 0 }} siswa
                            </span>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4 text-muted">Status Kelas</div>
                        <div class="col-md-8">
                            @if ($kelas->status === 'aktif')
                                <span class="badge bg-success-subtle text-success">
                                    Aktif
                                </span>
                            @else
                                <span class="badge bg-danger-subtle text-danger">
                                    Nonaktif
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-4 text-muted">Dibuat / Diperbarui</div>
                        <div class="col-md-8 fw-semibold">
                            {{ $kelas->created_at ? $kelas->created_at->format('d-m-Y H:i') : '-' }}
                            /
                            {{ $kelas->updated_at ? $kelas->updated_at->format('d-m-Y H:i') : '-' }}
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route($routePrefix . '.kelas.index') }}" class="btn btn-outline-secondary">
                            Kembali
                        </a>

                        <a href="{{ route($routePrefix . '.kelas.edit', $kelas) }}" class="btn btn-primary">
                            <i class="bi bi-pencil-square me-1"></i>
                            Edit Kelas
                        </a>
                    </div>

                </div>
            </div>

        </div>
    </div>

@endsection
