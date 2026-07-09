{{-- penjelasan: Halaman ini menampilkan riwayat absensi satu murid. --}}
{{-- penjelasan: Admin dan super admin hanya melihat data, tidak bisa mengedit. --}}

@extends('admin.layouts.app')

@section('title', 'Riwayat Absensi Murid')

@section('content')

@php
    $queryString = request()->getQueryString();

    $statusClasses = [
        'hadir' => 'bg-success-subtle text-success',
        'izin' => 'bg-info-subtle text-info',
        'sakit' => 'bg-danger-subtle text-danger',
        'alpha' => 'bg-dark-subtle text-dark',
        'alpa' => 'bg-dark-subtle text-dark',
        'terlambat' => 'bg-warning-subtle text-warning',
    ];

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
            <h1>Riwayat Absensi Murid</h1>
            <p>Riwayat absensi {{ $murid->nama_murid }}. Data hanya dapat dilihat oleh admin dan super admin.</p>
        </div>

        <a
            href="{{ route($routePrefix . '.rekap-absensi-murid.kelas', $murid->kelas_id) . ($queryString ? '?' . $queryString : '') }}"
            class="btn btn-outline"
        >
            <i class="bi bi-arrow-left me-1"></i>
            Kembali ke List Murid
        </a>
    </div>

    <div class="info-grid">
        <div>
            <span>Nama Murid</span>
            <strong>{{ $murid->nama_murid }}</strong>
        </div>

        <div>
            <span>Kelas</span>
            <strong>{{ $murid->kelas?->nama_kelas ?? '-' }}</strong>
        </div>

        <div>
            <span>NIS/NISN</span>
            <strong>{{ $murid->nis ?? $murid->nisn ?? '-' }}</strong>
        </div>

        <div>
            <span>Wali Murid</span>
            <strong>{{ $murid->waliMurid?->nama_wali ?? '-' }}</strong>
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
                <h2>Riwayat Absensi</h2>
                <p>Daftar absensi murid berdasarkan filter yang dibawa dari halaman rekap.</p>
            </div>

            <span class="badge-count">
                {{ $absensiMurids->total() }} data ditemukan
            </span>
        </div>

        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 130px;">Tanggal</th>
                        <th>Mata Pelajaran</th>
                        <th>Guru</th>
                        <th>Jadwal</th>
                        <th style="width: 120px;">Status</th>
                        <th>Keterangan</th>
                        <th style="width: 150px;">Waktu Input</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($absensiMurids as $absensi)
                        @php
                            $badgeClass = $statusClasses[$absensi->status_absen] ?? 'bg-secondary-subtle text-secondary';
                        @endphp

                        <tr>
                            <td>
                                <strong>{{ $absensi->tanggal_absen?->format('d-m-Y') ?? '-' }}</strong>
                            </td>

                            <td>
                                <strong>{{ $absensi->mataPelajaran?->nama_mapel ?? '-' }}</strong>
                                <div class="small-muted">
                                    {{ $absensi->mataPelajaran?->kode_mapel ?? '-' }}
                                </div>
                            </td>

                            <td>{{ $absensi->guru?->nama_pegawai ?? '-' }}</td>

                            <td>
                                @if ($absensi->jadwalPelajaran)
                                    <strong>{{ $absensi->jadwalPelajaran->hari_label }}</strong>
                                    <div class="small-muted">
                                        {{ $absensi->jadwalPelajaran->jam_pelajaran }}
                                    </div>
                                @else
                                    -
                                @endif
                            </td>

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
                            <td colspan="7" class="text-center text-muted py-4">
                                Riwayat absensi murid belum tersedia sesuai filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination-wrapper">
            {{ $absensiMurids->links() }}
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
    .summary-card span {
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

    .badge {
        border-radius: 8px;
        padding: 6px 10px;
        font-weight: 800;
        font-size: 13px;
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

    .pagination-wrapper {
        margin-top: 18px;
    }

    .text-center {
        text-align: center;
    }

    .text-muted {
        color: #64748b;
    }

    .py-4 {
        padding-top: 24px;
        padding-bottom: 24px;
    }

    @media (max-width: 1200px) {
        .summary-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .info-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 800px) {
        .page-header-card,
        .card-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .summary-grid,
        .info-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

@endsection
