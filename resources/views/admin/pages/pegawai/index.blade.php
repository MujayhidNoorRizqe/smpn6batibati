{{-- penjelasan: File ini adalah halaman daftar pegawai. --}}
{{-- penjelasan: File ini dipanggil oleh PegawaiController method index(). --}}
{{-- penjelasan: Halaman ini bisa diakses oleh Super Admin dan Admin. --}}
{{-- penjelasan: Data $pegawais dikirim dari controller menggunakan compact('pegawais', 'routePrefix'). --}}
{{-- penjelasan: routePrefix dipakai agar link bisa menyesuaikan apakah user login sebagai super-admin atau admin. --}}
{{-- penjelasan: Alert berhasil, gagal, dan validasi sudah memakai komponen global admin.components.alert. --}}
{{-- penjelasan: Tombol aktif/nonaktif memakai modal konfirmasi global melalui data-confirm="true". --}}

@extends('admin.layouts.app')

@section('title', 'Data Pegawai')

@section('content')

    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                    <div>
                        <h4 class="fw-bold mb-1">Data Pegawai</h4>
                        <p class="text-muted mb-0">
                            Kelola data guru dan staff SMPN 6 Bati-Bati.
                        </p>
                    </div>

                    <a href="{{ route($routePrefix . '.pegawai.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i>
                        Tambah Pegawai
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route($routePrefix . '.pegawai.index') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">
                        Cari Pegawai

                    <input
                        type="text"
                        name="search"
                        class="form-control"
                        placeholder="Cari nama, NIP, atau jabatan"
                        value="{{ request('search') }}"
                    >
                </div>

                <div class="col-md-3">
                    <label class="form-label">
                        Jenis Pegawai
                    </label>

                    <select name="jenis_pegawai" class="form-select">
                        <option value="">Semua Jenis</option>
                        <option value="guru" {{ request('jenis_pegawai') === 'guru' ? 'selected' : '' }}>Guru</option>
                        <option value="staff" {{ request('jenis_pegawai') === 'staff' ? 'selected' : '' }}>Staff</option>
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
                <h6 class="fw-bold mb-0">Daftar Pegawai</h6>
                <small class="text-muted">Data guru dan staff yang terdaftar</small>
            </div>

            <span class="badge bg-primary-subtle text-primary">
                {{ $pegawais->count() }} data tampil
            </span>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle admin-table">
                    <thead class="table-light">
                        <tr>
                            <th>Nama Pegawai</th>
                            <th>NIP</th>
                            <th>Jenis</th>
                            <th>Jabatan</th>
                            <th>Akun Login</th>
                            <th>Status</th>
                            <th class="text-end table-action-column">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($pegawais as $pegawai)
                            @php
                                // penjelasan: Pesan konfirmasi dibuat sesuai status pegawai.
                                $confirmMessage = $pegawai->status === 'aktif'
                                    ? 'Apakah Anda yakin ingin menonaktifkan pegawai ini? Data tidak dihapus, hanya statusnya menjadi nonaktif.'
                                    : 'Apakah Anda yakin ingin mengaktifkan pegawai ini kembali?';
                            @endphp

                            <tr>
                                <td class="fw-semibold">
                                    {{ $pegawai->nama_pegawai }}
                                </td>

                                <td>
                                    {{ $pegawai->nip ?? '-' }}
                                </td>

                                <td>
                                    <span class="badge {{ $pegawai->jenis_pegawai === 'guru' ? 'bg-primary-subtle text-primary' : 'bg-info-subtle text-info' }}">
                                        {{ ucfirst($pegawai->jenis_pegawai) }}
                                    </span>
                                </td>

                                <td>
                                    {{ $pegawai->jabatan ?? '-' }}
                                </td>

                                <td>
                                    @if ($pegawai->user)
                                        <span class="badge bg-success-subtle text-success">
                                            {{ $pegawai->user->email }}
                                        </span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary">
                                            Belum terhubung
                                        </span>
                                    @endif
                                </td>

                                <td>
                                    @if ($pegawai->status === 'aktif')
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
                                        <a href="{{ route($routePrefix . '.pegawai.show', $pegawai) }}" class="btn btn-sm btn-outline-secondary action-btn">
                                            <i class="bi bi-eye"></i>
                                            <span>Detail</span>
                                        </a>

                                        <a href="{{ route($routePrefix . '.pegawai.edit', $pegawai) }}" class="btn btn-sm btn-outline-primary action-btn">
                                            <i class="bi bi-pencil-square"></i>
                                            <span>Edit</span>
                                        </a>

                                        <form action="{{ route($routePrefix . '.pegawai.toggle-status', $pegawai) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')

                                            <button
                                                type="submit"
                                                class="btn btn-sm action-btn {{ $pegawai->status === 'aktif' ? 'btn-outline-danger' : 'btn-outline-success' }}"
                                                data-confirm="true"
                                                data-confirm-message="{{ $confirmMessage }}"
                                                data-confirm-yes="{{ $pegawai->status === 'aktif' ? 'Ya, Nonaktifkan' : 'Ya, Aktifkan' }}"
                                                data-confirm-yes-class="{{ $pegawai->status === 'aktif' ? 'btn-danger' : 'btn-success' }}"
                                            >
                                                @if ($pegawai->status === 'aktif')
                                                    <i class="bi bi-person-x"></i>
                                                    <span>Nonaktif</span>
                                                @else
                                                    <i class="bi bi-person-check"></i>
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
                                    Data pegawai belum tersedia.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $pegawais->links() }}
            </div>
        </div>
    </div>

@endsection
