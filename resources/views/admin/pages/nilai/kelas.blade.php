{{-- penjelasan: Halaman ini menampilkan list murid berdasarkan kelas yang dipilih. --}}
{{-- penjelasan: Admin/Super Admin hanya melihat rekap nilai, tidak bisa input atau edit nilai. --}}
{{-- penjelasan: Klik murid untuk melihat detail nilai murid seperti rapor. --}}

@extends('admin.layouts.app')

@section('title', 'Nilai per Kelas')

@section('content')

@php
    $queryString = request()->getQueryString();

    $summaryCards = [
        ['label' => 'Total Murid', 'value' => $totalMurid],
        ['label' => 'Total Data Nilai', 'value' => $totalData],
        ['label' => 'Total Mapel', 'value' => $totalMapel],
        ['label' => 'Rata-rata Kelas', 'value' => $rataRataKelas ?? '-'],
        ['label' => 'Predikat Kelas', 'value' => $rataRataKelas === null ? '-' : ($rataRataKelas >= 90 ? 'A' : ($rataRataKelas >= 80 ? 'B' : ($rataRataKelas >= 70 ? 'C' : 'D')))],
    ];
@endphp

<div class="page-content">
    <div class="page-header-card">
        <div>
            <h1>Nilai Kelas {{ $kelas->nama_kelas }}</h1>
            <p>Daftar murid dalam kelas {{ $kelas->nama_kelas }}. Klik murid untuk melihat detail nilai.</p>
        </div>

        <a
            href="{{ route($routePrefix . '.nilai.index') . ($queryString ? '?' . $queryString : '') }}"
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
            <span>Mode</span>
            <strong>Lihat Nilai</strong>
        </div>
    </div>

    <div class="summary-grid">
        @foreach ($summaryCards as $card)
            <div class="summary-card">
                <span>{{ $card['label'] }}</span>
                <strong>{{ $card['value'] }}</strong>
            </div>
        @endforeach
    </div>

    <div class="card">
        <div class="card-header">
            <div>
                <h2>Daftar Murid Kelas {{ $kelas->nama_kelas }}</h2>
                <p>Data nilai yang tampil mengikuti filter dari halaman sebelumnya.</p>
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
                        href="{{ route($routePrefix . '.nilai.murid', $rekap->murid->id) . ($queryString ? '?' . $queryString : '') }}"
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
                                {{ $rekap->total_nilai }} nilai
                            </span>
                        </div>

                        <div class="student-meta">
                            <span>{{ $rekap->total_mapel }} mapel</span>
                            <span>{{ $rekap->total_guru }} guru</span>
                            <span>Status: {{ $rekap->status_nilai }}</span>
                        </div>

                        <div class="score-grid">
                            <div>
                                <span>Terendah</span>
                                <strong>{{ $rekap->nilai_terendah ?? '-' }}</strong>
                            </div>

                            <div>
                                <span>Tertinggi</span>
                                <strong>{{ $rekap->nilai_tertinggi ?? '-' }}</strong>
                            </div>

                            <div>
                                <span>Rata-rata</span>
                                <strong>{{ $rekap->rata_rata ?? '-' }}</strong>
                            </div>

                            <div>
                                <span>Predikat</span>
                                <strong>{{ $rekap->predikat }}</strong>
                            </div>
                        </div>

                        <div class="student-footer">
                            <span>{{ $rekap->keterangan_predikat }}</span>

                            <strong>
                                Lihat Detail Nilai
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
    .score-grid span,
    .student-footer span {
        color: #64748b;
        font-size: 13px;
    }

    .info-grid strong {
        color: #0f172a;
    }

    .summary-grid {
        display: grid;
        grid-template-columns: repeat(5, minmax(0, 1fr));
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

    .score-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 8px;
    }

    .score-grid div {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 10px;
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .score-grid strong {
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

        .score-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
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
        .score-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

@endsection
