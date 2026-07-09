{{-- penjelasan: File ini adalah halaman pilih kelas untuk membuat jadwal pelajaran. --}}
{{-- penjelasan: Jadwal pelajaran dibuat per kelas. --}}
{{-- penjelasan: Kelas yang tampil berasal dari fitur Data Kelas. --}}
{{-- penjelasan: Kelas dipisahkan berdasarkan tingkat, misalnya 7, 8, dan 9. --}}
{{-- penjelasan: Setelah memilih kelas, user masuk ke halaman list hari Senin sampai Sabtu. --}}

@extends('admin.layouts.app')

@section('title', 'Tambah Jadwal Pelajaran')

@section('content')

<div class="page-content">
    <div class="page-header-card">
        <div>
            <h1>Tambah Jadwal Pelajaran</h1>
            <p>Pilih kelas terlebih dahulu, lalu susun jadwal per hari untuk satu minggu.</p>
        </div>

        <a href="{{ route($routePrefix . '.jadwal-pelajaran.index') }}" class="btn btn-outline">
            <i class="bi bi-arrow-left me-1"></i>
            Kembali
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
                <h2>1. Pilih Kelas</h2>
                <p>Kelas yang tampil berasal dari fitur Data Kelas dan dipisahkan berdasarkan tingkat.</p>
            </div>
        </div>

        @if ($kelasPerTingkat->isEmpty())
            <div class="empty-state">
                Belum ada kelas aktif. Silakan tambahkan kelas terlebih dahulu pada menu Data Kelas.
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
                                    href="{{ route($routePrefix . '.jadwal-pelajaran.create-kelas', $kelas->id) }}"
                                    class="class-card"
                                >
                                    <div class="class-icon">
                                        <i class="bi bi-building"></i>
                                    </div>

                                    <div>
                                        <span>Kelas</span>
                                        <strong>{{ $kelas->nama_kelas }}</strong>
                                    </div>

                                    <small>
                                        Wali Kelas: {{ $kelas->waliKelas->nama_pegawai ?? '-' }}
                                    </small>
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

    .card-header {
        margin-bottom: 18px;
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
        align-items: center;
        gap: 14px;
        margin-bottom: 14px;
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

    .badge {
        background: #dbeafe;
        color: #2563eb;
        border-radius: 8px;
        padding: 6px 10px;
        font-weight: 800;
        font-size: 13px;
    }

    .class-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 14px;
    }

    .class-card {
        min-height: 130px;
        border: 1px solid #dbe3ef;
        border-radius: 14px;
        padding: 16px;
        background: #f8fafc;
        color: inherit;
        text-decoration: none;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        gap: 10px;
        transition: 0.2s ease;
    }

    .class-card:hover {
        border-color: #2563eb;
        background: #eff6ff;
        transform: translateY(-2px);
    }

    .class-icon {
        width: 38px;
        height: 38px;
        border-radius: 12px;
        background: #dbeafe;
        color: #2563eb;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
    }

    .class-card span {
        color: #64748b;
        font-size: 12px;
    }

    .class-card strong {
        display: block;
        color: #0f172a;
        font-size: 24px;
        margin-top: 2px;
    }

    .class-card small {
        color: #64748b;
        font-size: 12px;
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
        .class-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }
    }

    @media (max-width: 900px) {
        .class-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 700px) {
        .page-header-card,
        .level-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .class-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

@endsection
