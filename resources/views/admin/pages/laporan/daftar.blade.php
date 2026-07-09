{{-- penjelasan: File ini dipakai untuk dua mode. --}}
{{-- penjelasan: Mode daftar menampilkan list guru atau list kelas. --}}
{{-- penjelasan: Mode murid menampilkan list murid dari kelas yang dipilih. --}}

@extends('admin.layouts.app')

@section('title', $jenisLabel)

@section('content')

<div class="page-content">
    <div class="page-header-card">
        <div>
            <h1>{{ $jenisLabel }}</h1>

            @if ($mode === 'daftar')
                @if ($jenis === 'absensi_guru')
                    <p>Pilih guru untuk melihat pilihan periode laporan absensi.</p>
                @elseif ($jenis === 'absensi_murid')
                    <p>Pilih kelas terlebih dahulu, lalu pilih murid untuk melihat laporan absensi.</p>
                @else
                    <p>Pilih kelas berdasarkan tingkat, lalu pilih murid untuk melihat laporan nilai.</p>
                @endif
            @else
                <p>Pilih murid dari kelas {{ $kelas->nama_kelas }} untuk melihat pilihan periode laporan.</p>
            @endif
        </div>

        <a href="{{ $mode === 'daftar' ? route($routePrefix . '.laporan.index') : route($routePrefix . '.laporan.daftar', $jenis) }}" class="btn btn-outline">
            <i class="bi bi-arrow-left me-1"></i>
            Kembali
        </a>
    </div>

    @if ($mode === 'daftar' && $jenis === 'absensi_guru')
        <div class="card">
            <div class="card-header">
                <div>
                    <h2>Daftar Guru</h2>
                    <p>Klik salah satu guru untuk memilih periode laporan absensi.</p>
                </div>

                <span class="badge-count">
                    {{ $pegawaiList->count() }} guru
                </span>
            </div>

            @if ($pegawaiList->isEmpty())
                <div class="empty-state">Belum ada guru aktif.</div>
            @else
                <div class="person-grid">
                    @foreach ($pegawaiList as $pegawai)
                        <a href="{{ route($routePrefix . '.laporan.periode', ['jenis' => $jenis, 'targetType' => 'guru', 'targetId' => $pegawai->id]) }}" class="person-card">
                            <div class="avatar">
                                {{ strtoupper(substr($pegawai->nama_pegawai, 0, 1)) }}
                            </div>

                            <div>
                                <h3>{{ $pegawai->nama_pegawai }}</h3>
                                <p>{{ $pegawai->jabatan ?? '-' }}</p>

                                <span class="badge-count">
                                    {{ $rekapCounts[$pegawai->id] ?? 0 }} data absensi
                                </span>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    @endif

    @if ($mode === 'daftar' && in_array($jenis, ['absensi_murid', 'nilai'], true))
        <div class="card">
            <div class="card-header">
                <div>
                    <h2>Daftar Kelas per Tingkat</h2>
                    <p>Kelas dikelompokkan berdasarkan tingkat agar lebih rapi.</p>
                </div>

                <span class="badge-count">
                    {{ $kelasList->count() }} kelas
                </span>
            </div>

            @if ($kelasPerTingkat->isEmpty())
                <div class="empty-state">Belum ada kelas aktif.</div>
            @else
                <div class="level-list">
                    @foreach ($kelasPerTingkat as $tingkat => $kelasItems)
                        <div class="level-card">
                            <div class="level-header">
                                <div>
                                    <span>Tingkat</span>
                                    <h3>{{ $tingkat }}</h3>
                                </div>

                                <span class="badge-count">
                                    {{ $kelasItems->count() }} kelas
                                </span>
                            </div>

                            <div class="class-grid">
                                @foreach ($kelasItems as $kelas)
                                    <a href="{{ route($routePrefix . '.laporan.daftar-murid', ['jenis' => $jenis, 'kelas' => $kelas->id]) }}" class="class-card">
                                        <div>
                                            <span>Kelas</span>
                                            <h4>{{ $kelas->nama_kelas }}</h4>
                                        </div>

                                        <div class="class-meta">
                                            <span>{{ $kelas->total_murid ?? 0 }} murid</span>
                                            <span>{{ $rekapCounts[$kelas->id] ?? 0 }} data</span>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    @endif

    @if ($mode === 'murid')
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
                <strong>{{ $kelas->waliKelas?->nama_pegawai ?? '-' }}</strong>
            </div>

            <div>
                <span>Total Murid</span>
                <strong>{{ $muridList->count() }}</strong>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <div>
                    <h2>Daftar Murid Kelas {{ $kelas->nama_kelas }}</h2>
                    <p>Klik murid untuk memilih periode laporan.</p>
                </div>

                <span class="badge-count">
                    {{ $muridList->count() }} murid
                </span>
            </div>

            @if ($muridList->isEmpty())
                <div class="empty-state">Belum ada murid aktif pada kelas ini.</div>
            @else
                <div class="person-grid">
                    @foreach ($muridList as $murid)
                        <a href="{{ route($routePrefix . '.laporan.periode', ['jenis' => $jenis, 'targetType' => 'murid', 'targetId' => $murid->id]) }}" class="person-card">
                            <div class="avatar">
                                {{ strtoupper(substr($murid->nama_murid, 0, 1)) }}
                            </div>

                            <div>
                                <h3>{{ $murid->nama_murid }}</h3>
                                <p>NIS/NISN: {{ $murid->nis ?? $murid->nisn ?? '-' }}</p>

                                <span class="badge-count">
                                    {{ $rekapCounts[$murid->id] ?? 0 }} data
                                </span>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    @endif
</div>

<style>
    .page-content {
        display: flex;
        flex-direction: column;
        gap: 18px;
    }

    .page-header-card,
    .card,
    .info-grid {
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
        font-weight: 900;
    }

    .page-header-card p,
    .card-header p {
        margin: 6px 0 0;
        color: #64748b;
    }

    .card-header,
    .level-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 14px;
        margin-bottom: 18px;
    }

    .person-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 14px;
    }

    .person-card,
    .class-card {
        border: 1px solid #e2e8f0;
        background: #f8fafc;
        border-radius: 16px;
        padding: 16px;
        color: inherit;
        text-decoration: none;
        transition: 0.2s ease;
    }

    .person-card {
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .person-card:hover,
    .class-card:hover {
        border-color: #2563eb;
        background: #eff6ff;
        transform: translateY(-2px);
    }

    .avatar {
        width: 48px;
        height: 48px;
        border-radius: 999px;
        background: #dbeafe;
        color: #2563eb;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 900;
        flex: 0 0 auto;
    }

    .person-card h3 {
        margin: 0;
        color: #0f172a;
        font-size: 17px;
        font-weight: 900;
    }

    .person-card p {
        margin: 4px 0 8px;
        color: #64748b;
        font-size: 13px;
    }

    .level-list {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .level-card {
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 18px;
        background: #ffffff;
    }

    .level-header span,
    .class-card span,
    .info-grid span {
        color: #64748b;
        font-size: 13px;
    }

    .level-header h3 {
        margin: 3px 0 0;
        font-size: 24px;
        font-weight: 900;
        color: #0f172a;
    }

    .class-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 12px;
    }

    .class-card h4 {
        margin: 3px 0 10px;
        font-size: 24px;
        font-weight: 900;
        color: #0f172a;
    }

    .class-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
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

    .info-grid strong {
        color: #0f172a;
    }

    .badge-count {
        background: #dbeafe;
        color: #2563eb;
        border-radius: 8px;
        padding: 6px 10px;
        font-weight: 800;
        font-size: 13px;
        display: inline-flex;
        width: fit-content;
        white-space: nowrap;
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
        .person-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .class-grid,
        .info-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 700px) {
        .page-header-card,
        .card-header,
        .level-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .person-grid,
        .class-grid,
        .info-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

@endsection
