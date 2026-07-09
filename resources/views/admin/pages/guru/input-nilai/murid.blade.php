@extends('admin.layouts.app')

@section('title', 'Input Nilai Murid')

@section('content')
<div class="page-content">
    <div class="page-header-card">
        <div>
            <h1>Input Nilai Rapor Murid</h1>
            <p>Isi nilai ujian semester berdasarkan mata pelajaran yang diajar.</p>
        </div>

        <a
            href="{{ route('guru.input-nilai.index', [
                'kelas_id' => $kelas->id,
                'tahun_ajaran_id' => $tahunAjaran->id,
                'semester_id' => $semester->id,
            ]) }}"
            class="btn btn-outline"
        >
            Kembali
        </a>
    </div>

    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Data belum lengkap.</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="rapor-card">
        <div class="school-header">
            <div>
                <h2>SMPN 6 Bati-Bati</h2>
                <p>Sistem Informasi Akademik</p>
            </div>

            <div class="semester-box">
                <strong>Nilai Ujian Semester</strong>
                <span>{{ $semester->nama_semester_label }} - {{ $tahunAjaran->nama_tahun_ajaran }}</span>
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

        @if ($mataPelajarans->isEmpty())
            <div class="empty-state">
                Belum ada mata pelajaran yang dapat diinput untuk kelas ini.
            </div>
        @else
            <form method="POST" action="{{ route('guru.input-nilai.murid.store', $murid->id) }}">
                @csrf

                <input type="hidden" name="kelas_id" value="{{ $kelas->id }}">
                <input type="hidden" name="tahun_ajaran_id" value="{{ $tahunAjaran->id }}">
                <input type="hidden" name="semester_id" value="{{ $semester->id }}">

                <div class="table-responsive">
                    <table class="rapor-table">
                        <thead>
                            <tr>
                                <th style="width: 60px;">No</th>
                                <th>Mata Pelajaran</th>
                                <th style="width: 180px;">Nilai</th>
                                <th style="width: 160px;">Predikat</th>
                                <th>Deskripsi / Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($mataPelajarans as $mapel)
                                @php
                                    $nilaiLama = $nilaiTersimpan->get($mapel->id);
                                    $nilaiValue = old('nilai.' . $mapel->id, $nilaiLama->nilai_ujian ?? '');
                                    $keteranganValue = old('keterangan.' . $mapel->id, $nilaiLama->keterangan ?? '');
                                @endphp

                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <strong>{{ $mapel->nama_mapel }}</strong>
                                        <div class="small-muted">{{ $mapel->kode_mapel ?? '-' }}</div>
                                    </td>
                                    <td>
                                        <input
                                            type="number"
                                            name="nilai[{{ $mapel->id }}]"
                                            min="0"
                                            max="100"
                                            value="{{ $nilaiValue }}"
                                            placeholder="0-100"
                                            required
                                        >
                                    </td>
                                    <td>
                                        @if ($nilaiLama)
                                            <span class="badge-soft">
                                                {{ $nilaiLama->predikat }} - {{ $nilaiLama->keterangan_predikat }}
                                            </span>
                                        @else
                                            <span class="text-muted">Otomatis</span>
                                        @endif
                                    </td>
                                    <td>
                                        <input
                                            type="text"
                                            name="keterangan[{{ $mapel->id }}]"
                                            value="{{ $keteranganValue }}"
                                            placeholder="Contoh: Menguasai materi dengan baik"
                                        >
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="rapor-footer">
                    <div class="note-box">
                        <strong>Catatan:</strong>
                        <span>Predikat akan dihitung otomatis dari nilai angka.</span>
                    </div>

                    <button
                        type="submit"
                        class="btn btn-primary"
                        data-confirm="true"
                        data-confirm-message="Apakah Anda yakin ingin menyimpan nilai rapor murid ini?"
                        data-confirm-yes="Ya, Simpan"
                        data-confirm-yes-class="btn-primary"
                    >
                        <i class="bi bi-save me-1"></i>
                        Simpan Nilai Rapor
                    </button>
                </div>
            </form>
        @endif
    </div>
</div>

@if (session('error'))
    <div id="flash-error" data-message="{{ session('error') }}" hidden></div>
@endif

@if ($errors->any())
    <div id="flash-validation" data-message="Data belum lengkap atau belum sesuai. Silakan cek kembali nilai yang diinput." hidden></div>
@endif

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const errorFlash = document.getElementById('flash-error');
        const validationFlash = document.getElementById('flash-validation');

        if (errorFlash && errorFlash.dataset.message) {
            alert(errorFlash.dataset.message);
        }

        if (validationFlash && validationFlash.dataset.message) {
            alert(validationFlash.dataset.message);
        }
    });
</script>

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
        align-items: center;
        justify-content: space-between;
        gap: 16px;
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
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        border-bottom: 2px solid #0f172a;
        padding-bottom: 16px;
        margin-bottom: 18px;
    }

    .school-header h2 {
        margin: 0;
        color: #0f172a;
        font-size: 24px;
    }

    .school-header p {
        margin: 4px 0 0;
        color: #64748b;
    }

    .semester-box {
        text-align: right;
        display: flex;
        flex-direction: column;
        gap: 4px;
        color: #0f172a;
    }

    .semester-box span {
        color: #64748b;
    }

    .identity-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 14px;
        margin-bottom: 22px;
    }

    .identity-item {
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 12px;
        background: #f8fafc;
        display: flex;
        flex-direction: column;
        gap: 5px;
    }

    .identity-item span {
        color: #64748b;
        font-size: 13px;
    }

    .identity-item strong {
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

    .rapor-table input {
        width: 100%;
        border: 1px solid #dbe3ef;
        border-radius: 10px;
        padding: 10px 12px;
        font-size: 14px;
        outline: none;
        background: #ffffff;
    }

    .rapor-table input:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
    }

    .small-muted {
        margin-top: 3px;
        color: #94a3b8;
        font-size: 12px;
    }

    .text-muted {
        color: #94a3b8;
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

    .rapor-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        margin-top: 18px;
    }

    .note-box {
        color: #475569;
        display: flex;
        gap: 6px;
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

    .alert {
        border-radius: 12px;
        padding: 14px 16px;
    }

    .alert-danger {
        background: #fee2e2;
        color: #991b1b;
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
        .identity-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 700px) {
        .page-header-card,
        .school-header,
        .rapor-footer {
            flex-direction: column;
            align-items: flex-start;
        }

        .semester-box {
            text-align: left;
        }

        .identity-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection
