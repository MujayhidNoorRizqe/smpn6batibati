{{-- penjelasan: Halaman ini menampilkan detail nilai satu murid. --}}
{{-- penjelasan: Tampilan dibuat seperti ringkasan rapor. --}}
{{-- penjelasan: Admin/Super Admin hanya melihat data, tidak bisa input atau edit nilai. --}}

@extends('admin.layouts.app')

@section('title', 'Detail Nilai Murid')

@section('content')

@php
    $queryString = request()->getQueryString();
@endphp

<div class="page-content">
    <div class="page-header-card">
        <div>
            <h1>Detail Nilai Murid</h1>
            <p>Nilai ujian semester {{ $murid->nama_murid }} dalam format rekap rapor.</p>
        </div>

        <a
            href="{{ route($routePrefix . '.nilai.kelas', $murid->kelas_id) . ($queryString ? '?' . $queryString : '') }}"
            class="btn btn-outline"
        >
            <i class="bi bi-arrow-left me-1"></i>
            Kembali ke List Murid
        </a>
    </div>

    <div class="report-card">
        <div class="report-header">
            <div>
                <h2>REKAP NILAI UJIAN SEMESTER</h2>
                <p>SMPN 6 Bati-Bati</p>
            </div>

            <div class="report-period">
                <div>
                    <span>Tahun Ajaran</span>
                    <strong>{{ $tahunAjaranDipilih->nama_tahun_ajaran ?? $tahunAjaranDipilih->tahun_ajaran ?? 'Semua Tahun Ajaran' }}</strong>
                </div>

                <div>
                    <span>Semester</span>
                    <strong>{{ $semesterDipilih->nama_semester_label ?? $semesterDipilih->nama_semester ?? 'Semua Semester' }}</strong>
                </div>
            </div>
        </div>

        <div class="identity-grid">
            <div>
                <span>Nama Murid</span>
                <strong>{{ $murid->nama_murid }}</strong>
            </div>

            <div>
                <span>NIS/NISN</span>
                <strong>{{ $murid->nis ?? $murid->nisn ?? '-' }}</strong>
            </div>

            <div>
                <span>Kelas</span>
                <strong>{{ $murid->kelas?->nama_kelas ?? '-' }}</strong>
            </div>

            <div>
                <span>Wali Kelas</span>
                <strong>{{ $murid->kelas?->waliKelas?->nama_pegawai ?? '-' }}</strong>
            </div>

            <div>
                <span>Wali Murid</span>
                <strong>{{ $murid->waliMurid?->nama_wali ?? '-' }}</strong>
            </div>

            <div>
                <span>Mode Akses</span>
                <strong>Lihat Nilai</strong>
            </div>
        </div>
    </div>

    <div class="summary-grid">
        <div class="summary-card">
            <span>Total Nilai</span>
            <strong>{{ $ringkasan['total_nilai'] }}</strong>
        </div>

        <div class="summary-card">
            <span>Total Mapel</span>
            <strong>{{ $ringkasan['total_mapel'] }}</strong>
        </div>

        <div class="summary-card">
            <span>Nilai Terendah</span>
            <strong>{{ $ringkasan['nilai_terendah'] ?? '-' }}</strong>
        </div>

        <div class="summary-card">
            <span>Nilai Tertinggi</span>
            <strong>{{ $ringkasan['nilai_tertinggi'] ?? '-' }}</strong>
        </div>

        <div class="summary-card">
            <span>Rata-rata</span>
            <strong>{{ $ringkasan['rata_rata'] ?? '-' }}</strong>
        </div>

        <div class="summary-card">
            <span>Predikat</span>
            <strong>{{ $ringkasan['predikat'] }}</strong>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <div>
                <h2>Capaian Nilai</h2>
                <p>Daftar nilai ujian semester berdasarkan mata pelajaran.</p>
            </div>

            <span class="badge-count">
                {{ $nilaiMurids->count() }} nilai
            </span>
        </div>

        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 60px;">No</th>
                        <th>Mata Pelajaran</th>
                        <th style="width: 120px;">Nilai</th>
                        <th style="width: 160px;">Predikat</th>
                        <th>Deskripsi / Keterangan</th>
                        <th>Guru Input</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($nilaiMurids as $nilai)
                        <tr>
                            <td>{{ $loop->iteration }}</td>

                            <td>
                                <strong>{{ $nilai->mataPelajaran?->nama_mapel ?? '-' }}</strong>
                                <div class="small-muted">
                                    {{ $nilai->mataPelajaran?->kode_mapel ?? '-' }}
                                </div>
                            </td>

                            <td>
                                <strong>{{ $nilai->nilai_ujian }}</strong>
                            </td>

                            <td>
                                <span class="predicate-badge">
                                    {{ $nilai->predikat }} - {{ $nilai->keterangan_predikat }}
                                </span>
                            </td>

                            <td>
                                {{ $nilai->keterangan ?? $nilai->keterangan_predikat }}
                            </td>

                            <td>
                                {{ $nilai->pegawai?->nama_pegawai ?? '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="empty-row">
                                Belum ada data nilai sesuai filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="final-summary">
            <div>
                <span>Rata-rata Akhir</span>
                <strong>{{ $ringkasan['rata_rata'] ?? '-' }}</strong>
            </div>

            <div>
                <span>Predikat Akhir</span>
                <strong>{{ $ringkasan['predikat'] }} - {{ $ringkasan['keterangan_predikat'] }}</strong>
            </div>
        </div>
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
    .report-card {
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
    .card h2,
    .report-header h2 {
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
        align-items: flex-start;
        gap: 18px;
        border-bottom: 2px solid #0f172a;
        padding-bottom: 18px;
        margin-bottom: 18px;
    }

    .report-period {
        display: flex;
        flex-direction: column;
        gap: 8px;
        text-align: right;
    }

    .report-period div {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .report-period span,
    .identity-grid span,
    .summary-card span,
    .final-summary span {
        color: #64748b;
        font-size: 13px;
    }

    .report-period strong,
    .identity-grid strong {
        color: #0f172a;
    }

    .identity-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 14px;
    }

    .identity-grid div {
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 12px;
        background: #f8fafc;
        display: flex;
        flex-direction: column;
        gap: 4px;
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

    .badge-count {
        background: #dbeafe;
        color: #2563eb;
        border-radius: 8px;
        padding: 6px 10px;
        font-weight: 800;
        font-size: 13px;
        white-space: nowrap;
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
        font-weight: 800;
    }

    .small-muted {
        color: #64748b;
        font-size: 13px;
        margin-top: 4px;
    }

    .predicate-badge {
        display: inline-flex;
        background: #eff6ff;
        color: #1d4ed8;
        border-radius: 8px;
        padding: 6px 10px;
        font-weight: 800;
        font-size: 13px;
    }

    .empty-row {
        text-align: center;
        color: #64748b;
        padding: 28px !important;
    }

    .final-summary {
        margin-top: 18px;
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px;
    }

    .final-summary div {
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 14px;
        background: #f8fafc;
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .final-summary strong {
        color: #0f172a;
        font-size: 20px;
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

    .btn-outline {
        background: #ffffff;
        color: #475569;
        border-color: #cbd5e1;
    }

    @media (max-width: 1200px) {
        .summary-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .identity-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 800px) {
        .page-header-card,
        .card-header,
        .report-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .report-period {
            text-align: left;
        }

        .summary-grid,
        .identity-grid,
        .final-summary {
            grid-template-columns: 1fr;
        }
    }
</style>

@endsection
