{{-- penjelasan: File ini adalah component sidebar dashboard. --}}
{{-- penjelasan: Sidebar ini dipanggil oleh layout utama admin.layouts.app. --}}
{{-- penjelasan: Sidebar menampilkan menu berbeda sesuai role user yang sedang login. --}}
{{-- penjelasan: Sidebar dibuat fixed lewat admin.css, sehingga tidak ikut bergerak saat halaman utama discroll. --}}
{{-- penjelasan: Jika menu sidebar panjang, sidebar bisa discroll sendiri karena memakai overflow-y: auto di admin.css. --}}
{{-- penjelasan: File ini memakai route() Laravel untuk mengarahkan menu ke halaman masing-masing. --}}
{{-- penjelasan: request()->routeIs() dipakai untuk memberi class active pada menu yang sedang dibuka. --}}
{{-- penjelasan: Menu yang belum dibuat routenya tetap memakai href="#" agar tidak menyebabkan error route not defined. --}}
{{-- penjelasan: Menu Absen Saya untuk guru dan staff sudah aktif menuju modul absensi-pegawai. --}}
{{-- penjelasan: Menu Absensi Pegawai untuk admin dan super admin sudah aktif menuju modul rekap absensi pegawai. --}}

@php
    // penjelasan: auth()->user() mengambil data user yang sedang login.
    // penjelasan: Data ini berasal dari tabel users.
    // penjelasan: Data user dipakai untuk menampilkan nama, role, dan menentukan menu sidebar.
    $user = auth()->user();

    // penjelasan: Role user digunakan untuk menentukan menu yang tampil.
    // penjelasan: Role yang digunakan pada sistem ini adalah super_admin, admin, guru, dan staff.
    $role = $user->role ?? null;

    // penjelasan: roleLabel dipakai untuk menampilkan role dengan format lebih rapi.
    // penjelasan: Contoh super_admin menjadi Super Admin.
    $roleLabel = $role ? ucwords(str_replace('_', ' ', $role)) : '-';
@endphp

<aside class="admin-sidebar">

    <div class="sidebar-brand">
        <div class="brand-logo">
            <i class="bi bi-mortarboard-fill"></i>
        </div>

        <div>
            <h5 class="brand-title">SMPN 6 Bati-Bati</h5>
            <small class="brand-subtitle">Sistem Akademik</small>
        </div>
    </div>

    <div class="sidebar-user">
        <div class="user-avatar">
            {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
        </div>

        <div class="user-info">
            <div class="user-name">{{ $user->name ?? 'User' }}</div>
            <div class="user-role">{{ $roleLabel }}</div>
        </div>
    </div>

    <nav class="sidebar-menu">

        @if ($role === 'super_admin')
            <div class="sidebar-section-title">Utama</div>

            <a href="{{ route('super-admin.dashboard') }}" class="{{ request()->routeIs('super-admin.dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i>
                <span>Dashboard</span>
            </a>

            <div class="sidebar-section-title">Manajemen User</div>

            <a href="{{ route('super-admin.users.index') }}" class="{{ request()->routeIs('super-admin.users.*') ? 'active' : '' }}">
                <i class="bi bi-people"></i>
                <span>Manajemen User</span>
            </a>

            <div class="sidebar-section-title">Data Master</div>

            <a href="{{ route('super-admin.pegawai.index') }}" class="{{ request()->routeIs('super-admin.pegawai.*') ? 'active' : '' }}">
                <i class="bi bi-person-badge"></i>
                <span>Data Pegawai</span>
            </a>

            <a href="{{ route('super-admin.murid.index') }}" class="{{ request()->routeIs('super-admin.murid.*') ? 'active' : '' }}">
                <i class="bi bi-person-lines-fill"></i>
                <span>Data Murid</span>
            </a>

            <a href="{{ route('super-admin.wali-murid.index') }}" class="{{ request()->routeIs('super-admin.wali-murid.*') ? 'active' : '' }}">
                <i class="bi bi-person-heart"></i>
                <span>Data Wali Murid</span>
            </a>

            <a href="{{ route('super-admin.kelas.index') }}" class="{{ request()->routeIs('super-admin.kelas.*') ? 'active' : '' }}">
                <i class="bi bi-building"></i>
                <span>Data Kelas</span>
            </a>

            <a href="{{ route('super-admin.mata-pelajaran.index') }}" class="{{ request()->routeIs('super-admin.mata-pelajaran.*') ? 'active' : '' }}">
                <i class="bi bi-book"></i>
                <span>Mata Pelajaran</span>
            </a>

            <div class="sidebar-section-title">Akademik</div>

            <a href="{{ route('super-admin.tahun-ajaran.index') }}" class="{{ request()->routeIs('super-admin.tahun-ajaran.*') ? 'active' : '' }}">
                <i class="bi bi-calendar-range"></i>
                <span>Tahun Ajaran</span>
            </a>

            <a href="{{ route('super-admin.semester.index') }}" class="{{ request()->routeIs('super-admin.semester.*') ? 'active' : '' }}">
                <i class="bi bi-calendar2-week"></i>
                <span>Semester</span>
            </a>

            <a href="{{ route('super-admin.jadwal-pelajaran.index') }}" class="{{ request()->routeIs('super-admin.jadwal-pelajaran.*') ? 'active' : '' }}">
                <i class="bi bi-calendar-check"></i>
                <span>Jadwal Pelajaran</span>
            </a>

            <div class="sidebar-section-title">Absensi & Nilai</div>

            <a href="{{ route('super-admin.absensi-pegawai.index') }}" class="{{ request()->routeIs('super-admin.absensi-pegawai.*') ? 'active' : '' }}">
                <i class="bi bi-fingerprint"></i>
                <span>Absensi Pegawai</span>
            </a>

            <a href="{{ route('super-admin.persetujuan-absensi-pegawai.index') }}" class="{{ request()->routeIs('super-admin.persetujuan-absensi-pegawai.*') ? 'active' : '' }}">
                <i class="bi bi-clipboard-check"></i>
                <span>Persetujuan Absensi</span>
            </a>

            <a href="#" aria-disabled="true" title="Modul Absensi Murid belum dibuat">
                <i class="bi bi-person-check"></i>
                <span>Absensi Murid</span>
            </a>

            <a href="#" aria-disabled="true" title="Modul Nilai belum dibuat">
                <i class="bi bi-journal-text"></i>
                <span>Nilai</span>
            </a>

            <a href="#" aria-disabled="true" title="Modul Laporan belum dibuat">
                <i class="bi bi-file-earmark-bar-graph"></i>
                <span>Laporan</span>
            </a>

            <div class="sidebar-section-title">Website & Sistem</div>

            <a href="#" aria-disabled="true" title="Modul WhatsApp Fonnte belum dibuat">
                <i class="bi bi-whatsapp"></i>
                <span>WhatsApp Fonnte</span>
            </a>

            <a href="#" aria-disabled="true" title="Modul Berita/Kegiatan belum dibuat">
                <i class="bi bi-newspaper"></i>
                <span>Berita / Kegiatan</span>
            </a>

            <a href="#" aria-disabled="true" title="Modul Galeri belum dibuat">
                <i class="bi bi-images"></i>
                <span>Galeri</span>
            </a>

            <a href="#" aria-disabled="true" title="Modul Pengaturan belum dibuat">
                <i class="bi bi-gear"></i>
                <span>Pengaturan</span>
            </a>

            <a href="#" aria-disabled="true" title="Modul Log Aktivitas belum dibuat">
                <i class="bi bi-clock-history"></i>
                <span>Log Aktivitas</span>
            </a>
        @endif

        @if ($role === 'admin')
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

            <a href="{{ route('admin.murid.index') }}" class="{{ request()->routeIs('admin.murid.*') ? 'active' : '' }}">
                <i class="bi bi-person-lines-fill"></i>
                <span>Data Murid</span>
            </a>

            <a href="{{ route('admin.wali-murid.index') }}" class="{{ request()->routeIs('admin.wali-murid.*') ? 'active' : '' }}">
                <i class="bi bi-person-heart"></i>
                <span>Data Wali Murid</span>
            </a>

            <a href="{{ route('admin.kelas.index') }}" class="{{ request()->routeIs('admin.kelas.*') ? 'active' : '' }}">
                <i class="bi bi-building"></i>
                <span>Data Kelas</span>
            </a>

            <a href="{{ route('admin.mata-pelajaran.index') }}" class="{{ request()->routeIs('admin.mata-pelajaran.*') ? 'active' : '' }}">
                <i class="bi bi-book"></i>
                <span>Mata Pelajaran</span>
            </a>

            <div class="sidebar-section-title">Akademik</div>

            <a href="{{ route('admin.tahun-ajaran.index') }}" class="{{ request()->routeIs('admin.tahun-ajaran.*') ? 'active' : '' }}">
                <i class="bi bi-calendar-range"></i>
                <span>Tahun Ajaran</span>
            </a>

            <a href="{{ route('admin.semester.index') }}" class="{{ request()->routeIs('admin.semester.*') ? 'active' : '' }}">
                <i class="bi bi-calendar2-week"></i>
                <span>Semester</span>
            </a>

            <a href="{{ route('admin.jadwal-pelajaran.index') }}" class="{{ request()->routeIs('admin.jadwal-pelajaran.*') ? 'active' : '' }}">
                <i class="bi bi-calendar-check"></i>
                <span>Jadwal Pelajaran</span>
            </a>

            <div class="sidebar-section-title">Absensi & Nilai</div>

            <a href="{{ route('admin.absensi-pegawai.index') }}" class="{{ request()->routeIs('admin.absensi-pegawai.*') ? 'active' : '' }}">
                <i class="bi bi-fingerprint"></i>
                <span>Absensi Pegawai</span>
            </a>

            <a href="{{ route('admin.persetujuan-absensi-pegawai.index') }}" class="{{ request()->routeIs('admin.persetujuan-absensi-pegawai.*') ? 'active' : '' }}">
                <i class="bi bi-clipboard-check"></i>
                <span>Persetujuan Absensi</span>
            </a>

            <a href="#" aria-disabled="true" title="Modul Absensi Murid belum dibuat">
                <i class="bi bi-person-check"></i>
                <span>Absensi Murid</span>
            </a>

            <a href="#" aria-disabled="true" title="Modul Nilai belum dibuat">
                <i class="bi bi-journal-text"></i>
                <span>Nilai</span>
            </a>

            <a href="#" aria-disabled="true" title="Modul Laporan belum dibuat">
                <i class="bi bi-file-earmark-bar-graph"></i>
                <span>Laporan</span>
            </a>

            <div class="sidebar-section-title">Website</div>

            <a href="#" aria-disabled="true" title="Modul WhatsApp Fonnte belum dibuat">
                <i class="bi bi-whatsapp"></i>
                <span>WhatsApp Fonnte</span>
            </a>

            <a href="#" aria-disabled="true" title="Modul Berita/Kegiatan belum dibuat">
                <i class="bi bi-newspaper"></i>
                <span>Berita / Kegiatan</span>
            </a>

            <a href="#" aria-disabled="true" title="Modul Galeri belum dibuat">
                <i class="bi bi-images"></i>
                <span>Galeri</span>
            </a>
        @endif

        @if ($role === 'guru')
            <div class="sidebar-section-title">Utama</div>

            <a href="{{ route('guru.dashboard') }}" class="{{ request()->routeIs('guru.dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i>
                <span>Dashboard</span>
            </a>

            <div class="sidebar-section-title">Absensi</div>

            <a href="{{ route('guru.absensi-pegawai.index') }}" class="{{ request()->routeIs('guru.absensi-pegawai.*') ? 'active' : '' }}">
                <i class="bi bi-fingerprint"></i>
                <span>Absen Saya</span>
            </a>

            <a href="{{ route('guru.pengajuan-absensi-pegawai.index') }}" class="{{ request()->routeIs('guru.pengajuan-absensi-pegawai.*') ? 'active' : '' }}">
                <i class="bi bi-send-check"></i>
                <span>Pengajuan Absensi</span>
            </a>

            <a href="#" aria-disabled="true" title="Modul Riwayat Absensi terpisah belum dibuat karena riwayat sudah tampil di halaman Absen Saya">
                <i class="bi bi-clock-history"></i>
                <span>Riwayat Absensi</span>
            </a>

            <div class="sidebar-section-title">Akademik</div>

            <a href="#" aria-disabled="true" title="Modul Jadwal Mengajar Guru belum dibuat">
                <i class="bi bi-calendar-check"></i>
                <span>Jadwal Mengajar</span>
            </a>

            <a href="#" aria-disabled="true" title="Modul Absen Murid belum dibuat">
                <i class="bi bi-clipboard-check"></i>
                <span>Absen Murid</span>
            </a>

            <a href="#" aria-disabled="true" title="Modul Rekap Absen Murid belum dibuat">
                <i class="bi bi-card-checklist"></i>
                <span>Rekap Absen Murid</span>
            </a>

            <a href="#" aria-disabled="true" title="Modul Input Nilai belum dibuat">
                <i class="bi bi-journal-plus"></i>
                <span>Input Nilai</span>
            </a>

            <a href="#" aria-disabled="true" title="Modul Rekap Nilai belum dibuat">
                <i class="bi bi-journal-text"></i>
                <span>Rekap Nilai</span>
            </a>

            <div class="sidebar-section-title">Akun</div>

            <a href="#" aria-disabled="true" title="Modul Profil Saya belum dibuat">
                <i class="bi bi-person-circle"></i>
                <span>Profil Saya</span>
            </a>

            <a href="#" aria-disabled="true" title="Modul Ganti Password belum dibuat">
                <i class="bi bi-key"></i>
                <span>Ganti Password</span>
            </a>
        @endif

        @if ($role === 'staff')
            <div class="sidebar-section-title">Utama</div>

            <a href="{{ route('staff.dashboard') }}" class="{{ request()->routeIs('staff.dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i>
                <span>Dashboard</span>
            </a>

            <div class="sidebar-section-title">Absensi</div>

            <a href="{{ route('staff.absensi-pegawai.index') }}" class="{{ request()->routeIs('staff.absensi-pegawai.*') ? 'active' : '' }}">
                <i class="bi bi-fingerprint"></i>
                <span>Absen Saya</span>
            </a>

            <a href="{{ route('staff.pengajuan-absensi-pegawai.index') }}" class="{{ request()->routeIs('staff.pengajuan-absensi-pegawai.*') ? 'active' : '' }}">
                <i class="bi bi-send-check"></i>
                <span>Pengajuan Absensi</span>
            </a>

            <a href="#" aria-disabled="true" title="Modul Riwayat Absensi terpisah belum dibuat karena riwayat sudah tampil di halaman Absen Saya">
                <i class="bi bi-clock-history"></i>
                <span>Riwayat Absensi</span>
            </a>

            <div class="sidebar-section-title">Akun</div>

            <a href="#" aria-disabled="true" title="Modul Profil Saya belum dibuat">
                <i class="bi bi-person-circle"></i>
                <span>Profil Saya</span>
            </a>

            <a href="#" aria-disabled="true" title="Modul Ganti Password belum dibuat">
                <i class="bi bi-key"></i>
                <span>Ganti Password</span>
            </a>
        @endif

    </nav>
</aside>
