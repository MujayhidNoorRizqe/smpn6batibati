{{-- penjelasan: File ini adalah halaman daftar murid. --}}
{{-- penjelasan: File ini dipanggil oleh MuridController method index(). --}}
{{-- penjelasan: Halaman ini bisa diakses oleh Super Admin dan Admin. --}}
{{-- penjelasan: Data $murids dikirim dari controller dan berisi daftar murid dengan relasi kelas dan wali murid. --}}
{{-- penjelasan: Data $kelasList dikirim untuk pilihan filter kelas. --}}
{{-- penjelasan: routePrefix dipakai agar link otomatis menyesuaikan role login, yaitu super-admin atau admin. --}}
{{-- penjelasan: Alert berhasil, gagal, dan validasi sudah memakai komponen global admin.components.alert. --}}
{{-- penjelasan: Tombol aktif/nonaktif memakai modal konfirmasi global melalui data-confirm="true". --}}

@extends('admin.layouts.app')

@section('title', 'Data Murid')

@section('content')

    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                    <div>
                        <h4 class="fw-bold mb-1">Data Murid</h4>
                        <p class="text-muted mb-0">
                            Kelola data siswa, kelas, jenis kelamin, NISN, tanggal lahir, dan wali murid yang terhubung.
                        </p>
                    </div>

                    <a href="{{ route($routePrefix . '.murid.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i>
                        Tambah Murid
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route($routePrefix . '.murid.index') }}" method="GET" class="row g-3">

                <div class="col-md-3">
                    <label class="form-label">
                        Cari Murid
                    </label>

                    <input
                        type="text"
                        name="search"
                        class="form-control"
                        placeholder="Cari nama, NIS, atau NISN"
                        value="{{ request('search') }}"
                    >
                </div>

                <div class="col-md-3">
                    <label class="form-label">
                        Kelas
                    </label>

                    <select name="kelas_id" class="form-select">
                        <option value="">Semua Kelas</option>

                        @foreach ($kelasList as $kelas)
                            <option value="{{ $kelas->id }}" {{ request('kelas_id') == $kelas->id ? 'selected' : '' }}>
                                {{ $kelas->nama_kelas }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label">
                        Jenis Kelamin
                    </label>

                    <select name="jenis_kelamin" class="form-select">
                        <option value="">Semua</option>
                        <option value="L" {{ request('jenis_kelamin') === 'L' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="P" {{ request('jenis_kelamin') === 'P' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label">
                        Status
                    </label>

                    <select name="status" class="form-select">
                        <option value="">Semua</option>
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
                <h6 class="fw-bold mb-0">Daftar Murid</h6>
                <small class="text-muted">Data siswa yang terdaftar pada sistem</small>
            </div>

            <span class="badge bg-primary-subtle text-primary">
                {{ $murids->count() }} data tampil
            </span>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle admin-table">
                    <thead class="table-light">
                        <tr>
                            <th>Nama Murid</th>
                            <th>NIS / NISN</th>
                            <th>Kelas</th>
                            <th>Wali Murid</th>
                            <th>JK</th>
                            <th>Status</th>
                            <th class="text-end table-action-column">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($murids as $murid)
                            @php
                                // penjelasan: Pesan konfirmasi dibuat sesuai status murid.
                                $confirmMessage = $murid->status === 'aktif'
                                    ? 'Apakah Anda yakin ingin menonaktifkan murid ini? Data tidak dihapus, hanya statusnya menjadi nonaktif.'
                                    : 'Apakah Anda yakin ingin mengaktifkan murid ini kembali?';
                            @endphp

                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        @if ($murid->foto)
                                            <img src="{{ asset('storage/' . $murid->foto) }}" alt="Foto Murid" width="38" height="38" class="rounded-circle" style="object-fit: cover;">
                                        @else
                                            <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center" style="width:38px; height:38px;">
                                                {{ strtoupper(substr($murid->nama_murid, 0, 1)) }}
                                            </div>
                                        @endif

                                        <div>
                                            <div class="fw-semibold">{{ $murid->nama_murid }}</div>

                                            <small class="text-muted">
                                                @if ($murid->tanggal_lahir)
                                                    Lahir: {{ $murid->tanggal_lahir->format('d-m-Y') }}
                                                @else
                                                    Tanggal lahir belum diisi
                                                @endif
                                            </small>
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    <div>NIS: {{ $murid->nis ?? '-' }}</div>
                                    <small class="text-muted">NISN: {{ $murid->nisn ?? '-' }}</small>
                                </td>

                                <td>
                                    @if ($murid->kelas)
                                        <span class="badge bg-primary-subtle text-primary">
                                            {{ $murid->kelas->nama_kelas }}
                                        </span>
                                    @else
                                        <span class="badge bg-warning-subtle text-warning">
                                            Belum ada kelas
                                        </span>
                                    @endif
                                </td>

                                <td>
                                    @if ($murid->waliMurid)
                                        <div>{{ $murid->waliMurid->nama_wali }}</div>

                                        <small class="text-muted">
                                            {{ ucfirst($murid->waliMurid->hubungan) }} |
                                            {{ $murid->waliMurid->no_whatsapp }}
                                        </small>
                                    @else
                                        <span class="badge bg-warning-subtle text-warning">
                                            Belum terhubung
                                        </span>
                                    @endif
                                </td>

                                <td>
                                    {{ $murid->jenis_kelamin === 'L' ? 'L' : 'P' }}
                                </td>

                                <td>
                                    @if ($murid->status === 'aktif')
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
                                        <a href="{{ route($routePrefix . '.murid.show', $murid) }}" class="btn btn-sm btn-outline-secondary action-btn">
                                            <i class="bi bi-eye"></i>
                                            <span>Detail</span>
                                        </a>

                                        <a href="{{ route($routePrefix . '.murid.edit', $murid) }}" class="btn btn-sm btn-outline-primary action-btn">
                                            <i class="bi bi-pencil-square"></i>
                                            <span>Edit</span>
                                        </a>

                                        <form action="{{ route($routePrefix . '.murid.toggle-status', $murid) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')

                                            <button
                                                type="submit"
                                                class="btn btn-sm action-btn {{ $murid->status === 'aktif' ? 'btn-outline-danger' : 'btn-outline-success' }}"
                                                data-confirm="true"
                                                data-confirm-message="{{ $confirmMessage }}"
                                                data-confirm-yes="{{ $murid->status === 'aktif' ? 'Ya, Nonaktifkan' : 'Ya, Aktifkan' }}"
                                                data-confirm-yes-class="{{ $murid->status === 'aktif' ? 'btn-danger' : 'btn-success' }}"
                                            >
                                                @if ($murid->status === 'aktif')
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
                                <td colspan="7" class="text-center text-muted py-4">
                                    Data murid belum tersedia.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $murids->links() }}
            </div>
        </div>
    </div>

@endsection
