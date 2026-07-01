{{-- penjelasan: File ini adalah component topbar dashboard. --}}
{{-- penjelasan: File ini dipanggil oleh resources/views/admin/layouts/app.blade.php. --}}
{{-- penjelasan: Topbar menampilkan nama user, role, dan tombol logout. --}}

@php
    // penjelasan: Variabel user digunakan untuk menyimpan data user yang sedang login.
    $user = auth()->user();

    // penjelasan: Variabel roleLabel digunakan untuk menampilkan role agar lebih enak dibaca.
    // penjelasan: Contoh super_admin diubah menjadi Super Admin.
    $roleLabel = $user ? ucwords(str_replace('_', ' ', $user->role)) : '-';
@endphp

<nav class="topbar d-flex align-items-center px-4">

    {{-- penjelasan: Bagian kiri topbar menampilkan judul halaman dari @yield('title'). --}}
    <div>
        <h6 class="mb-0 fw-bold">@yield('title', 'Dashboard')</h6>
        <small class="text-muted">Sistem Informasi Akademik SMPN 6 Bati-Bati</small>
    </div>

    {{-- penjelasan: Bagian kanan topbar menampilkan data user dan tombol logout. --}}
    <div class="ms-auto d-flex align-items-center gap-3">

        <div class="text-end d-none d-md-block">
            {{-- penjelasan: Menampilkan nama user yang sedang login. --}}
            <div class="fw-semibold small">{{ $user->name ?? 'User' }}</div>

            {{-- penjelasan: Menampilkan role user yang sedang login. --}}
            <div class="text-muted small">{{ $roleLabel }}</div>
        </div>

        {{-- penjelasan: Form logout mengirim request ke route logout. --}}
        {{-- penjelasan: Route logout memanggil LoginController method logout(). --}}
        <form action="{{ route('logout') }}" method="POST" class="mb-0">

            {{-- penjelasan: @csrf adalah token keamanan form bawaan Laravel. --}}
            @csrf

            <button type="submit" class="btn btn-outline-danger btn-sm">
                Logout
            </button>
        </form>

    </div>
</nav>
