{{-- penjelasan: File ini adalah component sidebar dashboard. --}}
{{-- penjelasan: File ini dipanggil oleh resources/views/admin/layouts/app.blade.php memakai @include('admin.components.sidebar'). --}}
{{-- penjelasan: Sidebar ini menampilkan menu berbeda sesuai role user yang sedang login. --}}
{{-- penjelasan: Role user diambil dari auth()->user()->role. --}}

@php
    // penjelasan: auth()->user() digunakan untuk mengambil user yang sedang login.
    // penjelasan: role digunakan untuk menentukan menu apa saja yang boleh tampil.
    $role = auth()->user()->role ?? null;
@endphp

<aside class="dashboard-sidebar">

    {{-- penjelasan: Bagian brand menampilkan nama sistem/sekolah di sidebar. --}}
    <div class="sidebar-brand">
        <h5 class="mb-1 fw-bold">SMPN 6 Bati-Bati</h5>
        <small class="text-secondary">Sistem Akademik</small>
    </div>

    {{-- penjelasan: Bagian sidebar-menu berisi daftar menu dashboard. --}}
    <div class="sidebar-menu">

        {{-- penjelasan: Menu dashboard super admin hanya tampil jika role user adalah super_admin. --}}
        @if ($role === 'super_admin')
            <div class="sidebar-section-title">Utama</div>

            <a href="{{ route('super-admin.dashboard') }}" class="{{ request()->routeIs('super-admin.dashboard') ? 'active' : '' }}">
                Dashboard
            </a>

            <div class="sidebar-section-title">Manajemen</div>

            {{-- penjelasan: Menu di bawah ini belum diberi route asli karena fitur belum dibuat. --}}
            {{-- penjelasan: Untuk sementara href memakai # agar tidak error route. --}}
            <a href="#">Manajemen User</a>
            <a href="#">Data Pegawai</a>
            <a href="#">Data Murid</a>
            <a href="#">Data Wali Murid</a>
            <a href="#">Data Kelas</a>
            <a href="#">Mata Pelajaran</a>

            <div class="sidebar-section-title">Akademik</div>

            <a href="#">Tahun Ajaran</a>
            <a href="#">Semester</a>
            <a href="#">Jadwal Pelajaran</a>

            <div class="sidebar-section-title">Absensi & Nilai</div>

            <a href="#">Absensi Pegawai</a>
            <a href="#">Pengajuan Dinas</a>
            <a href="#">Absensi Murid</a>
            <a href="#">Nilai</a>
            <a href="#">Laporan</a>

            <div class="sidebar-section-title">Website & Sistem</div>

            <a href="#">WhatsApp Fonnte</a>
            <a href="#">Berita / Kegiatan</a>
            <a href="#">Galeri</a>
            <a href="#">Pengaturan</a>
        @endif

        {{-- penjelasan: Menu admin hanya tampil jika role user adalah admin. --}}
        @if ($role === 'admin')
            <div class="sidebar-section-title">Utama</div>

            <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                Dashboard
            </a>

            <div class="sidebar-section-title">Data Master</div>

            <a href="#">Data Pegawai</a>
            <a href="#">Data Murid</a>
            <a href="#">Data Wali Murid</a>
            <a href="#">Data Kelas</a>
            <a href="#">Mata Pelajaran</a>

            <div class="sidebar-section-title">Akademik</div>

            <a href="#">Tahun Ajaran</a>
            <a href="#">Semester</a>
            <a href="#">Jadwal Pelajaran</a>

            <div class="sidebar-section-title">Absensi & Nilai</div>

            <a href="#">Absensi Pegawai</a>
            <a href="#">Persetujuan Dinas</a>
            <a href="#">Absensi Murid</a>
            <a href="#">Nilai</a>
            <a href="#">Laporan</a>

            <div class="sidebar-section-title">Website</div>

            <a href="#">WhatsApp Fonnte</a>
            <a href="#">Berita / Kegiatan</a>
            <a href="#">Galeri</a>
        @endif

        {{-- penjelasan: Menu guru hanya tampil jika role user adalah guru. --}}
        @if ($role === 'guru')
            <div class="sidebar-section-title">Utama</div>

            <a href="{{ route('guru.dashboard') }}" class="{{ request()->routeIs('guru.dashboard') ? 'active' : '' }}">
                Dashboard
            </a>

            <div class="sidebar-section-title">Absensi</div>

            <a href="#">Absen Saya</a>
            <a href="#">Pengajuan Dinas</a>
            <a href="#">Riwayat Absensi</a>

            <div class="sidebar-section-title">Akademik</div>

            <a href="#">Jadwal Mengajar</a>
            <a href="#">Absen Murid</a>
            <a href="#">Rekap Absen Murid</a>
            <a href="#">Input Nilai</a>
            <a href="#">Rekap Nilai</a>

            <div class="sidebar-section-title">Akun</div>

            <a href="#">Profil Saya</a>
            <a href="#">Ganti Password</a>
        @endif

        {{-- penjelasan: Menu staff hanya tampil jika role user adalah staff. --}}
        @if ($role === 'staff')
            <div class="sidebar-section-title">Utama</div>

            <a href="{{ route('staff.dashboard') }}" class="{{ request()->routeIs('staff.dashboard') ? 'active' : '' }}">
                Dashboard
            </a>

            <div class="sidebar-section-title">Absensi</div>

            <a href="#">Absen Saya</a>
            <a href="#">Pengajuan Dinas</a>
            <a href="#">Riwayat Absensi</a>

            <div class="sidebar-section-title">Akun</div>

            <a href="#">Profil Saya</a>
            <a href="#">Ganti Password</a>
        @endif

    </div>
</aside>
