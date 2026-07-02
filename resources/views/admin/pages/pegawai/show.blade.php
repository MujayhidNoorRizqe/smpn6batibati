{{-- penjelasan: File ini adalah halaman detail pegawai. --}}
{{-- penjelasan: File ini dipanggil oleh PegawaiController method show(). --}}
{{-- penjelasan: Halaman ini menampilkan data lengkap pegawai dan akun login yang terhubung. --}}
{{-- penjelasan: Halaman ini tidak memiliki form, sehingga tidak membutuhkan validasi input. --}}

@extends('admin.layouts.app')

@section('title', 'Detail Pegawai')

@section('content')

    <div class="row">
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body text-center">

                    @if ($pegawai->foto)
                        <img src="{{ asset('storage/' . $pegawai->foto) }}" alt="Foto Pegawai" class="rounded-circle mb-3" width="120" height="120" style="object-fit: cover;">
                    @else
                        <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center mb-3" style="width:120px; height:120px; font-size:42px;">
                            {{ strtoupper(substr($pegawai->nama_pegawai, 0, 1)) }}
                        </div>
                    @endif

                    <h5 class="fw-bold mb-1">{{ $pegawai->nama_pegawai }}</h5>

                    <span class="badge {{ $pegawai->jenis_pegawai === 'guru' ? 'bg-primary-subtle text-primary' : 'bg-info-subtle text-info' }}">
                        {{ ucfirst($pegawai->jenis_pegawai) }}
                    </span>

                    <div class="mt-3">
                        @if ($pegawai->status === 'aktif')
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
                    <h5 class="fw-bold mb-0">Informasi Pegawai</h5>
                    <small class="text-muted">Detail data guru/staff</small>
                </div>

                <div class="card-body">

                    <div class="row mb-3">
                        <div class="col-md-4 text-muted">NIP</div>
                        <div class="col-md-8 fw-semibold">{{ $pegawai->nip ?? '-' }}</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4 text-muted">Nama Pegawai</div>
                        <div class="col-md-8 fw-semibold">{{ $pegawai->nama_pegawai }}</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4 text-muted">Jenis Pegawai</div>
                        <div class="col-md-8 fw-semibold">{{ ucfirst($pegawai->jenis_pegawai) }}</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4 text-muted">Jabatan</div>
                        <div class="col-md-8 fw-semibold">{{ $pegawai->jabatan ?? '-' }}</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4 text-muted">Jenis Kelamin</div>
                        <div class="col-md-8 fw-semibold">
                            @if ($pegawai->jenis_kelamin === 'L')
                                Laki-laki
                            @elseif ($pegawai->jenis_kelamin === 'P')
                                Perempuan
                            @else
                                -
                            @endif
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4 text-muted">Nomor HP</div>
                        <div class="col-md-8 fw-semibold">{{ $pegawai->no_hp ?? '-' }}</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4 text-muted">Alamat</div>
                        <div class="col-md-8 fw-semibold">{{ $pegawai->alamat ?? '-' }}</div>
                    </div>

                    <hr>

                    <div class="row mb-3">
                        <div class="col-md-4 text-muted">Akun Login</div>
                        <div class="col-md-8 fw-semibold">
                            @if ($pegawai->user)
                                <div>{{ $pegawai->user->name }}</div>
                                <small class="text-muted">{{ $pegawai->user->email }}</small>
                            @else
                                Belum terhubung
                            @endif
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-4 text-muted">Dibuat / Diperbarui</div>
                        <div class="col-md-8 fw-semibold">
                            {{ $pegawai->created_at ? $pegawai->created_at->format('d-m-Y H:i') : '-' }}
                            /
                            {{ $pegawai->updated_at ? $pegawai->updated_at->format('d-m-Y H:i') : '-' }}
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route($routePrefix . '.pegawai.index') }}" class="btn btn-outline-secondary">
                            Kembali
                        </a>

                        <a href="{{ route($routePrefix . '.pegawai.edit', $pegawai) }}" class="btn btn-primary">
                            <i class="bi bi-pencil-square me-1"></i>
                            Edit Pegawai
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>

@endsection
