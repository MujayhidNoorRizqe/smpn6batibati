{{-- penjelasan: File ini adalah component topbar dashboard. --}}
{{-- penjelasan: File ini dipanggil oleh layout utama admin.layouts.app. --}}
{{-- penjelasan: Topbar menampilkan judul halaman, nama user, role, status akun, dan tombol logout. --}}

@php
    // penjelasan: Mengambil user yang sedang login.
    $user = auth()->user();

    // penjelasan: Mengubah role dari format database menjadi format tampilan.
    // penjelasan: Contoh super_admin menjadi Super Admin.
    $roleLabel = $user ? ucwords(str_replace('_', ' ', $user->role)) : '-';

    // penjelasan: Mengambil status akun user.
    $status = $user->status ?? '-';
@endphp

<header class="admin-topbar">

    <div>
        {{-- penjelasan: Judul halaman diambil dari @section('title') pada file halaman. --}}
        <h5 class="topbar-title">@yield('title', 'Dashboard')</h5>

        {{-- penjelasan: Subtitle topbar menjelaskan nama sistem. --}}
        <small class="topbar-subtitle">Sistem Informasi Akademik SMPN 6 Bati-Bati</small>
    </div>

    <div class="topbar-right">

        {{-- penjelasan: Badge status menampilkan apakah akun aktif atau nonaktif. --}}
        <span class="status-badge status-{{ $status }}">
            {{ ucfirst($status) }}
        </span>

        {{-- penjelasan: Bagian ini menampilkan nama dan role user. --}}
        <div class="topbar-user d-none d-md-block">
            <div class="topbar-user-name">{{ $user->name ?? 'User' }}</div>
            <div class="topbar-user-role">{{ $roleLabel }}</div>
        </div>

        {{-- penjelasan: Form logout dikirim ke route logout. --}}
        {{-- penjelasan: Route logout memanggil LoginController method logout(). --}}
        <form action="{{ route('logout') }}" method="POST" class="mb-0">

            {{-- penjelasan: @csrf adalah token keamanan form bawaan Laravel. --}}
            @csrf

            <button type="submit" class="btn btn-outline-danger btn-sm">
                <i class="bi bi-box-arrow-right"></i>
                Logout
            </button>
        </form>

    </div>
</header>
