{{-- penjelasan: File ini adalah halaman form tambah user. --}}
{{-- penjelasan: File ini dipanggil oleh UserController method create(). --}}
{{-- penjelasan: Form pada halaman ini dikirim ke UserController method store(). --}}

@extends('admin.layouts.app')

@section('title', 'Tambah User')

@section('content')

    <div class="row justify-content-center">
        <div class="col-lg-8">

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <h5 class="fw-bold mb-0">Tambah User</h5>
                    <small class="text-muted">Buat akun login untuk admin, guru, atau staff.</small>
                </div>

                <div class="card-body">

                    {{-- penjelasan: Error validasi akan tampil di bagian ini. --}}
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            @foreach ($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    @endif

                    {{-- penjelasan: Form ini mengirim data user baru ke route super-admin.users.store. --}}
                    <form action="{{ route('super-admin.users.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Nama User</label>
                            <input
                                type="text"
                                name="name"
                                class="form-control"
                                value="{{ old('name') }}"
                                placeholder="Masukkan nama user"
                                required
                            >
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input
                                type="email"
                                name="email"
                                class="form-control"
                                value="{{ old('email') }}"
                                placeholder="Masukkan email login"
                                required
                            >
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Role</label>
                            <select name="role" class="form-select" required>
                                <option value="">Pilih Role</option>
                                <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="guru" {{ old('role') === 'guru' ? 'selected' : '' }}>Guru</option>
                                <option value="staff" {{ old('role') === 'staff' ? 'selected' : '' }}>Staff</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select" required>
                                <option value="aktif" {{ old('status') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="nonaktif" {{ old('status') === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input
                                type="password"
                                name="password"
                                class="form-control"
                                placeholder="Minimal 8 karakter"
                                required
                            >
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Konfirmasi Password</label>
                            <input
                                type="password"
                                name="password_confirmation"
                                class="form-control"
                                placeholder="Ulangi password"
                                required
                            >
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('super-admin.users.index') }}" class="btn btn-outline-secondary">
                                Batal
                            </a>

                            <button type="submit" class="btn btn-primary">
                                Simpan User
                            </button>
                        </div>

                    </form>

                </div>
            </div>

        </div>
    </div>

@endsection
