{{-- penjelasan: Halaman ini dipakai setelah admin memilih wali murid. --}}
{{-- penjelasan: Halaman ini tetap membawa kelas_id dari halaman filter kelas. --}}
{{-- penjelasan: Daftar murid yang tampil hanya murid aktif dari kelas yang dipilih. --}}
{{-- penjelasan: Admin memilih murid, lalu memilih jenis pesan yang ingin dikirim: absen murid dan/atau nilai ujian semester. --}}

@extends('admin.layouts.app')

@section('title', 'Kirim WhatsApp Wali Murid')

@section('content')

@php
    $routePrefix = auth()->user()->role === 'super_admin' ? 'super-admin' : 'admin';
@endphp

<div class="page-content">
    @if (session('success'))
        <div id="flash-success" data-message="{{ session('success') }}" class="alert-success">
            <strong>Berhasil!</strong>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if (session('error'))
        <div id="flash-error" data-message="{{ session('error') }}" class="alert-danger">
            <strong>Gagal!</strong>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    @if ($errors->any())
        <div id="flash-validation" data-message="Data belum valid. Silakan cek kembali pilihan murid dan jenis pesan." class="alert-danger">
            <strong>Data belum valid.</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="page-header-card">
        <div>
            <h1>Kirim WhatsApp Wali Murid</h1>
            <p>Pilih murid dari kelas {{ $kelasTerpilih->nama_kelas }} dan jenis pesan yang ingin dikirim.</p>
        </div>

        <a href="{{ route($routePrefix . '.whatsapp-fonnte.index', ['kelas_id' => $kelasId]) }}" class="btn btn-outline">
            <i class="bi bi-arrow-left me-1"></i>
            Kembali
        </a>
    </div>

    @if (! $tokenTersedia)
        <div class="warning-card">
            <i class="bi bi-exclamation-triangle"></i>
            <div>
                <strong>Token Fonnte belum tersedia.</strong>
                <p>Pesan tidak bisa dikirim sebelum token tersedia pada <code>FONNTE_TOKEN</code>.</p>
            </div>
        </div>
    @endif

    <div class="info-grid">
        <div class="info-card">
            <span>Kelas Dipilih</span>
            <strong>{{ $kelasTerpilih->nama_kelas }}</strong>
        </div>

        <div class="info-card">
            <span>Nama Wali Murid</span>
            <strong>{{ $waliMurid->nama_wali }}</strong>
        </div>

        <div class="info-card">
            <span>Nomor WhatsApp</span>
            <strong>{{ $waliMurid->no_whatsapp }}</strong>
        </div>

        <div class="info-card">
            <span>Murid di Kelas Ini</span>
            <strong>{{ $waliMurid->murids->count() }} murid</strong>
        </div>
    </div>

    <div class="layout-grid">
        <div class="card">
            <div class="card-header">
                <div>
                    <h2>Daftar Murid</h2>
                    <p>Murid yang tampil hanya dari kelas {{ $kelasTerpilih->nama_kelas }}.</p>
                </div>
            </div>

            @if ($waliMurid->murids->isEmpty())
                <div class="empty-state">
                    Wali murid ini belum memiliki data murid aktif pada kelas {{ $kelasTerpilih->nama_kelas }}.
                </div>
            @else
                <div class="murid-list">
                    @foreach ($waliMurid->murids as $murid)
                        <a
                            href="{{ route($routePrefix . '.whatsapp-fonnte.show', ['waliMurid' => $waliMurid->id, 'kelas_id' => $kelasId, 'murid_id' => $murid->id]) }}"
                            class="murid-card {{ $selectedMurid && $selectedMurid->id === $murid->id ? 'active' : '' }}"
                        >
                            <div>
                                <h3>{{ $murid->nama_murid }}</h3>
                                <p>
                                    Kelas:
                                    <strong>{{ $murid->kelas?->nama_kelas ?? '-' }}</strong>
                                </p>
                            </div>

                            <i class="bi bi-check-circle-fill"></i>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="card">
            <div class="card-header">
                <div>
                    <h2>Pilihan Kirim Pesan</h2>
                    <p>Centang salah satu atau keduanya.</p>
                </div>
            </div>

            @if (! $selectedMurid)
                <div class="empty-state">
                    Pilih murid terlebih dahulu.
                </div>
            @else
                <form method="POST" action="{{ route($routePrefix . '.whatsapp-fonnte.send', $waliMurid->id) }}">
                    @csrf

                    <input type="hidden" name="murid_id" value="{{ $selectedMurid->id }}">
                    <input type="hidden" name="kelas_id" value="{{ $kelasId }}">

                    <div class="selected-student">
                        <span>Murid Dipilih</span>
                        <strong>{{ $selectedMurid->nama_murid }}</strong>
                        <p>Kelas: {{ $selectedMurid->kelas?->nama_kelas ?? '-' }}</p>
                    </div>

                    <div class="check-list">
                        <label class="check-card">
                            <input type="checkbox" name="send_absensi" value="1" {{ old('send_absensi') ? 'checked' : '' }}>
                            <div>
                                <strong>Kirim Absen Murid</strong>
                                <span>Mengirim 5 data absensi terbaru murid ke wali murid.</span>
                            </div>
                        </label>

                        <label class="check-card">
                            <input type="checkbox" name="send_nilai" value="1" {{ old('send_nilai') ? 'checked' : '' }}>
                            <div>
                                <strong>Kirim Nilai Ujian Semester</strong>
                                <span>Mengirim nilai ujian semester terbaru murid ke wali murid.</span>
                            </div>
                        </label>
                    </div>

                    <button
                        type="submit"
                        class="btn btn-primary btn-full"
                        data-confirm="true"
                        data-confirm-message="Apakah Anda yakin ingin mengirim pesan WhatsApp ke wali murid ini?"
                        data-confirm-yes="Ya, Kirim"
                        data-confirm-yes-class="btn-primary"
                        {{ ! $tokenTersedia ? 'disabled' : '' }}
                    >
                        <i class="bi bi-send me-1"></i>
                        Kirim WhatsApp
                    </button>
                </form>
            @endif
        </div>
    </div>

    @if ($selectedMurid)
        <div class="preview-grid">
            <div class="card">
                <div class="card-header">
                    <div>
                        <h2>Preview Absen Murid</h2>
                        <p>Data absen terbaru yang akan dikirim jika opsi absen dicentang.</p>
                    </div>

                    <span class="badge-count">
                        {{ $latestAbsensi->count() }} data
                    </span>
                </div>

                @if ($latestAbsensi->isEmpty())
                    <div class="empty-state">
                        Belum ada data absen murid.
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Mata Pelajaran</th>
                                    <th>Status</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($latestAbsensi as $absensi)
                                    <tr>
                                        <td>{{ $absensi->tanggal_absen ? $absensi->tanggal_absen->format('d-m-Y') : '-' }}</td>
                                        <td>{{ $absensi->mataPelajaran?->nama_mapel ?? '-' }}</td>
                                        <td>
                                            <span class="status-mini">
                                                {{ $absensi->status_absen_label }}
                                            </span>
                                        </td>
                                        <td>{{ $absensi->keterangan ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <div class="card">
                <div class="card-header">
                    <div>
                        <h2>Preview Nilai Ujian Semester</h2>
                        <p>Data nilai semester terbaru yang akan dikirim jika opsi nilai dicentang.</p>
                    </div>

                    <span class="badge-count">
                        {{ $latestNilais->count() }} data
                    </span>
                </div>

                @if ($latestNilais->isEmpty())
                    <div class="empty-state">
                        Belum ada data nilai ujian semester.
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Mata Pelajaran</th>
                                    <th>Nilai</th>
                                    <th>Predikat</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($latestNilais as $nilai)
                                    <tr>
                                        <td>{{ $nilai->mataPelajaran?->nama_mapel ?? '-' }}</td>
                                        <td>
                                            <strong>{{ $nilai->nilai_ujian }}</strong>
                                        </td>
                                        <td>
                                            <span class="status-mini">
                                                {{ $nilai->predikat }} - {{ $nilai->keterangan_predikat }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="average-box">
                        <span>Rata-rata Nilai</span>
                        <strong>{{ round($latestNilais->avg('nilai_ujian'), 2) }}</strong>
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>

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
    .card,
    .info-card,
    .warning-card {
        background: #ffffff;
        border-radius: 16px;
        padding: 22px;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
    }

    .page-header-card,
    .card-header,
    .warning-card {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
    }

    h1,
    h2,
    h3 {
        margin: 0;
        color: #0f172a;
        font-weight: 900;
    }

    p {
        margin: 6px 0 0;
        color: #64748b;
    }

    code {
        background: #f1f5f9;
        border-radius: 6px;
        padding: 2px 6px;
        color: #0f172a;
    }

    .warning-card {
        justify-content: flex-start;
        background: #fff7ed;
        color: #9a3412;
    }

    .warning-card i {
        font-size: 24px;
    }

    .warning-card p {
        color: #9a3412;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 14px;
    }

    .info-card {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .info-card span {
        color: #64748b;
        font-size: 13px;
    }

    .info-card strong {
        color: #0f172a;
        font-size: 18px;
    }

    .layout-grid {
        display: grid;
        grid-template-columns: 1fr 1.2fr;
        gap: 18px;
    }

    .preview-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 18px;
    }

    .murid-list {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .murid-card {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        border: 1px solid #e2e8f0;
        background: #f8fafc;
        border-radius: 14px;
        padding: 14px;
        text-decoration: none;
        color: inherit;
        transition: 0.2s ease;
    }

    .murid-card:hover,
    .murid-card.active {
        border-color: #2563eb;
        background: #eff6ff;
    }

    .murid-card h3 {
        font-size: 16px;
    }

    .murid-card i {
        color: #2563eb;
        opacity: 0;
    }

    .murid-card.active i {
        opacity: 1;
    }

    .selected-student {
        border: 1px solid #e2e8f0;
        background: #f8fafc;
        border-radius: 14px;
        padding: 14px;
        margin-bottom: 14px;
    }

    .selected-student span {
        color: #64748b;
        font-size: 13px;
    }

    .selected-student strong {
        display: block;
        color: #0f172a;
        font-size: 20px;
        margin-top: 4px;
    }

    .check-list {
        display: grid;
        grid-template-columns: 1fr;
        gap: 12px;
        margin-bottom: 14px;
    }

    .check-card {
        border: 1px solid #e2e8f0;
        background: #f8fafc;
        border-radius: 14px;
        padding: 16px;
        display: flex;
        align-items: flex-start;
        gap: 12px;
        cursor: pointer;
    }

    .check-card input {
        margin-top: 4px;
    }

    .check-card strong {
        color: #0f172a;
    }

    .check-card span {
        color: #64748b;
        display: block;
        margin-top: 4px;
        font-size: 13px;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        padding: 11px 18px;
        font-weight: 800;
        border: 1px solid transparent;
        cursor: pointer;
        text-decoration: none;
        white-space: nowrap;
    }

    .btn-primary {
        background: #2563eb;
        color: #ffffff;
    }

    .btn-primary:disabled {
        background: #94a3b8;
        cursor: not-allowed;
    }

    .btn-outline {
        background: #ffffff;
        color: #475569;
        border-color: #cbd5e1;
    }

    .btn-full {
        width: 100%;
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
        padding: 12px 10px;
        text-align: left;
        vertical-align: middle;
        white-space: nowrap;
    }

    .data-table th {
        background: #f8fafc;
        color: #0f172a;
        font-weight: 800;
    }

    .status-mini {
        background: #dbeafe;
        color: #2563eb;
        border-radius: 8px;
        padding: 5px 8px;
        font-weight: 800;
        font-size: 12px;
    }

    .average-box {
        margin-top: 14px;
        border: 1px solid #e2e8f0;
        background: #f8fafc;
        border-radius: 12px;
        padding: 12px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .average-box span {
        color: #64748b;
        font-weight: 700;
    }

    .average-box strong {
        color: #0f172a;
        font-size: 22px;
    }

    .empty-state {
        padding: 28px;
        text-align: center;
        color: #64748b;
        border: 1px dashed #cbd5e1;
        border-radius: 12px;
        background: #f8fafc;
    }

    .alert-success,
    .alert-danger {
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

    .alert-danger ul {
        margin: 8px 0 0;
    }

    @media (max-width: 1100px) {
        .info-grid,
        .preview-grid,
        .layout-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 800px) {
        .page-header-card,
        .card-header,
        .warning-card {
            flex-direction: column;
            align-items: flex-start;
        }
    }
</style>

@endsection
