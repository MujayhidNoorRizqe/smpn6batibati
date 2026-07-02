{{-- penjelasan: File ini adalah halaman daftar mata pelajaran. --}}
{{-- penjelasan: File ini dipanggil oleh MataPelajaranController method index(). --}}
{{-- penjelasan: Halaman ini bisa diakses oleh Super Admin dan Admin. --}}
{{-- penjelasan: Data $mataPelajarans berisi daftar mata pelajaran dari database. --}}
{{-- penjelasan: routePrefix dipakai agar link otomatis menyesuaikan role login, yaitu super-admin atau admin. --}}
{{-- penjelasan: Alert berhasil, gagal, dan validasi sudah memakai komponen global admin.components.alert. --}}
{{-- penjelasan: Tombol aktif/nonaktif memakai modal konfirmasi global melalui data-confirm="true". --}}

@extends('admin.layouts.app')

@section('title', 'Data Mata Pelajaran')

@section('content')

    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                    <div>
                        <h4 class="fw-bold mb-1">Data Mata Pelajaran</h4>
                        <p class="text-muted mb-0">
                            Kelola data mata pelajaran untuk jadwal pelajaran dan nilai murid.
                        </p>
                    </div>

                    <a href="{{ route($routePrefix . '.mata-pelajaran.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i>
                        Tambah Mata Pelajaran
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">

            <form action="{{ route($routePrefix . '.mata-pelajaran.index') }}" method="GET" class="row g-3">

                <div class="col-md-4">
                    <label class="form-label">
                        Cari Mata Pelajaran
                    </label>

                    <input
                        type="text"
                        name="search"
                        class="form-control"
                        placeholder="Cari kode atau nama mata pelajaran"
                        value="{{ request('search') }}"
                    >
                </div>

                <div class="col-md-3">
                    <label class="form-label">
                        Kelompok
                    </label>

                    <select name="kelompok" class="form-select">
                        <option value="">Semua Kelompok</option>
                        <option value="umum" {{ request('kelompok') === 'umum' ? 'selected' : '' }}>Umum</option>
                        <option value="muatan_lokal" {{ request('kelompok') === 'muatan_lokal' ? 'selected' : '' }}>Muatan Lokal</option>
                        <option value="ekstrakurikuler" {{ request('kelompok') === 'ekstrakurikuler' ? 'selected' : '' }}>Ekstrakurikuler</option>
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
                <h6 class="fw-bold mb-0">Daftar Mata Pelajaran</h6>
                <small class="text-muted">Data mata pelajaran yang terdaftar pada sistem</small>
            </div>

            <span class="badge bg-primary-subtle text-primary">
                {{ $mataPelajarans->count() }} data tampil
            </span>
        </div>

        <div class="card-body">

            <div class="table-responsive">
                <table class="table table-hover align-middle admin-table">
                    <thead class="table-light">
                        <tr>
                            <th>Kode</th>
                            <th>Nama Mata Pelajaran</th>
                            <th>Kelompok</th>
                            <th>Status</th>
                            <th class="text-end table-action-column">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($mataPelajarans as $mataPelajaran)
                            @php
                                // penjelasan: Pesan konfirmasi dibuat sesuai status mata pelajaran.
                                $confirmMessage = $mataPelajaran->status === 'aktif'
                                    ? 'Apakah Anda yakin ingin menonaktifkan mata pelajaran ini? Data tidak dihapus, hanya statusnya menjadi nonaktif.'
                                    : 'Apakah Anda yakin ingin mengaktifkan mata pelajaran ini kembali?';
                            @endphp

                            <tr>
                                <td>
                                    <span class="badge bg-dark-subtle text-dark">
                                        {{ $mataPelajaran->kode_mapel }}
                                    </span>
                                </td>

                                <td>
                                    <div class="fw-semibold">{{ $mataPelajaran->nama_mapel }}</div>
                                    <small class="text-muted">
                                        {{ $mataPelajaran->deskripsi ? Str::limit($mataPelajaran->deskripsi, 60) : 'Tidak ada deskripsi' }}
                                    </small>
                                </td>

                                <td>
                                    <span class="badge bg-primary-subtle text-primary">
                                        {{ $mataPelajaran->kelompok_label }}
                                    </span>
                                </td>

                                <td>
                                    @if ($mataPelajaran->status === 'aktif')
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

                                        <a href="{{ route($routePrefix . '.mata-pelajaran.show', $mataPelajaran) }}" class="btn btn-sm btn-outline-secondary action-btn">
                                            <i class="bi bi-eye"></i>
                                            <span>Detail</span>
                                        </a>

                                        <a href="{{ route($routePrefix . '.mata-pelajaran.edit', $mataPelajaran) }}" class="btn btn-sm btn-outline-primary action-btn">
                                            <i class="bi bi-pencil-square"></i>
                                            <span>Edit</span>
                                        </a>

                                        <form action="{{ route($routePrefix . '.mata-pelajaran.toggle-status', $mataPelajaran) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')

                                            <button
                                                type="submit"
                                                class="btn btn-sm action-btn {{ $mataPelajaran->status === 'aktif' ? 'btn-outline-danger' : 'btn-outline-success' }}"
                                                data-confirm="true"
                                                data-confirm-message="{{ $confirmMessage }}"
                                                data-confirm-yes="{{ $mataPelajaran->status === 'aktif' ? 'Ya, Nonaktifkan' : 'Ya, Aktifkan' }}"
                                                data-confirm-yes-class="{{ $mataPelajaran->status === 'aktif' ? 'btn-danger' : 'btn-success' }}"
                                            >
                                                @if ($mataPelajaran->status === 'aktif')
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
                                <td colspan="5" class="text-center text-muted py-4">
                                    Data mata pelajaran belum tersedia.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $mataPelajarans->links() }}
            </div>

        </div>
    </div>

@endsection
