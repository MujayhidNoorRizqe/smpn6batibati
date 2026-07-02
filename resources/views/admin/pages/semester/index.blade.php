{{-- penjelasan: File ini adalah halaman daftar semester. --}}
{{-- penjelasan: File ini dipanggil oleh SemesterController method index(). --}}
{{-- penjelasan: Halaman ini bisa diakses oleh Super Admin dan Admin. --}}
{{-- penjelasan: Alert berhasil, gagal, dan validasi sudah memakai komponen global admin.components.alert. --}}
{{-- penjelasan: Tombol aktif/nonaktif memakai modal konfirmasi global melalui data-confirm="true". --}}

@extends('admin.layouts.app')

@section('title', 'Semester')

@section('content')

    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                    <div>
                        <h4 class="fw-bold mb-1">Semester</h4>
                        <p class="text-muted mb-0">
                            Kelola semester ganjil dan genap sesuai tahun ajaran.
                        </p>
                    </div>

                    <a href="{{ route($routePrefix . '.semester.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i>
                        Tambah Semester
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route($routePrefix . '.semester.index') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">
                        Tahun Ajaran
                    </label>

                    <select name="tahun_ajaran_id" class="form-select">
                        <option value="">Semua Tahun Ajaran</option>

                        @foreach ($tahunAjarans as $tahunAjaran)
                            <option value="{{ $tahunAjaran->id }}" {{ request('tahun_ajaran_id') == $tahunAjaran->id ? 'selected' : '' }}>
                                {{ $tahunAjaran->nama_tahun_ajaran }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">
                        Semester
                    </label>

                    <select name="nama_semester" class="form-select">
                        <option value="">Semua Semester</option>
                        <option value="ganjil" {{ request('nama_semester') === 'ganjil' ? 'selected' : '' }}>Ganjil</option>
                        <option value="genap" {{ request('nama_semester') === 'genap' ? 'selected' : '' }}>Genap</option>
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
                <h6 class="fw-bold mb-0">Daftar Semester</h6>
                <small class="text-muted">Data semester yang terdaftar</small>
            </div>

            <span class="badge bg-primary-subtle text-primary">
                {{ $semesters->count() }} data tampil
            </span>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle admin-table">
                    <thead class="table-light">
                        <tr>
                            <th>Semester</th>
                            <th>Tahun Ajaran</th>
                            <th>Periode</th>
                            <th>Status</th>
                            <th class="text-end table-action-column">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($semesters as $semester)
                            @php
                                // penjelasan: Pesan konfirmasi dibuat sesuai status semester.
                                $confirmMessage = $semester->status === 'aktif'
                                    ? 'Apakah Anda yakin ingin menonaktifkan semester ini? Jika dinonaktifkan, tidak ada semester aktif sampai Anda mengaktifkan yang lain.'
                                    : 'Apakah Anda yakin ingin mengaktifkan semester ini? Semester aktif lainnya otomatis menjadi nonaktif.';

                                // penjelasan: Semester tidak bisa diaktifkan jika tahun ajarannya belum aktif.
                                $canActivateSemester = $semester->tahunAjaran && $semester->tahunAjaran->status === 'aktif';
                            @endphp

                            <tr>
                                <td class="fw-semibold">
                                    {{ $semester->nama_semester_label }}
                                </td>

                                <td>
                                    @if ($semester->tahunAjaran)
                                        <div>{{ $semester->tahunAjaran->nama_tahun_ajaran }}</div>

                                        @if ($semester->tahunAjaran->status === 'aktif')
                                            <small class="text-success">Tahun ajaran aktif</small>
                                        @else
                                            <small class="text-muted">Tahun ajaran nonaktif</small>
                                        @endif
                                    @else
                                        -
                                    @endif
                                </td>

                                <td>
                                    {{ $semester->tanggal_mulai ? $semester->tanggal_mulai->format('d-m-Y') : '-' }}
                                    sampai
                                    {{ $semester->tanggal_selesai ? $semester->tanggal_selesai->format('d-m-Y') : '-' }}
                                </td>

                                <td>
                                    @if ($semester->status === 'aktif')
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
                                        <a href="{{ route($routePrefix . '.semester.show', $semester) }}" class="btn btn-sm btn-outline-secondary action-btn">
                                            <i class="bi bi-eye"></i>
                                            <span>Detail</span>
                                        </a>

                                        <a href="{{ route($routePrefix . '.semester.edit', $semester) }}" class="btn btn-sm btn-outline-primary action-btn">
                                            <i class="bi bi-pencil-square"></i>
                                            <span>Edit</span>
                                        </a>

                                        <form action="{{ route($routePrefix . '.semester.toggle-status', $semester) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')

                                            <button
                                                type="submit"
                                                class="btn btn-sm action-btn {{ $semester->status === 'aktif' ? 'btn-outline-danger' : 'btn-outline-success' }}"
                                                {{ $semester->status !== 'aktif' && ! $canActivateSemester ? 'disabled' : '' }}
                                                data-confirm="true"
                                                data-confirm-message="{{ $confirmMessage }}"
                                                data-confirm-yes="{{ $semester->status === 'aktif' ? 'Ya, Nonaktifkan' : 'Ya, Aktifkan' }}"
                                                data-confirm-yes-class="{{ $semester->status === 'aktif' ? 'btn-danger' : 'btn-success' }}"
                                            >
                                                @if ($semester->status === 'aktif')
                                                    <i class="bi bi-x-circle"></i>
                                                    <span>Nonaktif</span>
                                                @else
                                                    <i class="bi bi-check-circle"></i>
                                                    <span>Aktifkan</span>
                                                @endif
                                            </button>
                                        </form>
                                    </div>

                                    @if ($semester->status !== 'aktif' && ! $canActivateSemester)
                                        <small class="text-muted d-block mt-1">
                                            Aktifkan tahun ajarannya terlebih dahulu.
                                        </small>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    Data semester belum tersedia.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $semesters->links() }}
            </div>
        </div>
    </div>

@endsection
