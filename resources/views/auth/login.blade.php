{{-- penjelasan: File ini adalah halaman login sistem. --}}
{{-- penjelasan: File ini dipanggil oleh LoginController pada method showLoginForm(). --}}
{{-- penjelasan: Form login pada halaman ini dikirim ke route login.process atau POST /login. --}}
{{-- penjelasan: File ini memakai Bootstrap CDN sebagai library CSS untuk mempercantik tampilan. --}}
{{-- penjelasan: Halaman ini memakai validasi client-side agar pesan wajib email/password tampil jelas dalam Bahasa Indonesia. --}}
{{-- penjelasan: Alert login gagal, akun nonaktif, dan validasi server ditampilkan lebih jelas di bagian atas form. --}}

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">

    {{-- penjelasan: Viewport membuat tampilan halaman menyesuaikan ukuran layar laptop dan HP. --}}
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    {{-- penjelasan: Title adalah judul halaman yang tampil di tab browser. --}}
    <title>Login - SMPN 6 Bati-Bati</title>

    {{-- penjelasan: Bootstrap adalah library CSS eksternal. --}}
    {{-- penjelasan: Library ini dipakai agar tampilan form login lebih rapi tanpa menulis CSS dari awal. --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- penjelasan: Bootstrap Icons dipakai untuk icon pada alert dan form login. --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        /* penjelasan: CSS ini hanya dipakai pada halaman login agar tampil lebih rapi dan jelas. */
        body {
            min-height: 100vh;
            background:
                radial-gradient(circle at top left, rgba(13, 110, 253, 0.16), transparent 32%),
                linear-gradient(135deg, #f3f7ff 0%, #eef5f2 100%);
        }

        /* penjelasan: Kotak login dibuat lembut dan modern. */
        .login-card {
            border-radius: 22px;
            overflow: hidden;
        }

        /* penjelasan: Header kecil login untuk memberi identitas sekolah. */
        .login-icon {
            width: 58px;
            height: 58px;
            border-radius: 18px;
            background: #0d6efd;
            color: #ffffff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            margin-bottom: 14px;
            box-shadow: 0 12px 24px rgba(13, 110, 253, 0.25);
        }

        /* penjelasan: Alert dibuat lebih terlihat tanpa terlalu ramai. */
        .login-alert {
            border: 0;
            border-radius: 14px;
            padding: 14px 16px;
        }

        .login-alert-title {
            font-weight: 700;
            margin-bottom: 2px;
        }

        .login-alert-message {
            font-size: 14px;
            margin-bottom: 0;
        }

        /* penjelasan: Label wajib diberi tanda merah. */
        .required-mark {
            color: #dc3545;
            font-weight: 700;
        }

        /* penjelasan: Input dibuat lebih nyaman dibaca. */
        .form-control {
            border-radius: 12px;
            padding: 10px 12px;
        }

        .form-check-input {
            cursor: pointer;
        }

        .btn-login {
            border-radius: 12px;
            padding: 10px 14px;
            font-weight: 600;
        }

        .invalid-feedback {
            font-size: 13px;
            font-weight: 500;
        }
    </style>
</head>

<body>

    {{-- penjelasan: Container utama untuk membuat form login berada di tengah layar. --}}
    <div class="container min-vh-100 d-flex align-items-center justify-content-center py-4">
        <div class="row w-100 justify-content-center">
            <div class="col-md-6 col-lg-4">

                {{-- penjelasan: Card digunakan sebagai kotak utama form login. --}}
                <div class="card shadow border-0 login-card">
                    <div class="card-body p-4">

                        <div class="text-center mb-4">
                            <div class="login-icon">
                                <i class="bi bi-mortarboard-fill"></i>
                            </div>

                            <h4 class="fw-bold mb-1">Login Sistem</h4>
                            <p class="text-muted mb-0">SMPN 6 Bati-Bati</p>
                        </div>

                        {{-- penjelasan: Alert client-side untuk field kosong sebelum form dikirim ke server. --}}
                        <div id="clientLoginAlert" class="alert alert-danger login-alert d-none" role="alert">
                            <div class="d-flex gap-2">
                                <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                                <div>
                                    <div class="login-alert-title">Data login belum lengkap</div>
                                    <p class="login-alert-message mb-0" id="clientLoginAlertMessage">
                                        Email dan password wajib diisi.
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- penjelasan: Bagian ini menampilkan pesan sukses, contohnya setelah logout. --}}
                        @if (session('success'))
                            <div class="alert alert-success login-alert" role="alert">
                                <div class="d-flex gap-2">
                                    <i class="bi bi-check-circle-fill fs-5"></i>
                                    <div>
                                        <div class="login-alert-title">Berhasil</div>
                                        <p class="login-alert-message">{{ session('success') }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- penjelasan: Bagian ini menampilkan pesan error login gagal, akun nonaktif, atau validasi server. --}}
                        @if ($errors->any())
                            @php
                                // penjelasan: loginErrorType dikirim dari LoginController agar judul alert lebih sesuai.
                                $loginErrorType = session('login_error_type');

                                $alertTitle = match ($loginErrorType) {
                                    'failed' => 'Login gagal',
                                    'inactive' => 'Akun nonaktif',
                                    default => 'Periksa data login',
                                };

                                $alertIcon = match ($loginErrorType) {
                                    'inactive' => 'bi-person-lock',
                                    default => 'bi-exclamation-triangle-fill',
                                };
                            @endphp

                            <div class="alert alert-danger login-alert" role="alert">
                                <div class="d-flex gap-2">
                                    <i class="bi {{ $alertIcon }} fs-5"></i>

                                    <div>
                                        <div class="login-alert-title">{{ $alertTitle }}</div>

                                        @foreach ($errors->all() as $error)
                                            <p class="login-alert-message">{{ $error }}</p>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- penjelasan: Form ini mengirim data login ke route login.process. --}}
                        {{-- penjelasan: novalidate dipakai agar validasi bawaan browser berbahasa Inggris tidak muncul. --}}
                        <form action="{{ route('login.process') }}" method="POST" id="loginForm" novalidate>

                            {{-- penjelasan: @csrf adalah token keamanan bawaan Laravel untuk melindungi form dari serangan CSRF. --}}
                            @csrf

                            <div class="mb-3">
                                <label for="email" class="form-label">
                                    Email <span class="required-mark">*</span>
                                </label>

                                {{-- penjelasan: Input ini digunakan untuk memasukkan email user. --}}
                                {{-- penjelasan: old('email') digunakan agar email tetap muncul jika login gagal. --}}
                                <input
                                    type="email"
                                    name="email"
                                    id="email"
                                    class="form-control @error('email') is-invalid @enderror"
                                    value="{{ old('email') }}"
                                    placeholder="Masukkan email"
                                    autocomplete="email"
                                    autofocus
                                    required
                                    data-label="Email"
                                >

                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @else
                                    <div class="invalid-feedback" id="emailFeedback">Email wajib diisi.</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">
                                    Password <span class="required-mark">*</span>
                                </label>

                                {{-- penjelasan: Input ini digunakan untuk memasukkan password user. --}}
                                <input
                                    type="password"
                                    name="password"
                                    id="password"
                                    class="form-control @error('password') is-invalid @enderror"
                                    placeholder="Masukkan password"
                                    autocomplete="current-password"
                                    required
                                    data-label="Password"
                                >

                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @else
                                    <div class="invalid-feedback" id="passwordFeedback">Password wajib diisi.</div>
                                @enderror
                            </div>

                            <div class="mb-3 form-check">
                                {{-- penjelasan: Checkbox remember digunakan untuk fitur ingat saya. --}}
                                <input
                                    type="checkbox"
                                    name="remember"
                                    id="remember"
                                    class="form-check-input"
                                    value="1"
                                    {{ old('remember') ? 'checked' : '' }}
                                >

                                <label for="remember" class="form-check-label">
                                    Ingat saya
                                </label>
                            </div>

                            {{-- penjelasan: Tombol ini mengirim form ke LoginController method login(). --}}
                            <button type="submit" class="btn btn-primary btn-login w-100">
                                <i class="bi bi-box-arrow-in-right me-1"></i>
                                Login
                            </button>
                        </form>

                        <div class="text-center mt-3">
                            {{-- penjelasan: Link ini mengarahkan user kembali ke halaman public. --}}
                            <a href="{{ route('public.home') }}" class="text-decoration-none">
                                Kembali ke Website
                            </a>
                        </div>

                    </div>
                </div>

                <p class="text-center text-muted mt-3 mb-0">
                    &copy; {{ date('Y') }} SMPN 6 Bati-Bati
                </p>

            </div>
        </div>
    </div>

    <script>
        // penjelasan: Script ini membuat validasi login lebih jelas dan memakai Bahasa Indonesia.
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('loginForm');
            const emailInput = document.getElementById('email');
            const passwordInput = document.getElementById('password');
            const clientAlert = document.getElementById('clientLoginAlert');
            const clientAlertMessage = document.getElementById('clientLoginAlertMessage');
            const emailFeedback = document.getElementById('emailFeedback');
            const passwordFeedback = document.getElementById('passwordFeedback');

            function showClientAlert(message) {
                if (!clientAlert || !clientAlertMessage) {
                    return;
                }

                clientAlertMessage.textContent = message;
                clientAlert.classList.remove('d-none');
            }

            function hideClientAlert() {
                if (!clientAlert) {
                    return;
                }

                clientAlert.classList.add('d-none');
            }

            function setInvalid(input, feedback, message) {
                input.classList.add('is-invalid');

                if (feedback) {
                    feedback.textContent = message;
                }
            }

            function setValid(input) {
                input.classList.remove('is-invalid');
            }

            function isValidEmail(value) {
                return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
            }

            if (form) {
                form.addEventListener('submit', function (event) {
                    hideClientAlert();

                    let messages = [];

                    const emailValue = emailInput.value.trim();
                    const passwordValue = passwordInput.value.trim();

                    setValid(emailInput);
                    setValid(passwordInput);

                    if (!emailValue) {
                        messages.push('Email wajib diisi.');
                        setInvalid(emailInput, emailFeedback, 'Email wajib diisi.');
                    } else if (!isValidEmail(emailValue)) {
                        messages.push('Format email tidak valid.');
                        setInvalid(emailInput, emailFeedback, 'Format email tidak valid.');
                    }

                    if (!passwordValue) {
                        messages.push('Password wajib diisi.');
                        setInvalid(passwordInput, passwordFeedback, 'Password wajib diisi.');
                    }

                    if (messages.length > 0) {
                        event.preventDefault();
                        showClientAlert(messages.join(' '));

                        if (!emailValue || !isValidEmail(emailValue)) {
                            emailInput.focus();
                        } else {
                            passwordInput.focus();
                        }
                    }
                });
            }

            [emailInput, passwordInput].forEach(function (input) {
                if (!input) {
                    return;
                }

                input.addEventListener('input', function () {
                    input.classList.remove('is-invalid');
                });
            });
        });
    </script>

</body>
</html>
