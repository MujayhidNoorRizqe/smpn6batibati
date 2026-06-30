{{-- penjelasan: File ini adalah layout utama dashboard internal. --}}
{{-- penjelasan: Layout ini dipakai oleh dashboard super admin, admin, guru, dan staff. --}}
{{-- penjelasan: Tujuannya agar struktur HTML, navbar, Bootstrap, dan tombol logout tidak ditulis berulang di setiap halaman. --}}
{{-- penjelasan: File dashboard tiap role akan memanggil layout ini memakai @extends('admin.layouts.app'). --}}

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">

    {{-- penjelasan: Viewport membuat dashboard responsive di laptop dan HP. --}}
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    {{-- penjelasan: @yield('title') akan diganti oleh judul dari halaman yang memakai layout ini. --}}
    <title>@yield('title', 'Dashboard') - SMPN 6 Bati-Bati</title>

    {{-- penjelasan: Bootstrap adalah library CSS eksternal. --}}
    {{-- penjelasan: Bootstrap dipakai sementara untuk membuat tampilan dashboard rapi dan cepat dibuat. --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    {{-- penjelasan: Navbar atas dashboard. --}}
    <nav class="navbar navbar-expand-lg bg-white border-bottom shadow-sm">
        <div class="container-fluid px-4">

            {{-- penjelasan: Nama aplikasi/sekolah ditampilkan di kiri navbar. --}}
            <span class="navbar-brand fw-bold">
                SMPN 6 Bati-Bati
            </span>

            <div class="d-flex align-items-center gap-3 ms-auto">

                {{-- penjelasan: auth()->user() mengambil data user yang sedang login. --}}
                {{-- penjelasan: Data ini berasal dari tabel users setelah proses login berhasil. --}}
                <span class="text-muted small">
                    {{ auth()->user()->name ?? 'User' }} |
                    {{ auth()->user()->role ?? '-' }}
                </span>

                {{-- penjelasan: Form logout dikirim ke route logout. --}}
                {{-- penjelasan: Logout memakai method POST karena lebih aman dibanding GET. --}}
                <form action="{{ route('logout') }}" method="POST" class="mb-0">
                    @csrf

                    <button type="submit" class="btn btn-outline-danger btn-sm">
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </nav>

    {{-- penjelasan: Bagian utama dashboard. --}}
    <main class="container-fluid px-4 py-4">

        {{-- penjelasan: @yield('content') adalah tempat isi halaman dashboard dimasukkan. --}}
        {{-- penjelasan: Setiap dashboard role akan mengisi bagian ini dengan @section('content'). --}}
        @yield('content')

    </main>

    {{-- penjelasan: Bootstrap JavaScript dipakai untuk komponen Bootstrap yang butuh interaksi. --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
