{{-- penjelasan: File ini adalah halaman daftar awal Jadwal Pelajaran. --}}
{{-- penjelasan: Halaman ini hanya menampilkan daftar kelas. --}}
{{-- penjelasan: Ketika card kelas diklik, user masuk ke halaman detail jadwal kelas tersebut. --}}
{{-- penjelasan: Jadwal yang sudah dibuat akan dilihat pada halaman kelas, bukan dicampur di halaman utama. --}}

@extends('admin.layouts.app')

@section('title', 'Jadwal Pelajaran')

@section('content')

<div class="page-content">
    <div class="page-header-card">
        <div>
            <h1>Jadwal Pelajaran</h1>
            <p>Pilih kelas untuk melihat atau mengelola jadwal pelajaran yang sudah dibuat.</p>
        </div>

        <a href="{{ route($routePrefix . '.jadwal-pelajaran.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i>
            Tambah Jadwal
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

    <div class="card">
        <div class="card-header">
            <div>
                <h2>Filter Jadwal</h2>
                <p>Filter ini mempengaruhi jumlah jadwal yang tampil pada setiap card kelas.</p>
            </div>
        </div>

        <form method="GET" action="{{ route($routePrefix . '.jadwal-pelajaran.index') }}" class="filter-grid">
            <div class="form-group">
                <label>Tahun Ajaran</label>
                <select name="tahun_ajaran_id">
                    <option value="">Semua Tahun Ajaran</option>
                    @foreach ($tahunAjarans as $tahunAjaran)
                        <option value="{{ $tahunAjaran->id }}" @selected((string) $selectedTahunAjaranId === (string) $tahunAjaran->id)>
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
                        <option value="{{ $semester->id }}" @selected((string) $selectedSemesterId === (string) $semester->id)>
                            {{ $semester->nama_semester_label ?? $semester->nama_semester ?? $semester->semester ?? '-' }}
                            @if ($semester->tahunAjaran)
                                - {{ $semester->tahunAjaran->nama_tahun_ajaran ?? $semester->tahunAjaran->tahun_ajaran ?? '-' }}
                            @endif
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>Status Jadwal</label>
                <select name="status">
                    <option value="">Semua Status</option>
                    <option value="aktif" @selected($selectedStatus === 'aktif')>Aktif</option>
                    <option value="nonaktif" @selected($selectedStatus === 'nonaktif')>Nonaktif</option>
                </select>
            </div>

            <div class="filter-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-search me-1"></i>
                    Filter
                </button>

                <a href="{{ route($routePrefix . '.jadwal-pelajaran.index') }}" class="btn btn-outline">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <div class="card">
        <div class="card-header">
            <div>
                <h2>Daftar Kelas</h2>
                <p>Kelas berasal dari fitur Data Kelas dan dipisahkan berdasarkan tingkat.</p>
            </div>

            <div class="badge-group">
                <span class="badge">{{ $totalKelas }} kelas</span>
                <span class="badge">{{ $totalJadwal }} jadwal</span>
            </div>
        </div>

        @if ($kelasPerTingkat->isEmpty())
            <div class="empty-state">
                Belum ada kelas aktif. Silakan buat kelas terlebih dahulu di menu Data Kelas.
            </div>
        @else
            <div class="level-list">
                @foreach ($kelasPerTingkat as $tingkat => $kelasItems)
                    <div class="level-card">
                        <div class="level-header">
                            <div>
                                <span>Tingkat</span>
                                <h3>{{ $tingkat }}</h3>
                            </div>

                            <span class="badge">{{ $kelasItems->count() }} kelas</span>
                        </div>

                        <div class="class-grid">
                            @foreach ($kelasItems as $kelas)
                                <a
                                    href="{{ route($routePrefix . '.jadwal-pelajaran.kelas', [
                                        'kelas' => $kelas->id,
                                        'tahun_ajaran_id' => $selectedTahunAjaranId,
                                        'semester_id' => $selectedSemesterId,
                                        'status' => $selectedStatus,
                                    ]) }}"
                                    class="class-card"
                                >
                                    <div class="class-main">
                                        <span>Kelas</span>
                                        <strong>{{ $kelas->nama_kelas }}</strong>
                                    </div>

                                    <div class="class-meta">
                                        <div>
                                            <span>Wali Kelas</span>
                                            <strong>{{ $kelas->waliKelas->nama_pegawai ?? '-' }}</strong>
                                        </div>

                                        <div>
                                            <span>Total Jadwal</span>
                                            <strong>{{ $kelas->total_jadwal }}</strong>
                                        </div>

                                        <div>
                                            <span>Jadwal Aktif</span>
                                            <strong>{{ $kelas->total_jadwal_aktif }}</strong>
                                        </div>
                                    </div>

                                    <div class="class-footer">
                                        <span>{{ $kelas->total_mapel }} mapel</span>
                                        <span>{{ $kelas->total_guru }} guru</span>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
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
    .card {
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
        grid-template-columns: repeat(3, minmax(0, 1fr)) 230px;
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

    .badge-group {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .badge {
        background: #dbeafe;
        color: #2563eb;
        border-radius: 8px;
        padding: 6px 10px;
        font-size: 13px;
        font-weight: 800;
        display: inline-flex;
    }

    .level-list {
        display: flex;
        flex-direction: column;
        gap: 18px;
    }

    .level-card {
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 18px;
        background: #ffffff;
    }

    .level-header {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        align-items: center;
        margin-bottom: 16px;
    }

    .level-header span {
        color: #64748b;
        font-size: 13px;
    }

    .level-header h3 {
        margin: 3px 0 0;
        color: #0f172a;
        font-size: 24px;
        font-weight: 900;
    }

    .class-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 14px;
    }

    .class-card {
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 16px;
        background: #f8fafc;
        text-decoration: none;
        color: inherit;
        transition: 0.2s ease;
        display: flex;
        flex-direction: column;
        gap: 14px;
    }

    .class-card:hover {
        border-color: #2563eb;
        background: #eff6ff;
        transform: translateY(-2px);
    }

    .class-main span,
    .class-meta span,
    .class-footer span {
        color: #64748b;
        font-size: 12px;
    }

    .class-main strong {
        display: block;
        color: #0f172a;
        font-size: 28px;
        margin-top: 3px;
    }

    .class-meta {
        display: grid;
        grid-template-columns: 1fr;
        gap: 8px;
    }

    .class-meta div {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 9px;
        display: flex;
        flex-direction: column;
        gap: 3px;
    }

    .class-meta strong {
        color: #0f172a;
        font-size: 14px;
    }

    .class-footer {
        display: flex;
        justify-content: space-between;
        gap: 10px;
        border-top: 1px solid #e2e8f0;
        padding-top: 10px;
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

    @media (max-width: 1200px) {
        .filter-grid,
        .class-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 800px) {
        .page-header-card,
        .card-header,
        .level-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .filter-grid,
        .class-grid {
            grid-template-columns: 1fr;
        }

        .filter-actions {
            flex-direction: column;
            align-items: stretch;
        }
    }
</style>

@endsection
