{{-- penjelasan: Halaman ini digunakan guru untuk mengganti password akun sendiri. --}}
{{-- penjelasan: Halaman ini dipanggil oleh PasswordController method edit(). --}}
{{-- penjelasan: Form mengirim data ke route guru.password.update. --}}
{{-- penjelasan: Validasi password lama dan password baru diproses di PasswordController method update(). --}}

@extends('admin.layouts.app')

@section('title', 'Ganti Password')

@section('content')

<div class="page-content">
    @if (session('success'))
        <div id="flash-success" data-message="{{ session('success') }}" class="alert-success">
            <strong>Berhasil!</strong>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if (session('error'))
        <div id="flash-error" data-message="{{ session('error') }}" class="alert-danger">
            <strong>Gagal!</strong>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    @if ($errors->any())
        <div id="flash-validation" data-message="Data belum valid. Silakan cek kembali password yang diinput." class="alert-danger">
            <strong>Data belum valid.</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="page-header-card">
        <div>
            <h1>Ganti Password</h1>
            <p>Perbarui password akun guru agar keamanan akun tetap terjaga.</p>
        </div>

        <span class="status-badge">
            <i class="bi bi-shield-lock me-1"></i>
            Keamanan Akun
        </span>
    </div>

    <div class="content-grid">
        <div class="card">
            <div class="card-header">
                <div>
                    <h2>Form Ganti Password</h2>
                    <p>Field bertanda <span class="required">*</span> wajib diisi.</p>
                </div>
            </div>

            <form method="POST" action="{{ route('guru.password.update') }}">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="current_password">
                        Password Lama <span class="required">*</span>
                    </label>

                    <input
                        type="password"
                        id="current_password"
                        name="current_password"
                        placeholder="Masukkan password lama"
                        required
                    >

                    @error('current_password')
                        <small class="error-text">{{ $message }}</small>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password">
                        Password Baru <span class="required">*</span>
                    </label>

                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Masukkan password baru minimal 8 karakter"
                        required
                    >

                    @error('password')
                        <small class="error-text">{{ $message }}</small>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password_confirmation">
                        Konfirmasi Password Baru <span class="required">*</span>
                    </label>

                    <input
                        type="password"
                        id="password_confirmation"
                        name="password_confirmation"
                        placeholder="Ulangi password baru"
                        required
                    >
                </div>

                <div class="form-actions">
                    <a href="{{ route('guru.dashboard') }}" class="btn btn-outline">
                        <i class="bi bi-arrow-left me-1"></i>
                        Kembali
                    </a>

                    <button
                        type="submit"
                        class="btn btn-primary"
                        data-confirm="true"
                        data-confirm-message="Apakah Anda yakin ingin mengganti password akun ini?"
                        data-confirm-yes="Ya, Simpan"
                        data-confirm-yes-class="btn-primary"
                    >
                        <i class="bi bi-save me-1"></i>
                        Simpan Password
                    </button>
                </div>
            </form>
        </div>

        <div class="card info-card">
            <div class="info-icon">
                <i class="bi bi-info-circle"></i>
            </div>

            <h2>Tips Password Aman</h2>

            <ul>
                <li>Gunakan minimal 8 karakter.</li>
                <li>Gabungkan huruf besar, huruf kecil, angka, dan simbol.</li>
                <li>Jangan gunakan tanggal lahir atau nama sendiri.</li>
                <li>Jangan membagikan password kepada siapa pun.</li>
            </ul>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const successFlash = document.getElementById('flash-success');
        const errorFlash = document.getElementById('flash-error');
        const validationFlash = document.getElementById('flash-validation');

        if (successFlash && successFlash.dataset.message) {
            alert(successFlash.dataset.message);
        }

        if (errorFlash && errorFlash.dataset.message) {
            alert(errorFlash.dataset.message);
        }

        if (validationFlash && validationFlash.dataset.message) {
            alert(validationFlash.dataset.message);
        }
    });
</script>

<style>
    .page-content {
        display: flex;
        flex-direction: column;
        gap: 18px;
    }

    .page-header-card,
    .card {
        background: #ffffff;
        border-radius: 16px;
        padding: 22px;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
    }

    .page-header-card,
    .card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
    }

    h1,
    h2 {
        margin: 0;
        color: #0f172a;
        font-weight: 900;
    }

    .page-header-card p,
    .card-header p {
        margin: 6px 0 0;
        color: #64748b;
    }

    .content-grid {
        display: grid;
        grid-template-columns: minmax(0, 1.4fr) minmax(320px, 0.6fr);
        gap: 18px;
        align-items: start;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        border-radius: 999px;
        padding: 8px 12px;
        background: #dbeafe;
        color: #2563eb;
        font-weight: 800;
        white-space: nowrap;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 8px;
        margin-top: 16px;
    }

    label {
        color: #0f172a;
        font-weight: 800;
    }

    input {
        width: 100%;
        border: 1px solid #dbe3ef;
        border-radius: 10px;
        padding: 11px 12px;
        outline: none;
        background: #ffffff;
        font-size: 14px;
    }

    input:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
    }

    .required {
        color: #dc2626;
    }

    .error-text {
        color: #dc2626;
        font-weight: 600;
    }

    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        margin-top: 20px;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        padding: 11px 18px;
        font-weight: 800;
        border: 1px solid transparent;
        cursor: pointer;
        text-decoration: none;
        white-space: nowrap;
    }

    .btn-primary {
        background: #2563eb;
        color: #ffffff;
    }

    .btn-outline {
        background: #ffffff;
        color: #475569;
        border-color: #cbd5e1;
    }

    .info-card {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
    }

    .info-icon {
        width: 46px;
        height: 46px;
        border-radius: 999px;
        background: #dbeafe;
        color: #2563eb;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        margin-bottom: 12px;
    }

    .info-card ul {
        margin: 14px 0 0;
        padding-left: 20px;
        color: #475569;
    }

    .info-card li {
        margin-bottom: 8px;
    }

    .alert-success,
    .alert-danger {
        border-radius: 12px;
        padding: 14px 16px;
    }

    .alert-success {
        background: #dcfce7;
        color: #166534;
    }

    .alert-danger {
        background: #fee2e2;
        color: #991b1b;
    }

    .alert-danger ul {
        margin: 8px 0 0;
    }

    @media (max-width: 1000px) {
        .content-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 700px) {
        .page-header-card,
        .card-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .form-actions {
            flex-direction: column;
            align-items: stretch;
        }

        .btn {
            width: 100%;
        }
    }
</style>

@endsection
