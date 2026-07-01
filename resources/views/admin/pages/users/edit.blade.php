{{-- penjelasan: File ini adalah halaman edit user. --}}
{{-- penjelasan: File ini dipanggil oleh UserController method edit(). --}}
{{-- penjelasan: Form pada halaman ini dikirim ke UserController method update(). --}}

@extends('admin.layouts.app')

@section('title', 'Edit User')

@section('content')

    <div class="row justify-content-center">
        <div class="col-lg-8">

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <h5 class="fw-bold mb-0">Edit User</h5>
                    <small class="text-muted">Ubah nama, email, role, dan status user.</small>
                </div>

                <div class="card-body">

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            @foreach ($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    @endif

                    <form action="{{ route('super-admin.users.update', $user) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label">Nama User</label>
                            <input
                                type="text"
                                name="name"
                                class="form-control"
                                value="{{ old('name', $user->name) }}"
                                required
                            >
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input
                                type="email"
                                name="email"
                                class="form-control"
                                value="{{ old('email', $user->email) }}"
                                required
                            >
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Role</label>

                            @if ($user->role === 'super_admin')
                                <input type="text" class="form-control" value="Super Admin" disabled>
                                <input type="hidden" name="role" value="admin">
                                <small class="text-muted">Role super admin tidak diubah dari halaman ini.</small>
                            @else
                                <select name="role" class="form-select" required>
                                    <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Admin</option>
                                    <option value="guru" {{ old('role', $user->role) === 'guru' ? 'selected' : '' }}>Guru</option>
                                    <option value="staff" {{ old('role', $user->role) === 'staff' ? 'selected' : '' }}>Staff</option>
                                </select>
                            @endif
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Status</label>

                            @if ($user->id === auth()->id() || $user->role === 'super_admin')
                                <input type="text" class="form-control" value="{{ ucfirst($user->status) }}" disabled>
                                <input type="hidden" name="status" value="{{ $user->status }}">
                                <small class="text-muted">Status akun sendiri atau super admin tidak bisa dinonaktifkan dari sini.</small>
                            @else
                                <select name="status" class="form-select" required>
                                    <option value="aktif" {{ old('status', $user->status) === 'aktif' ? 'selected' : '' }}>Aktif</option>
                                    <option value="nonaktif" {{ old('status', $user->status) === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                                </select>
                            @endif
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('super-admin.users.index') }}" class="btn btn-outline-secondary">
                                Batal
                            </a>

                            <button type="submit" class="btn btn-primary">
                                Simpan Perubahan
                            </button>
                        </div>

                    </form>

                </div>
            </div>

        </div>
    </div>

@endsection
