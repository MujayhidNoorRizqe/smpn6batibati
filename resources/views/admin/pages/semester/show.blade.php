{{-- penjelasan: File ini adalah halaman detail semester. --}}
{{-- penjelasan: File ini menampilkan informasi semester dan tahun ajaran yang terhubung. --}}

@extends('admin.layouts.app')

@section('title', 'Detail Semester')

@section('content')

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="fw-bold mb-0">Detail Semester</h5>
                        <small class="text-muted">Informasi lengkap semester</small>
                    </div>

                    <span class="badge {{ $semester->status === 'aktif' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }}">
                        {{ ucfirst($semester->status) }}
                    </span>
                </div>

                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4 text-muted">Semester</div>
                        <div class="col-md-8 fw-semibold">{{ $semester->nama_semester_label }}</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4 text-muted">Tahun Ajaran</div>
                        <div class="col-md-8 fw-semibold">
                            {{ $semester->tahunAjaran ? $semester->tahunAjaran->nama_tahun_ajaran : '-' }}
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4 text-muted">Tanggal Mulai</div>
                        <div class="col-md-8 fw-semibold">
                            {{ $semester->tanggal_mulai ? $semester->tanggal_mulai->format('d-m-Y') : '-' }}
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4 text-muted">Tanggal Selesai</div>
                        <div class="col-md-8 fw-semibold">
                            {{ $semester->tanggal_selesai ? $semester->tanggal_selesai->format('d-m-Y') : '-' }}
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route($routePrefix . '.semester.index') }}" class="btn btn-outline-secondary">
                            Kembali
                        </a>

                        <a href="{{ route($routePrefix . '.semester.edit', $semester) }}" class="btn btn-primary">
                            Edit Semester
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
