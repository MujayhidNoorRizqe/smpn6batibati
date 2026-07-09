{{-- penjelasan: File ini adalah halaman list hari untuk kelas yang dipilih. --}}
{{-- penjelasan: Setelah memilih kelas, user memilih tahun ajaran dan semester. --}}
{{-- penjelasan: Setelah itu user memilih hari Senin sampai Sabtu untuk mengisi jadwal harian. --}}

@extends('admin.layouts.app')

@section('title', 'Pilih Hari Jadwal')

@section('content')

<div class="page-content">
    <div class="page-header-card">
        <div>
            <h1>Jadwal Kelas {{ $kelas->nama_kelas }}</h1>
            <p>Pilih hari untuk mengisi jadwal pelajaran harian. Maksimal 6 pelajaran per hari.</p>
        </div>

        <a href="{{ route($routePrefix . '.jadwal-pelajaran.create') }}" class="btn btn-outline">
            <i class="bi bi-arrow-left me-1"></i>
            Kembali Pilih Kelas
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Data belum sesuai.</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <div>
                <h2>1. Pilih Periode</h2>
                <p>Tahun ajaran dan semester akan digunakan untuk jadwal kelas ini.</p>
            </div>
        </div>

        <form method="GET" action="{{ route($routePrefix . '.jadwal-pelajaran.create-kelas', $kelas->id) }}" class="filter-grid">
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
                            @if ($semester->tahunAjaran)
                                - {{ $semester->tahunAjaran->nama_tahun_ajaran ?? $semester->tahunAjaran->tahun_ajaran ?? '-' }}
                            @endif
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="filter-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle me-1"></i>
                    Gunakan Periode
                </button>
            </div>
        </form>
    </div>

    <div class="class-info-card">
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
            <strong>{{ $kelas->waliKelas->nama_pegawai ?? '-' }}</strong>
        </div>

        <div>
            <span>Periode</span>
            <strong>
                {{ $tahunAjaranDipilih->nama_tahun_ajaran ?? $tahunAjaranDipilih->tahun_ajaran ?? '-' }}
                /
                {{ $semesterDipilih->nama_semester_label ?? $semesterDipilih->nama_semester ?? $semesterDipilih->semester ?? '-' }}
            </strong>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <div>
                <h2>2. Pilih Hari</h2>
                <p>Pilih salah satu hari untuk mengisi jadwal pelajaran. Setiap hari maksimal 6 pelajaran.</p>
            </div>

            <a
                href="{{ route($routePrefix . '.jadwal-pelajaran.kelas', [
                    'kelas' => $kelas->id,
                    'tahun_ajaran_id' => $selectedTahunAjaranId,
                    'semester_id' => $selectedSemesterId,
                ]) }}"
                class="btn btn-outline"
            >
                Lihat Jadwal Kelas
            </a>
        </div>

        <div class="day-grid">
            @foreach ($jadwalPerHari as $item)
                <a
                    href="{{ route($routePrefix . '.jadwal-pelajaran.create-hari', [
                        'kelas' => $kelas->id,
                        'hari' => $item->hari,
                        'tahun_ajaran_id' => $selectedTahunAjaranId,
                        'semester_id' => $selectedSemesterId,
                    ]) }}"
                    class="day-card"
                >
                    <div class="day-top">
                        <div>
                            <span>Hari</span>
                            <strong>{{ $item->label }}</strong>
                        </div>

                        @if ($item->total_aktif > 0)
                            <span class="badge-success">{{ $item->total_aktif }} aktif</span>
                        @else
                            <span class="badge">{{ $item->total_jadwal }} jadwal</span>
                        @endif
                    </div>

                    <div class="day-info">
                        <div>
                            <span>Total Jadwal</span>
                            <strong>{{ $item->total_jadwal }}</strong>
                        </div>

                        <div>
                            <span>Maksimal</span>
                            <strong>6</strong>
                        </div>
                    </div>

                    <div class="day-footer">
                        Isi / Edit Jadwal
                        <i class="bi bi-arrow-right"></i>
                    </div>
                </a>
            @endforeach
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
    .class-info-card {
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

    .alert {
        border-radius: 12px;
        padding: 14px 16px;
    }

    .alert-success {
        background: #dcfce7;
        color: #166534;
    }

    .alert-danger {
        background: #fee2e2;
        color: #991b1b;
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
        grid-template-columns: repeat(2, minmax(0, 1fr)) 220px;
        gap: 16px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    label {
        color: #0f172a;
        font-weight: 700;
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
    }

    .class-info-card {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 14px;
    }

    .class-info-card div {
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 12px;
        background: #f8fafc;
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .class-info-card span {
        color: #64748b;
        font-size: 13px;
    }

    .class-info-card strong {
        color: #0f172a;
    }

    .day-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 14px;
    }

    .day-card {
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 16px;
        background: #f8fafc;
        color: inherit;
        text-decoration: none;
        display: flex;
        flex-direction: column;
        gap: 14px;
        transition: 0.2s ease;
    }

    .day-card:hover {
        border-color: #2563eb;
        background: #eff6ff;
        transform: translateY(-2px);
    }

    .day-top {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 10px;
    }

    .day-top span,
    .day-info span {
        color: #64748b;
        font-size: 12px;
    }

    .day-top strong {
        display: block;
        color: #0f172a;
        font-size: 24px;
        margin-top: 3px;
    }

    .day-info {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 10px;
    }

    .day-info div {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 10px;
        display: flex;
        flex-direction: column;
        gap: 3px;
    }

    .day-info strong {
        color: #0f172a;
        font-size: 18px;
    }

    .day-footer {
        border-top: 1px solid #e2e8f0;
        padding-top: 10px;
        color: #2563eb;
        font-weight: 800;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .badge,
    .badge-success {
        border-radius: 8px;
        padding: 6px 10px;
        font-size: 13px;
        font-weight: 800;
        display: inline-flex;
    }

    .badge {
        background: #dbeafe;
        color: #2563eb;
    }

    .badge-success {
        background: #dcfce7;
        color: #166534;
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

    .mb-0 {
        margin-bottom: 0;
    }

    .mt-2 {
        margin-top: 8px;
    }

    @media (max-width: 1100px) {
        .filter-grid,
        .class-info-card,
        .day-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 800px) {
        .page-header-card,
        .card-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .filter-grid,
        .class-info-card,
        .day-grid {
            grid-template-columns: 1fr;
        }

        .filter-actions {
            align-items: stretch;
        }
    }
</style>

@endsection
