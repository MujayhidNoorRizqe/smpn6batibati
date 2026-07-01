{{-- penjelasan: File ini adalah layout utama dashboard internal. --}}
{{-- penjelasan: Layout ini dipakai oleh halaman dashboard super admin, admin, guru, dan staff. --}}
{{-- penjelasan: File ini dipanggil oleh halaman lain memakai @extends('admin.layouts.app'). --}}
{{-- penjelasan: File ini memakai Bootstrap CDN sebagai library CSS dan JavaScript untuk tampilan dashboard. --}}

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">

    {{-- penjelasan: Viewport membuat tampilan dashboard menyesuaikan ukuran layar laptop dan HP. --}}
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    {{-- penjelasan: @yield('title') akan diisi oleh halaman yang memakai layout ini. --}}
    <title>@yield('title', 'Dashboard') - SMPN 6 Bati-Bati</title>

    {{-- penjelasan: Bootstrap adalah library CSS eksternal. --}}
    {{-- penjelasan: Bootstrap digunakan agar tampilan dashboard rapi tanpa membuat CSS dari nol. --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- penjelasan: CSS di bawah ini adalah CSS internal khusus untuk layout dashboard. --}}
    {{-- penjelasan: CSS ini mengatur sidebar, topbar, dan area konten dashboard. --}}
    <style>
        body {
            min-height: 100vh;
            background-color: #f5f6fa;
        }

        .dashboard-wrapper {
            display: flex;
            min-height: 100vh;
        }

        .dashboard-sidebar {
            width: 260px;
            background-color: #0f172a;
            color: #ffffff;
            flex-shrink: 0;
        }

        .dashboard-content {
            flex: 1;
            min-width: 0;
        }

        .sidebar-brand {
            padding: 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar-menu {
            padding: 15px;
        }

        .sidebar-menu a {
            display: block;
            color: #cbd5e1;
            text-decoration: none;
            padding: 10px 12px;
            border-radius: 8px;
            margin-bottom: 5px;
            font-size: 14px;
        }

        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            background-color: #1e293b;
            color: #ffffff;
        }

        .sidebar-section-title {
            color: #94a3b8;
            font-size: 12px;
            text-transform: uppercase;
            margin: 18px 12px 8px;
            letter-spacing: 0.5px;
        }

        .topbar {
            height: 64px;
            background-color: #ffffff;
            border-bottom: 1px solid #e5e7eb;
        }

        @media (max-width: 768px) {
            .dashboard-wrapper {
                flex-direction: column;
            }

            .dashboard-sidebar {
                width: 100%;
            }
        }
    </style>
</head>

<body>

    {{-- penjelasan: dashboard-wrapper membungkus sidebar dan konten utama dashboard. --}}
    <div class="dashboard-wrapper">

        {{-- penjelasan: Sidebar dipisahkan ke file component agar lebih rapi. --}}
        {{-- penjelasan: File yang dipanggil adalah resources/views/admin/components/sidebar.blade.php. --}}
        @include('admin.components.sidebar')

        {{-- penjelasan: dashboard-content adalah area kanan yang berisi topbar dan halaman utama. --}}
        <div class="dashboard-content">

            {{-- penjelasan: Topbar dipisahkan ke component agar bisa dipakai berulang. --}}
            {{-- penjelasan: File yang dipanggil adalah resources/views/admin/components/topbar.blade.php. --}}
            @include('admin.components.topbar')

            {{-- penjelasan: Main adalah area isi halaman. --}}
            <main class="p-4">

                {{-- penjelasan: @yield('content') akan diisi oleh halaman dashboard masing-masing role. --}}
                @yield('content')

            </main>
        </div>
    </div>

    {{-- penjelasan: Bootstrap JavaScript dipakai untuk komponen Bootstrap yang membutuhkan interaksi. --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
