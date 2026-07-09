@extends('admin.layouts.app')

@section('title', 'Detail Nilai Murid')

@section('content')
<div class="page-content">
    <div class="page-header-card">
        <div>
            <h1>Detail Nilai Murid</h1>
            <p>Detail nilai ujian semester per murid dalam format rapor sederhana.</p>
        </div>

        <a
            href="{{ route('guru.rekap-nilai.kelas', [
                'kelas' => $kelas->id,
                'tahun_ajaran_id' => $tahunAjaran->id,
                'semester_id' => $semester->id,
            ]) }}"
            class="btn btn-outline"
        >
            Kembali
        </a>
    </div>

    <div class="rapor-card">
        <div class="school-header">
            <div>
                <h2>SMPN 6 Bati-Bati</h2>
                <p>Rekap Nilai Ujian Semester</p>
            </div>

            <div class="semester-box">
                <strong>{{ $semester->nama_semester_label ?? $semester->nama_semester ?? $semester->semester ?? '-' }}</strong>
                <span>{{ $tahunAjaran->nama_tahun_ajaran ?? $tahunAjaran->tahun_ajaran ?? '-' }}</span>
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
                <span>Total Mapel</span>
                <strong>{{ $ringkasan['total_mapel'] }}</strong>
            </div>

            <div class="summary-card">
                <span>Nilai Terisi</span>
                <strong>{{ $ringkasan['nilai_terisi'] }}</strong>
            </div>

            <div class="summary-card">
                <span>Rata-rata</span>
                <strong>{{ $ringkasan['rata_rata'] ?? '-' }}</strong>
            </div>

            <div class="summary-card">
                <span>Predikat</span>
                <strong>
                    {{ $ringkasan['predikat'] }}
                    @if ($ringkasan['keterangan_predikat'] !== '-')
                        - {{ $ringkasan['keterangan_predikat'] }}
                    @endif
                </strong>
            </div>
        </div>

        <div class="table-responsive">
            <table class="rapor-table">
                <thead>
                    <tr>
                        <th style="width: 60px;">No</th>
                        <th>Mata Pelajaran</th>
                        <th style="width: 140px;">Nilai</th>
                        <th style="width: 160px;">Predikat</th>
                        <th>Deskripsi / Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($mataPelajarans as $mapel)
                        @php
                            $nilai = $nilaiTersimpan->get($mapel->id);
                        @endphp

                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <strong>{{ $mapel->nama_mapel }}</strong>
                                <div class="small-muted">{{ $mapel->kode_mapel ?? '-' }}</div>
                            </td>
                            <td class="text-center">
                                <strong>{{ $nilai->nilai_ujian ?? '-' }}</strong>
                            </td>
                            <td class="text-center">
                                @if ($nilai)
                                    <span class="badge-soft">
                                        {{ $nilai->predikat }} - {{ $nilai->keterangan_predikat }}
                                    </span>
                                @else
                                    <span class="text-muted">Belum diisi</span>
                                @endif
                            </td>
                            <td>{{ $nilai->keterangan ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="rapor-footer">
            <a
                href="{{ route('guru.input-nilai.murid', [
                    'murid' => $murid->id,
                    'kelas_id' => $kelas->id,
                    'tahun_ajaran_id' => $tahunAjaran->id,
                    'semester_id' => $semester->id,
                ]) }}"
                class="btn btn-primary"
            >
                Edit Nilai Murid
            </a>
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
    .rapor-card {
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

    .page-header-card h1 {
        margin: 0;
        color: #0f172a;
    }

    .page-header-card p {
        margin: 6px 0 0;
        color: #64748b;
    }

    .school-header {
        display: flex;
        justify-content: space-between;
        gap: 16px;
        border-bottom: 2px solid #0f172a;
        padding-bottom: 16px;
        margin-bottom: 18px;
    }

    .school-header h2 {
        margin: 0;
        color: #0f172a;
    }

    .school-header p {
        margin: 6px 0 0;
        color: #64748b;
    }

    .semester-box {
        display: flex;
        flex-direction: column;
        text-align: right;
        color: #0f172a;
    }

    .semester-box span {
        color: #64748b;
    }

    .identity-grid,
    .summary-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 14px;
        margin-bottom: 18px;
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

    .table-responsive {
        overflow-x: auto;
    }

    .rapor-table {
        width: 100%;
        border-collapse: collapse;
        border: 1px solid #cbd5e1;
    }

    .rapor-table th,
    .rapor-table td {
        border: 1px solid #cbd5e1;
        padding: 12px;
        text-align: left;
        vertical-align: middle;
    }

    .rapor-table th {
        background: #f1f5f9;
        color: #0f172a;
        font-weight: 700;
        text-align: center;
    }

    .text-center {
        text-align: center;
    }

    .text-muted {
        color: #94a3b8;
    }

    .small-muted {
        margin-top: 3px;
        color: #94a3b8;
        font-size: 12px;
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

    .rapor-footer {
        display: flex;
        justify-content: flex-end;
        margin-top: 18px;
    }

    @media (max-width: 900px) {
        .page-header-card,
        .school-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .semester-box {
            text-align: left;
        }

        .identity-grid,
        .summary-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection
