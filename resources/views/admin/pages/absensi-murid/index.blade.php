{{-- penjelasan: Halaman ini digunakan guru untuk melihat kelas yang diajar hari ini dan menginput absensi murid. --}}
{{-- penjelasan: Jadwal dikelompokkan berdasarkan kelas agar tampilan lebih rapi. --}}
{{-- penjelasan: Riwayat absensi murid juga dikelompokkan berdasarkan kelas agar tidak tampil sebagai tabel panjang. --}}
{{-- penjelasan: Setelah guru memilih jadwal pada kelas tertentu, sistem menampilkan daftar murid sesuai kelas tersebut. --}}
{{-- penjelasan: Rekap khusus guru akan dibuat pada part 2. --}}

@extends('admin.layouts.app')

@section('title', 'Absen Murid')

@section('content')

@php
    $statusClasses = [
        'hadir' => 'bg-success-subtle text-success',
        'izin' => 'bg-info-subtle text-info',
        'sakit' => 'bg-danger-subtle text-danger',
        'alpha' => 'bg-dark-subtle text-dark',
        'terlambat' => 'bg-warning-subtle text-warning',
    ];

    $progressLabels = [
        'tidak_ada_murid' => 'Tidak Ada Murid',
        'belum_diabsen' => 'Belum Diabsen',
        'belum_lengkap' => 'Belum Lengkap',
        'sudah_lengkap' => 'Sudah Lengkap',
    ];

    $progressClasses = [
        'tidak_ada_murid' => 'bg-secondary-subtle text-secondary',
        'belum_diabsen' => 'bg-danger-subtle text-danger',
        'belum_lengkap' => 'bg-warning-subtle text-warning',
        'sudah_lengkap' => 'bg-success-subtle text-success',
    ];
@endphp

<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div>
                    <h4 class="fw-bold mb-1">Absen Murid</h4>
                    <p class="text-muted mb-0">
                        Pilih kelas yang diajar hari ini, lalu input absensi murid sesuai jadwal.
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
    <div class="col-md-6 col-xl">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small">Hadir Hari Ini</div>
                <h4 class="fw-bold mb-0">{{ $statusCounts['hadir'] ?? 0 }}</h4>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xl">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small">Izin Hari Ini</div>
                <h4 class="fw-bold mb-0">{{ $statusCounts['izin'] ?? 0 }}</h4>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xl">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small">Sakit Hari Ini</div>
                <h4 class="fw-bold mb-0">{{ $statusCounts['sakit'] ?? 0 }}</h4>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xl">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small">Alpha Hari Ini</div>
                <h4 class="fw-bold mb-0">{{ $statusCounts['alpha'] ?? 0 }}</h4>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xl">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small">Terlambat Hari Ini</div>
                <h4 class="fw-bold mb-0">{{ $statusCounts['terlambat'] ?? 0 }}</h4>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-0 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
        <div>
            <h6 class="fw-bold mb-0">Kelas yang Diajar Hari Ini</h6>
            <small class="text-muted">
                Absensi dikelompokkan per kelas. Klik Input Absensi pada jadwal yang sesuai untuk membuka daftar murid kelas tersebut.
            </small>
        </div>

        <span class="badge bg-primary-subtle text-primary">
            {{ $kelasJadwalHariIni->count() }} kelas tampil
        </span>
    </div>

    <div class="card-body">
        <div class="row g-3">
            @forelse ($kelasJadwalHariIni as $kelasGroup)
                @php
                    $kelasStatus = $kelasGroup->status_absensi_hari_ini;
                    $kelasBadgeClass = $progressClasses[$kelasStatus] ?? 'bg-secondary-subtle text-secondary';
                    $kelasBadgeLabel = $progressLabels[$kelasStatus] ?? '-';
                @endphp

                <div class="col-xl-6">
                    <div class="card border shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                                <div>
                                    <div class="text-muted small">Kelas</div>
                                    <h5 class="fw-bold mb-1">
                                        {{ $kelasGroup->kelas?->nama_kelas ?? '-' }}
                                    </h5>

                                    <small class="text-muted">
                                        {{ $kelasGroup->total_jadwal }} jadwal hari ini • {{ $kelasGroup->total_murid_aktif }} murid aktif
                                    </small>
                                </div>

                                <span class="badge {{ $kelasBadgeClass }}">
                                    {{ $kelasBadgeLabel }}
                                </span>
                            </div>

                            <div class="mb-3">
                                <div class="text-muted small">Progress Total Kelas</div>
                                <div class="fw-semibold">
                                    {{ $kelasGroup->total_absensi_hari_ini }} / {{ $kelasGroup->target_absensi }} data absensi
                                </div>
                            </div>

                            <div class="border rounded-3 overflow-hidden">
                                @foreach ($kelasGroup->jadwals as $jadwal)
                                    @php
                                        $jadwalStatus = $jadwal->status_absensi_hari_ini;
                                        $jadwalBadgeClass = $progressClasses[$jadwalStatus] ?? 'bg-secondary-subtle text-secondary';
                                        $jadwalBadgeLabel = $progressLabels[$jadwalStatus] ?? '-';
                                    @endphp

                                    <div class="p-3 {{ ! $loop->last ? 'border-bottom' : '' }}">
                                        <div class="d-flex flex-column flex-lg-row justify-content-between gap-3">
                                            <div>
                                                <div class="fw-semibold">
                                                    {{ $jadwal->mataPelajaran?->nama_mapel ?? '-' }}
                                                </div>

                                                <small class="text-muted d-block">
                                                    Jam:
                                                    {{ $jadwal->jam_mulai ? substr($jadwal->jam_mulai, 0, 5) : '-' }}
                                                    -
                                                    {{ $jadwal->jam_selesai ? substr($jadwal->jam_selesai, 0, 5) : '-' }}
                                                </small>

                                                <small class="text-muted d-block">
                                                    Progress:
                                                    {{ $jadwal->total_absensi_hari_ini }} / {{ $jadwal->total_murid_aktif }} murid
                                                </small>
                                            </div>

                                            <div class="d-flex flex-column flex-sm-row align-items-sm-center gap-2">
                                                <span class="badge {{ $jadwalBadgeClass }}">
                                                    {{ $jadwalBadgeLabel }}
                                                </span>

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
                                @endforeach
                            </div>

                            <div class="mt-3">
                                <small class="text-muted">
                                    Daftar murid akan muncul setelah membuka salah satu jadwal pada kelas ini.
                                </small>
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
            <h6 class="fw-bold mb-0">Riwayat Absensi Murid per Kelas</h6>
            <small class="text-muted">Riwayat absensi murid yang pernah Anda input, dikelompokkan berdasarkan kelas.</small>
        </div>

        <span class="badge bg-primary-subtle text-primary">
            {{ $riwayatAbsensiTotal }} data total
        </span>
    </div>

    <div class="card-body">
        <div class="row g-3">
            @forelse ($riwayatAbsensiPerKelas as $riwayatKelas)
                <div class="col-xl-6">
                    <div class="card border shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                                <div>
                                    <div class="text-muted small">Kelas</div>
                                    <h5 class="fw-bold mb-1">
                                        {{ $riwayatKelas->kelas?->nama_kelas ?? '-' }}
                                    </h5>

                                    <small class="text-muted">
                                        {{ $riwayatKelas->total_murid }} murid • {{ $riwayatKelas->total_mapel }} mapel
                                    </small>
                                </div>

                                <span class="badge bg-primary-subtle text-primary">
                                    {{ $riwayatKelas->total_data }} data
                                </span>
                            </div>

                            <div class="row g-2 mb-3">
                                <div class="col-6 col-md">
                                    <div class="border rounded-3 p-2 h-100">
                                        <small class="text-muted d-block">Hadir</small>
                                        <span class="fw-bold">{{ $riwayatKelas->hadir }}</span>
                                    </div>
                                </div>

                                <div class="col-6 col-md">
                                    <div class="border rounded-3 p-2 h-100">
                                        <small class="text-muted d-block">Izin</small>
                                        <span class="fw-bold">{{ $riwayatKelas->izin }}</span>
                                    </div>
                                </div>

                                <div class="col-6 col-md">
                                    <div class="border rounded-3 p-2 h-100">
                                        <small class="text-muted d-block">Sakit</small>
                                        <span class="fw-bold">{{ $riwayatKelas->sakit }}</span>
                                    </div>
                                </div>

                                <div class="col-6 col-md">
                                    <div class="border rounded-3 p-2 h-100">
                                        <small class="text-muted d-block">Alpha</small>
                                        <span class="fw-bold">{{ $riwayatKelas->alpha }}</span>
                                    </div>
                                </div>

                                <div class="col-6 col-md">
                                    <div class="border rounded-3 p-2 h-100">
                                        <small class="text-muted d-block">Terlambat</small>
                                        <span class="fw-bold">{{ $riwayatKelas->terlambat }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <small class="text-muted">
                                    Tanggal terakhir:
                                    {{ $riwayatKelas->tanggal_terakhir ? $riwayatKelas->tanggal_terakhir->format('d-m-Y') : '-' }}
                                </small>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-hover align-middle admin-table mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Tanggal</th>
                                            <th>Murid</th>
                                            <th>Mata Pelajaran</th>
                                            <th>Status</th>
                                            <th>Keterangan</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @foreach ($riwayatKelas->absensis as $absensi)
                                            @php
                                                $badgeClass = $statusClasses[$absensi->status_absen] ?? 'bg-secondary-subtle text-secondary';
                                            @endphp

                                            <tr>
                                                <td>{{ $absensi->tanggal_absen?->format('d-m-Y') }}</td>
                                                <td>{{ $absensi->murid?->nama_murid ?? '-' }}</td>
                                                <td>{{ $absensi->mataPelajaran?->nama_mapel ?? '-' }}</td>

                                                <td>
                                                    <span class="badge {{ $badgeClass }}">
                                                        {{ $absensi->status_absen_label }}
                                                    </span>
                                                </td>

                                                <td>
                                                    {{ $absensi->keterangan ? \Illuminate\Support\Str::limit($absensi->keterangan, 35) : '-' }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            @if ($riwayatKelas->total_data > $riwayatKelas->absensis->count())
                                <div class="mt-2">
                                    <small class="text-muted">
                                        Menampilkan {{ $riwayatKelas->absensis->count() }} data terbaru dari total {{ $riwayatKelas->total_data }} data kelas ini.
                                    </small>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-warning border-0 rounded-3 mb-0">
                        <i class="bi bi-exclamation-triangle me-1"></i>
                        Riwayat absensi murid belum tersedia.
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</div>

@endsection
