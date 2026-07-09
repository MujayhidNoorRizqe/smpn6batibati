{{-- penjelasan: Halaman ini digunakan admin dan super admin untuk melihat detail rekap absensi murid berdasarkan kelas. --}}
{{-- penjelasan: Detail ini menampilkan daftar absensi murid pada kelas yang dipilih. --}}
{{-- penjelasan: Filter dari halaman index tetap dibawa ke halaman detail kelas. --}}

@extends('admin.layouts.app')

@section('title', 'Detail Rekap Absensi Murid')

@section('content')

@php
    $statusClasses = [
        'hadir' => 'bg-success-subtle text-success',
        'izin' => 'bg-info-subtle text-info',
        'sakit' => 'bg-danger-subtle text-danger',
        'alpha' => 'bg-dark-subtle text-dark',
        'terlambat' => 'bg-warning-subtle text-warning',
    ];
@endphp

<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div>
                    <h4 class="fw-bold mb-1">Detail Rekap Absensi Murid</h4>
                    <p class="text-muted mb-0">
                        Detail absensi murid untuk kelas {{ $kelas->nama_kelas ?? '-' }}.
                    </p>
                </div>

                <div class="text-md-end">
                    <div class="fw-semibold">{{ now()->format('d-m-Y') }}</div>
                    <small class="text-muted">Tanggal sistem hari ini</small>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-0">
        <h6 class="fw-bold mb-0">Informasi Kelas</h6>
        <small class="text-muted">Informasi kelas yang sedang dilihat.</small>
    </div>

    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-3">
                <div class="text-muted small">Nama Kelas</div>
                <div class="fw-semibold">{{ $kelas->nama_kelas ?? '-' }}</div>
            </div>

            <div class="col-md-3">
                <div class="text-muted small">Tingkat</div>
                <div class="fw-semibold">{{ $kelas->tingkat ?? '-' }}</div>
            </div>

            <div class="col-md-3">
                <div class="text-muted small">Wali Kelas</div>
                <div class="fw-semibold">{{ $kelas->waliKelas?->nama_pegawai ?? '-' }}</div>
            </div>

            <div class="col-md-3">
                <div class="text-muted small">Total Data Absensi</div>
                <div class="fw-semibold">{{ $totalData }}</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4 col-xl-2">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small">Total Data</div>
                <h4 class="fw-bold mb-0">{{ $totalData }}</h4>
            </div>
        </div>
    </div>

    <div class="col-md-4 col-xl-2">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small">Hadir</div>
                <h4 class="fw-bold mb-0">{{ $statusCounts['hadir'] ?? 0 }}</h4>
            </div>
        </div>
    </div>

    <div class="col-md-4 col-xl-2">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small">Izin</div>
                <h4 class="fw-bold mb-0">{{ $statusCounts['izin'] ?? 0 }}</h4>
            </div>
        </div>
    </div>

    <div class="col-md-4 col-xl-2">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small">Sakit</div>
                <h4 class="fw-bold mb-0">{{ $statusCounts['sakit'] ?? 0 }}</h4>
            </div>
        </div>
    </div>

    <div class="col-md-4 col-xl-2">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small">Alpha</div>
                <h4 class="fw-bold mb-0">{{ $statusCounts['alpha'] ?? 0 }}</h4>
            </div>
        </div>
    </div>

    <div class="col-md-4 col-xl-2">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small">Terlambat</div>
                <h4 class="fw-bold mb-0">{{ $statusCounts['terlambat'] ?? 0 }}</h4>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
        <div>
            <h6 class="fw-bold mb-0">Daftar Absensi Murid Kelas {{ $kelas->nama_kelas ?? '-' }}</h6>
            <small class="text-muted">Data absensi murid sesuai kelas dan filter yang dipilih.</small>
        </div>

        <span class="badge bg-primary-subtle text-primary">
            {{ $absensiMurids->total() }} data ditemukan
        </span>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle admin-table">
                <thead class="table-light">
                    <tr>
                        <th>Tanggal</th>
                        <th>Murid</th>
                        <th>Wali Murid</th>
                        <th>Mata Pelajaran</th>
                        <th>Guru</th>
                        <th>Status</th>
                        <th>Keterangan</th>
                        <th>Waktu Input</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($absensiMurids as $absensi)
                        @php
                            $badgeClass = $statusClasses[$absensi->status_absen] ?? 'bg-secondary-subtle text-secondary';
                        @endphp

                        <tr>
                            <td>{{ $absensi->tanggal_absen?->format('d-m-Y') }}</td>

                            <td>
                                <div class="fw-semibold">{{ $absensi->murid?->nama_murid ?? '-' }}</div>
                                <small class="text-muted">
                                    NIS/NISN:
                                    {{ $absensi->murid?->nis ?? $absensi->murid?->nisn ?? '-' }}
                                </small>
                            </td>

                            <td>
                                <div>{{ $absensi->murid?->waliMurid?->nama_wali ?? '-' }}</div>
                                <small class="text-muted">
                                    WA:
                                    {{ $absensi->murid?->waliMurid?->no_whatsapp ?? '-' }}
                                </small>
                            </td>

                            <td>{{ $absensi->mataPelajaran?->nama_mapel ?? '-' }}</td>
                            <td>{{ $absensi->guru?->nama_pegawai ?? '-' }}</td>

                            <td>
                                <span class="badge {{ $badgeClass }}">
                                    {{ $absensi->status_absen_label }}
                                </span>
                            </td>

                            <td>{{ $absensi->keterangan ?? '-' }}</td>

                            <td>
                                {{ $absensi->created_at ? $absensi->created_at->format('d-m-Y H:i') : '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                Data absensi murid untuk kelas ini belum tersedia.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $absensiMurids->links() }}
        </div>

        <div class="d-flex justify-content-end mt-3">
            <a
                href="{{ route($routePrefix . '.rekap-absensi-murid.index') . (request()->getQueryString() ? '?' . request()->getQueryString() : '') }}"
                class="btn btn-outline-secondary"
            >
                <i class="bi bi-arrow-left me-1"></i>
                Kembali
            </a>
        </div>
    </div>
</div>

@endsection
