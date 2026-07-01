{{-- penjelasan: File ini adalah halaman reset password user. --}}
{{-- penjelasan: File ini dipanggil oleh UserController method resetPassword(). --}}
{{-- penjelasan: Password lama tidak ditampilkan karena password di database berbentuk hash. --}}

@extends('admin.layouts.app')

@section('title', 'Reset Password User')

@section('content')

    <div class="row justify-content-center">
        <div class="col-lg-7">

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <h5 class="fw-bold mb-0">Reset Password</h5>
                    <small class="text-muted">
                        Reset password untuk user: {{ $user->name }}.
                    </small>
                </div>

                <div class="card-body">

                    <div class="alert alert-info">
                        Password lama tidak dapat dilihat karena disimpan dalam bentuk hash.
                        Super admin hanya bisa membuat password baru.
                    </div>

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            @foreach ($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    @endif

                    <form action="{{ route('super-admin.users.update-password', $user) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label">Password Baru</label>
                            <input
                                type="password"
                                name="password"
                                class="form-control"
                                placeholder="Minimal 8 karakter"
                                required
                            >
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Konfirmasi Password Baru</label>
                            <input
                                type="password"
                                name="password_confirmation"
                                class="form-control"
                                placeholder="Ulangi password baru"
                                required
                            >
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('super-admin.users.index') }}" class="btn btn-outline-secondary">
                                Batal
                            </a>

                            <button type="submit" class="btn btn-warning">
                                Reset Password
                            </button>
                        </div>

                    </form>

                </div>
            </div>

        </div>
    </div>

@endsection
