{{-- penjelasan: File ini adalah halaman login sistem. --}}
{{-- penjelasan: File ini dipanggil oleh LoginController pada method showLoginForm(). --}}
{{-- penjelasan: Form login pada halaman ini dikirim ke route login.process atau POST /login. --}}
{{-- penjelasan: File ini memakai Bootstrap CDN sebagai library CSS untuk mempercantik tampilan. --}}

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
</head>

<body class="bg-light">

    {{-- penjelasan: Container utama untuk membuat form login berada di tengah layar. --}}
    <div class="container min-vh-100 d-flex align-items-center justify-content-center">
        <div class="row w-100 justify-content-center">
            <div class="col-md-5 col-lg-4">

                {{-- penjelasan: Card digunakan sebagai kotak utama form login. --}}
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">

                        <div class="text-center mb-4">
                            <h4 class="fw-bold mb-1">Login Sistem</h4>
                            <p class="text-muted mb-0">SMPN 6 Bati-Bati</p>
                        </div>

                        {{-- penjelasan: Bagian ini menampilkan pesan sukses, contohnya setelah logout. --}}
                        @if (session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        {{-- penjelasan: Bagian ini menampilkan pesan error, contohnya email/password salah. --}}
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                @foreach ($errors->all() as $error)
                                    <div>{{ $error }}</div>
                                @endforeach
                            </div>
                        @endif

                        {{-- penjelasan: Form ini mengirim data login ke route login.process. --}}
                        {{-- penjelasan: Method POST digunakan karena data email dan password dikirim ke server. --}}
                        <form action="{{ route('login.process') }}" method="POST">

                            {{-- penjelasan: @csrf adalah token keamanan bawaan Laravel untuk melindungi form dari serangan CSRF. --}}
                            @csrf

                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>

                                {{-- penjelasan: Input ini digunakan untuk memasukkan email user. --}}
                                {{-- penjelasan: old('email') digunakan agar email tetap muncul jika login gagal. --}}
                                <input
                                    type="email"
                                    name="email"
                                    id="email"
                                    class="form-control"
                                    value="{{ old('email') }}"
                                    placeholder="Masukkan email"
                                    required
                                    autofocus
                                >
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>

                                {{-- penjelasan: Input ini digunakan untuk memasukkan password user. --}}
                                <input
                                    type="password"
                                    name="password"
                                    id="password"
                                    class="form-control"
                                    placeholder="Masukkan password"
                                    required
                                >
                            </div>

                            <div class="mb-3 form-check">
                                {{-- penjelasan: Checkbox remember digunakan untuk fitur ingat saya. --}}
                                <input
                                    type="checkbox"
                                    name="remember"
                                    id="remember"
                                    class="form-check-input"
                                    value="1"
                                >

                                <label for="remember" class="form-check-label">
                                    Ingat saya
                                </label>
                            </div>

                            {{-- penjelasan: Tombol ini mengirim form ke LoginController method login(). --}}
                            <button type="submit" class="btn btn-primary w-100">
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

</body>
</html>
