{{-- penjelasan: File ini adalah halaman detail murid. --}}
{{-- penjelasan: File ini dipanggil oleh MuridController method show(). --}}
{{-- penjelasan: Halaman ini menampilkan informasi lengkap murid, kelas, dan wali murid. --}}

@extends('admin.layouts.app')

@section('title', 'Detail Murid')

@section('content')

    <div class="row">
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body text-center">

                    @if ($murid->foto)
                        <img src="{{ asset('storage/' . $murid->foto) }}" alt="Foto Murid" class="rounded-circle mb-3" width="120" height="120" style="object-fit: cover;">
                    @else
                        <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center mb-3" style="width:120px; height:120px; font-size:42px;">
                            {{ strtoupper(substr($murid->nama_murid, 0, 1)) }}
                        </div>
                    @endif

                    <h5 class="fw-bold mb-1">{{ $murid->nama_murid }}</h5>

                    <span class="badge bg-primary-subtle text-primary">
                        {{ $murid->kelas ? $murid->kelas->nama_kelas : 'Tanpa Kelas' }}
                    </span>

                    <div class="mt-3">
                        @if ($murid->status === 'aktif')
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
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <h5 class="fw-bold mb-0">Informasi Murid</h5>
                    <small class="text-muted">Detail data siswa</small>
                </div>

                <div class="card-body">

                    <div class="row mb-3">
                        <div class="col-md-4 text-muted">Nama Murid</div>
                        <div class="col-md-8 fw-semibold">{{ $murid->nama_murid }}</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4 text-muted">Kelas</div>
                        <div class="col-md-8 fw-semibold">
                            {{ $murid->kelas ? $murid->kelas->nama_kelas : '-' }}
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4 text-muted">Jenis Kelamin</div>
                        <div class="col-md-8 fw-semibold">
                            {{ $murid->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4 text-muted">NIS</div>
                        <div class="col-md-8 fw-semibold">{{ $murid->nis ?? '-' }}</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4 text-muted">NISN</div>
                        <div class="col-md-8 fw-semibold">{{ $murid->nisn ?? '-' }}</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4 text-muted">Tanggal Lahir</div>
                        <div class="col-md-8 fw-semibold">
                            {{ $murid->tanggal_lahir ? $murid->tanggal_lahir->format('d-m-Y') : '-' }}
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4 text-muted">Tempat Lahir</div>
                        <div class="col-md-8 fw-semibold">{{ $murid->tempat_lahir ?? '-' }}</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4 text-muted">Agama</div>
                        <div class="col-md-8 fw-semibold">{{ $murid->agama ?? '-' }}</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4 text-muted">Alamat</div>
                        <div class="col-md-8 fw-semibold">{{ $murid->alamat ?? '-' }}</div>
                    </div>

                    <hr>

                    <div class="row mb-3">
                        <div class="col-md-4 text-muted">Wali Murid</div>
                        <div class="col-md-8 fw-semibold">
                            @if ($murid->waliMurid)
                                {{ $murid->waliMurid->nama_wali }}

                                <small class="text-muted d-block">
                                    {{ ucfirst($murid->waliMurid->hubungan) }} |
                                    WA: {{ $murid->waliMurid->no_whatsapp }}
                                </small>
                            @else
                                -
                            @endif
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-4 text-muted">Dibuat / Diperbarui</div>
                        <div class="col-md-8 fw-semibold">
                            {{ $murid->created_at ? $murid->created_at->format('d-m-Y H:i') : '-' }}
                            /
                            {{ $murid->updated_at ? $murid->updated_at->format('d-m-Y H:i') : '-' }}
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route($routePrefix . '.murid.index') }}" class="btn btn-outline-secondary">
                            Kembali
                        </a>

                        <a href="{{ route($routePrefix . '.murid.edit', $murid) }}" class="btn btn-primary">
                            <i class="bi bi-pencil-square me-1"></i>
                            Edit Murid
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>

@endsection
