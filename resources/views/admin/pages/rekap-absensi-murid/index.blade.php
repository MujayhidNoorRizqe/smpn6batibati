{{-- penjelasan: Halaman ini digunakan admin dan super admin untuk melihat rekap absensi murid. --}}
{{-- penjelasan: Data absensi murid berasal dari input yang dilakukan oleh guru. --}}
{{-- penjelasan: Halaman awal menampilkan rekap absensi murid per kelas. --}}
{{-- penjelasan: Ketika kelas diklik, admin/super admin akan masuk ke list murid per kelas. --}}
{{-- penjelasan: Admin dan super admin hanya bisa melihat data, tidak bisa mengedit absensi murid. --}}

@extends('admin.layouts.app')

@section('title', 'Rekap Absensi Murid')

@section('content')

@php
    $queryString = request()->getQueryString();

    $statusSummary = [
        ['label' => 'Total Data', 'value' => $totalData],
        ['label' => 'Hadir', 'value' => $statusCounts['hadir'] ?? 0],
        ['label' => 'Izin', 'value' => $statusCounts['izin'] ?? 0],
        ['label' => 'Sakit', 'value' => $statusCounts['sakit'] ?? 0],
        ['label' => 'Alpha', 'value' => ($statusCounts['alpha'] ?? 0) + ($statusCounts['alpa'] ?? 0)],
        ['label' => 'Terlambat', 'value' => $statusCounts['terlambat'] ?? 0],
    ];
@endphp

<div class="page-content">
    <div class="page-header-card">
        <div>
            <h1>Rekap Absensi Murid</h1>
            <p>Rekap absensi murid ditampilkan per kelas. Klik kelas untuk melihat daftar murid.</p>
        </div>

        <div class="header-date">
            <strong>{{ now()->format('d-m-Y') }}</strong>
            <span>Tanggal sistem hari ini</span>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Filter belum valid.</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="summary-grid">
        @foreach ($statusSummary as $item)
            <div class="summary-card">
                <span>{{ $item['label'] }}</span>
                <strong>{{ $item['value'] }}</strong>
            </div>
        @endforeach
    </div>

    <div class="card">
        <div class="card-header">
            <div>
                <h2>Filter Rekap</h2>
                <p>Gunakan filter berdasarkan tanggal, kelas, mata pelajaran, dan guru.</p>
            </div>
        </div>

        <form action="{{ route($routePrefix . '.rekap-absensi-murid.index') }}" method="GET" class="filter-grid">
            <div class="form-group">
                <label>Tanggal Mulai</label>
                <input
                    type="date"
                    name="tanggal_mulai"
                    value="{{ request('tanggal_mulai') }}"
                >
            </div>

            <div class="form-group">
                <label>Tanggal Selesai</label>
                <input
                    type="date"
                    name="tanggal_selesai"
                    value="{{ request('tanggal_selesai') }}"
                >
            </div>

            <div class="form-group">
                <label>Kelas</label>
                <select name="kelas_id">
                    <option value="">Semua Kelas</option>
                    @foreach ($kelasList as $kelas)
                        <option value="{{ $kelas->id }}" @selected((string) request('kelas_id') === (string) $kelas->id)>
                            {{ $kelas->nama_kelas }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>Mata Pelajaran</label>
                <select name="mata_pelajaran_id">
                    <option value="">Semua Mata Pelajaran</option>
                    @foreach ($mataPelajaranList as $mataPelajaran)
                        <option value="{{ $mataPelajaran->id }}" @selected((string) request('mata_pelajaran_id') === (string) $mataPelajaran->id)>
                            {{ $mataPelajaran->nama_mapel }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>Guru</label>
                <select name="guru_id">
                    <option value="">Semua Guru</option>
                    @foreach ($guruList as $guru)
                        <option value="{{ $guru->id }}" @selected((string) request('guru_id') === (string) $guru->id)>
                            {{ $guru->nama_pegawai }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="filter-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-search me-1"></i>
                    Filter
                </button>

                <a href="{{ route($routePrefix . '.rekap-absensi-murid.index') }}" class="btn btn-outline">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <div class="card">
        <div class="card-header">
            <div>
                <h2>Rekap Absensi Murid per Kelas</h2>
                <p>Klik salah satu kelas untuk melihat daftar murid di kelas tersebut.</p>
            </div>

            <span class="badge-count">
                {{ $rekapPerKelas->count() }} kelas ditemukan
            </span>
        </div>

        @if ($rekapPerKelas->isEmpty())
            <div class="empty-state">
                Data rekap absensi murid belum tersedia sesuai filter.
            </div>
        @else
            <div class="class-grid">
                @foreach ($rekapPerKelas as $rekap)
                    <a
                        href="{{ route($routePrefix . '.rekap-absensi-murid.kelas', $rekap->kelas_id) . ($queryString ? '?' . $queryString : '') }}"
                        class="class-card"
                    >
                        <div class="class-card-header">
                            <div>
                                <span>Kelas</span>
                                <h3>{{ $rekap->kelas?->nama_kelas ?? '-' }}</h3>
                            </div>

                            <span class="badge-count">
                                {{ $rekap->total_data }} data
                            </span>
                        </div>

                        <div class="class-meta">
                            <span>{{ $rekap->total_murid }} murid</span>
                            <span>{{ $rekap->total_mapel }} mapel</span>
                            <span>{{ $rekap->total_guru }} guru</span>
                        </div>

                        <div class="status-grid">
                            <div>
                                <span>Hadir</span>
                                <strong>{{ $rekap->hadir }}</strong>
                            </div>

                            <div>
                                <span>Izin</span>
                                <strong>{{ $rekap->izin }}</strong>
                            </div>

                            <div>
                                <span>Sakit</span>
                                <strong>{{ $rekap->sakit }}</strong>
                            </div>

                            <div>
                                <span>Alpha</span>
                                <strong>{{ $rekap->alpha }}</strong>
                            </div>

                            <div>
                                <span>Terlambat</span>
                                <strong>{{ $rekap->terlambat }}</strong>
                            </div>
                        </div>

                        <div class="class-footer">
                            <span>
                                Tanggal terakhir:
                                {{ $rekap->tanggal_terakhir ? $rekap->tanggal_terakhir->format('d-m-Y') : '-' }}
                            </span>

                            <strong>
                                Lihat Murid
                                <i class="bi bi-arrow-right"></i>
                            </strong>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</div>

<style>
    .page-content {
        display: flex;
        flex-direction: column;
        gap: 18px;
    }

    .page-header-card,
    .card,
    .summary-card {
        background: #ffffff;
        border-radius: 16px;
        padding: 22px;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
    }

    .page-header-card {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
    }

    .page-header-card h1,
    .card h2 {
        margin: 0;
        color: #0f172a;
    }

    .page-header-card p,
    .card-header p {
        margin: 6px 0 0;
        color: #64748b;
    }

    .header-date {
        text-align: right;
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .header-date span {
        color: #64748b;
        font-size: 13px;
    }

    .alert {
        border-radius: 12px;
        padding: 14px 16px;
    }

    .alert-danger {
        background: #fee2e2;
        color: #991b1b;
    }

    .summary-grid {
        display: grid;
        grid-template-columns: repeat(6, minmax(0, 1fr));
        gap: 14px;
    }

    .summary-card {
        padding: 18px;
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .summary-card span {
        color: #64748b;
        font-size: 13px;
    }

    .summary-card strong {
        color: #0f172a;
        font-size: 26px;
        line-height: 1;
    }

    .card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 14px;
        margin-bottom: 18px;
    }

    .filter-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 16px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    label {
        font-weight: 700;
        color: #0f172a;
    }

    input,
    select {
        width: 100%;
        border: 1px solid #dbe3ef;
        border-radius: 10px;
        padding: 11px 12px;
        outline: none;
        background: #ffffff;
    }

    input:focus,
    select:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
    }

    .filter-actions {
        display: flex;
        align-items: end;
        gap: 10px;
    }

    .class-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 16px;
    }

    .class-card {
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 18px;
        background: #f8fafc;
        color: inherit;
        text-decoration: none;
        display: flex;
        flex-direction: column;
        gap: 14px;
        transition: 0.2s ease;
    }

    .class-card:hover {
        border-color: #2563eb;
        background: #eff6ff;
        transform: translateY(-2px);
    }

    .class-card-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 12px;
    }

    .class-card-header span,
    .class-meta span,
    .status-grid span,
    .class-footer span {
        color: #64748b;
        font-size: 13px;
    }

    .class-card-header h3 {
        margin: 3px 0 0;
        color: #0f172a;
        font-size: 30px;
        font-weight: 900;
    }

    .class-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .status-grid {
        display: grid;
        grid-template-columns: repeat(5, minmax(0, 1fr));
        gap: 8px;
    }

    .status-grid div {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 10px;
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .status-grid strong {
        color: #0f172a;
        font-size: 18px;
    }

    .class-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        border-top: 1px solid #e2e8f0;
        padding-top: 12px;
    }

    .class-footer strong {
        color: #2563eb;
    }

    .badge-count {
        background: #dbeafe;
        color: #2563eb;
        border-radius: 8px;
        padding: 6px 10px;
        font-weight: 800;
        font-size: 13px;
        white-space: nowrap;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        padding: 11px 18px;
        font-weight: 700;
        text-decoration: none;
        border: 1px solid transparent;
        cursor: pointer;
        white-space: nowrap;
    }

    .btn-primary {
        background: #2563eb;
        color: #ffffff;
    }

    .btn-outline {
        background: #ffffff;
        color: #475569;
        border-color: #cbd5e1;
    }

    .empty-state {
        padding: 28px;
        text-align: center;
        color: #64748b;
        border: 1px dashed #cbd5e1;
        border-radius: 12px;
        background: #f8fafc;
    }

    .mb-0 {
        margin-bottom: 0;
    }

    .mt-2 {
        margin-top: 8px;
    }

    @media (max-width: 1200px) {
        .summary-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .filter-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .status-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }
    }

    @media (max-width: 800px) {
        .page-header-card,
        .card-header,
        .class-footer {
            flex-direction: column;
            align-items: flex-start;
        }

        .header-date {
            text-align: left;
        }

        .summary-grid,
        .filter-grid,
        .class-grid,
        .status-grid {
            grid-template-columns: 1fr;
        }

        .filter-actions {
            flex-direction: column;
            align-items: stretch;
        }
    }
</style>

@endsection
