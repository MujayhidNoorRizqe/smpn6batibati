{{-- penjelasan: Halaman ini digunakan guru untuk melihat detail absensi murid pada jadwal hari ini. --}}
{{-- penjelasan: Data yang tampil adalah absensi murid sesuai jadwal pelajaran dan tanggal hari ini. --}}

@extends('admin.layouts.app')

@section('title', 'Detail Absensi Murid')

@section('content')

@php
    $statusClasses = [
        'hadir' => 'bg-success-subtle text-success',
        'izin' => 'bg-info-subtle text-info',
        'sakit' => 'bg-danger-subtle text-danger',
        'alpha' => 'bg-danger-subtle text-danger',
    ];
@endphp

<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div>
                    <h4 class="fw-bold mb-1">Detail Absensi Murid</h4>
                    <p class="text-muted mb-0">
                        Detail absensi murid sesuai jadwal pelajaran hari ini.
                    </p>
                </div>

                <div class="text-md-end">
                    <div class="fw-semibold">{{ now()->format('d-m-Y') }}</div>
                    <small class="text-muted">Tanggal otomatis dari sistem</small>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small">Hadir</div>
                <h4 class="fw-bold mb-0">{{ $statusCounts['hadir'] ?? 0 }}</h4>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small">Izin</div>
                <h4 class="fw-bold mb-0">{{ $statusCounts['izin'] ?? 0 }}</h4>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small">Sakit</div>
                <h4 class="fw-bold mb-0">{{ $statusCounts['sakit'] ?? 0 }}</h4>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small">Alpha</div>
                <h4 class="fw-bold mb-0">{{ $statusCounts['alpha'] ?? 0 }}</h4>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-0">
        <h6 class="fw-bold mb-0">Informasi Jadwal</h6>
        <small class="text-muted">Jadwal pelajaran yang diabsen.</small>
    </div>

    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-3">
                <div class="text-muted small">Kelas</div>
                <div class="fw-semibold">{{ $jadwalPelajaran->kelas?->nama_kelas ?? '-' }}</div>
            </div>

            <div class="col-md-3">
                <div class="text-muted small">Mata Pelajaran</div>
                <div class="fw-semibold">{{ $jadwalPelajaran->mataPelajaran?->nama_mapel ?? '-' }}</div>
            </div>

            <div class="col-md-3">
                <div class="text-muted small">Jam</div>
                <div class="fw-semibold">
                    {{ $jadwalPelajaran->jam_mulai ? substr($jadwalPelajaran->jam_mulai, 0, 5) : '-' }}
                    -
                    {{ $jadwalPelajaran->jam_selesai ? substr($jadwalPelajaran->jam_selesai, 0, 5) : '-' }}
                </div>
            </div>

            <div class="col-md-3">
                <div class="text-muted small">Semester</div>
                <div class="fw-semibold">
                    {{ ucfirst($jadwalPelajaran->semester?->nama_semester ?? '-') }}
                    -
                    {{ $jadwalPelajaran->tahunAjaran?->nama_tahun_ajaran ?? '-' }}
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
        <div>
            <h6 class="fw-bold mb-0">Data Absensi Murid</h6>
            <small class="text-muted">Daftar murid yang sudah diabsen pada jadwal ini.</small>
        </div>

        <a href="{{ route('guru.absensi-murid.create', $jadwalPelajaran) }}" class="btn btn-primary btn-sm">
            <i class="bi bi-pencil-square me-1"></i>
            Edit Absensi
        </a>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle admin-table">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Nama Murid</th>
                        <th>NISN</th>
                        <th>Status</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($absensiMurids as $absensi)
                        @php
                            $badgeClass = $statusClasses[$absensi->status_absen] ?? 'bg-secondary-subtle text-secondary';
                        @endphp

                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td class="fw-semibold">{{ $absensi->murid?->nama_murid ?? '-' }}</td>
                            <td>{{ $absensi->murid?->nisn ?? '-' }}</td>

                            <td>
                                <span class="badge {{ $badgeClass }}">
                                    {{ $absensi->status_absen_label }}
                                </span>
                            </td>

                            <td>{{ $absensi->keterangan ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                Absensi murid pada jadwal ini belum diinput.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-end mt-3">
            <a
                href="{{ route('guru.absensi-murid.index') }}"
                class="btn btn-outline-secondary"
                data-confirm="true"
                data-confirm-message="Kembali ke halaman Absen Murid?"
                data-confirm-yes="Ya, Kembali"
                data-confirm-yes-class="btn-secondary"
            >
                <i class="bi bi-arrow-left me-1"></i>
                Kembali
            </a>
        </div>
    </div>
</div>

@endsection
