{{-- penjelasan: Halaman ini menampilkan fitur WhatsApp Fonnte. --}}
{{-- penjelasan: Admin/Super Admin wajib memilih kelas terlebih dahulu. --}}
{{-- penjelasan: Setelah kelas dipilih, daftar wali murid dari kelas tersebut baru tampil. --}}
{{-- penjelasan: Link ke detail wali murid membawa kelas_id agar daftar murid tetap sesuai kelas yang dipilih. --}}

@extends('admin.layouts.app')

@section('title', 'WhatsApp Fonnte')

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
        <div id="flash-validation" data-message="Data belum valid. Silakan cek kembali input yang tersedia." class="alert-danger">
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
            <h1>WhatsApp Fonnte</h1>
            <p>Pilih kelas terlebih dahulu, lalu pilih wali murid untuk mengirim pesan WhatsApp.</p>
        </div>

        <span class="status-badge {{ $tokenTersedia ? 'active' : 'inactive' }}">
            {{ $tokenTersedia ? 'Token tersedia' : 'Token belum tersedia' }}
        </span>
    </div>

    @if (! $tokenTersedia)
        <div class="warning-card">
            <i class="bi bi-exclamation-triangle"></i>
            <div>
                <strong>Token Fonnte belum tersedia.</strong>
                <p>Isi token pada file <code>.env</code> dengan nama <code>FONNTE_TOKEN</code> agar pesan bisa dikirim.</p>
            </div>
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <div>
                <h2>1. Pilih Kelas</h2>
                <p>Daftar wali murid baru akan muncul setelah kelas dipilih.</p>
            </div>
        </div>

        <form method="GET" action="{{ route($routePrefix . '.whatsapp-fonnte.index') }}" class="class-filter-grid">
            <div class="form-group">
                <label>Kelas <span class="required">*</span></label>
                <select name="kelas_id" required>
                    <option value="">Pilih Kelas</option>

                    @foreach ($kelasList as $kelas)
                        <option value="{{ $kelas->id }}" @selected((string) $kelasId === (string) $kelas->id)>
                            {{ $kelas->nama_kelas }}
                            @if ($kelas->tingkat)
                                - Tingkat {{ $kelas->tingkat }}
                            @endif
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>Kata Kunci <span class="optional">(Opsional)</span></label>
                <input
                    type="text"
                    name="keyword"
                    value="{{ $keyword }}"
                    placeholder="Cari nama wali, nomor, atau nama murid..."
                >
            </div>

            <div class="filter-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-search me-1"></i>
                    Tampilkan Wali
                </button>

                <a href="{{ route($routePrefix . '.whatsapp-fonnte.index') }}" class="btn btn-outline">
                    Reset
                </a>
            </div>
        </form>

        <div class="kelas-group-wrapper">
            @foreach ($kelasPerTingkat as $tingkat => $kelasGroup)
                <div class="kelas-group">
                    <div class="kelas-group-header">
                        <span>Tingkat {{ $tingkat }}</span>
                        <strong>{{ $kelasGroup->count() }} kelas</strong>
                    </div>

                    <div class="kelas-chip-list">
                        @foreach ($kelasGroup as $kelas)
                            <a
                                href="{{ route($routePrefix . '.whatsapp-fonnte.index', ['kelas_id' => $kelas->id]) }}"
                                class="kelas-chip {{ (string) $kelasId === (string) $kelas->id ? 'active' : '' }}"
                            >
                                <i class="bi bi-building"></i>
                                <span>{{ $kelas->nama_kelas }}</span>
                                <small>{{ $kelas->total_murid_aktif }} murid</small>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="summary-grid">
        <div class="summary-card">
            <span>Kelas Dipilih</span>
            <strong>{{ $kelasTerpilih ? $kelasTerpilih->nama_kelas : '-' }}</strong>
        </div>

        <div class="summary-card">
            <span>Wali dengan WhatsApp</span>
            <strong>{{ $hasKelasFilter ? $totalWaliWhatsapp : 0 }}</strong>
        </div>

        <div class="summary-card">
            <span>Murid Terhubung</span>
            <strong>{{ $hasKelasFilter ? $totalMuridTerhubung : 0 }}</strong>
        </div>

        <div class="summary-card">
            <span>Data Tampil</span>
            <strong>{{ $hasKelasFilter ? $waliMurids->total() : 0 }}</strong>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <div>
                <h2>2. Daftar Nomor Wali Murid</h2>

                @if ($hasKelasFilter && $kelasTerpilih)
                    <p>Menampilkan wali murid yang memiliki murid aktif di kelas {{ $kelasTerpilih->nama_kelas }}.</p>
                @else
                    <p>Pilih kelas terlebih dahulu untuk menampilkan daftar wali murid.</p>
                @endif
            </div>

            <span class="badge-count">
                {{ $hasKelasFilter ? $waliMurids->total() . ' data' : 'Belum pilih kelas' }}
            </span>
        </div>

        @if (! $hasKelasFilter)
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="bi bi-filter-circle"></i>
                </div>

                <h3>Pilih kelas terlebih dahulu</h3>
                <p>Daftar wali murid tidak ditampilkan sebelum kelas dipilih agar proses pengiriman WhatsApp lebih terarah.</p>
            </div>
        @elseif ($waliMurids->isEmpty())
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="bi bi-person-x"></i>
                </div>

                <h3>Data wali murid tidak ditemukan</h3>
                <p>Belum ada wali murid aktif dengan nomor WhatsApp untuk kelas {{ $kelasTerpilih->nama_kelas }}.</p>
            </div>
        @else
            <div class="wali-grid">
                @foreach ($waliMurids as $waliMurid)
                    <a
                        href="{{ route($routePrefix . '.whatsapp-fonnte.show', ['waliMurid' => $waliMurid->id, 'kelas_id' => $kelasTerpilih->id]) }}"
                        class="wali-card"
                    >
                        <div class="wali-card-top">
                            <div class="avatar">
                                {{ strtoupper(substr($waliMurid->nama_wali, 0, 1)) }}
                            </div>

                            <div>
                                <h3>{{ $waliMurid->nama_wali }}</h3>
                                <p>{{ $waliMurid->hubungan ?? 'Wali Murid' }}</p>
                            </div>
                        </div>

                        <div class="info-list">
                            <div>
                                <span>Nomor WhatsApp</span>
                                <strong>{{ $waliMurid->no_whatsapp }}</strong>
                            </div>

                            <div>
                                <span>Murid di Kelas Ini</span>
                                <strong>{{ $waliMurid->murids_count }} murid</strong>
                            </div>
                        </div>

                        <div class="murid-list">
                            @forelse ($waliMurid->murids as $murid)
                                <span>
                                    {{ $murid->nama_murid }}
                                    @if ($murid->kelas)
                                        - {{ $murid->kelas->nama_kelas }}
                                    @endif
                                </span>
                            @empty
                                <span>Belum ada murid terhubung</span>
                            @endforelse
                        </div>

                        <div class="card-action">
                            <span>Pilih Wali Murid</span>
                            <i class="bi bi-arrow-right"></i>
                        </div>
                    </a>
                @endforeach
            </div>

            <div class="pagination-wrapper">
                {{ $waliMurids->links() }}
            </div>
        @endif
    </div>
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
    .summary-card,
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

    .required {
        color: #dc2626;
    }

    .optional {
        color: #64748b;
        font-weight: 600;
    }

    .status-badge {
        border-radius: 999px;
        padding: 8px 12px;
        font-weight: 800;
        white-space: nowrap;
    }

    .status-badge.active {
        background: #dcfce7;
        color: #15803d;
    }

    .status-badge.inactive {
        background: #fee2e2;
        color: #b91c1c;
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

    .class-filter-grid {
        display: grid;
        grid-template-columns: 280px minmax(0, 1fr) auto;
        gap: 16px;
        align-items: end;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    label {
        color: #0f172a;
        font-weight: 800;
    }

    input,
    select {
        width: 100%;
        border: 1px solid #dbe3ef;
        border-radius: 10px;
        padding: 11px 12px;
        outline: none;
        background: #ffffff;
        font-size: 14px;
    }

    input:focus,
    select:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
    }

    .filter-actions {
        display: flex;
        gap: 10px;
    }

    .kelas-group-wrapper {
        display: flex;
        flex-direction: column;
        gap: 14px;
        margin-top: 20px;
    }

    .kelas-group {
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 14px;
        background: #f8fafc;
    }

    .kelas-group-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 10px;
        margin-bottom: 12px;
    }

    .kelas-group-header span {
        color: #64748b;
        font-weight: 700;
    }

    .kelas-group-header strong {
        background: #dbeafe;
        color: #2563eb;
        border-radius: 999px;
        padding: 4px 9px;
        font-size: 12px;
    }

    .kelas-chip-list {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .kelas-chip {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        border: 1px solid #dbe3ef;
        background: #ffffff;
        color: #0f172a;
        border-radius: 12px;
        padding: 10px 12px;
        text-decoration: none;
        font-weight: 800;
        transition: 0.2s ease;
    }

    .kelas-chip small {
        color: #64748b;
        font-weight: 700;
    }

    .kelas-chip:hover,
    .kelas-chip.active {
        border-color: #2563eb;
        background: #eff6ff;
        color: #2563eb;
    }

    .summary-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 14px;
    }

    .summary-card {
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
        font-size: 28px;
        line-height: 1;
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

    .btn-outline {
        background: #ffffff;
        color: #475569;
        border-color: #cbd5e1;
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

    .wali-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 14px;
    }

    .wali-card {
        border: 1px solid #e2e8f0;
        background: #f8fafc;
        border-radius: 16px;
        padding: 16px;
        text-decoration: none;
        color: inherit;
        display: flex;
        flex-direction: column;
        gap: 14px;
        transition: 0.2s ease;
    }

    .wali-card:hover {
        border-color: #2563eb;
        background: #eff6ff;
        transform: translateY(-2px);
    }

    .wali-card-top {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .avatar {
        width: 46px;
        height: 46px;
        border-radius: 999px;
        background: #dbeafe;
        color: #2563eb;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 900;
        flex: 0 0 auto;
    }

    .wali-card h3 {
        font-size: 17px;
    }

    .wali-card p {
        font-size: 13px;
    }

    .info-list {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
    }

    .info-list div {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 10px;
    }

    .info-list span {
        display: block;
        color: #64748b;
        font-size: 12px;
        margin-bottom: 4px;
    }

    .info-list strong {
        color: #0f172a;
        font-size: 13px;
    }

    .murid-list {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
    }

    .murid-list span {
        background: #e0f2fe;
        color: #0369a1;
        border-radius: 999px;
        padding: 5px 8px;
        font-size: 12px;
        font-weight: 700;
    }

    .card-action {
        display: flex;
        align-items: center;
        justify-content: space-between;
        color: #2563eb;
        font-weight: 900;
        margin-top: auto;
    }

    .empty-state {
        padding: 34px 24px;
        text-align: center;
        color: #64748b;
        border: 1px dashed #cbd5e1;
        border-radius: 12px;
        background: #f8fafc;
    }

    .empty-state h3 {
        font-size: 20px;
        margin-top: 10px;
    }

    .empty-icon {
        width: 58px;
        height: 58px;
        border-radius: 999px;
        background: #dbeafe;
        color: #2563eb;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 26px;
    }

    .pagination-wrapper {
        margin-top: 18px;
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

    @media (max-width: 1200px) {
        .summary-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .wali-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .class-filter-grid {
            grid-template-columns: 1fr 1fr;
        }

        .filter-actions {
            grid-column: span 2;
        }
    }

    @media (max-width: 800px) {
        .page-header-card,
        .card-header,
        .warning-card {
            flex-direction: column;
            align-items: flex-start;
        }

        .summary-grid,
        .wali-grid,
        .class-filter-grid,
        .info-list {
            grid-template-columns: 1fr;
        }

        .filter-actions {
            grid-column: auto;
            flex-direction: column;
            align-items: stretch;
        }

        .kelas-chip {
            width: 100%;
            justify-content: space-between;
        }
    }
</style>

@endsection
