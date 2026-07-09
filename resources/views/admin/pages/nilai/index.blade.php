{{-- penjelasan: Halaman ini digunakan Admin dan Super Admin untuk melihat rekap nilai. --}}
{{-- penjelasan: Admin/Super Admin hanya bisa melihat data nilai, tidak bisa input atau edit. --}}
{{-- penjelasan: Halaman awal menampilkan rekap nilai per kelas. --}}
{{-- penjelasan: Klik kelas untuk melihat list murid dalam kelas tersebut. --}}

@extends('admin.layouts.app')

@section('title', 'Nilai')

@section('content')

@php
    $queryString = request()->getQueryString();

    $summaryCards = [
        ['label' => 'Total Data Nilai', 'value' => $totalData],
        ['label' => 'Total Kelas', 'value' => $totalKelas],
        ['label' => 'Total Murid', 'value' => $totalMurid],
        ['label' => 'Total Mapel', 'value' => $totalMapel],
        ['label' => 'Rata-rata Umum', 'value' => $rataRataUmum ?? '-'],
        ['label' => 'Predikat Umum', 'value' => $rataRataUmum === null ? '-' : ($rataRataUmum >= 90 ? 'A' : ($rataRataUmum >= 80 ? 'B' : ($rataRataUmum >= 70 ? 'C' : 'D')))],
    ];
@endphp

<div class="page-content">
    <div class="page-header-card">
        <div>
            <h1>Nilai Ujian Semester</h1>
            <p>Rekap nilai ujian semester ganjil dan genap. Admin dan Super Admin hanya dapat melihat data.</p>
        </div>

        <div class="readonly-badge">
            <i class="bi bi-eye"></i>
            Mode Lihat
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
                <h2>Filter Nilai</h2>
                <p>Gunakan filter berdasarkan tahun ajaran, semester, kelas, dan mata pelajaran.</p>
            </div>
        </div>

        <form action="{{ route($routePrefix . '.nilai.index') }}" method="GET" class="filter-grid">
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
                            {{ $semester->nama_semester_label ?? $semester->nama_semester ?? '-' }}
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

            <div class="filter-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-search me-1"></i>
                    Filter
                </button>

                <a href="{{ route($routePrefix . '.nilai.index') }}" class="btn btn-outline">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <div class="card">
        <div class="card-header">
            <div>
                <h2>Rekap Nilai per Kelas</h2>
                <p>Klik kelas untuk melihat daftar murid dan detail nilai per murid.</p>
            </div>

            <span class="badge-count">
                {{ $rekapPerKelas->count() }} kelas ditemukan
            </span>
        </div>

        @if ($rekapPerKelas->isEmpty())
            <div class="empty-state">
                Belum ada data nilai sesuai filter.
            </div>
        @else
            <div class="class-grid">
                @foreach ($rekapPerKelas as $rekap)
                    <a
                        href="{{ route($routePrefix . '.nilai.kelas', $rekap->kelas_id) . ($queryString ? '?' . $queryString : '') }}"
                        class="class-card"
                    >
                        <div class="class-card-header">
                            <div>
                                <span>Kelas</span>
                                <h3>{{ $rekap->kelas?->nama_kelas ?? '-' }}</h3>
                            </div>

                            <span class="badge-count">
                                {{ $rekap->total_data }} nilai
                            </span>
                        </div>

                        <div class="class-meta">
                            <span>{{ $rekap->total_murid }} murid</span>
                            <span>{{ $rekap->total_mapel }} mapel</span>
                            <span>{{ $rekap->total_guru }} guru</span>
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

                        <div class="class-footer">
                            <span>{{ $rekap->keterangan_predikat }}</span>

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

    .readonly-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #eff6ff;
        color: #2563eb;
        border: 1px solid #bfdbfe;
        border-radius: 999px;
        padding: 10px 14px;
        font-weight: 800;
        white-space: nowrap;
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

    select {
        width: 100%;
        border: 1px solid #dbe3ef;
        border-radius: 10px;
        padding: 11px 12px;
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
    .score-grid span,
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

        .score-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 800px) {
        .page-header-card,
        .card-header,
        .class-footer {
            flex-direction: column;
            align-items: flex-start;
        }

        .summary-grid,
        .filter-grid,
        .class-grid,
        .score-grid {
            grid-template-columns: 1fr;
        }

        .filter-actions {
            flex-direction: column;
            align-items: stretch;
        }
    }
</style>

@endsection
