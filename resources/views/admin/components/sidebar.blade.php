{{-- penjelasan: File ini adalah component sidebar dashboard. --}}
{{-- penjelasan: Sidebar ini dipanggil oleh layout utama admin.layouts.app. --}}
{{-- penjelasan: Sidebar menampilkan menu berbeda sesuai role user yang sedang login. --}}
{{-- penjelasan: Sidebar dibuat fixed lewat admin.css, sehingga tidak ikut bergerak saat halaman utama discroll. --}}
{{-- penjelasan: Jika menu sidebar panjang, sidebar bisa discroll sendiri karena memakai overflow-y: auto di admin.css. --}}
{{-- penjelasan: File ini memakai route() Laravel untuk mengarahkan menu ke halaman masing-masing. --}}
{{-- penjelasan: request()->routeIs() dipakai untuk memberi class active pada menu yang sedang dibuka. --}}

@php
    // penjelasan: auth()->user() mengambil data user yang sedang login.
    // penjelasan: Data ini berasal dari tabel users.
    // penjelasan: Data user dipakai untuk menampilkan nama, role, dan menentukan menu sidebar.
    $user = auth()->user();

    // penjelasan: Role user digunakan untuk menentukan menu yang tampil.
    // penjelasan: Role yang digunakan pada sistem ini adalah super_admin, admin, guru, dan staff.
    $role = $user->role ?? null;
@endphp

<aside class="admin-sidebar">

    {{-- penjelasan: sidebar-brand adalah bagian atas sidebar. --}}
    {{-- penjelasan: Bagian ini menampilkan icon dan nama sistem sekolah. --}}
    <div class="sidebar-brand">
        <div class="brand-logo">
            <i class="bi bi-mortarboard-fill"></i>
        </div>

        <div>
            <h5 class="brand-title">SMPN 6 Bati-Bati</h5>
            <small class="brand-subtitle">Sistem Akademik</small>
        </div>
    </div>

    {{-- penjelasan: sidebar-user menampilkan informasi user yang sedang login. --}}
    {{-- penjelasan: Avatar mengambil huruf pertama dari nama user. --}}
    {{-- penjelasan: Role ditampilkan dengan format lebih rapi, misalnya super_admin menjadi Super Admin. --}}
    <div class="sidebar-user">
        <div class="user-avatar">
            {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
        </div>

        <div class="user-info">
            <div class="user-name">{{ $user->name ?? 'User' }}</div>
            <div class="user-role">{{ ucwords(str_replace('_', ' ', $role ?? '-')) }}</div>
        </div>
    </div>

    {{-- penjelasan: sidebar-menu adalah area daftar menu dashboard. --}}
    {{-- penjelasan: Isi menu akan berubah sesuai role user. --}}
    <nav class="sidebar-menu">

        @if ($role === 'super_admin')
            {{-- penjelasan: Menu ini khusus untuk Super Admin. --}}
            {{-- penjelasan: Super Admin memiliki akses paling lengkap, termasuk Manajemen User. --}}

            <div class="sidebar-section-title">Utama</div>

            {{-- penjelasan: Menu Dashboard Super Admin. --}}
            {{-- penjelasan: route('super-admin.dashboard') mengarah ke /super-admin/dashboard. --}}
            <a href="{{ route('super-admin.dashboard') }}" class="{{ request()->routeIs('super-admin.dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i>
                <span>Dashboard</span>
            </a>

            <div class="sidebar-section-title">Manajemen</div>

            {{-- penjelasan: Menu Manajemen User hanya ada pada Super Admin. --}}
            {{-- penjelasan: Menu ini digunakan untuk membuat akun admin, guru, dan staff. --}}
            <a href="{{ route('super-admin.users.index') }}" class="{{ request()->routeIs('super-admin.users.*') ? 'active' : '' }}">
                <i class="bi bi-people"></i>
                <span>Manajemen User</span>
            </a>

            {{-- penjelasan: Menu Data Pegawai mengarah ke daftar guru dan staff. --}}
            {{-- penjelasan: request()->routeIs('super-admin.pegawai.*') membuat menu aktif pada semua halaman pegawai. --}}
            <a href="{{ route('super-admin.pegawai.index') }}" class="{{ request()->routeIs('super-admin.pegawai.*') ? 'active' : '' }}">
                <i class="bi bi-person-badge"></i>
                <span>Data Pegawai</span>
            </a>

            {{-- penjelasan: Menu Data Murid masih belum aktif karena modul Data Murid belum dibuat. --}}
            {{-- penjelasan: Sementara href masih # agar tidak error route. --}}
            <a href="#">
                <i class="bi bi-person-lines-fill"></i>
                <span>Data Murid</span>
            </a>

            {{-- penjelasan: Menu Data Wali Murid sudah aktif. --}}
            {{-- penjelasan: Menu ini mengarah ke modul wali murid untuk super admin. --}}
            {{-- penjelasan: Route ini baru berjalan setelah WaliMuridController dan route wali-murid dibuat. --}}
            <a href="{{ route('super-admin.wali-murid.index') }}" class="{{ request()->routeIs('super-admin.wali-murid.*') ? 'active' : '' }}">
                <i class="bi bi-person-heart"></i>
                <span>Data Wali Murid</span>
            </a>

            {{-- penjelasan: Menu Data Kelas mengarah ke modul kelas untuk super admin. --}}
            <a href="{{ route('super-admin.kelas.index') }}" class="{{ request()->routeIs('super-admin.kelas.*') ? 'active' : '' }}">
                <i class="bi bi-building"></i>
                <span>Data Kelas</span>
            </a>

            {{-- penjelasan: Menu Mata Pelajaran belum aktif karena modulnya belum dibuat. --}}
            <a href="#">
                <i class="bi bi-book"></i>
                <span>Mata Pelajaran</span>
            </a>

            <div class="sidebar-section-title">Akademik</div>

            {{-- penjelasan: Menu Tahun Ajaran belum aktif karena modulnya belum dibuat. --}}
            <a href="#">
                <i class="bi bi-calendar-range"></i>
                <span>Tahun Ajaran</span>
            </a>

            {{-- penjelasan: Menu Semester belum aktif karena modulnya belum dibuat. --}}
            <a href="#">
                <i class="bi bi-calendar2-week"></i>
                <span>Semester</span>
            </a>

            {{-- penjelasan: Menu Jadwal Pelajaran belum aktif karena modulnya belum dibuat. --}}
            <a href="#">
                <i class="bi bi-calendar-check"></i>
                <span>Jadwal Pelajaran</span>
            </a>

            <div class="sidebar-section-title">Absensi & Nilai</div>

            {{-- penjelasan: Menu Absensi Pegawai belum aktif karena modul absensi pegawai belum dibuat. --}}
            <a href="#">
                <i class="bi bi-fingerprint"></i>
                <span>Absensi Pegawai</span>
            </a>

            {{-- penjelasan: Menu Pengajuan Dinas belum aktif karena modul pengajuan dinas belum dibuat. --}}
            <a href="#">
                <i class="bi bi-briefcase"></i>
                <span>Pengajuan Dinas</span>
            </a>

            {{-- penjelasan: Menu Absensi Murid belum aktif karena modul absensi murid belum dibuat. --}}
            <a href="#">
                <i class="bi bi-clipboard-check"></i>
                <span>Absensi Murid</span>
            </a>

            {{-- penjelasan: Menu Nilai belum aktif karena modul nilai belum dibuat. --}}
            <a href="#">
                <i class="bi bi-journal-text"></i>
                <span>Nilai</span>
            </a>

            {{-- penjelasan: Menu Laporan belum aktif karena modul laporan belum dibuat. --}}
            <a href="#">
                <i class="bi bi-file-earmark-bar-graph"></i>
                <span>Laporan</span>
            </a>

            <div class="sidebar-section-title">Website & Sistem</div>

            {{-- penjelasan: Menu WhatsApp Fonnte belum aktif karena integrasi Fonnte belum dibuat. --}}
            <a href="#">
                <i class="bi bi-whatsapp"></i>
                <span>WhatsApp Fonnte</span>
            </a>

            {{-- penjelasan: Menu Berita/Kegiatan belum aktif karena modul website public belum dibuat. --}}
            <a href="#">
                <i class="bi bi-newspaper"></i>
                <span>Berita / Kegiatan</span>
            </a>

            {{-- penjelasan: Menu Galeri belum aktif karena modul galeri belum dibuat. --}}
            <a href="#">
                <i class="bi bi-images"></i>
                <span>Galeri</span>
            </a>

            {{-- penjelasan: Menu Pengaturan belum aktif karena modul pengaturan belum dibuat. --}}
            <a href="#">
                <i class="bi bi-gear"></i>
                <span>Pengaturan</span>
            </a>

            {{-- penjelasan: Menu Log Aktivitas belum aktif karena modul log belum dibuat. --}}
            <a href="#">
                <i class="bi bi-clock-history"></i>
                <span>Log Aktivitas</span>
            </a>
        @endif

        @if ($role === 'admin')
            {{-- penjelasan: Menu ini khusus untuk Admin. --}}
            {{-- penjelasan: Admin bisa mengelola data master dan akademik, tetapi tidak mengelola Manajemen User. --}}

            <div class="sidebar-section-title">Utama</div>

            {{-- penjelasan: Menu Dashboard Admin. --}}
            <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i>
                <span>Dashboard</span>
            </a>

            <div class="sidebar-section-title">Data Master</div>

            {{-- penjelasan: Menu Data Pegawai untuk Admin. --}}
            <a href="{{ route('admin.pegawai.index') }}" class="{{ request()->routeIs('admin.pegawai.*') ? 'active' : '' }}">
                <i class="bi bi-person-badge"></i>
                <span>Data Pegawai</span>
            </a>

            {{-- penjelasan: Menu Data Murid belum aktif karena modul Data Murid belum dibuat. --}}
            <a href="#">
                <i class="bi bi-person-lines-fill"></i>
                <span>Data Murid</span>
            </a>

            {{-- penjelasan: Menu Data Wali Murid untuk Admin sudah aktif. --}}
            <a href="{{ route('admin.wali-murid.index') }}" class="{{ request()->routeIs('admin.wali-murid.*') ? 'active' : '' }}">
                <i class="bi bi-person-heart"></i>
                <span>Data Wali Murid</span>
            </a>

            {{-- penjelasan: Menu Data Kelas untuk Admin. --}}
            <a href="{{ route('admin.kelas.index') }}" class="{{ request()->routeIs('admin.kelas.*') ? 'active' : '' }}">
                <i class="bi bi-building"></i>
                <span>Data Kelas</span>
            </a>

            {{-- penjelasan: Menu Mata Pelajaran belum aktif karena modulnya belum dibuat. --}}
            <a href="#">
                <i class="bi bi-book"></i>
                <span>Mata Pelajaran</span>
            </a>

            <div class="sidebar-section-title">Akademik</div>

            {{-- penjelasan: Menu Tahun Ajaran belum aktif karena modulnya belum dibuat. --}}
            <a href="#">
                <i class="bi bi-calendar-range"></i>
                <span>Tahun Ajaran</span>
            </a>

            {{-- penjelasan: Menu Semester belum aktif karena modulnya belum dibuat. --}}
            <a href="#">
                <i class="bi bi-calendar2-week"></i>
                <span>Semester</span>
            </a>

            {{-- penjelasan: Menu Jadwal Pelajaran belum aktif karena modulnya belum dibuat. --}}
            <a href="#">
                <i class="bi bi-calendar-check"></i>
                <span>Jadwal Pelajaran</span>
            </a>

            <div class="sidebar-section-title">Absensi & Nilai</div>

            {{-- penjelasan: Menu Absensi Pegawai belum aktif karena modulnya belum dibuat. --}}
            <a href="#">
                <i class="bi bi-fingerprint"></i>
                <span>Absensi Pegawai</span>
            </a>

            {{-- penjelasan: Menu Persetujuan Dinas belum aktif karena modul dinas belum dibuat. --}}
            <a href="#">
                <i class="bi bi-briefcase"></i>
                <span>Persetujuan Dinas</span>
            </a>

            {{-- penjelasan: Menu Absensi Murid belum aktif karena modulnya belum dibuat. --}}
            <a href="#">
                <i class="bi bi-clipboard-check"></i>
                <span>Absensi Murid</span>
            </a>

            {{-- penjelasan: Menu Nilai belum aktif karena modulnya belum dibuat. --}}
            <a href="#">
                <i class="bi bi-journal-text"></i>
                <span>Nilai</span>
            </a>

            {{-- penjelasan: Menu Laporan belum aktif karena modulnya belum dibuat. --}}
            <a href="#">
                <i class="bi bi-file-earmark-bar-graph"></i>
                <span>Laporan</span>
            </a>

            <div class="sidebar-section-title">Website</div>

            {{-- penjelasan: Menu WhatsApp Fonnte belum aktif karena integrasi Fonnte belum dibuat. --}}
            <a href="#">
                <i class="bi bi-whatsapp"></i>
                <span>WhatsApp Fonnte</span>
            </a>

            {{-- penjelasan: Menu Berita/Kegiatan belum aktif karena modul website public belum dibuat. --}}
            <a href="#">
                <i class="bi bi-newspaper"></i>
                <span>Berita / Kegiatan</span>
            </a>

            {{-- penjelasan: Menu Galeri belum aktif karena modul galeri belum dibuat. --}}
            <a href="#">
                <i class="bi bi-images"></i>
                <span>Galeri</span>
            </a>
        @endif

        @if ($role === 'guru')
            {{-- penjelasan: Menu ini khusus untuk Guru. --}}
            {{-- penjelasan: Guru nanti fokus pada absensi pribadi, pengajuan dinas, jadwal mengajar, absen murid, dan nilai. --}}

            <div class="sidebar-section-title">Utama</div>

            {{-- penjelasan: Menu Dashboard Guru. --}}
            <a href="{{ route('guru.dashboard') }}" class="{{ request()->routeIs('guru.dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i>
                <span>Dashboard</span>
            </a>

            <div class="sidebar-section-title">Absensi</div>

            {{-- penjelasan: Menu Absen Saya belum aktif karena modul Absensi Pegawai belum dibuat. --}}
            <a href="#">
                <i class="bi bi-fingerprint"></i>
                <span>Absen Saya</span>
            </a>

            {{-- penjelasan: Menu Pengajuan Dinas belum aktif karena modul Pengajuan Dinas belum dibuat. --}}
            <a href="#">
                <i class="bi bi-briefcase"></i>
                <span>Pengajuan Dinas</span>
            </a>

            {{-- penjelasan: Menu Riwayat Absensi belum aktif karena modul Absensi Pegawai belum dibuat. --}}
            <a href="#">
                <i class="bi bi-clock-history"></i>
                <span>Riwayat Absensi</span>
            </a>

            <div class="sidebar-section-title">Akademik</div>

            {{-- penjelasan: Menu Jadwal Mengajar belum aktif karena modul Jadwal Pelajaran belum dibuat. --}}
            <a href="#">
                <i class="bi bi-calendar-check"></i>
                <span>Jadwal Mengajar</span>
            </a>

            {{-- penjelasan: Menu Absen Murid belum aktif karena modul Absensi Murid belum dibuat. --}}
            <a href="#">
                <i class="bi bi-clipboard-check"></i>
                <span>Absen Murid</span>
            </a>

            {{-- penjelasan: Menu Rekap Absen Murid belum aktif karena modul Absensi Murid belum dibuat. --}}
            <a href="#">
                <i class="bi bi-card-checklist"></i>
                <span>Rekap Absen Murid</span>
            </a>

            {{-- penjelasan: Menu Input Nilai belum aktif karena modul Nilai belum dibuat. --}}
            <a href="#">
                <i class="bi bi-journal-plus"></i>
                <span>Input Nilai</span>
            </a>

            {{-- penjelasan: Menu Rekap Nilai belum aktif karena modul Nilai belum dibuat. --}}
            <a href="#">
                <i class="bi bi-journal-text"></i>
                <span>Rekap Nilai</span>
            </a>

            <div class="sidebar-section-title">Akun</div>

            {{-- penjelasan: Menu Profil Saya belum aktif karena modul profil belum dibuat. --}}
            <a href="#">
                <i class="bi bi-person-circle"></i>
                <span>Profil Saya</span>
            </a>

            {{-- penjelasan: Menu Ganti Password belum aktif karena fitur ganti password personal belum dibuat. --}}
            <a href="#">
                <i class="bi bi-key"></i>
                <span>Ganti Password</span>
            </a>
        @endif

        @if ($role === 'staff')
            {{-- penjelasan: Menu ini khusus untuk Staff. --}}
            {{-- penjelasan: Staff nanti fokus pada absensi pribadi, pengajuan dinas, dan riwayat absensi. --}}

            <div class="sidebar-section-title">Utama</div>

            {{-- penjelasan: Menu Dashboard Staff. --}}
            <a href="{{ route('staff.dashboard') }}" class="{{ request()->routeIs('staff.dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i>
                <span>Dashboard</span>
            </a>

            <div class="sidebar-section-title">Absensi</div>

            {{-- penjelasan: Menu Absen Saya belum aktif karena modul Absensi Pegawai belum dibuat. --}}
            <a href="#">
                <i class="bi bi-fingerprint"></i>
                <span>Absen Saya</span>
            </a>

            {{-- penjelasan: Menu Pengajuan Dinas belum aktif karena modul Pengajuan Dinas belum dibuat. --}}
            <a href="#">
                <i class="bi bi-briefcase"></i>
                <span>Pengajuan Dinas</span>
            </a>

            {{-- penjelasan: Menu Riwayat Absensi belum aktif karena modul Absensi Pegawai belum dibuat. --}}
            <a href="#">
                <i class="bi bi-clock-history"></i>
                <span>Riwayat Absensi</span>
            </a>

            <div class="sidebar-section-title">Akun</div>

            {{-- penjelasan: Menu Profil Saya belum aktif karena modul profil belum dibuat. --}}
            <a href="#">
                <i class="bi bi-person-circle"></i>
                <span>Profil Saya</span>
            </a>

            {{-- penjelasan: Menu Ganti Password belum aktif karena fitur ganti password personal belum dibuat. --}}
            <a href="#">
                <i class="bi bi-key"></i>
                <span>Ganti Password</span>
            </a>
        @endif

    </nav>
</aside>
