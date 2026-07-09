{{-- penjelasan: Halaman ini muncul setelah guru/murid dipilih. --}}
{{-- penjelasan: User memilih periode laporan: satu tahun ajaran, satu semester, atau satu minggu. --}}
{{-- penjelasan: Setiap periode punya aksi Lihat, Export CSV, dan Cetak/PDF. --}}

@extends('admin.layouts.app')

@section('title', 'Pilih Periode Laporan')

@section('content')

@php
    $routeParams = [
        'jenis' => $jenis,
        'targetType' => $targetType,
        'targetId' => $targetId,
    ];

    $previewRoute = route($routePrefix . '.laporan.preview', $routeParams);
    $exportRoute = route($routePrefix . '.laporan.export.csv', $routeParams);
    $cetakRoute = route($routePrefix . '.laporan.cetak', $routeParams);
@endphp

<div class="page-content">
    <div class="page-header-card">
        <div>
            <h1>Pilih Periode Laporan</h1>
            <p>{{ $jenisLabel }} untuk <strong>{{ $targetTitle }}</strong>.</p>
        </div>

        <a href="{{ $backUrl }}" class="btn btn-outline">
            <i class="bi bi-arrow-left me-1"></i>
            Kembali
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Data periode belum valid.</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="info-card">
        <div>
            <span>Jenis Laporan</span>
            <strong>{{ $jenisLabel }}</strong>
        </div>

        <div>
            <span>Nama</span>
            <strong>{{ $targetTitle }}</strong>
        </div>

        @if ($jenis !== 'absensi_guru')
            <div>
                <span>Kelas</span>
                <strong>{{ $target->kelas?->nama_kelas ?? '-' }}</strong>
            </div>
        @endif

        <div>
            <span>Mode</span>
            <strong>Lihat / Export / Cetak</strong>
        </div>
    </div>

    @if ($jenis === 'nilai')
        <div class="note-card">
            <i class="bi bi-info-circle"></i>
            <span>Untuk laporan nilai periode mingguan, data difilter berdasarkan tanggal input nilai.</span>
        </div>
    @endif

    <div class="period-grid">
        <form method="GET" class="period-card">
            <input type="hidden" name="periode" value="tahun_ajaran">

            <div class="period-icon">
                <i class="bi bi-calendar-range"></i>
            </div>

            <h2>Satu Tahun Ajaran</h2>
            <p>Pilih satu tahun ajaran untuk melihat laporan penuh dalam periode tahun ajaran tersebut.</p>

            <div class="form-group">
                <label>Tahun Ajaran</label>
                <select name="tahun_ajaran_id" required>
                    <option value="">Pilih Tahun Ajaran</option>
                    @foreach ($tahunAjarans as $tahunAjaran)
                        <option value="{{ $tahunAjaran->id }}">
                            {{ $tahunAjaran->nama_tahun_ajaran ?? $tahunAjaran->tahun_ajaran ?? '-' }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="button-grid">
                <button type="submit" formaction="{{ $previewRoute }}" class="btn btn-primary">
                    Lihat
                </button>

                <button type="submit" formaction="{{ $exportRoute }}" class="btn btn-success">
                    CSV
                </button>

                <button type="submit" formaction="{{ $cetakRoute }}" formtarget="_blank" class="btn btn-outline">
                    PDF
                </button>
            </div>
        </form>

        <form method="GET" class="period-card">
            <input type="hidden" name="periode" value="semester">

            <div class="period-icon">
                <i class="bi bi-calendar2-week"></i>
            </div>

            <h2>Satu Semester</h2>
            <p>Pilih satu semester untuk melihat laporan pada semester tertentu.</p>

            <div class="form-group">
                <label>Semester</label>
                <select name="semester_id" required>
                    <option value="">Pilih Semester</option>
                    @foreach ($semesters as $semester)
                        <option value="{{ $semester->id }}">
                            {{ $semester->nama_semester_label ?? $semester->nama_semester ?? '-' }}
                            @if ($semester->tahunAjaran)
                                - {{ $semester->tahunAjaran->nama_tahun_ajaran ?? $semester->tahunAjaran->tahun_ajaran ?? '-' }}
                            @endif
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="button-grid">
                <button type="submit" formaction="{{ $previewRoute }}" class="btn btn-primary">
                    Lihat
                </button>

                <button type="submit" formaction="{{ $exportRoute }}" class="btn btn-success">
                    CSV
                </button>

                <button type="submit" formaction="{{ $cetakRoute }}" formtarget="_blank" class="btn btn-outline">
                    PDF
                </button>
            </div>
        </form>

        <form method="GET" class="period-card">
            <input type="hidden" name="periode" value="minggu">

            <div class="period-icon">
                <i class="bi bi-calendar-week"></i>
            </div>

            <h2>Satu Minggu</h2>
            <p>Pilih tanggal awal minggu. Sistem otomatis mengambil 7 hari dari tanggal tersebut.</p>

            <div class="form-group">
                <label>Tanggal Awal Minggu</label>
                <input type="date" name="tanggal_mulai" required>
            </div>

            <div class="button-grid">
                <button type="submit" formaction="{{ $previewRoute }}" class="btn btn-primary">
                    Lihat
                </button>

                <button type="submit" formaction="{{ $exportRoute }}" class="btn btn-success">
                    CSV
                </button>

                <button type="submit" formaction="{{ $cetakRoute }}" formtarget="_blank" class="btn btn-outline">
                    PDF
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    .page-content {
        display: flex;
        flex-direction: column;
        gap: 18px;
    }

    .page-header-card,
    .info-card,
    .period-card,
    .note-card {
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

    .page-header-card h1 {
        margin: 0;
        color: #0f172a;
        font-weight: 900;
    }

    .page-header-card p {
        margin: 6px 0 0;
        color: #64748b;
    }

    .info-card {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 14px;
    }

    .info-card div {
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 12px;
        background: #f8fafc;
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .info-card span {
        color: #64748b;
        font-size: 13px;
    }

    .info-card strong {
        color: #0f172a;
    }

    .note-card {
        display: flex;
        align-items: center;
        gap: 10px;
        background: #eff6ff;
        color: #1d4ed8;
        font-weight: 700;
    }

    .period-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 18px;
    }

    .period-card {
        border: 1px solid #e2e8f0;
        display: flex;
        flex-direction: column;
        gap: 14px;
    }

    .period-icon {
        width: 50px;
        height: 50px;
        border-radius: 15px;
        background: #dbeafe;
        color: #2563eb;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
    }

    .period-card h2 {
        margin: 0;
        color: #0f172a;
        font-size: 22px;
        font-weight: 900;
    }

    .period-card p {
        margin: 0;
        color: #64748b;
        line-height: 1.6;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 8px;
        margin-top: auto;
    }

    label {
        font-weight: 800;
        color: #0f172a;
    }

    select,
    input {
        width: 100%;
        border: 1px solid #dbe3ef;
        border-radius: 10px;
        padding: 11px 12px;
        outline: none;
        background: #ffffff;
    }

    select:focus,
    input:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
    }

    .button-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 8px;
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

    .btn-success {
        background: #16a34a;
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

    .mb-0 {
        margin-bottom: 0;
    }

    .mt-2 {
        margin-top: 8px;
    }

    @media (max-width: 1100px) {
        .period-grid {
            grid-template-columns: 1fr;
        }

        .info-card {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 700px) {
        .page-header-card {
            flex-direction: column;
            align-items: flex-start;
        }

        .info-card,
        .button-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

@endsection
