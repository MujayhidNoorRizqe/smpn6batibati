{{-- penjelasan: File ini adalah halaman daftar user. --}}
{{-- penjelasan: File ini dipanggil oleh UserController method index(). --}}
{{-- penjelasan: Halaman ini hanya bisa diakses oleh Super Admin melalui route /super-admin/users. --}}
{{-- penjelasan: Halaman ini berfungsi untuk menampilkan data user, filter user, tambah user, edit user, reset password, dan aktif/nonaktif akun. --}}
{{-- penjelasan: File ini memakai layout dashboard utama dari admin.layouts.app. --}}
{{-- penjelasan: Alert berhasil, gagal, dan validasi sudah memakai komponen global admin.components.alert. --}}
{{-- penjelasan: Tombol aktif/nonaktif memakai modal konfirmasi global melalui data-confirm="true". --}}

@extends('admin.layouts.app')

@section('title', 'Manajemen User')

@section('content')

    <div class="row mb-4">
        <div class="col-12">

            <div class="card border-0 shadow-sm">
                <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">

                    <div>
                        <h4 class="fw-bold mb-1">Manajemen User</h4>

                        <p class="text-muted mb-0">
                            Kelola akun login super admin, admin, guru, dan staff.
                        </p>
                    </div>

                    <a href="{{ route('super-admin.users.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i>
                        Tambah User
                    </a>

                </div>
            </div>

        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">

            <form action="{{ route('super-admin.users.index') }}" method="GET" class="row g-3">

                <div class="col-md-4">
                    <label class="form-label">
                        Cari User
                    </label>

                    <input
                        type="text"
                        name="search"
                        class="form-control"
                        placeholder="Cari nama atau email"
                        value="{{ request('search') }}"
                    >
                </div>

                <div class="col-md-3">
                    <label class="form-label">
                        Role
                    </label>

                    <select name="role" class="form-select">
                        <option value="">Semua Role</option>
                        <option value="super_admin" {{ request('role') === 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                        <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="guru" {{ request('role') === 'guru' ? 'selected' : '' }}>Guru</option>
                        <option value="staff" {{ request('role') === 'staff' ? 'selected' : '' }}>Staff</option>
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
                <h6 class="fw-bold mb-0">Daftar User</h6>
                <small class="text-muted">Data akun yang terdaftar pada sistem</small>
            </div>

            <span class="badge bg-primary-subtle text-primary">
                {{ $users->count() }} data tampil
            </span>
        </div>

        <div class="card-body">

            <div class="table-responsive">
                <table class="table table-hover align-middle admin-table">
                    <thead class="table-light">
                        <tr>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Login Terakhir</th>
                            <th class="text-end table-action-column">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($users as $user)
                            @php
                                // penjelasan: User yang dilindungi tidak boleh dinonaktifkan dari daftar user.
                                $isProtectedUser = $user->id === auth()->id() || $user->role === 'super_admin';

                                // penjelasan: Pesan konfirmasi dibuat sesuai status user.
                                $confirmMessage = $user->status === 'aktif'
                                    ? 'Apakah Anda yakin ingin menonaktifkan user ini? User tidak akan bisa login setelah dinonaktifkan.'
                                    : 'Apakah Anda yakin ingin mengaktifkan user ini? User akan bisa login kembali.';
                            @endphp

                            <tr>
                                <td class="fw-semibold">
                                    {{ $user->name }}
                                </td>

                                <td>
                                    {{ $user->email }}
                                </td>

                                <td>
                                    <span class="badge bg-primary-subtle text-primary">
                                        {{ ucwords(str_replace('_', ' ', $user->role)) }}
                                    </span>
                                </td>

                                <td>
                                    @if ($user->status === 'aktif')
                                        <span class="badge bg-success-subtle text-success">
                                            Aktif
                                        </span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger">
                                            Nonaktif
                                        </span>
                                    @endif
                                </td>

                                <td>
                                    {{ $user->last_login_at ? $user->last_login_at->format('d-m-Y H:i') : '-' }}
                                </td>

                                <td class="text-end">
                                    <div class="action-buttons">

                                        <a
                                            href="{{ route('super-admin.users.edit', $user) }}"
                                            class="btn btn-sm btn-outline-primary action-btn"
                                        >
                                            <i class="bi bi-pencil-square"></i>
                                            <span>Edit</span>
                                        </a>

                                        <a
                                            href="{{ route('super-admin.users.reset-password', $user) }}"
                                            class="btn btn-sm btn-outline-warning action-btn"
                                        >
                                            <i class="bi bi-key"></i>
                                            <span>Reset</span>
                                        </a>

                                        <form
                                            action="{{ route('super-admin.users.toggle-status', $user) }}"
                                            method="POST"
                                            class="d-inline"
                                        >
                                            @csrf
                                            @method('PATCH')

                                            <button
                                                type="submit"
                                                class="btn btn-sm action-btn {{ $user->status === 'aktif' ? 'btn-outline-danger' : 'btn-outline-success' }}"
                                                {{ $isProtectedUser ? 'disabled' : '' }}
                                                data-confirm="true"
                                                data-confirm-message="{{ $confirmMessage }}"
                                                data-confirm-yes="{{ $user->status === 'aktif' ? 'Ya, Nonaktifkan' : 'Ya, Aktifkan' }}"
                                                data-confirm-yes-class="{{ $user->status === 'aktif' ? 'btn-danger' : 'btn-success' }}"
                                            >
                                                @if ($user->status === 'aktif')
                                                    <i class="bi bi-person-x"></i>
                                                    <span>Nonaktif</span>
                                                @else
                                                    <i class="bi bi-person-check"></i>
                                                    <span>Aktifkan</span>
                                                @endif
                                            </button>
                                        </form>

                                    </div>

                                    @if ($user->id === auth()->id())
                                        <small class="text-muted d-block mt-1">Akun sendiri tidak bisa dinonaktifkan.</small>
                                    @elseif ($user->role === 'super_admin')
                                        <small class="text-muted d-block mt-1">Akun super admin dilindungi.</small>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    Data user belum tersedia.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $users->links() }}
            </div>

        </div>
    </div>

@endsection
