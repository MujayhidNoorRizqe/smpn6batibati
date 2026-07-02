{{-- penjelasan: File ini adalah halaman reset password user. --}}
{{-- penjelasan: File ini dipanggil oleh UserController method resetPassword(). --}}
{{-- penjelasan: Password lama tidak ditampilkan karena password di database berbentuk hash. --}}
{{-- penjelasan: Alert validasi tidak ditulis lokal karena sudah ditampilkan global dari admin.components.alert. --}}
{{-- penjelasan: Tombol reset password memakai modal konfirmasi global agar tidak terpencet tanpa sengaja. --}}

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

                    <div class="alert alert-info border-0 shadow-sm rounded-3">
                        <i class="bi bi-info-circle me-1"></i>
                        Password lama tidak dapat dilihat karena disimpan dalam bentuk hash.
                        Super admin hanya bisa membuat password baru.
                    </div>

                    <div class="alert alert-warning border-0 shadow-sm rounded-3">
                        <i class="bi bi-exclamation-triangle me-1"></i>
                        Setelah password direset, user harus login menggunakan password baru.
                    </div>

                    <form action="{{ route('super-admin.users.update-password', $user) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label">
                                Password Baru <span class="text-danger">*</span>
                            </label>

                            <input
                                type="password"
                                name="password"
                                class="form-control @error('password') is-invalid @enderror"
                                placeholder="Minimal 8 karakter"
                                minlength="8"
                                required
                            >

                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror

                            <small class="text-muted">
                                Password minimal 8 karakter.
                            </small>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">
                                Konfirmasi Password Baru <span class="text-danger">*</span>
                            </label>

                            <input
                                type="password"
                                name="password_confirmation"
                                class="form-control"
                                placeholder="Ulangi password baru"
                                minlength="8"
                                required
                            >
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a
                                href="{{ route('super-admin.users.index') }}"
                                class="btn btn-outline-secondary"
                                data-confirm="true"
                                data-confirm-message="Batalkan reset password? Password baru yang belum disimpan akan hilang."
                                data-confirm-yes="Ya, Batalkan"
                                data-confirm-yes-class="btn-danger"
                            >
                                Batal
                            </a>

                            <button
                                type="submit"
                                class="btn btn-warning"
                                data-confirm="true"
                                data-confirm-message="Apakah Anda yakin ingin mereset password user ini?"
                                data-confirm-yes="Ya, Reset Password"
                                data-confirm-yes-class="btn-warning"
                            >
                                <i class="bi bi-key me-1"></i>
                                Reset Password
                            </button>
                        </div>

                    </form>

                </div>
            </div>

        </div>
    </div>

@endsection
