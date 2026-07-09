{{-- penjelasan: File ini adalah halaman detail jadwal per kelas. --}}
{{-- penjelasan: Halaman ini muncul saat card kelas pada halaman utama diklik. --}}
{{-- penjelasan: Jadwal ditampilkan berdasarkan hari Senin sampai Sabtu. --}}

@extends('admin.layouts.app')

@section('title', 'Jadwal Kelas')

@section('content')

<div class="page-content">
    <div class="page-header-card">
        <div>
            <h1>Jadwal Kelas {{ $kelas->nama_kelas }}</h1>
            <p>Daftar jadwal pelajaran yang sudah dibuat untuk kelas ini.</p>
        </div>

        <div class="header-actions">
            <a
                href="{{ route($routePrefix . '.jadwal-pelajaran.create-kelas', [
                    'kelas' => $kelas->id,
                    'tahun_ajaran_id' => $selectedTahunAjaranId,
                    'semester_id' => $selectedSemesterId,
                ]) }}"
                class="btn btn-primary"
            >
                <i class="bi bi-plus-circle me-1"></i>
                Isi Jadwal
            </a>

            <a href="{{ route($routePrefix . '.jadwal-pelajaran.index') }}" class="btn btn-outline">
                Kembali
            </a>
        </div>
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
                <h2>Filter Jadwal Kelas</h2>
                <p>Pilih periode jadwal yang ingin ditampilkan.</p>
            </div>
        </div>

        <form method="GET" action="{{ route($routePrefix . '.jadwal-pelajaran.kelas', $kelas->id) }}" class="filter-grid">
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
                <label>Status</label>
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

                <a href="{{ route($routePrefix . '.jadwal-pelajaran.kelas', $kelas->id) }}" class="btn btn-outline">
                    Reset
                </a>
            </div>
        </form>
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
            <strong>{{ $kelas->waliKelas->nama_pegawai ?? '-' }}</strong>
        </div>

        <div>
            <span>Total Jadwal</span>
            <strong>{{ $jadwalPelajarans->count() }}</strong>
        </div>
    </div>

    <div class="day-list">
        @foreach ($jadwalPerHari as $hariGroup)
            <div class="day-card">
                <div class="day-header">
                    <div>
                        <span>Hari</span>
                        <h2>{{ $hariGroup->label }}</h2>
                    </div>

                    <div class="day-actions">
                        <span class="badge">{{ $hariGroup->total_jadwal }} jadwal</span>

                        <a
                            href="{{ route($routePrefix . '.jadwal-pelajaran.create-hari', [
                                'kelas' => $kelas->id,
                                'hari' => $hariGroup->hari,
                                'tahun_ajaran_id' => $selectedTahunAjaranId,
                                'semester_id' => $selectedSemesterId,
                            ]) }}"
                            class="btn btn-primary btn-sm"
                        >
                            Isi/Edit Hari Ini
                        </a>
                    </div>
                </div>

                @if ($hariGroup->jadwals->isEmpty())
                    <div class="empty-state">
                        Belum ada jadwal untuk hari {{ $hariGroup->label }}.
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th style="width: 60px;">No</th>
                                    <th style="width: 150px;">Jam</th>
                                    <th>Mata Pelajaran</th>
                                    <th>Guru</th>
                                    <th>Periode</th>
                                    <th style="width: 110px;">Status</th>
                                    <th style="width: 290px;" class="text-end">Aksi</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($hariGroup->jadwals as $jadwal)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <strong>{{ $jadwal->jam_pelajaran }}</strong>
                                        </td>
                                        <td>
                                            <strong>{{ $jadwal->mataPelajaran->nama_mapel ?? '-' }}</strong>
                                            <div class="small-muted">{{ $jadwal->mataPelajaran->kode_mapel ?? '-' }}</div>
                                        </td>
                                        <td>{{ $jadwal->guru->nama_pegawai ?? '-' }}</td>
                                        <td>
                                            <strong>{{ $jadwal->tahunAjaran->nama_tahun_ajaran ?? $jadwal->tahunAjaran->tahun_ajaran ?? '-' }}</strong>
                                            <div class="small-muted">
                                                {{ $jadwal->semester->nama_semester_label ?? $jadwal->semester->nama_semester ?? $jadwal->semester->semester ?? '-' }}
                                            </div>
                                        </td>
                                        <td>
                                            @if ($jadwal->status === 'aktif')
                                                <span class="badge-success">Aktif</span>
                                            @else
                                                <span class="badge-danger">Nonaktif</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <div class="action-buttons">
                                                <a href="{{ route($routePrefix . '.jadwal-pelajaran.show', $jadwal->id) }}" class="btn btn-outline btn-sm">
                                                    Detail
                                                </a>

                                                <a href="{{ route($routePrefix . '.jadwal-pelajaran.edit', $jadwal->id) }}" class="btn btn-outline-primary btn-sm">
                                                    Edit
                                                </a>

                                                <form action="{{ route($routePrefix . '.jadwal-pelajaran.toggle-status', $jadwal->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PATCH')

                                                    @if ($jadwal->status === 'aktif')
                                                        <button
                                                            type="submit"
                                                            class="btn btn-outline-danger btn-sm"
                                                            data-confirm="true"
                                                            data-confirm-message="Apakah Anda yakin ingin menonaktifkan jadwal ini?"
                                                            data-confirm-yes="Ya, Nonaktifkan"
                                                            data-confirm-yes-class="btn-danger"
                                                        >
                                                            Nonaktif
                                                        </button>
                                                    @else
                                                        <button
                                                            type="submit"
                                                            class="btn btn-outline-success btn-sm"
                                                            data-confirm="true"
                                                            data-confirm-message="Apakah Anda yakin ingin mengaktifkan jadwal ini?"
                                                            data-confirm-yes="Ya, Aktifkan"
                                                            data-confirm-yes-class="btn-success"
                                                        >
                                                            Aktifkan
                                                        </button>
                                                    @endif
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        @endforeach
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
    .info-grid,
    .day-card {
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

    .header-actions {
        display: flex;
        gap: 10px;
    }

    .page-header-card h1,
    .card h2,
    .day-header h2 {
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

    .card-header,
    .day-header {
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
    .day-header span {
        color: #64748b;
        font-size: 13px;
    }

    .info-grid strong {
        color: #0f172a;
    }

    .day-list {
        display: flex;
        flex-direction: column;
        gap: 18px;
    }

    .day-card {
        border: 1px solid #e2e8f0;
        box-shadow: none;
    }

    .day-actions {
        display: flex;
        gap: 10px;
        align-items: center;
    }

    .badge,
    .badge-success,
    .badge-danger {
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

    .badge-danger {
        background: #fee2e2;
        color: #991b1b;
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
        padding: 12px;
        text-align: left;
        vertical-align: middle;
    }

    .data-table th {
        background: #f8fafc;
        color: #0f172a;
        font-weight: 800;
    }

    .text-end {
        text-align: right;
    }

    .small-muted {
        color: #64748b;
        font-size: 13px;
        margin-top: 4px;
    }

    .action-buttons {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
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

    .btn-sm {
        padding: 8px 12px;
        font-size: 13px;
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

    .btn-outline-primary {
        background: #ffffff;
        color: #2563eb;
        border-color: #2563eb;
    }

    .btn-outline-danger {
        background: #ffffff;
        color: #dc2626;
        border-color: #dc2626;
    }

    .btn-outline-success {
        background: #ffffff;
        color: #16a34a;
        border-color: #16a34a;
    }

    .empty-state {
        padding: 22px;
        text-align: center;
        color: #64748b;
        border: 1px dashed #cbd5e1;
        border-radius: 12px;
        background: #f8fafc;
    }

    .d-inline {
        display: inline;
    }

    @media (max-width: 1100px) {
        .filter-grid,
        .info-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 800px) {
        .page-header-card,
        .card-header,
        .day-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .header-actions,
        .day-actions,
        .filter-actions {
            width: 100%;
            flex-direction: column;
            align-items: stretch;
        }

        .filter-grid,
        .info-grid {
            grid-template-columns: 1fr;
        }

        .action-buttons {
            justify-content: flex-start;
        }
    }
</style>

@endsection
