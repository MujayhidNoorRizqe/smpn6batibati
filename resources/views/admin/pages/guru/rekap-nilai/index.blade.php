@extends('admin.layouts.app')

@section('title', 'Rekap Nilai')

@section('content')
<div class="page-content">
    <div class="page-header-card">
        <div>
            <h1>Rekap Nilai Ujian Semester</h1>
            <p>Filter berdasarkan tahun ajaran, semester, dan kelas. Setelah itu pilih kelas untuk melihat daftar murid.</p>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <div>
                <h2>Filter Rekap Nilai</h2>
                <p>Pilih filter terlebih dahulu untuk mencari kelas yang ingin direkap.</p>
            </div>
        </div>

        <form method="GET" action="{{ route('guru.rekap-nilai.index') }}" class="filter-grid">
            <div class="form-group">
                <label>Tahun Ajaran <span class="required">*</span></label>
                <select name="tahun_ajaran_id" required>
                    <option value="">Pilih Tahun Ajaran</option>
                    @foreach ($tahunAjarans as $tahunAjaran)
                        <option value="{{ $tahunAjaran->id }}" @selected((string) $selectedTahunAjaranId === (string) $tahunAjaran->id)>
                            {{ $tahunAjaran->nama_tahun_ajaran ?? $tahunAjaran->tahun_ajaran ?? '-' }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>Semester <span class="required">*</span></label>
                <select name="semester_id" required>
                    <option value="">Pilih Semester</option>
                    @foreach ($semesters as $semester)
                        <option value="{{ $semester->id }}" @selected((string) $selectedSemesterId === (string) $semester->id)>
                            {{ $semester->nama_semester_label ?? $semester->nama_semester ?? $semester->semester ?? '-' }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>Kelas <span class="required">*</span></label>
                <select name="kelas_id" required>
                    <option value="">Pilih Kelas</option>
                    @foreach ($kelas as $item)
                        <option value="{{ $item->id }}" @selected((string) $selectedKelasId === (string) $item->id)>
                            {{ $item->nama_kelas }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="filter-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-search me-1"></i>
                    Filter
                </button>

                <a href="{{ route('guru.rekap-nilai.index') }}" class="btn btn-outline">
                    Reset
                </a>
            </div>
        </form>
    </div>

    @if (! $selectedKelasId || ! $selectedTahunAjaranId || ! $selectedSemesterId)
        <div class="card">
            <div class="empty-state">
                Silakan pilih tahun ajaran, semester, dan kelas terlebih dahulu.
            </div>
        </div>
    @else
        <div class="card">
            <div class="card-header">
                <div>
                    <h2>Kelas Ditemukan</h2>
                    <p>Berikut kelas yang sesuai dengan filter rekap nilai.</p>
                </div>

                <span class="badge">{{ $kelasHasil->count() }} kelas</span>
            </div>

            @if ($kelasHasil->isEmpty())
                <div class="empty-state">
                    Tidak ada kelas yang sesuai dengan filter.
                </div>
            @else
                <div class="kelas-grid">
                    @foreach ($kelasHasil as $kelasItem)
                        <div class="kelas-card">
                            <div class="kelas-card-header">
                                <div>
                                    <span>Kelas</span>
                                    <h3>{{ $kelasItem->nama_kelas }}</h3>
                                </div>

                                <span class="badge-soft">
                                    {{ $tahunAjaranDipilih->nama_tahun_ajaran ?? $tahunAjaranDipilih->tahun_ajaran ?? '-' }}
                                    /
                                    {{ $semesterDipilih->nama_semester_label ?? $semesterDipilih->nama_semester ?? $semesterDipilih->semester ?? '-' }}
                                </span>
                            </div>

                            <div class="kelas-info-grid">
                                <div>
                                    <span>Wali Kelas</span>
                                    <strong>{{ $kelasItem->waliKelas->nama_pegawai ?? '-' }}</strong>
                                </div>

                                <div>
                                    <span>Total Murid</span>
                                    <strong>{{ $kelasItem->total_murid ?? 0 }}</strong>
                                </div>

                                <div>
                                    <span>Total Mapel</span>
                                    <strong>{{ $kelasItem->total_mapel ?? 0 }}</strong>
                                </div>

                                <div>
                                    <span>Nilai Terisi</span>
                                    <strong>{{ $kelasItem->total_nilai_terisi ?? 0 }}</strong>
                                </div>
                            </div>

                            <div class="kelas-actions">
                                <a
                                    href="{{ route('guru.rekap-nilai.kelas', [
                                        'kelas' => $kelasItem->id,
                                        'tahun_ajaran_id' => $selectedTahunAjaranId,
                                        'semester_id' => $selectedSemesterId,
                                    ]) }}"
                                    class="btn btn-primary"
                                >
                                    <i class="bi bi-people me-1"></i>
                                    Lihat Murid
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    @endif
</div>

<style>
    .page-content {
        display: flex;
        flex-direction: column;
        gap: 18px;
    }

    .page-header-card,
    .card {
        background: #ffffff;
        border-radius: 16px;
        padding: 22px;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
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

    .card-header {
        display: flex;
        justify-content: space-between;
        gap: 14px;
        align-items: center;
        margin-bottom: 18px;
    }

    .filter-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr)) 260px;
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

    .required {
        color: #ef4444;
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

    .badge-soft {
        background: #eff6ff;
        color: #1d4ed8;
        border-radius: 8px;
        padding: 6px 10px;
        font-size: 13px;
        font-weight: 600;
        display: inline-flex;
    }

    .kelas-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 16px;
    }

    .kelas-card {
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 18px;
        background: #ffffff;
    }

    .kelas-card-header {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        align-items: flex-start;
        margin-bottom: 18px;
    }

    .kelas-card-header span {
        color: #64748b;
        font-size: 13px;
    }

    .kelas-card-header h3 {
        margin: 4px 0 0;
        color: #0f172a;
        font-size: 28px;
    }

    .kelas-info-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 10px;
    }

    .kelas-info-grid div {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 12px;
        display: flex;
        flex-direction: column;
        gap: 5px;
    }

    .kelas-info-grid span {
        color: #64748b;
        font-size: 13px;
    }

    .kelas-info-grid strong {
        color: #0f172a;
    }

    .kelas-actions {
        display: flex;
        justify-content: flex-end;
        margin-top: 18px;
    }

    .empty-state {
        padding: 28px;
        text-align: center;
        color: #64748b;
        border: 1px dashed #cbd5e1;
        border-radius: 12px;
        background: #f8fafc;
    }

    @media (max-width: 1100px) {
        .filter-grid,
        .kelas-grid {
            grid-template-columns: 1fr;
        }

        .kelas-info-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 700px) {
        .filter-actions,
        .card-header,
        .kelas-card-header {
            flex-direction: column;
            align-items: stretch;
        }

        .kelas-info-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection
