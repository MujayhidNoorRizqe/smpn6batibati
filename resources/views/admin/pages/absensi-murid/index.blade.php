{{-- penjelasan: Halaman ini digunakan guru untuk melihat jadwal mengajar hari ini dan menginput absensi murid. --}}
{{-- penjelasan: Jadwal yang tampil hanya jadwal aktif milik guru yang sedang login. --}}
{{-- penjelasan: Absensi murid memakai tanggal otomatis dari sistem. --}}

@extends('admin.layouts.app')

@section('title', 'Absen Murid')

@section('content')

@php
    $statusLabels = [
        'hadir' => 'Hadir',
        'izin' => 'Izin',
        'sakit' => 'Sakit',
        'alpha' => 'Alpha',
    ];

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
                    <h4 class="fw-bold mb-1">Absen Murid</h4>
                    <p class="text-muted mb-0">
                        Input absensi murid berdasarkan jadwal mengajar hari ini.
                    </p>
                </div>

                <div class="text-md-end">
                    <div class="fw-semibold">{{ ucfirst($hariHariIni) }}, {{ now()->format('d-m-Y') }}</div>
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
                <div class="text-muted small">Hadir Hari Ini</div>
                <h4 class="fw-bold mb-0">{{ $statusCounts['hadir'] ?? 0 }}</h4>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small">Izin Hari Ini</div>
                <h4 class="fw-bold mb-0">{{ $statusCounts['izin'] ?? 0 }}</h4>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small">Sakit Hari Ini</div>
                <h4 class="fw-bold mb-0">{{ $statusCounts['sakit'] ?? 0 }}</h4>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small">Alpha Hari Ini</div>
                <h4 class="fw-bold mb-0">{{ $statusCounts['alpha'] ?? 0 }}</h4>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-0">
        <h6 class="fw-bold mb-0">Jadwal Mengajar Hari Ini</h6>
        <small class="text-muted">Pilih jadwal untuk menginput absensi murid.</small>
    </div>

    <div class="card-body">
        <div class="row g-3">
            @forelse ($jadwalHariIni as $jadwal)
                @php
                    $sudahLengkap = $jadwal->total_murid_aktif > 0
                        && $jadwal->total_absensi_hari_ini >= $jadwal->total_murid_aktif;
                @endphp

                <div class="col-lg-6">
                    <div class="card border shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between gap-2 mb-2">
                                <div>
                                    <h6 class="fw-bold mb-1">
                                        {{ $jadwal->mataPelajaran?->nama_mapel ?? '-' }}
                                    </h6>

                                    <small class="text-muted">
                                        {{ $jadwal->jam_mulai ? substr($jadwal->jam_mulai, 0, 5) : '-' }}
                                        -
                                        {{ $jadwal->jam_selesai ? substr($jadwal->jam_selesai, 0, 5) : '-' }}
                                    </small>
                                </div>

                                @if ($sudahLengkap)
                                    <span class="badge bg-success-subtle text-success align-self-start">
                                        Sudah Diabsen
                                    </span>
                                @else
                                    <span class="badge bg-warning-subtle text-warning align-self-start">
                                        Belum Lengkap
                                    </span>
                                @endif
                            </div>

                            <div class="mb-2">
                                <div class="text-muted small">Kelas</div>
                                <div class="fw-semibold">{{ $jadwal->kelas?->nama_kelas ?? '-' }}</div>
                            </div>

                            <div class="mb-2">
                                <div class="text-muted small">Semester</div>
                                <div class="fw-semibold">
                                    {{ ucfirst($jadwal->semester?->nama_semester ?? '-') }}
                                    -
                                    {{ $jadwal->tahunAjaran?->nama_tahun_ajaran ?? '-' }}
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="text-muted small">Progress Absensi</div>
                                <div class="fw-semibold">
                                    {{ $jadwal->total_absensi_hari_ini }} / {{ $jadwal->total_murid_aktif }} murid
                                </div>
                            </div>

                            <div class="d-flex gap-2">
                                <a
                                    href="{{ route('guru.absensi-murid.create', $jadwal) }}"
                                    class="btn btn-primary btn-sm"
                                >
                                    <i class="bi bi-clipboard-check me-1"></i>
                                    Input Absensi
                                </a>

                                <a
                                    href="{{ route('guru.absensi-murid.show', $jadwal) }}"
                                    class="btn btn-outline-primary btn-sm"
                                >
                                    <i class="bi bi-eye me-1"></i>
                                    Detail
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-warning border-0 rounded-3 mb-0">
                        <i class="bi bi-exclamation-triangle me-1"></i>
                        Tidak ada jadwal mengajar aktif untuk hari ini.
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
        <div>
            <h6 class="fw-bold mb-0">Riwayat Absensi Murid</h6>
            <small class="text-muted">Riwayat absensi murid yang pernah Anda input.</small>
        </div>

        <span class="badge bg-primary-subtle text-primary">
            {{ $riwayatAbsensi->count() }} data tampil
        </span>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle admin-table">
                <thead class="table-light">
                    <tr>
                        <th>Tanggal</th>
                        <th>Murid</th>
                        <th>Kelas</th>
                        <th>Mata Pelajaran</th>
                        <th>Status</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($riwayatAbsensi as $absensi)
                        @php
                            $badgeClass = $statusClasses[$absensi->status_absen] ?? 'bg-secondary-subtle text-secondary';
                        @endphp

                        <tr>
                            <td>{{ $absensi->tanggal_absen?->format('d-m-Y') }}</td>
                            <td>{{ $absensi->murid?->nama_murid ?? '-' }}</td>
                            <td>{{ $absensi->kelas?->nama_kelas ?? '-' }}</td>
                            <td>{{ $absensi->mataPelajaran?->nama_mapel ?? '-' }}</td>

                            <td>
                                <span class="badge {{ $badgeClass }}">
                                    {{ $absensi->status_absen_label }}
                                </span>
                            </td>

                            <td>{{ Str::limit($absensi->keterangan, 60) ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                Riwayat absensi murid belum tersedia.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $riwayatAbsensi->links() }}
        </div>
    </div>
</div>

@endsection
