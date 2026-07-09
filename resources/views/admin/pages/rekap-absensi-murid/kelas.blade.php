{{-- penjelasan: Halaman ini menampilkan list murid berdasarkan kelas yang dipilih. --}}
{{-- penjelasan: Halaman ini hanya untuk admin dan super admin melihat rekap, bukan mengedit. --}}
{{-- penjelasan: Klik murid untuk melihat riwayat absensi murid tersebut. --}}

@extends('admin.layouts.app')

@section('title', 'Rekap Absensi Murid per Kelas')

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
            <h1>Rekap Kelas {{ $kelas->nama_kelas }}</h1>
            <p>List murid dalam kelas {{ $kelas->nama_kelas }}. Klik murid untuk melihat riwayat absensi.</p>
        </div>

        <a
            href="{{ route($routePrefix . '.rekap-absensi-murid.index') . ($queryString ? '?' . $queryString : '') }}"
            class="btn btn-outline"
        >
            <i class="bi bi-arrow-left me-1"></i>
            Kembali
        </a>
    </div>

    <div class="info-grid">
        <div>
            <span>Kelas</span>
            <strong>{{ $kelas->nama_kelas }}</strong>
        </div>

        <div>
            <span>Tingkat</span>
            <strong>{{ $kelas->tingkat ?? '-' }}</strong>
        </div>

        <div>
            <span>Wali Kelas</span>
            <strong>{{ $kelas->waliKelas?->nama_pegawai ?? '-' }}</strong>
        </div>

        <div>
            <span>Total Murid</span>
            <strong>{{ $rekapPerMurid->count() }}</strong>
        </div>
    </div>

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
                <h2>Daftar Murid Kelas {{ $kelas->nama_kelas }}</h2>
                <p>Data berikut hanya bersifat lihat/monitoring untuk admin dan super admin.</p>
            </div>

            <span class="badge-count">
                {{ $rekapPerMurid->count() }} murid
            </span>
        </div>

        @if ($rekapPerMurid->isEmpty())
            <div class="empty-state">
                Belum ada murid pada kelas ini.
            </div>
        @else
            <div class="student-grid">
                @foreach ($rekapPerMurid as $rekap)
                    <a
                        href="{{ route($routePrefix . '.rekap-absensi-murid.murid', $rekap->murid->id) . ($queryString ? '?' . $queryString : '') }}"
                        class="student-card"
                    >
                        <div class="student-header">
                            <div>
                                <span>Nama Murid</span>
                                <h3>{{ $rekap->murid->nama_murid }}</h3>
                                <p>
                                    NIS/NISN:
                                    {{ $rekap->murid->nis ?? $rekap->murid->nisn ?? '-' }}
                                </p>
                            </div>

                            <span class="badge-count">
                                {{ $rekap->total_data }} data
                            </span>
                        </div>

                        <div class="student-meta">
                            <span>{{ $rekap->total_mapel }} mapel</span>
                            <span>{{ $rekap->total_guru }} guru</span>
                            <span>
                                Wali:
                                {{ $rekap->murid->waliMurid?->nama_wali ?? '-' }}
                            </span>
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

                        <div class="student-footer">
                            <span>
                                Tanggal terakhir:
                                {{ $rekap->tanggal_terakhir ? $rekap->tanggal_terakhir->format('d-m-Y') : '-' }}
                            </span>

                            <strong>
                                Lihat Riwayat
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
    .summary-card,
    .info-grid {
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

    .info-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 14px;
    }

    .info-grid div {
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 12px;
        background: #f8fafc;
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .info-grid span,
    .summary-card span,
    .student-header span,
    .student-meta span,
    .status-grid span,
    .student-footer span {
        color: #64748b;
        font-size: 13px;
    }

    .info-grid strong {
        color: #0f172a;
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

    .student-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 16px;
    }

    .student-card {
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

    .student-card:hover {
        border-color: #2563eb;
        background: #eff6ff;
        transform: translateY(-2px);
    }

    .student-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 12px;
    }

    .student-header h3 {
        margin: 3px 0;
        color: #0f172a;
        font-size: 22px;
        font-weight: 900;
    }

    .student-header p {
        margin: 0;
        color: #64748b;
        font-size: 13px;
    }

    .student-meta {
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

    .student-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        border-top: 1px solid #e2e8f0;
        padding-top: 12px;
    }

    .student-footer strong {
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

    @media (max-width: 1200px) {
        .summary-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .info-grid,
        .student-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .status-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }
    }

    @media (max-width: 800px) {
        .page-header-card,
        .card-header,
        .student-footer {
            flex-direction: column;
            align-items: flex-start;
        }

        .summary-grid,
        .info-grid,
        .student-grid,
        .status-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

@endsection
