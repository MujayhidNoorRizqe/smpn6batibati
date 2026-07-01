{{-- penjelasan: File ini adalah layout utama dashboard internal. --}}
{{-- penjelasan: Layout ini dipakai oleh halaman dashboard super admin, admin, guru, staff, dan halaman admin lain nantinya. --}}
{{-- penjelasan: File ini dipanggil oleh halaman lain menggunakan @extends('admin.layouts.app'). --}}
{{-- penjelasan: File ini tidak menyimpan CSS utama secara langsung, karena CSS dipisah ke public/assets/admin/css/admin.css. --}}
{{-- penjelasan: File ini juga memanggil JavaScript dashboard dari public/assets/admin/js/admin.js. --}}

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">

    {{-- penjelasan: Viewport digunakan agar tampilan dashboard menyesuaikan ukuran layar laptop dan HP. --}}
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    {{-- penjelasan: @yield('title') akan diisi oleh halaman yang memakai layout ini. --}}
    <title>@yield('title', 'Dashboard') - SMPN 6 Bati-Bati</title>

    {{-- penjelasan: Bootstrap adalah library CSS eksternal untuk membuat tampilan lebih rapi dan responsive. --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- penjelasan: Bootstrap Icons adalah library icon eksternal. --}}
    {{-- penjelasan: Icon dipakai untuk mempercantik tombol dan menu dashboard. --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    {{-- penjelasan: admin.css adalah file CSS buatan kita sendiri untuk tampilan dashboard admin/internal. --}}
    {{-- penjelasan: asset() digunakan Laravel untuk mengambil file dari folder public. --}}
    <link href="{{ asset('assets/admin/css/admin.css') }}" rel="stylesheet">
</head>

<body>

    {{-- penjelasan: admin-wrapper adalah pembungkus utama dashboard. --}}
    {{-- penjelasan: Di dalamnya ada sidebar kiri dan area konten kanan. --}}
    <div class="admin-wrapper">

        {{-- penjelasan: Sidebar dipisahkan ke component agar kode layout lebih rapi. --}}
        {{-- penjelasan: File yang dipanggil adalah resources/views/admin/components/sidebar.blade.php. --}}
        @include('admin.components.sidebar')

        {{-- penjelasan: admin-main adalah area kanan yang berisi topbar dan konten halaman. --}}
        <div class="admin-main">

            {{-- penjelasan: Topbar dipisahkan ke component agar bisa dipakai ulang di semua halaman dashboard. --}}
            {{-- penjelasan: File yang dipanggil adalah resources/views/admin/components/topbar.blade.php. --}}
            @include('admin.components.topbar')

            {{-- penjelasan: admin-content adalah area isi halaman. --}}
            <main class="admin-content">

                {{-- penjelasan: @yield('content') akan diisi oleh halaman dashboard masing-masing role. --}}
                @yield('content')

            </main>

            {{-- penjelasan: Footer kecil dashboard. --}}
            <footer class="admin-footer">
                <span>&copy; {{ date('Y') }} SMPN 6 Bati-Bati</span>
                <span>Sistem Informasi Akademik</span>
            </footer>
        </div>
    </div>

    {{-- penjelasan: Tombol ini adalah tombol scroll up melayang di kanan bawah. --}}
    {{-- penjelasan: Tombol ini dikontrol oleh file admin.js. --}}
    <button type="button" class="scroll-up-btn" id="scrollUpBtn" aria-label="Scroll ke atas">
        <i class="bi bi-arrow-up"></i>
    </button>

    {{-- penjelasan: Bootstrap JavaScript dipakai untuk komponen Bootstrap yang butuh interaksi. --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    {{-- penjelasan: admin.js adalah file JavaScript buatan kita sendiri untuk fitur dashboard. --}}
    {{-- penjelasan: Saat ini admin.js dipakai untuk fitur tombol scroll up. --}}
    <script src="{{ asset('assets/admin/js/admin.js') }}"></script>
</body>
</html>
