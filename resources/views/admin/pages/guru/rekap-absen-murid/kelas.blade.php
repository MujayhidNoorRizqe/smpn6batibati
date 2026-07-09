@extends('admin.layouts.app')

@section('title', 'List Murid Rekap Absen')

@section('content')
<div class="page-content">
    <div class="page-header-card">
        <div>
            <h1>List Murid Kelas {{ $kelas->nama_kelas }}</h1>
            <p>Pilih murid untuk melihat riwayat absensi murid tersebut.</p>
        </div>

        <a
            href="{{ route('guru.rekap-absen-murid.index', [
                'tahun_ajaran_id' => $tahunAjaran->id,
                'semester_id' => $semester->id,
                'kelas_id' => $kelas->id,
            ]) }}"
            class="btn btn-outline"
        >
            Kembali
        </a>
    </div>

    <div class="report-card">
        <div class="report-header">
            <div>
                <h2>REKAP ABSENSI MURID PER KELAS</h2>
                <p>SMPN 6 Bati-Bati</p>
            </div>

            <div class="report-meta">
                <span>Kelas: <strong>{{ $kelas->nama_kelas }}</strong></span>
                <span>Tahun Ajaran: <strong>{{ $tahunAjaran->nama_tahun_ajaran ?? $tahunAjaran->tahun_ajaran ?? '-' }}</strong></span>
                <span>Semester: <strong>{{ $semester->nama_semester_label ?? $semester->nama_semester ?? $semester->semester ?? '-' }}</strong></span>
            </div>
        </div>

        <div class="summary-grid">
            <div class="summary-card">
                <span>Total Murid</span>
                <strong>{{ $murids->count() }}</strong>
            </div>

            <div class="summary-card">
                <span>Total Jadwal Guru</span>
                <strong>{{ $jadwalIds->count() }}</strong>
            </div>

            <div class="summary-card">
                <span>Wali Kelas</span>
                <strong>{{ $kelas->waliKelas->nama_pegawai ?? '-' }}</strong>
            </div>

            <div class="summary-card">
                <span>Guru Login</span>
                <strong>{{ $guru->nama_pegawai ?? auth()->user()->name }}</strong>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <div>
                <h2>Daftar Murid</h2>
                <p>Klik tombol riwayat untuk melihat detail absensi murid.</p>
            </div>

            <span class="badge">{{ $murids->count() }} murid</span>
        </div>

        @if ($murids->isEmpty())
            <div class="empty-state">
                Belum ada murid aktif pada kelas ini.
            </div>
        @else
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th style="width: 60px;">No</th>
                            <th>NIS</th>
                            <th>Nama Murid</th>
                            <th>Total</th>
                            <th>Hadir</th>
                            <th>Izin</th>
                            <th>Sakit</th>
                            <th>Alpha</th>
                            <th>Terlambat</th>
                            <th style="width: 160px;">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($murids as $murid)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $murid->nis ?? '-' }}</td>
                                <td>
                                    <strong>{{ $murid->nama_murid }}</strong>
                                </td>
                                <td>{{ $murid->total_absensi }}</td>
                                <td>{{ $murid->hadir }}</td>
                                <td>{{ $murid->izin }}</td>
                                <td>{{ $murid->sakit }}</td>
                                <td>{{ $murid->alpha }}</td>
                                <td>{{ $murid->terlambat }}</td>
                                <td>
                                    <a
                                        href="{{ route('guru.rekap-absen-murid.murid', [
                                            'murid' => $murid->id,
                                            'kelas_id' => $kelas->id,
                                            'tahun_ajaran_id' => $tahunAjaran->id,
                                            'semester_id' => $semester->id,
                                        ]) }}"
                                        class="btn btn-primary btn-sm"
                                    >
                                        Riwayat
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
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
    .report-card {
        background: #ffffff;
        border-radius: 16px;
        padding: 22px;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
    }

    .page-header-card {
        display: flex;
        justify-content: space-between;
        gap: 16px;
        align-items: center;
    }

    .page-header-card h1,
    .card h2,
    .report-card h2 {
        margin: 0;
        color: #0f172a;
    }

    .page-header-card p,
    .card-header p,
    .report-header p {
        margin: 6px 0 0;
        color: #64748b;
    }

    .report-header {
        display: flex;
        justify-content: space-between;
        gap: 16px;
        border-bottom: 2px solid #0f172a;
        padding-bottom: 16px;
        margin-bottom: 18px;
    }

    .report-meta {
        display: flex;
        flex-direction: column;
        gap: 5px;
        color: #475569;
        text-align: right;
    }

    .summary-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 14px;
    }

    .summary-card {
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 14px;
        background: #f8fafc;
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
    }

    .card-header {
        display: flex;
        justify-content: space-between;
        gap: 14px;
        align-items: center;
        margin-bottom: 18px;
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

    .badge {
        background: #dbeafe;
        color: #2563eb;
        border-radius: 8px;
        padding: 6px 10px;
        font-weight: 700;
    }

    .table-responsive {
        overflow-x: auto;
    }

    .data-table {
        width: 100%;
        border-collapse: collapse;
    }

    .data-table th,
    .data-table td {
        border-bottom: 1px solid #e2e8f0;
        padding: 13px 12px;
        text-align: left;
        vertical-align: middle;
    }

    .data-table th {
        background: #f8fafc;
        color: #0f172a;
        font-weight: 700;
    }

    .empty-state {
        padding: 28px;
        text-align: center;
        color: #64748b;
        border: 1px dashed #cbd5e1;
        border-radius: 12px;
        background: #f8fafc;
    }

    @media (max-width: 900px) {
        .page-header-card,
        .report-header,
        .card-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .report-meta {
            text-align: left;
        }

        .summary-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection
