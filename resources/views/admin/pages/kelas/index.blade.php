{{-- penjelasan: File ini adalah halaman daftar kelas. --}}
{{-- penjelasan: File ini dipanggil oleh KelasController method index(). --}}
{{-- penjelasan: Halaman ini bisa diakses oleh Super Admin dan Admin. --}}
{{-- penjelasan: Data kelas dikelompokkan berdasarkan tingkat agar tampilan lebih rapi. --}}
{{-- penjelasan: routePrefix dipakai agar link bisa menyesuaikan apakah user login sebagai super-admin atau admin. --}}
{{-- penjelasan: Alert berhasil, gagal, dan validasi sudah memakai komponen global admin.components.alert. --}}
{{-- penjelasan: Tombol aktif/nonaktif memakai modal konfirmasi global melalui data-confirm="true". --}}
{{-- penjelasan: Halaman ini juga menampilkan total jumlah siswa per kelas dari relasi murids. --}}

@extends('admin.layouts.app')

@section('title', 'Data Kelas')

@section('content')

    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                    <div>
                        <h4 class="fw-bold mb-1">Data Kelas</h4>
                        <p class="text-muted mb-0">
                            Kelola data kelas berdasarkan tingkat, wali kelas, status kelas, dan jumlah siswa.
                        </p>
                    </div>

                    <a href="{{ route($routePrefix . '.kelas.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i>
                        Tambah Kelas
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route($routePrefix . '.kelas.index') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">
                        Cari Kelas
                    </label>

                    <input
                        type="text"
                        name="search"
                        class="form-control"
                        placeholder="Cari nama kelas atau tingkat"
                        value="{{ request('search') }}"
                    >
                </div>

                <div class="col-md-3">
                    <label class="form-label">
                        Tingkat
                    </label>

                    <select name="tingkat" class="form-select">
                        <option value="">Semua Tingkat</option>
                        <option value="7" {{ request('tingkat') === '7' ? 'selected' : '' }}>Tingkat 7</option>
                        <option value="8" {{ request('tingkat') === '8' ? 'selected' : '' }}>Tingkat 8</option>
                        <option value="9" {{ request('tingkat') === '9' ? 'selected' : '' }}>Tingkat 9</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">
                        Status
                    </label>

                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="aktif" {{ request('status') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="nonaktif" {{ request('status') === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>

                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search me-1"></i>
                        Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="summary-card">
                <span>Total Kelas</span>
                <strong>{{ $totalKelas }}</strong>
            </div>
        </div>

        <div class="col-md-4">
            <div class="summary-card">
                <span>Total Siswa</span>
                <strong>{{ $totalSiswa }}</strong>
            </div>
        </div>

        <div class="col-md-4">
            <div class="summary-card">
                <span>Tingkat Tampil</span>
                <strong>{{ $kelasPerTingkat->count() }}</strong>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
            <div>
                <h6 class="fw-bold mb-0">Daftar Kelas per Tingkat</h6>
                <small class="text-muted">Data kelas dikelompokkan berdasarkan tingkat</small>
            </div>

            <span class="badge bg-primary-subtle text-primary">
                {{ $totalKelas }} data tampil
            </span>
        </div>

        <div class="card-body">
            @if ($kelasPerTingkat->isEmpty())
                <div class="text-center text-muted py-4">
                    Data kelas belum tersedia.
                </div>
            @else
                <div class="kelas-level-list">
                    @foreach ($kelasPerTingkat as $tingkat => $kelasItems)
                        <div class="kelas-level-card">
                            <div class="kelas-level-header">
                                <div>
                                    <span class="level-label">Tingkat</span>
                                    <h5 class="fw-bold mb-0">{{ $tingkat }}</h5>
                                </div>

                                <div class="level-badges">
                                    <span class="badge bg-primary-subtle text-primary">
                                        {{ $kelasItems->count() }} kelas
                                    </span>

                                    <span class="badge bg-info-subtle text-info">
                                        {{ $kelasItems->sum('total_siswa') }} siswa
                                    </span>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-hover align-middle admin-table mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 160px;">Nama Kelas</th>
                                            <th>Wali Kelas</th>
                                            <th style="width: 150px;">Jumlah Siswa</th>
                                            <th style="width: 130px;">Status</th>
                                            <th class="text-end table-action-column">Aksi</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @foreach ($kelasItems as $kelas)
                                            @php
                                                $confirmMessage = $kelas->status === 'aktif'
                                                    ? 'Apakah Anda yakin ingin menonaktifkan kelas ini? Data tidak dihapus, hanya statusnya menjadi nonaktif.'
                                                    : 'Apakah Anda yakin ingin mengaktifkan kelas ini kembali?';
                                            @endphp

                                            <tr>
                                                <td>
                                                    <div class="class-name-box">
                                                        <span>Kelas</span>
                                                        <strong>{{ $kelas->nama_kelas }}</strong>
                                                    </div>
                                                </td>

                                                <td>
                                                    @if ($kelas->waliKelas)
                                                        <span class="fw-semibold">
                                                            {{ $kelas->waliKelas->nama_pegawai }}
                                                        </span>
                                                    @else
                                                        <span class="badge bg-warning-subtle text-warning">
                                                            Belum ditentukan
                                                        </span>
                                                    @endif
                                                </td>

                                                <td>
                                                    <span class="badge bg-info-subtle text-info">
                                                        {{ $kelas->total_siswa ?? 0 }} siswa
                                                    </span>
                                                </td>

                                                <td>
                                                    @if ($kelas->status === 'aktif')
                                                        <span class="badge bg-success-subtle text-success">
                                                            Aktif
                                                        </span>
                                                    @else
                                                        <span class="badge bg-danger-subtle text-danger">
                                                            Nonaktif
                                                        </span>
                                                    @endif
                                                </td>

                                                <td class="text-end">
                                                    <div class="action-buttons">
                                                        <a href="{{ route($routePrefix . '.kelas.show', $kelas) }}" class="btn btn-sm btn-outline-secondary action-btn">
                                                            <i class="bi bi-eye"></i>
                                                            <span>Detail</span>
                                                        </a>

                                                        <a href="{{ route($routePrefix . '.kelas.edit', $kelas) }}" class="btn btn-sm btn-outline-primary action-btn">
                                                            <i class="bi bi-pencil-square"></i>
                                                            <span>Edit</span>
                                                        </a>

                                                        <form action="{{ route($routePrefix . '.kelas.toggle-status', $kelas) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('PATCH')

                                                            <button
                                                                type="submit"
                                                                class="btn btn-sm action-btn {{ $kelas->status === 'aktif' ? 'btn-outline-danger' : 'btn-outline-success' }}"
                                                                data-confirm="true"
                                                                data-confirm-message="{{ $confirmMessage }}"
                                                                data-confirm-yes="{{ $kelas->status === 'aktif' ? 'Ya, Nonaktifkan' : 'Ya, Aktifkan' }}"
                                                                data-confirm-yes-class="{{ $kelas->status === 'aktif' ? 'btn-danger' : 'btn-success' }}"
                                                            >
                                                                @if ($kelas->status === 'aktif')
                                                                    <i class="bi bi-x-circle"></i>
                                                                    <span>Nonaktif</span>
                                                                @else
                                                                    <i class="bi bi-check-circle"></i>
                                                                    <span>Aktifkan</span>
                                                                @endif
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <style>
        .summary-card {
            background: #ffffff;
            border-radius: 14px;
            padding: 18px;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
            display: flex;
            flex-direction: column;
            gap: 6px;
            border: 1px solid #eef2f7;
        }

        .summary-card span {
            color: #64748b;
            font-size: 13px;
            font-weight: 600;
        }

        .summary-card strong {
            color: #0f172a;
            font-size: 26px;
            line-height: 1;
        }

        .kelas-level-list {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        .kelas-level-card {
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            background: #ffffff;
            overflow: hidden;
        }

        .kelas-level-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 14px;
            padding: 18px;
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
        }

        .level-label {
            display: block;
            color: #64748b;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 3px;
        }

        .level-badges {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .class-name-box {
            display: inline-flex;
            flex-direction: column;
            gap: 2px;
        }

        .class-name-box span {
            color: #64748b;
            font-size: 12px;
        }

        .class-name-box strong {
            color: #0f172a;
            font-size: 17px;
        }

        .action-buttons {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        @media (max-width: 768px) {
            .kelas-level-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .action-buttons {
                justify-content: flex-start;
            }
        }
    </style>

@endsection
