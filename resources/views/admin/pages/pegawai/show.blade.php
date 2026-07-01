{{-- penjelasan: File ini adalah halaman detail pegawai. --}}
{{-- penjelasan: File ini dipanggil oleh PegawaiController method show(). --}}
{{-- penjelasan: Halaman ini menampilkan data lengkap pegawai dan akun login yang terhubung. --}}

@extends('admin.layouts.app')

@section('title', 'Detail Pegawai')

@section('content')

    <div class="row">
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
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
                        <span class="badge {{ $pegawai->status === 'aktif' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }}">
                            {{ ucfirst($pegawai->status) }}
                        </span>
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
                                {{ $pegawai->user->name }} - {{ $pegawai->user->email }}
                            @else
                                Belum terhubung
                            @endif
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route($routePrefix . '.pegawai.index') }}" class="btn btn-outline-secondary">
                            Kembali
                        </a>

                        <a href="{{ route($routePrefix . '.pegawai.edit', $pegawai) }}" class="btn btn-primary">
                            Edit Pegawai
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>

@endsection
