{{-- penjelasan: File ini adalah halaman daftar kelas. --}}
{{-- penjelasan: File ini dipanggil oleh KelasController method index(). --}}
{{-- penjelasan: Halaman ini bisa diakses oleh Super Admin dan Admin. --}}
{{-- penjelasan: Data $kelasList dikirim dari controller menggunakan compact('kelasList', 'routePrefix'). --}}
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
                            Kelola data kelas, tingkat, wali kelas, status kelas, dan jumlah siswa per kelas.
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
                        <option value="7" {{ request('tingkat') === '7' ? 'selected' : '' }}>7</option>
                        <option value="8" {{ request('tingkat') === '8' ? 'selected' : '' }}>8</option>
                        <option value="9" {{ request('tingkat') === '9' ? 'selected' : '' }}>9</option>
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

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
            <div>
                <h6 class="fw-bold mb-0">Daftar Kelas</h6>
                <small class="text-muted">Data kelas yang terdaftar pada sistem</small>
            </div>

            <span class="badge bg-primary-subtle text-primary">
                {{ $kelasList->count() }} data tampil
            </span>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle admin-table">
                    <thead class="table-light">
                        <tr>
                            <th>Nama Kelas</th>
                            <th>Tingkat</th>
                            <th>Wali Kelas</th>
                            <th>Jumlah Siswa</th>
                            <th>Status</th>
                            <th class="text-end table-action-column">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($kelasList as $kelas)
                            @php
                                // penjelasan: Pesan konfirmasi dibuat sesuai status kelas.
                                $confirmMessage = $kelas->status === 'aktif'
                                    ? 'Apakah Anda yakin ingin menonaktifkan kelas ini? Data tidak dihapus, hanya statusnya menjadi nonaktif.'
                                    : 'Apakah Anda yakin ingin mengaktifkan kelas ini kembali?';
                            @endphp

                            <tr>
                                <td class="fw-semibold">
                                    {{ $kelas->nama_kelas }}
                                </td>

                                <td>
                                    <span class="badge bg-primary-subtle text-primary">
                                        Tingkat {{ $kelas->tingkat }}
                                    </span>
                                </td>

                                <td>
                                    @if ($kelas->waliKelas)
                                        {{ $kelas->waliKelas->nama_pegawai }}
                                    @else
                                        <span class="badge bg-warning-subtle text-warning">
                                            Belum ditentukan
                                        </span>
                                    @endif
                                </td>

                                <td>
                                    {{-- penjelasan: total_siswa berasal dari withCount relasi murids di KelasController. --}}
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
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    Data kelas belum tersedia.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $kelasList->links() }}
            </div>
        </div>
    </div>

@endsection
