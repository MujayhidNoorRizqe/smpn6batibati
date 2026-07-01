{{-- penjelasan: File ini adalah halaman daftar semester. --}}
{{-- penjelasan: File ini dipanggil oleh SemesterController method index(). --}}

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

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route($routePrefix . '.semester.index') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Tahun Ajaran</label>
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
                    <label class="form-label">Semester</label>
                    <select name="nama_semester" class="form-select">
                        <option value="">Semua Semester</option>
                        <option value="ganjil" {{ request('nama_semester') === 'ganjil' ? 'selected' : '' }}>Ganjil</option>
                        <option value="genap" {{ request('nama_semester') === 'genap' ? 'selected' : '' }}>Genap</option>
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

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
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
                            <tr>
                                <td class="fw-semibold">{{ $semester->nama_semester_label }}</td>

                                <td>
                                    {{ $semester->tahunAjaran ? $semester->tahunAjaran->nama_tahun_ajaran : '-' }}
                                </td>

                                <td>
                                    {{ $semester->tanggal_mulai ? $semester->tanggal_mulai->format('d-m-Y') : '-' }}
                                    sampai
                                    {{ $semester->tanggal_selesai ? $semester->tanggal_selesai->format('d-m-Y') : '-' }}
                                </td>

                                <td>
                                    <span class="badge {{ $semester->status === 'aktif' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }}">
                                        {{ ucfirst($semester->status) }}
                                    </span>
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
                                                onclick="return confirm('Yakin ingin mengubah status semester ini?')"
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
