{{-- penjelasan: Halaman ini digunakan admin dan super admin untuk melihat detail absensi pegawai. --}}
{{-- penjelasan: Detail berisi data pegawai, tanggal, jam, status, metode, lokasi, keterangan, dan pengajuan terkait jika ada. --}}

@extends('admin.layouts.app')

@section('title', 'Detail Absensi Pegawai')

@section('content')

@php
    $statusClass = match ($absensiPegawai->status_absen) {
        'hadir' => 'bg-success-subtle text-success',
        'terlambat' => 'bg-warning-subtle text-warning',
        'dinas' => 'bg-primary-subtle text-primary',
        'izin' => 'bg-info-subtle text-info',
        'sakit' => 'bg-danger-subtle text-danger',
        'alpha' => 'bg-danger-subtle text-danger',
        default => 'bg-secondary-subtle text-secondary',
    };
@endphp

<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div>
                    <h4 class="fw-bold mb-1">Detail Absensi Pegawai</h4>
                    <p class="text-muted mb-0">
                        Detail data absensi pegawai yang tercatat pada sistem.
                    </p>
                </div>

                <span class="badge {{ $statusClass }}">
                    {{ $absensiPegawai->status_absen_label }}
                </span>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-0">
                <h5 class="fw-bold mb-0">Data Pegawai</h5>
                <small class="text-muted">Informasi pegawai yang memiliki absensi</small>
            </div>

            <div class="card-body">
                <div class="mb-3">
                    <div class="text-muted">Nama Pegawai</div>
                    <div class="fw-semibold">{{ $absensiPegawai->pegawai?->nama_pegawai ?? '-' }}</div>
                </div>

                <div class="mb-3">
                    <div class="text-muted">NIP</div>
                    <div class="fw-semibold">{{ $absensiPegawai->pegawai?->nip ?? '-' }}</div>
                </div>

                <div class="mb-3">
                    <div class="text-muted">Jenis Pegawai</div>
                    <div class="fw-semibold">{{ ucfirst($absensiPegawai->pegawai?->jenis_pegawai ?? '-') }}</div>
                </div>

                <div class="mb-3">
                    <div class="text-muted">Jabatan</div>
                    <div class="fw-semibold">{{ $absensiPegawai->pegawai?->jabatan ?? '-' }}</div>
                </div>

                <div class="mb-3">
                    <div class="text-muted">Akun Login</div>
                    <div class="fw-semibold">
                        {{ $absensiPegawai->pegawai?->user?->email ?? '-' }}
                    </div>
                </div>

                <div class="mb-0">
                    <div class="text-muted">Status Pegawai</div>
                    <div class="fw-semibold">{{ ucfirst($absensiPegawai->pegawai?->status ?? '-') }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-0">
                <h5 class="fw-bold mb-0">Data Absensi</h5>
                <small class="text-muted">Informasi tanggal, jam, metode, dan keterangan absensi</small>
            </div>

            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="text-muted">Tanggal Absen</div>
                        <div class="fw-semibold">{{ $absensiPegawai->tanggal_absen?->format('d-m-Y') }}</div>
                    </div>

                    <div class="col-md-6">
                        <div class="text-muted">Status Absen</div>
                        <span class="badge {{ $statusClass }}">
                            {{ $absensiPegawai->status_absen_label }}
                        </span>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="text-muted">Jam Masuk</div>
                        <div class="fw-semibold">{{ $absensiPegawai->jam_masuk_format }}</div>
                    </div>

                    <div class="col-md-6">
                        <div class="text-muted">Jam Pulang</div>
                        <div class="fw-semibold">{{ $absensiPegawai->jam_pulang_format }}</div>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="text-muted">Metode Absen</div>
                    <div class="fw-semibold">{{ $absensiPegawai->metode_absen_label }}</div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="text-muted">Latitude</div>
                        <div class="fw-semibold">{{ $absensiPegawai->latitude ?? '-' }}</div>
                    </div>

                    <div class="col-md-6">
                        <div class="text-muted">Longitude</div>
                        <div class="fw-semibold">{{ $absensiPegawai->longitude ?? '-' }}</div>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="text-muted">Keterangan</div>
                    <div class="fw-semibold">{{ $absensiPegawai->keterangan ?? '-' }}</div>
                </div>

                <div class="mb-0">
                    <div class="text-muted">Pengajuan Terkait</div>

                    @if ($absensiPegawai->pengajuanAbsensiPegawai)
                        <a
                            href="{{ route($routePrefix . '.persetujuan-absensi-pegawai.show', $absensiPegawai->pengajuanAbsensiPegawai) }}"
                            class="btn btn-sm btn-outline-primary mt-1"
                        >
                            <i class="bi bi-eye me-1"></i>
                            Lihat Pengajuan
                        </a>
                    @else
                        <div class="fw-semibold">Tidak ada pengajuan terkait.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<div class="d-flex justify-content-end mt-4">
    <a
        href="{{ route($routePrefix . '.absensi-pegawai.index') }}"
        class="btn btn-outline-secondary"
        data-confirm="true"
        data-confirm-message="Kembali ke daftar rekap absensi pegawai?"
        data-confirm-yes="Ya, Kembali"
        data-confirm-yes-class="btn-secondary"
    >
        <i class="bi bi-arrow-left me-1"></i>
        Kembali
    </a>
</div>

@endsection
