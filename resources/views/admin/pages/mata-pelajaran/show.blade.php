{{-- penjelasan: File ini adalah halaman detail mata pelajaran. --}}
{{-- penjelasan: File ini dipanggil oleh MataPelajaranController method show(). --}}
{{-- penjelasan: Halaman ini menampilkan informasi lengkap mata pelajaran. --}}

@extends('admin.layouts.app')

@section('title', 'Detail Mata Pelajaran')

@section('content')

    <div class="row justify-content-center">
        <div class="col-lg-8">

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
                    <div>
                        <h5 class="fw-bold mb-0">Detail Mata Pelajaran</h5>
                        <small class="text-muted">Informasi lengkap mata pelajaran</small>
                    </div>

                    @if ($mataPelajaran->status === 'aktif')
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
                        <div class="col-md-4 text-muted">Kode Mata Pelajaran</div>
                        <div class="col-md-8 fw-semibold">
                            <span class="badge bg-dark-subtle text-dark">
                                {{ $mataPelajaran->kode_mapel }}
                            </span>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4 text-muted">Nama Mata Pelajaran</div>
                        <div class="col-md-8 fw-semibold">{{ $mataPelajaran->nama_mapel }}</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4 text-muted">Kelompok</div>
                        <div class="col-md-8">
                            <span class="badge bg-primary-subtle text-primary">
                                {{ $mataPelajaran->kelompok_label }}
                            </span>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4 text-muted">Deskripsi</div>
                        <div class="col-md-8 fw-semibold">
                            {{ $mataPelajaran->deskripsi ?? '-' }}
                        </div>
                    </div>

                    <hr>

                    <div class="row mb-4">
                        <div class="col-md-4 text-muted">Dibuat / Diperbarui</div>
                        <div class="col-md-8 fw-semibold">
                            {{ $mataPelajaran->created_at ? $mataPelajaran->created_at->format('d-m-Y H:i') : '-' }}
                            /
                            {{ $mataPelajaran->updated_at ? $mataPelajaran->updated_at->format('d-m-Y H:i') : '-' }}
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route($routePrefix . '.mata-pelajaran.index') }}" class="btn btn-outline-secondary">
                            Kembali
                        </a>

                        <a href="{{ route($routePrefix . '.mata-pelajaran.edit', $mataPelajaran) }}" class="btn btn-primary">
                            <i class="bi bi-pencil-square me-1"></i>
                            Edit Mata Pelajaran
                        </a>
                    </div>

                </div>
            </div>

        </div>
    </div>

@endsection
