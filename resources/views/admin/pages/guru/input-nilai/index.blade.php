@extends('admin.layouts.app')

@section('title', 'Input Nilai')

@section('content')
<div class="page-content">
    <div class="page-header-card">
        <div>
            <h1>Input Nilai Ujian Semester</h1>
            <p>Pilih tahun ajaran, semester, dan kelas. Setelah itu pilih murid untuk mengisi nilai seperti format rapor.</p>
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

    <div class="card">
        <div class="card-header">
            <div>
                <h2>Filter Input Nilai</h2>
                <p>Filter ini digunakan untuk menampilkan daftar murid sesuai kelas dan semester.</p>
            </div>
        </div>

        <form method="GET" action="{{ route('guru.input-nilai.index') }}" class="filter-grid">
            <div class="form-group">
                <label>Tahun Ajaran <span class="required">*</span></label>
                <select name="tahun_ajaran_id" required>
                    <option value="">Pilih Tahun Ajaran</option>
                    @foreach ($tahunAjarans as $tahunAjaran)
                        <option value="{{ $tahunAjaran->id }}" @selected((string) $selectedTahunAjaranId === (string) $tahunAjaran->id)>
                            {{ $tahunAjaran->nama_tahun_ajaran ?? '-' }}
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
                            {{ $semester->nama_semester_label }}
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
                    Filter
                </button>

                <a href="{{ route('guru.input-nilai.index') }}" class="btn btn-outline">
                    Reset
                </a>
            </div>
        </form>
    </div>

    @if ($selectedKelasId && $selectedTahunAjaranId && $selectedSemesterId)
        <div class="card">
            <div class="card-header">
                <div>
                    <h2>Daftar Murid</h2>
                    <p>Pilih salah satu murid untuk mengisi nilai ujian semester dalam format rapor.</p>
                </div>

                <span class="badge">{{ $murids->count() }} murid</span>
            </div>

            @if ($murids->isEmpty())
                <div class="empty-state">
                    Belum ada murid aktif pada kelas ini.
                </div>
            @else
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th style="width: 60px;">No</th>
                                <th>NIS</th>
                                <th>Nama Murid</th>
                                <th>Kelas</th>
                                <th>Status Nilai</th>
                                <th style="width: 180px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($murids as $murid)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $murid->nis ?? '-' }}</td>
                                    <td>
                                        <strong>{{ $murid->nama_murid }}</strong>
                                    </td>
                                    <td>{{ $murid->kelas->nama_kelas ?? '-' }}</td>
                                    <td>
                                        @if ($murid->jumlah_nilai_terisi > 0)
                                            <span class="badge-soft">
                                                {{ $murid->jumlah_nilai_terisi }} nilai terisi
                                            </span>
                                        @else
                                            <span class="badge-warning">
                                                Belum diisi
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <a
                                            href="{{ route('guru.input-nilai.murid', [
                                                'murid' => $murid->id,
                                                'kelas_id' => $selectedKelasId,
                                                'tahun_ajaran_id' => $selectedTahunAjaranId,
                                                'semester_id' => $selectedSemesterId,
                                            ]) }}"
                                            class="btn btn-primary btn-sm"
                                        >
                                            Input Nilai
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    @endif
</div>

@if (session('success'))
    <div id="flash-success" data-message="{{ session('success') }}" hidden></div>
@endif

@if (session('error'))
    <div id="flash-error" data-message="{{ session('error') }}" hidden></div>
@endif

@if ($errors->any())
    <div id="flash-validation" data-message="Data belum lengkap atau belum sesuai. Silakan cek kembali nilai yang diinput." hidden></div>
@endif

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const successFlash = document.getElementById('flash-success');
        const errorFlash = document.getElementById('flash-error');
        const validationFlash = document.getElementById('flash-validation');

        if (successFlash && successFlash.dataset.message) {
            alert(successFlash.dataset.message);
        }

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

    .badge-warning {
        background: #fef3c7;
        color: #92400e;
        border-radius: 8px;
        padding: 6px 10px;
        font-size: 13px;
        font-weight: 600;
        display: inline-flex;
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
        padding: 13px 12px;
        text-align: left;
        vertical-align: middle;
    }

    .data-table th {
        background: #f8fafc;
        color: #0f172a;
        font-weight: 700;
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
        .filter-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 700px) {
        .filter-grid {
            grid-template-columns: 1fr;
        }

        .filter-actions {
            flex-direction: column;
            align-items: stretch;
        }

        .card-header {
            flex-direction: column;
            align-items: flex-start;
        }
    }
</style>
@endsection
