{{-- penjelasan: File ini adalah halaman daftar tahun ajaran. --}}
{{-- penjelasan: File ini dipanggil oleh TahunAjaranController method index(). --}}
{{-- penjelasan: Halaman ini bisa diakses oleh Super Admin dan Admin. --}}
{{-- penjelasan: Alert berhasil, gagal, dan validasi sudah memakai komponen global admin.components.alert. --}}
{{-- penjelasan: Tombol aktif/nonaktif memakai modal konfirmasi global melalui data-confirm="true". --}}

@extends('admin.layouts.app')

@section('title', 'Tahun Ajaran')

@section('content')

    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                    <div>
                        <h4 class="fw-bold mb-1">Tahun Ajaran</h4>
                        <p class="text-muted mb-0">
                            Kelola periode tahun ajaran sekolah dan status tahun ajaran aktif.
                        </p>
                    </div>

                    <a href="{{ route($routePrefix . '.tahun-ajaran.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i>
                        Tambah Tahun Ajaran
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route($routePrefix . '.tahun-ajaran.index') }}" method="GET" class="row g-3">
                <div class="col-md-5">
                    <label class="form-label">
                        Cari Tahun Ajaran
                    </label>

                    <input
                        type="text"
                        name="search"
                        class="form-control"
                        placeholder="Contoh: 2025/2026"
                        value="{{ request('search') }}"
                    >
                </div>

                <div class="col-md-4">
                    <label class="form-label">
                        Status 
                    </label>

                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="aktif" {{ request('status') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="nonaktif" {{ request('status') === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>

                <div class="col-md-3 d-flex align-items-end">
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
                <h6 class="fw-bold mb-0">Daftar Tahun Ajaran</h6>
                <small class="text-muted">Periode akademik yang terdaftar</small>
            </div>

            <span class="badge bg-primary-subtle text-primary">
                {{ $tahunAjarans->count() }} data tampil
            </span>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle admin-table">
                    <thead class="table-light">
                        <tr>
                            <th>Nama Tahun Ajaran</th>
                            <th>Periode</th>
                            <th>Jumlah Semester</th>
                            <th>Status</th>
                            <th class="text-end table-action-column">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($tahunAjarans as $tahunAjaran)
                            @php
                                // penjelasan: Pesan konfirmasi dibuat sesuai status tahun ajaran.
                                $confirmMessage = $tahunAjaran->status === 'aktif'
                                    ? 'Apakah Anda yakin ingin menonaktifkan tahun ajaran ini? Jika dinonaktifkan, tidak ada tahun ajaran aktif sampai Anda mengaktifkan yang lain.'
                                    : 'Apakah Anda yakin ingin mengaktifkan tahun ajaran ini? Tahun ajaran aktif lainnya otomatis menjadi nonaktif.';
                            @endphp

                            <tr>
                                <td class="fw-semibold">
                                    {{ $tahunAjaran->nama_tahun_ajaran }}
                                </td>

                                <td>
                                    {{ $tahunAjaran->tanggal_mulai ? $tahunAjaran->tanggal_mulai->format('d-m-Y') : '-' }}
                                    sampai
                                    {{ $tahunAjaran->tanggal_selesai ? $tahunAjaran->tanggal_selesai->format('d-m-Y') : '-' }}
                                </td>

                                <td>
                                    <span class="badge bg-primary-subtle text-primary">
                                        {{ $tahunAjaran->semesters_count }} semester
                                    </span>
                                </td>

                                <td>
                                    @if ($tahunAjaran->status === 'aktif')
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
                                        <a href="{{ route($routePrefix . '.tahun-ajaran.show', $tahunAjaran) }}" class="btn btn-sm btn-outline-secondary action-btn">
                                            <i class="bi bi-eye"></i>
                                            <span>Detail</span>
                                        </a>

                                        <a href="{{ route($routePrefix . '.tahun-ajaran.edit', $tahunAjaran) }}" class="btn btn-sm btn-outline-primary action-btn">
                                            <i class="bi bi-pencil-square"></i>
                                            <span>Edit</span>
                                        </a>

                                        <form action="{{ route($routePrefix . '.tahun-ajaran.toggle-status', $tahunAjaran) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')

                                            <button
                                                type="submit"
                                                class="btn btn-sm action-btn {{ $tahunAjaran->status === 'aktif' ? 'btn-outline-danger' : 'btn-outline-success' }}"
                                                data-confirm="true"
                                                data-confirm-message="{{ $confirmMessage }}"
                                                data-confirm-yes="{{ $tahunAjaran->status === 'aktif' ? 'Ya, Nonaktifkan' : 'Ya, Aktifkan' }}"
                                                data-confirm-yes-class="{{ $tahunAjaran->status === 'aktif' ? 'btn-danger' : 'btn-success' }}"
                                            >
                                                @if ($tahunAjaran->status === 'aktif')
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
                                    Data tahun ajaran belum tersedia.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $tahunAjarans->links() }}
            </div>
        </div>
    </div>

@endsection
