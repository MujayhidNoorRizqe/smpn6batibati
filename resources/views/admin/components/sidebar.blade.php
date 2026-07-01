{{-- penjelasan: File ini adalah component sidebar dashboard. --}}
{{-- penjelasan: Sidebar ini dipanggil oleh layout utama admin.layouts.app. --}}
{{-- penjelasan: Sidebar menampilkan menu berbeda sesuai role user yang sedang login. --}}
{{-- penjelasan: Sidebar dibuat fixed lewat admin.css, sehingga tidak ikut bergerak saat halaman utama discroll. --}}
{{-- penjelasan: Jika menu sidebar panjang, sidebar bisa discroll sendiri karena memakai overflow-y: auto di admin.css. --}}

@php
    // penjelasan: Mengambil data user yang sedang login.
    $user = auth()->user();

    // penjelasan: Mengambil role user, misalnya super_admin, admin, guru, atau staff.
    $role = $user->role ?? null;
@endphp

<aside class="admin-sidebar">

    {{-- penjelasan: Bagian brand/sidebar header untuk menampilkan identitas sistem. --}}
    <div class="sidebar-brand">
        <div class="brand-logo">
            <i class="bi bi-mortarboard-fill"></i>
        </div>

        <div>
            <h5 class="brand-title">SMPN 6 Bati-Bati</h5>
            <small class="brand-subtitle">Sistem Akademik</small>
        </div>
    </div>

    {{-- penjelasan: sidebar-user menampilkan nama dan role user di sidebar. --}}
    <div class="sidebar-user">
        <div class="user-avatar">
            {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
        </div>

        <div class="user-info">
            <div class="user-name">{{ $user->name ?? 'User' }}</div>
            <div class="user-role">{{ ucwords(str_replace('_', ' ', $role ?? '-')) }}</div>
        </div>
    </div>

    {{-- penjelasan: sidebar-menu adalah daftar menu utama dashboard. --}}
    <nav class="sidebar-menu">

        @if ($role === 'super_admin')
            {{-- penjelasan: Menu ini khusus super admin. --}}

            <div class="sidebar-section-title">Utama</div>

            <a href="{{ route('super-admin.dashboard') }}" class="{{ request()->routeIs('super-admin.dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i>
                <span>Dashboard</span>
            </a>

            <div class="sidebar-section-title">Manajemen</div>

            <a href="{{ route('super-admin.users.index') }}" class="{{ request()->routeIs('super-admin.users.*') ? 'active' : '' }}">
                <i class="bi bi-people"></i>
                <span>Manajemen User</span>
            </a>

            <a href="{{ route('super-admin.pegawai.index') }}" class="{{ request()->routeIs('super-admin.pegawai.*') ? 'active' : '' }}">
                <i class="bi bi-person-badge"></i>
                <span>Data Pegawai</span>
            </a>

            <a href="#">
                <i class="bi bi-person-lines-fill"></i>
                <span>Data Murid</span>
            </a>

            <a href="#">
                <i class="bi bi-person-heart"></i>
                <span>Data Wali Murid</span>
            </a>

            <a href="{{ route('super-admin.kelas.index') }}" class="{{ request()->routeIs('super-admin.kelas.*') ? 'active' : '' }}">
                <i class="bi bi-building"></i>
                <span>Data Kelas</span>
            </a>

            <a href="#">
                <i class="bi bi-book"></i>
                <span>Mata Pelajaran</span>
            </a>

            <div class="sidebar-section-title">Akademik</div>

            <a href="#">
                <i class="bi bi-calendar-range"></i>
                <span>Tahun Ajaran</span>
            </a>

            <a href="#">
                <i class="bi bi-calendar2-week"></i>
                <span>Semester</span>
            </a>

            <a href="#">
                <i class="bi bi-calendar-check"></i>
                <span>Jadwal Pelajaran</span>
            </a>

            <div class="sidebar-section-title">Absensi & Nilai</div>

            <a href="#">
                <i class="bi bi-fingerprint"></i>
                <span>Absensi Pegawai</span>
            </a>

            <a href="#">
                <i class="bi bi-briefcase"></i>
                <span>Pengajuan Dinas</span>
            </a>

            <a href="#">
                <i class="bi bi-clipboard-check"></i>
                <span>Absensi Murid</span>
            </a>

            <a href="#">
                <i class="bi bi-journal-text"></i>
                <span>Nilai</span>
            </a>

            <a href="#">
                <i class="bi bi-file-earmark-bar-graph"></i>
                <span>Laporan</span>
            </a>

            <div class="sidebar-section-title">Website & Sistem</div>

            <a href="#">
                <i class="bi bi-whatsapp"></i>
                <span>WhatsApp Fonnte</span>
            </a>

            <a href="#">
                <i class="bi bi-newspaper"></i>
                <span>Berita / Kegiatan</span>
            </a>

            <a href="#">
                <i class="bi bi-images"></i>
                <span>Galeri</span>
            </a>

            <a href="#">
                <i class="bi bi-gear"></i>
                <span>Pengaturan</span>
            </a>

            <a href="#">
                <i class="bi bi-clock-history"></i>
                <span>Log Aktivitas</span>
            </a>
        @endif

        @if ($role === 'admin')
            {{-- penjelasan: Menu ini khusus admin. --}}

            <div class="sidebar-section-title">Utama</div>

            <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i>
                <span>Dashboard</span>
            </a>

            <div class="sidebar-section-title">Data Master</div>

            <a href="{{ route('admin.pegawai.index') }}" class="{{ request()->routeIs('admin.pegawai.*') ? 'active' : '' }}">
                <i class="bi bi-person-badge"></i>
                <span>Data Pegawai</span>
            </a>

            <a href="#">
                <i class="bi bi-person-lines-fill"></i>
                <span>Data Murid</span>
            </a>

            <a href="#">
                <i class="bi bi-person-heart"></i>
                <span>Data Wali Murid</span>
            </a>

            <a href="{{ route('admin.kelas.index') }}" class="{{ request()->routeIs('admin.kelas.*') ? 'active' : '' }}">
                <i class="bi bi-building"></i>
                <span>Data Kelas</span>
            </a>

            <a href="#">
                <i class="bi bi-book"></i>
                <span>Mata Pelajaran</span>
            </a>

            <div class="sidebar-section-title">Akademik</div>

            <a href="#">
                <i class="bi bi-calendar-range"></i>
                <span>Tahun Ajaran</span>
            </a>

            <a href="#">
                <i class="bi bi-calendar2-week"></i>
                <span>Semester</span>
            </a>

            <a href="#">
                <i class="bi bi-calendar-check"></i>
                <span>Jadwal Pelajaran</span>
            </a>

            <div class="sidebar-section-title">Absensi & Nilai</div>

            <a href="#">
                <i class="bi bi-fingerprint"></i>
                <span>Absensi Pegawai</span>
            </a>

            <a href="#">
                <i class="bi bi-briefcase"></i>
                <span>Persetujuan Dinas</span>
            </a>

            <a href="#">
                <i class="bi bi-clipboard-check"></i>
                <span>Absensi Murid</span>
            </a>

            <a href="#">
                <i class="bi bi-journal-text"></i>
                <span>Nilai</span>
            </a>

            <a href="#">
                <i class="bi bi-file-earmark-bar-graph"></i>
                <span>Laporan</span>
            </a>

            <div class="sidebar-section-title">Website</div>

            <a href="#">
                <i class="bi bi-whatsapp"></i>
                <span>WhatsApp Fonnte</span>
            </a>

            <a href="#">
                <i class="bi bi-newspaper"></i>
                <span>Berita / Kegiatan</span>
            </a>

            <a href="#">
                <i class="bi bi-images"></i>
                <span>Galeri</span>
            </a>
        @endif

        @if ($role === 'guru')
            {{-- penjelasan: Menu ini khusus guru. --}}

            <div class="sidebar-section-title">Utama</div>

            <a href="{{ route('guru.dashboard') }}" class="{{ request()->routeIs('guru.dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i>
                <span>Dashboard</span>
            </a>

            <div class="sidebar-section-title">Absensi</div>

            <a href="#">
                <i class="bi bi-fingerprint"></i>
                <span>Absen Saya</span>
            </a>

            <a href="#">
                <i class="bi bi-briefcase"></i>
                <span>Pengajuan Dinas</span>
            </a>

            <a href="#">
                <i class="bi bi-clock-history"></i>
                <span>Riwayat Absensi</span>
            </a>

            <div class="sidebar-section-title">Akademik</div>

            <a href="#">
                <i class="bi bi-calendar-check"></i>
                <span>Jadwal Mengajar</span>
            </a>

            <a href="#">
                <i class="bi bi-clipboard-check"></i>
                <span>Absen Murid</span>
            </a>

            <a href="#">
                <i class="bi bi-card-checklist"></i>
                <span>Rekap Absen Murid</span>
            </a>

            <a href="#">
                <i class="bi bi-journal-plus"></i>
                <span>Input Nilai</span>
            </a>

            <a href="#">
                <i class="bi bi-journal-text"></i>
                <span>Rekap Nilai</span>
            </a>

            <div class="sidebar-section-title">Akun</div>

            <a href="#">
                <i class="bi bi-person-circle"></i>
                <span>Profil Saya</span>
            </a>

            <a href="#">
                <i class="bi bi-key"></i>
                <span>Ganti Password</span>
            </a>
        @endif

        @if ($role === 'staff')
            {{-- penjelasan: Menu ini khusus staff. --}}

            <div class="sidebar-section-title">Utama</div>

            <a href="{{ route('staff.dashboard') }}" class="{{ request()->routeIs('staff.dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i>
                <span>Dashboard</span>
            </a>

            <div class="sidebar-section-title">Absensi</div>

            <a href="#">
                <i class="bi bi-fingerprint"></i>
                <span>Absen Saya</span>
            </a>

            <a href="#">
                <i class="bi bi-briefcase"></i>
                <span>Pengajuan Dinas</span>
            </a>

            <a href="#">
                <i class="bi bi-clock-history"></i>
                <span>Riwayat Absensi</span>
            </a>

            <div class="sidebar-section-title">Akun</div>

            <a href="#">
                <i class="bi bi-person-circle"></i>
                <span>Profil Saya</span>
            </a>

            <a href="#">
                <i class="bi bi-key"></i>
                <span>Ganti Password</span>
            </a>
        @endif

    </nav>
</aside>
