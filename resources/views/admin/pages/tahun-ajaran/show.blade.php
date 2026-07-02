{{-- penjelasan: File ini adalah halaman detail tahun ajaran. --}}
{{-- penjelasan: File ini dipanggil oleh TahunAjaranController method show(). --}}
{{-- penjelasan: File ini menampilkan informasi tahun ajaran dan daftar semester di dalamnya. --}}

@extends('admin.layouts.app')

@section('title', 'Detail Tahun Ajaran')

@section('content')

    <div class="row">
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0">
                    <h5 class="fw-bold mb-0">Detail Tahun Ajaran</h5>
                    <small class="text-muted">Informasi periode akademik</small>
                </div>

                <div class="card-body">
                    <div class="mb-3">
                        <div class="text-muted">Nama Tahun Ajaran</div>
                        <div class="fw-semibold">{{ $tahunAjaran->nama_tahun_ajaran }}</div>
                    </div>

                    <div class="mb-3">
                        <div class="text-muted">Tanggal Mulai</div>
                        <div class="fw-semibold">
                            {{ $tahunAjaran->tanggal_mulai ? $tahunAjaran->tanggal_mulai->format('d-m-Y') : '-' }}
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="text-muted">Tanggal Selesai</div>
                        <div class="fw-semibold">
                            {{ $tahunAjaran->tanggal_selesai ? $tahunAjaran->tanggal_selesai->format('d-m-Y') : '-' }}
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="text-muted">Status</div>

                        @if ($tahunAjaran->status === 'aktif')
                            <span class="badge bg-success-subtle text-success">
                                Aktif
                            </span>
                        @else
                            <span class="badge bg-danger-subtle text-danger">
                                Nonaktif
                            </span>
                        @endif
                    </div>

                    <div class="mb-4">
                        <div class="text-muted">Dibuat / Diperbarui</div>
                        <div class="fw-semibold">
                            {{ $tahunAjaran->created_at ? $tahunAjaran->created_at->format('d-m-Y H:i') : '-' }}
                            /
                            {{ $tahunAjaran->updated_at ? $tahunAjaran->updated_at->format('d-m-Y H:i') : '-' }}
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route($routePrefix . '.tahun-ajaran.index') }}" class="btn btn-outline-secondary">
                            Kembali
                        </a>

                        <a href="{{ route($routePrefix . '.tahun-ajaran.edit', $tahunAjaran) }}" class="btn btn-primary">
                            <i class="bi bi-pencil-square me-1"></i>
                            Edit
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <h5 class="fw-bold mb-0">Semester dalam Tahun Ajaran Ini</h5>
                    <small class="text-muted">Daftar semester yang terhubung</small>
                </div>

                <div class="card-body">
                    @forelse ($tahunAjaran->semesters as $semester)
                        <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                            <div>
                                <div class="fw-semibold">{{ $semester->nama_semester_label }}</div>

                                <small class="text-muted">
                                    {{ $semester->tanggal_mulai ? $semester->tanggal_mulai->format('d-m-Y') : '-' }}
                                    sampai
                                    {{ $semester->tanggal_selesai ? $semester->tanggal_selesai->format('d-m-Y') : '-' }}
                                </small>
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
                    @empty
                        <div class="text-muted text-center py-4">
                            Belum ada semester untuk tahun ajaran ini.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

@endsection
