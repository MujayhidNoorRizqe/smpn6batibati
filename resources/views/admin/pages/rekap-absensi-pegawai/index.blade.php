{{-- penjelasan: Halaman ini digunakan admin dan super admin untuk melihat rekap absensi pegawai/guru. --}}
{{-- penjelasan: Data yang tampil berasal dari absen masuk, absen pulang, pengajuan yang disetujui, dan generate alpha. --}}
{{-- penjelasan: Halaman awal tidak langsung menampilkan daftar rekap absensi. --}}
{{-- penjelasan: Daftar rekap absensi baru muncul setelah admin/super admin menggunakan filter. --}}
{{-- penjelasan: Generate alpha memakai popup konfirmasi global agar tidak terpencet tanpa sengaja. --}}
{{-- penjelasan: Role staff sudah tidak digunakan, sehingga rekap ini hanya menampilkan data guru. --}}

@extends('admin.layouts.app')

@section('title', 'Rekap Absensi Pegawai')

@section('content')

@php
    $statusLabels = [
        'hadir' => 'Hadir',
        'terlambat' => 'Terlambat',
        'dinas' => 'Dinas',
        'izin' => 'Izin',
        'sakit' => 'Sakit',
        'alpha' => 'Alpha',
    ];

    $statusClasses = [
        'hadir' => 'bg-success-subtle text-success',
        'terlambat' => 'bg-warning-subtle text-warning',
        'dinas' => 'bg-primary-subtle text-primary',
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
                    <h4 class="fw-bold mb-1">Rekap Absensi Pegawai</h4>
                    <p class="text-muted mb-0">
                        Pantau data hadir, terlambat, dinas, sakit, izin, dan alpha guru.
                    </p>
                </div>

                @if ($hasFilter)
                    <span class="badge bg-primary-subtle text-primary">
                        {{ $totalRekap }} total rekap sesuai filter
                    </span>
                @else
                    <span class="badge bg-secondary-subtle text-secondary">
                        Belum difilter
                    </span>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4 col-xl-2">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small">Hadir</div>
                <h4 class="fw-bold mb-0">{{ $hasFilter ? ($statusCounts['hadir'] ?? 0) : 0 }}</h4>
            </div>
        </div>
    </div>

    <div class="col-md-4 col-xl-2">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small">Terlambat</div>
                <h4 class="fw-bold mb-0">{{ $hasFilter ? ($statusCounts['terlambat'] ?? 0) : 0 }}</h4>
            </div>
        </div>
    </div>

    <div class="col-md-4 col-xl-2">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small">Dinas</div>
                <h4 class="fw-bold mb-0">{{ $hasFilter ? ($statusCounts['dinas'] ?? 0) : 0 }}</h4>
            </div>
        </div>
    </div>

    <div class="col-md-4 col-xl-2">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small">Izin</div>
                <h4 class="fw-bold mb-0">{{ $hasFilter ? ($statusCounts['izin'] ?? 0) : 0 }}</h4>
            </div>
        </div>
    </div>

    <div class="col-md-4 col-xl-2">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small">Sakit</div>
                <h4 class="fw-bold mb-0">{{ $hasFilter ? ($statusCounts['sakit'] ?? 0) : 0 }}</h4>
            </div>
        </div>
    </div>

    <div class="col-md-4 col-xl-2">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small">Alpha</div>
                <h4 class="fw-bold mb-0">{{ $hasFilter ? ($statusCounts['alpha'] ?? 0) : 0 }}</h4>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-0">
        <h6 class="fw-bold mb-0">Filter Rekap</h6>
        <small class="text-muted">
            Gunakan filter terlebih dahulu untuk menampilkan data rekap absensi pegawai.
        </small>
    </div>

    <div class="card-body">
        <form action="{{ route($routePrefix . '.absensi-pegawai.index') }}" method="GET" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Tanggal Mulai</label>
                <input
                    type="date"
                    name="tanggal_mulai"
                    class="form-control @error('tanggal_mulai') is-invalid @enderror"
                    value="{{ request('tanggal_mulai') }}"
                >

                @error('tanggal_mulai')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-3">
                <label class="form-label">Tanggal Selesai</label>
                <input
                    type="date"
                    name="tanggal_selesai"
                    class="form-control @error('tanggal_selesai') is-invalid @enderror"
                    value="{{ request('tanggal_selesai') }}"
                >

                @error('tanggal_selesai')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-3">
                <label class="form-label">Pegawai</label>
                <select name="pegawai_id" class="form-select @error('pegawai_id') is-invalid @enderror">
                    <option value="">Semua Pegawai</option>

                    @foreach ($pegawais as $pegawai)
                        <option value="{{ $pegawai->id }}" {{ request('pegawai_id') == $pegawai->id ? 'selected' : '' }}>
                            {{ $pegawai->nama_pegawai }}
                        </option>
                    @endforeach
                </select>

                @error('pegawai_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select name="status_absen" class="form-select @error('status_absen') is-invalid @enderror">
                    <option value="">Semua Status</option>

                    @foreach ($statusLabels as $value => $label)
                        <option value="{{ $value }}" {{ request('status_absen') === $value ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>

                @error('status_absen')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-3">
                <label class="form-label">Metode</label>
                <select name="metode_absen" class="form-select @error('metode_absen') is-invalid @enderror">
                    <option value="">Semua Metode</option>
                    <option value="lokasi" {{ request('metode_absen') === 'lokasi' ? 'selected' : '' }}>Lokasi GPS</option>
                    <option value="wifi" {{ request('metode_absen') === 'wifi' ? 'selected' : '' }}>WiFi Sekolah</option>
                    <option value="pengajuan" {{ request('metode_absen') === 'pengajuan' ? 'selected' : '' }}>Pengajuan</option>
                    <option value="manual" {{ request('metode_absen') === 'manual' ? 'selected' : '' }}>Manual</option>
                </select>

                @error('metode_absen')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-5">
                <label class="form-label">Cari Pegawai</label>
                <input
                    type="text"
                    name="search"
                    class="form-control @error('search') is-invalid @enderror"
                    value="{{ request('search') }}"
                    placeholder="Cari nama, NIP, atau jabatan"
                >

                @error('search')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search me-1"></i>
                    Filter
                </button>
            </div>

            <div class="col-md-2 d-flex align-items-end">
                <a href="{{ route($routePrefix . '.absensi-pegawai.index') }}" class="btn btn-outline-secondary w-100">
                    Reset
                </a>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-0">
        <h6 class="fw-bold mb-0">Alpha</h6>
        <small class="text-muted">
            Buat status alpha untuk guru aktif yang tidak memiliki absensi pada tanggal tertentu.
        </small>
    </div>

    <div class="card-body">
        <form action="{{ route($routePrefix . '.absensi-pegawai.generate-alpha') }}" method="POST" class="row g-3">
            @csrf

            <div class="col-md-4">
                <label class="form-label">
                    Tanggal Alpha <span class="text-danger">*</span>
                </label>

                <input
                    type="date"
                    name="tanggal_alpha"
                    class="form-control @error('tanggal_alpha') is-invalid @enderror"
                    value="{{ old('tanggal_alpha', $tanggalAlpha) }}"
                    required
                >

                @error('tanggal_alpha')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-5">
                <div class="alert alert-warning border-0 rounded-3 mb-0">
                    <i class="bi bi-exclamation-triangle me-1"></i>
                    Pada tanggal ini ada <strong>{{ $jumlahBelumAbsen }}</strong> guru aktif yang belum punya absensi.
                </div>
            </div>

            <div class="col-md-3 d-flex align-items-end">
                <button
                    type="submit"
                    class="btn btn-danger w-100"
                    data-confirm="true"
                    data-confirm-message="Generate alpha akan membuat data alpha untuk guru aktif yang belum absen pada tanggal tersebut. Lanjutkan?"
                    data-confirm-yes="Ya, Generate Alpha"
                    data-confirm-yes-class="btn-danger"
                >
                    <i class="bi bi-person-x me-1"></i>
                    Alpha
                </button>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
        <div>
            <h6 class="fw-bold mb-0">Data Rekap Absensi</h6>

            @if ($hasFilter)
                <small class="text-muted">
                    Daftar absensi pegawai yang sesuai dengan filter.
                </small>
            @else
                <small class="text-muted">
                    Data belum ditampilkan. Gunakan filter terlebih dahulu.
                </small>
            @endif
        </div>

        @if ($hasFilter)
            <span class="badge bg-primary-subtle text-primary">
                {{ $absensiPegawais->total() }} data ditemukan
            </span>
        @else
            <span class="badge bg-secondary-subtle text-secondary">
                Belum difilter
            </span>
        @endif
    </div>

    <div class="card-body">
        @if (! $hasFilter)
            <div class="empty-state-filter text-center">
                <div class="empty-icon">
                    <i class="bi bi-search"></i>
                </div>

                <h5 class="fw-bold mb-2">Silakan gunakan filter terlebih dahulu</h5>

                <p class="text-muted mb-0">
                    Daftar rekap absensi pegawai tidak ditampilkan otomatis.
                    Pilih tanggal, pegawai, status, metode, atau masukkan kata kunci pencarian untuk menampilkan data.
                </p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle admin-table">
                    <thead class="table-light">
                        <tr>
                            <th>Tanggal</th>
                            <th>Pegawai</th>
                            <th>Jenis</th>
                            <th>Status</th>
                            <th>Jam Masuk</th>
                            <th>Jam Pulang</th>
                            <th>Metode</th>
                            <th>Keterangan</th>
                            <th class="text-end table-action-column">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($absensiPegawais as $absensi)
                            @php
                                $badgeClass = $statusClasses[$absensi->status_absen] ?? 'bg-secondary-subtle text-secondary';
                            @endphp

                            <tr>
                                <td>{{ $absensi->tanggal_absen?->format('d-m-Y') }}</td>

                                <td>
                                    <div class="fw-semibold">{{ $absensi->pegawai?->nama_pegawai ?? '-' }}</div>
                                    <small class="text-muted">{{ $absensi->pegawai?->nip ?? 'NIP belum diisi' }}</small>
                                </td>

                                <td>{{ ucfirst($absensi->pegawai?->jenis_pegawai ?? '-') }}</td>

                                <td>
                                    <span class="badge {{ $badgeClass }}">
                                        {{ $absensi->status_absen_label }}
                                    </span>
                                </td>

                                <td>{{ $absensi->jam_masuk_format }}</td>
                                <td>{{ $absensi->jam_pulang_format }}</td>
                                <td>{{ $absensi->metode_absen_label }}</td>
                                <td>{{ Str::limit($absensi->keterangan, 55) }}</td>

                                <td class="text-end">
                                    <a
                                        href="{{ route($routePrefix . '.absensi-pegawai.show', $absensi) }}"
                                        class="btn btn-sm btn-outline-primary action-btn"
                                    >
                                        <i class="bi bi-eye"></i>
                                        <span>Detail</span>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">
                                    Data rekap absensi pegawai tidak ditemukan sesuai filter.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $absensiPegawais->links() }}
            </div>
        @endif
    </div>
</div>

<style>
    .empty-state-filter {
        border: 1px dashed #cbd5e1;
        border-radius: 14px;
        background: #f8fafc;
        padding: 42px 24px;
    }

    .empty-icon {
        width: 58px;
        height: 58px;
        border-radius: 999px;
        background: #dbeafe;
        color: #2563eb;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 26px;
        margin-bottom: 14px;
    }
</style>

@endsection
