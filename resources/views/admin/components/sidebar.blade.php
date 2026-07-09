{{-- penjelasan: File ini adalah component sidebar dashboard. --}}
{{-- penjelasan: Sidebar ini dipanggil oleh layout utama admin.layouts.app. --}}
{{-- penjelasan: Sidebar menampilkan menu berbeda sesuai role user yang sedang login. --}}
{{-- penjelasan: Role aktif pada sistem adalah super_admin, admin, dan guru. --}}
{{-- penjelasan: Role staff sudah tidak digunakan dan menu staff dihapus. --}}
{{-- penjelasan: Menu Profil Saya pada role guru sudah dihapus. --}}
{{-- penjelasan: Menu Ganti Password pada role guru sudah diaktifkan dan diarahkan ke halaman ganti password. --}}

@php
    $user = auth()->user();
    $role = $user->role ?? null;
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

            <a href="{{ route('super-admin.rekap-absensi-murid.index') }}" class="{{ request()->routeIs('super-admin.rekap-absensi-murid.*') ? 'active' : '' }}">
                <i class="bi bi-person-check"></i>
                <span>Rekap Absensi Murid</span>
            </a>

            <a href="{{ route('super-admin.nilai.index') }}" class="{{ request()->routeIs('super-admin.nilai.*') ? 'active' : '' }}">
                <i class="bi bi-journal-text"></i>
                <span>Nilai</span>
            </a>

            <a href="{{ route('super-admin.laporan.index') }}" class="{{ request()->routeIs('super-admin.laporan.*') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-bar-graph"></i>
                <span>Laporan</span>
            </a>

            <div class="sidebar-section-title">Website & Sistem</div>

            <a href="{{ route('super-admin.whatsapp-fonnte.index') }}" class="{{ request()->routeIs('super-admin.whatsapp-fonnte.*') ? 'active' : '' }}">
                <i class="bi bi-whatsapp"></i>
                <span>WhatsApp Fonnte</span>
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

            <a href="{{ route('admin.rekap-absensi-murid.index') }}" class="{{ request()->routeIs('admin.rekap-absensi-murid.*') ? 'active' : '' }}">
                <i class="bi bi-person-check"></i>
                <span>Rekap Absensi Murid</span>
            </a>

            <a href="{{ route('admin.nilai.index') }}" class="{{ request()->routeIs('admin.nilai.*') ? 'active' : '' }}">
                <i class="bi bi-journal-text"></i>
                <span>Nilai</span>
            </a>

            <a href="{{ route('admin.laporan.index') }}" class="{{ request()->routeIs('admin.laporan.*') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-bar-graph"></i>
                <span>Laporan</span>
            </a>

            <div class="sidebar-section-title">Website & Sistem</div>

            <a href="{{ route('admin.whatsapp-fonnte.index') }}" class="{{ request()->routeIs('admin.whatsapp-fonnte.*') ? 'active' : '' }}">
                <i class="bi bi-whatsapp"></i>
                <span>WhatsApp Fonnte</span>
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

            <a href="{{ route('guru.jadwal-mengajar.index') }}" class="{{ request()->routeIs('guru.jadwal-mengajar.*') ? 'active' : '' }}">
                <i class="bi bi-calendar-check"></i>
                <span>Jadwal Mengajar</span>
            </a>

            <a href="{{ route('guru.absensi-murid.index') }}" class="{{ request()->routeIs('guru.absensi-murid.*') ? 'active' : '' }}">
                <i class="bi bi-clipboard-check"></i>
                <span>Absen Murid</span>
            </a>

            <a href="{{ route('guru.rekap-absen-murid.index') }}" class="{{ request()->routeIs('guru.rekap-absen-murid.*') ? 'active' : '' }}">
                <i class="bi bi-card-checklist"></i>
                <span>Rekap Absen Murid</span>
            </a>

            <a href="{{ route('guru.input-nilai.index') }}" class="{{ request()->routeIs('guru.input-nilai.*') ? 'active' : '' }}">
                <i class="bi bi-journal-plus"></i>
                <span>Input Nilai</span>
            </a>

            <a href="{{ route('guru.rekap-nilai.index') }}" class="{{ request()->routeIs('guru.rekap-nilai.*') ? 'active' : '' }}">
                <i class="bi bi-journal-text"></i>
                <span>Rekap Nilai</span>
            </a>

            <div class="sidebar-section-title">Akun</div>

            <a href="{{ route('guru.password.edit') }}" class="{{ request()->routeIs('guru.password.*') ? 'active' : '' }}">
                <i class="bi bi-key"></i>
                <span>Ganti Password</span>
            </a>
        @endif

    </nav>
</aside>
