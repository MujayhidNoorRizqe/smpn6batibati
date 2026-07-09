@extends('admin.layouts.app')

@section('title', 'Riwayat Absensi Murid')

@section('content')
<div class="page-content">
    <div class="page-header-card">
        <div>
            <h1>Riwayat Absensi Murid</h1>
            <p>Riwayat absensi murid berdasarkan kelas, semester, dan tahun ajaran yang dipilih.</p>
        </div>

        <a
            href="{{ route('guru.rekap-absen-murid.kelas', [
                'kelas' => $kelas->id,
                'tahun_ajaran_id' => $tahunAjaran->id,
                'semester_id' => $semester->id,
            ]) }}"
            class="btn btn-outline"
        >
            Kembali
        </a>
    </div>

    <div class="report-card">
        <div class="report-header">
            <div>
                <h2>RIWAYAT ABSENSI MURID</h2>
                <p>SMPN 6 Bati-Bati</p>
            </div>

            <div class="report-meta">
                <span>Kelas: <strong>{{ $kelas->nama_kelas }}</strong></span>
                <span>Tahun Ajaran: <strong>{{ $tahunAjaran->nama_tahun_ajaran ?? $tahunAjaran->tahun_ajaran ?? '-' }}</strong></span>
                <span>Semester: <strong>{{ $semester->nama_semester_label ?? $semester->nama_semester ?? $semester->semester ?? '-' }}</strong></span>
            </div>
        </div>

        <div class="identity-grid">
            <div class="identity-item">
                <span>Nama Murid</span>
                <strong>{{ $murid->nama_murid }}</strong>
            </div>

            <div class="identity-item">
                <span>NIS / NISN</span>
                <strong>{{ $murid->nis ?? '-' }} / {{ $murid->nisn ?? '-' }}</strong>
            </div>

            <div class="identity-item">
                <span>Kelas</span>
                <strong>{{ $kelas->nama_kelas }}</strong>
            </div>

            <div class="identity-item">
                <span>Wali Kelas</span>
                <strong>{{ $kelas->waliKelas->nama_pegawai ?? '-' }}</strong>
            </div>
        </div>

        <div class="summary-grid">
            <div class="summary-card">
                <span>Total Absensi</span>
                <strong>{{ $totalAbsensi }}</strong>
            </div>

            <div class="summary-card">
                <span>Hadir</span>
                <strong>{{ $ringkasanStatus['hadir'] }}</strong>
            </div>

            <div class="summary-card">
                <span>Izin</span>
                <strong>{{ $ringkasanStatus['izin'] }}</strong>
            </div>

            <div class="summary-card">
                <span>Sakit</span>
                <strong>{{ $ringkasanStatus['sakit'] }}</strong>
            </div>

            <div class="summary-card">
                <span>Alpha</span>
                <strong>{{ $ringkasanStatus['alpha'] }}</strong>
            </div>

            <div class="summary-card">
                <span>Terlambat</span>
                <strong>{{ $ringkasanStatus['terlambat'] }}</strong>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <div>
                <h2>Detail Riwayat Absensi</h2>
                <p>Data yang tampil hanya berdasarkan jadwal mengajar guru login.</p>
            </div>

            <span class="badge">{{ $riwayatAbsensi->total() }} data</span>
        </div>

        @if ($riwayatAbsensi->isEmpty())
            <div class="empty-state">
                Belum ada riwayat absensi untuk murid ini.
            </div>
        @else
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th style="width: 60px;">No</th>
                            <th>Tanggal</th>
                            <th>Hari</th>
                            <th>Jam</th>
                            <th>Mata Pelajaran</th>
                            <th>Status</th>
                            <th>Keterangan</th>
                            <th>Guru</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($riwayatAbsensi as $absen)
                            <tr>
                                <td>{{ $riwayatAbsensi->firstItem() + $loop->index }}</td>
                                <td>{{ $absen->tanggal_absen ? $absen->tanggal_absen->format('d-m-Y') : '-' }}</td>
                                <td>{{ $absen->jadwalPelajaran->hari_label ?? '-' }}</td>
                                <td>{{ $absen->jadwalPelajaran->jam_pelajaran ?? '-' }}</td>
                                <td>{{ $absen->mataPelajaran->nama_mapel ?? '-' }}</td>
                                <td>
                                    @php
                                        $status = strtolower((string) $absen->status_absen);
                                    @endphp

                                    @if ($status === 'hadir')
                                        <span class="badge-success">Hadir</span>
                                    @elseif ($status === 'izin')
                                        <span class="badge-info">Izin</span>
                                    @elseif ($status === 'sakit')
                                        <span class="badge-warning">Sakit</span>
                                    @elseif ($status === 'terlambat')
                                        <span class="badge-orange">Terlambat</span>
                                    @else
                                        <span class="badge-danger">Alpha</span>
                                    @endif
                                </td>
                                <td>{{ $absen->keterangan ?? '-' }}</td>
                                <td>{{ $absen->guru->nama_pegawai ?? $guru->nama_pegawai ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="pagination-wrapper">
                {{ $riwayatAbsensi->links() }}
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

    .identity-grid,
    .summary-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 14px;
        margin-bottom: 18px;
    }

    .summary-grid {
        grid-template-columns: repeat(6, minmax(0, 1fr));
    }

    .identity-item,
    .summary-card {
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 12px;
        background: #f8fafc;
        display: flex;
        flex-direction: column;
        gap: 5px;
    }

    .identity-item span,
    .summary-card span {
        color: #64748b;
        font-size: 13px;
    }

    .identity-item strong,
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

    .btn-outline {
        background: #ffffff;
        color: #475569;
        border-color: #cbd5e1;
    }

    .badge,
    .badge-success,
    .badge-info,
    .badge-warning,
    .badge-danger,
    .badge-orange {
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

    .badge-info {
        background: #dbeafe;
        color: #1d4ed8;
    }

    .badge-warning {
        background: #fef3c7;
        color: #92400e;
    }

    .badge-danger {
        background: #fee2e2;
        color: #991b1b;
    }

    .badge-orange {
        background: #ffedd5;
        color: #c2410c;
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

    .pagination-wrapper {
        margin-top: 18px;
    }

    @media (max-width: 1100px) {
        .identity-grid,
        .summary-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 700px) {
        .page-header-card,
        .report-header,
        .card-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .report-meta {
            text-align: left;
        }

        .identity-grid,
        .summary-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection
