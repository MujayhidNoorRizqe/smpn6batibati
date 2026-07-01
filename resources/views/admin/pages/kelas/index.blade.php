{{-- penjelasan: File ini adalah halaman daftar kelas. --}}
{{-- penjelasan: File ini dipanggil oleh KelasController method index(). --}}
{{-- penjelasan: Halaman ini bisa diakses oleh Super Admin dan Admin. --}}
{{-- penjelasan: Data $kelasList dikirim dari controller menggunakan compact('kelasList', 'routePrefix'). --}}
{{-- penjelasan: routePrefix dipakai agar link bisa menyesuaikan apakah user login sebagai super-admin atau admin. --}}

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
                            Kelola data kelas, tingkat, dan wali kelas.
                        </p>
                    </div>

                    {{-- penjelasan: Tombol ini mengarah ke halaman tambah kelas. --}}
                    <a href="{{ route($routePrefix . '.kelas.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i>
                        Tambah Kelas
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- penjelasan: Alert sukses tampil setelah data berhasil ditambah, diedit, atau status diubah. --}}
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    {{-- penjelasan: Card ini berisi form pencarian dan filter kelas. --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route($routePrefix . '.kelas.index') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Cari Kelas</label>
                    <input
                        type="text"
                        name="search"
                        class="form-control"
                        placeholder="Cari nama kelas atau tingkat"
                        value="{{ request('search') }}"
                    >
                </div>

                <div class="col-md-3">
                    <label class="form-label">Tingkat</label>
                    <select name="tingkat" class="form-select">
                        <option value="">Semua Tingkat</option>
                        <option value="7" {{ request('tingkat') === '7' ? 'selected' : '' }}>7</option>
                        <option value="8" {{ request('tingkat') === '8' ? 'selected' : '' }}>8</option>
                        <option value="9" {{ request('tingkat') === '9' ? 'selected' : '' }}>9</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Status</label>
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

    {{-- penjelasan: Card ini berisi tabel data kelas. --}}
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
                            <th>Status</th>
                            <th class="text-end table-action-column">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($kelasList as $kelas)
                            <tr>
                                <td class="fw-semibold">{{ $kelas->nama_kelas }}</td>

                                <td>
                                    <span class="badge bg-primary-subtle text-primary">
                                        Tingkat {{ $kelas->tingkat }}
                                    </span>
                                </td>

                                <td>
                                    @if ($kelas->waliKelas)
                                        {{ $kelas->waliKelas->nama_pegawai }}
                                    @else
                                        <span class="text-muted">Belum ditentukan</span>
                                    @endif
                                </td>

                                <td>
                                    <span class="badge {{ $kelas->status === 'aktif' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }}">
                                        {{ ucfirst($kelas->status) }}
                                    </span>
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
                                                onclick="return confirm('Yakin ingin mengubah status kelas ini?')"
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
                                <td colspan="5" class="text-center text-muted py-4">
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
