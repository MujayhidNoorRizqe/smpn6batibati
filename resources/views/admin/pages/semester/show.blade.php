{{-- penjelasan: File ini adalah halaman detail semester. --}}
{{-- penjelasan: File ini dipanggil oleh SemesterController method show(). --}}
{{-- penjelasan: File ini menampilkan informasi semester dan tahun ajaran yang terhubung. --}}

@extends('admin.layouts.app')

@section('title', 'Detail Semester')

@section('content')

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
                    <div>
                        <h5 class="fw-bold mb-0">Detail Semester</h5>
                        <small class="text-muted">Informasi lengkap semester</small>
                    </div>

                    @if ($semester->status === 'aktif')
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
                        <div class="col-md-4 text-muted">Semester</div>
                        <div class="col-md-8 fw-semibold">{{ $semester->nama_semester_label }}</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4 text-muted">Tahun Ajaran</div>
                        <div class="col-md-8 fw-semibold">
                            @if ($semester->tahunAjaran)
                                {{ $semester->tahunAjaran->nama_tahun_ajaran }}

                                @if ($semester->tahunAjaran->status === 'aktif')
                                    <small class="text-success d-block">Tahun ajaran aktif</small>
                                @else
                                    <small class="text-muted d-block">Tahun ajaran nonaktif</small>
                                @endif
                            @else
                                -
                            @endif
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

                    <div class="row mb-4">
                        <div class="col-md-4 text-muted">Dibuat / Diperbarui</div>
                        <div class="col-md-8 fw-semibold">
                            {{ $semester->created_at ? $semester->created_at->format('d-m-Y H:i') : '-' }}
                            /
                            {{ $semester->updated_at ? $semester->updated_at->format('d-m-Y H:i') : '-' }}
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route($routePrefix . '.semester.index') }}" class="btn btn-outline-secondary">
                            Kembali
                        </a>

                        <a href="{{ route($routePrefix . '.semester.edit', $semester) }}" class="btn btn-primary">
                            <i class="bi bi-pencil-square me-1"></i>
                            Edit Semester
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
