@extends('admin.layouts.app')

@section('title', 'Jadwal Mengajar')

@section('content')
<div class="page-content">
    <div class="page-header-card">
        <div>
            <h1>Jadwal Mengajar</h1>
            <p>Daftar jadwal mengajar berdasarkan kelas, mata pelajaran, tahun ajaran, dan semester.</p>
        </div>
    </div>

    <div class="summary-grid">
        <div class="summary-card">
            <span>Total Jadwal</span>
            <strong>{{ $statistik['total_jadwal'] }}</strong>
        </div>

        <div class="summary-card">
            <span>Jadwal Aktif</span>
            <strong>{{ $statistik['total_jadwal_aktif'] }}</strong>
        </div>

        <div class="summary-card">
            <span>Total Kelas</span>
            <strong>{{ $statistik['total_kelas'] }}</strong>
        </div>

        <div class="summary-card">
            <span>Total Mapel</span>
            <strong>{{ $statistik['total_mapel'] }}</strong>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <div>
                <h2>Filter Jadwal Mengajar</h2>
                <p>Gunakan filter untuk melihat jadwal berdasarkan periode, kelas, mapel, hari, dan status.</p>
            </div>
        </div>

        <form method="GET" action="{{ route('guru.jadwal-mengajar.index') }}" class="filter-grid">
            <div class="form-group">
                <label>Tahun Ajaran</label>
                <select name="tahun_ajaran_id">
                    <option value="">Semua Tahun Ajaran</option>
                    @foreach ($tahunAjarans as $tahunAjaran)
                        <option value="{{ $tahunAjaran->id }}" @selected((string) request('tahun_ajaran_id') === (string) $tahunAjaran->id)>
                            {{ $tahunAjaran->nama_tahun_ajaran ?? $tahunAjaran->tahun_ajaran ?? '-' }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>Semester</label>
                <select name="semester_id">
                    <option value="">Semua Semester</option>
                    @foreach ($semesters as $semester)
                        <option value="{{ $semester->id }}" @selected((string) request('semester_id') === (string) $semester->id)>
                            {{ $semester->nama_semester_label ?? $semester->nama_semester ?? $semester->semester ?? '-' }}
                            @if ($semester->tahunAjaran)
                                - {{ $semester->tahunAjaran->nama_tahun_ajaran ?? $semester->tahunAjaran->tahun_ajaran ?? '-' }}
                            @endif
                        </option>
                    @endforeach
                </select>
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
                    @foreach ($mataPelajarans as $mapel)
                        <option value="{{ $mapel->id }}" @selected((string) request('mata_pelajaran_id') === (string) $mapel->id)>
                            {{ $mapel->nama_mapel }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>Hari</label>
                <select name="hari">
                    <option value="">Semua Hari</option>
                    <option value="senin" @selected(request('hari') === 'senin')>Senin</option>
                    <option value="selasa" @selected(request('hari') === 'selasa')>Selasa</option>
                    <option value="rabu" @selected(request('hari') === 'rabu')>Rabu</option>
                    <option value="kamis" @selected(request('hari') === 'kamis')>Kamis</option>
                    <option value="jumat" @selected(request('hari') === 'jumat')>Jumat</option>
                    <option value="sabtu" @selected(request('hari') === 'sabtu')>Sabtu</option>
                </select>
            </div>

            <div class="form-group">
                <label>Status</label>
                <select name="status">
                    <option value="">Semua Status</option>
                    <option value="aktif" @selected(request('status') === 'aktif')>Aktif</option>
                    <option value="nonaktif" @selected(request('status') === 'nonaktif')>Nonaktif</option>
                </select>
            </div>

            <div class="filter-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-search me-1"></i>
                    Filter
                </button>

                <a href="{{ route('guru.jadwal-mengajar.index') }}" class="btn btn-outline">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <div class="card">
        <div class="card-header">
            <div>
                <h2>Daftar Jadwal Mengajar</h2>
                <p>Data yang tampil hanya jadwal milik guru login.</p>
            </div>

            <span class="badge">{{ $jadwalMengajars->total() }} jadwal</span>
        </div>

        @if ($jadwalMengajars->isEmpty())
            <div class="empty-state">
                Belum ada jadwal mengajar yang sesuai dengan filter.
            </div>
        @else
            <div class="schedule-grid">
                @foreach ($jadwalMengajars as $jadwal)
                    <div class="schedule-card">
                        <div class="schedule-top">
                            <div>
                                <span class="day-label">{{ $jadwal->hari_label }}</span>
                                <h3>{{ $jadwal->jam_pelajaran }}</h3>
                            </div>

                            @if ($jadwal->status === 'aktif')
                                <span class="badge-success">Aktif</span>
                            @else
                                <span class="badge-danger">Nonaktif</span>
                            @endif
                        </div>

                        <div class="schedule-main">
                            <div class="mapel-title">
                                {{ $jadwal->mataPelajaran->nama_mapel ?? '-' }}
                            </div>

                            <div class="mapel-code">
                                {{ $jadwal->mataPelajaran->kode_mapel ?? '-' }}
                            </div>
                        </div>

                        <div class="schedule-info-grid">
                            <div>
                                <span>Kelas</span>
                                <strong>{{ $jadwal->kelas->nama_kelas ?? '-' }}</strong>
                            </div>

                            <div>
                                <span>Tahun Ajaran</span>
                                <strong>{{ $jadwal->tahunAjaran->nama_tahun_ajaran ?? $jadwal->tahunAjaran->tahun_ajaran ?? '-' }}</strong>
                            </div>

                            <div>
                                <span>Semester</span>
                                <strong>{{ $jadwal->semester->nama_semester_label ?? $jadwal->semester->nama_semester ?? $jadwal->semester->semester ?? '-' }}</strong>
                            </div>

                            <div>
                                <span>Guru</span>
                                <strong>{{ $jadwal->guru->nama_pegawai ?? $guru->nama_pegawai ?? '-' }}</strong>
                            </div>
                        </div>

                        <div class="schedule-actions">
                            @if ($jadwal->status === 'aktif')
                                <a
                                    href="{{ route('guru.absensi-murid.create', $jadwal->id) }}"
                                    class="btn btn-primary btn-sm"
                                >
                                    <i class="bi bi-clipboard-check me-1"></i>
                                    Absen Murid
                                </a>
                            @else
                                <button type="button" class="btn btn-outline btn-sm" disabled>
                                    Jadwal Nonaktif
                                </button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="pagination-wrapper">
                {{ $jadwalMengajars->links() }}
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
    .summary-card,
    .schedule-card {
        background: #ffffff;
        border-radius: 16px;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
    }

    .page-header-card,
    .card {
        padding: 22px;
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

    .summary-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 16px;
    }

    .summary-card {
        padding: 18px;
        display: flex;
        flex-direction: column;
        gap: 8px;
        border: 1px solid #e2e8f0;
    }

    .summary-card span {
        color: #64748b;
        font-size: 13px;
    }

    .summary-card strong {
        color: #0f172a;
        font-size: 26px;
    }

    .card-header {
        display: flex;
        justify-content: space-between;
        gap: 14px;
        align-items: center;
        margin-bottom: 18px;
    }

    .filter-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 16px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    label {
        color: #0f172a;
        font-weight: 600;
    }

    select {
        width: 100%;
        border: 1px solid #dbe3ef;
        border-radius: 10px;
        padding: 11px 12px;
        font-size: 14px;
        outline: none;
        background: #ffffff;
    }

    select:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
    }

    .filter-actions {
        display: flex;
        align-items: end;
        gap: 10px;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        padding: 11px 18px;
        font-weight: 600;
        text-decoration: none;
        border: 1px solid transparent;
        cursor: pointer;
    }

    .btn-sm {
        padding: 8px 12px;
        font-size: 13px;
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

    .btn:disabled {
        cursor: not-allowed;
        opacity: 0.65;
    }

    .badge,
    .badge-success,
    .badge-danger {
        border-radius: 8px;
        padding: 6px 10px;
        font-size: 13px;
        font-weight: 600;
        display: inline-flex;
    }

    .badge {
        background: #dbeafe;
        color: #2563eb;
    }

    .badge-success {
        background: #dcfce7;
        color: #166534;
    }

    .badge-danger {
        background: #fee2e2;
        color: #991b1b;
    }

    .schedule-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 16px;
    }

    .schedule-card {
        border: 1px solid #e2e8f0;
        padding: 18px;
    }

    .schedule-top {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 12px;
        margin-bottom: 16px;
    }

    .day-label {
        color: #2563eb;
        font-size: 14px;
        font-weight: 700;
    }

    .schedule-top h3 {
        margin: 4px 0 0;
        color: #0f172a;
        font-size: 26px;
    }

    .schedule-main {
        padding: 14px;
        border-radius: 12px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        margin-bottom: 14px;
    }

    .mapel-title {
        color: #0f172a;
        font-size: 18px;
        font-weight: 800;
    }

    .mapel-code {
        margin-top: 4px;
        color: #64748b;
        font-size: 13px;
    }

    .schedule-info-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 10px;
    }

    .schedule-info-grid div {
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 11px;
        background: #ffffff;
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .schedule-info-grid span {
        color: #64748b;
        font-size: 12px;
    }

    .schedule-info-grid strong {
        color: #0f172a;
    }

    .schedule-actions {
        display: flex;
        justify-content: flex-end;
        margin-top: 16px;
    }

    .empty-state {
        padding: 28px;
        text-align: center;
        color: #64748b;
        border: 1px dashed #cbd5e1;
        border-radius: 12px;
        background: #f8fafc;
    }

    .pagination-wrapper {
        margin-top: 18px;
    }

    @media (max-width: 1100px) {
        .summary-grid,
        .filter-grid,
        .schedule-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 700px) {
        .summary-grid,
        .filter-grid,
        .schedule-grid,
        .schedule-info-grid {
            grid-template-columns: 1fr;
        }

        .filter-actions,
        .card-header,
        .schedule-top,
        .schedule-actions {
            flex-direction: column;
            align-items: stretch;
        }
    }
</style>
@endsection
