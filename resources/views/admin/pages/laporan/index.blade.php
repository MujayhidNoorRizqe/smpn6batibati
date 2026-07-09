{{-- penjelasan: Halaman awal modul laporan. --}}
{{-- penjelasan: Tidak ada filter di halaman awal sesuai arahan. --}}
{{-- penjelasan: Halaman ini hanya menampilkan tiga pilihan laporan: Absensi Guru, Absensi Murid, dan Laporan Nilai. --}}

@extends('admin.layouts.app')

@section('title', 'Laporan')

@section('content')

<div class="page-content">
    <div class="page-header-card">
        <div>
            <h1>Laporan</h1>
            <p>Pilih jenis laporan terlebih dahulu. Filter periode berada setelah memilih guru atau murid.</p>
        </div>
    </div>

    <div class="report-type-grid">
        <a href="{{ route($routePrefix . '.laporan.daftar', 'absensi_guru') }}" class="report-type-card">
            <div class="report-type-icon">
                <i class="bi bi-fingerprint"></i>
            </div>

            <div>
                <h2>Absensi Guru</h2>
                <p>Laporan absensi guru per nama, lalu pilih periode tahun ajaran, semester, atau minggu.</p>

                <span class="badge-count">
                    {{ $totalGuru }} guru aktif
                </span>
            </div>
        </a>

        <a href="{{ route($routePrefix . '.laporan.daftar', 'absensi_murid') }}" class="report-type-card">
            <div class="report-type-icon">
                <i class="bi bi-person-check"></i>
            </div>

            <div>
                <h2>Absensi Murid</h2>
                <p>Laporan absensi murid dimulai dari kelas, lalu pilih murid dan periode laporan.</p>

                <span class="badge-count">
                    {{ $totalKelas }} kelas aktif
                </span>
            </div>
        </a>

        <a href="{{ route($routePrefix . '.laporan.daftar', 'nilai') }}" class="report-type-card">
            <div class="report-type-icon">
                <i class="bi bi-journal-text"></i>
            </div>

            <div>
                <h2>Laporan Nilai</h2>
                <p>Laporan nilai dimulai dari kelas per tingkat, lalu pilih murid dan periode laporan.</p>

                <span class="badge-count">
                    {{ $totalMurid }} murid aktif
                </span>
            </div>
        </a>
    </div>
</div>

<style>
    .page-content {
        display: flex;
        flex-direction: column;
        gap: 18px;
    }

    .page-header-card,
    .report-type-card {
        background: #ffffff;
        border-radius: 16px;
        padding: 22px;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
    }

    .page-header-card h1 {
        margin: 0;
        color: #0f172a;
        font-size: 34px;
        font-weight: 900;
    }

    .page-header-card p {
        margin: 6px 0 0;
        color: #64748b;
    }

    .report-type-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 18px;
    }

    .report-type-card {
        border: 1px solid #e2e8f0;
        color: inherit;
        text-decoration: none;
        display: flex;
        align-items: flex-start;
        gap: 16px;
        transition: 0.2s ease;
        min-height: 210px;
    }

    .report-type-card:hover {
        border-color: #2563eb;
        background: #eff6ff;
        transform: translateY(-2px);
    }

    .report-type-icon {
        width: 54px;
        height: 54px;
        border-radius: 16px;
        background: #dbeafe;
        color: #2563eb;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 25px;
        flex: 0 0 auto;
    }

    .report-type-card h2 {
        margin: 0;
        color: #0f172a;
        font-size: 22px;
        font-weight: 900;
    }

    .report-type-card p {
        margin: 8px 0 14px;
        color: #64748b;
        line-height: 1.6;
    }

    .badge-count {
        background: #dbeafe;
        color: #2563eb;
        border-radius: 8px;
        padding: 6px 10px;
        font-weight: 800;
        font-size: 13px;
        display: inline-flex;
    }

    @media (max-width: 1100px) {
        .report-type-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 700px) {
        .report-type-grid {
            grid-template-columns: 1fr;
        }

        .report-type-card {
            flex-direction: column;
        }
    }
</style>

@endsection
