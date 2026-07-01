{{-- penjelasan: File ini adalah halaman dashboard utama untuk super admin. --}}
{{-- penjelasan: File ini dipanggil oleh route /super-admin/dashboard di routes/web.php. --}}
{{-- penjelasan: Halaman ini hanya bisa dibuka oleh user login dengan role super_admin melalui RoleMiddleware. --}}
{{-- penjelasan: Data statistik pada halaman ini masih statis sementara, nanti akan dihubungkan ke database. --}}

@extends('admin.layouts.app')

@section('title', 'Dashboard Super Admin')

@section('content')

    {{-- penjelasan: Welcome card adalah kartu sambutan untuk user yang sedang login. --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">

                    <div>
                        {{-- penjelasan: auth()->user()->name mengambil nama user yang sedang login dari tabel users. --}}
                        <h4 class="fw-bold mb-1">Selamat Datang, {{ auth()->user()->name }}</h4>

                        <p class="text-muted mb-0">
                            Anda login sebagai Super Admin. Anda memiliki akses penuh terhadap sistem.
                        </p>
                    </div>

                    <div class="text-md-end">
                        <span class="badge bg-primary px-3 py-2">
                            <i class="bi bi-shield-check me-1"></i>
                            Super Admin
                        </span>
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- penjelasan: Bagian ini berisi card statistik utama. --}}
    {{-- penjelasan: Angka masih 0 karena belum diambil dari database. --}}
    <div class="row g-3 mb-4">

        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted">Total User</small>
                        <h3 class="fw-bold mb-0">0</h3>
                    </div>
                    <div class="rounded-circle bg-primary bg-opacity-10 text-primary p-3">
                        <i class="bi bi-people fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted">Total Pegawai</small>
                        <h3 class="fw-bold mb-0">0</h3>
                    </div>
                    <div class="rounded-circle bg-success bg-opacity-10 text-success p-3">
                        <i class="bi bi-person-badge fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted">Total Murid</small>
                        <h3 class="fw-bold mb-0">0</h3>
                    </div>
                    <div class="rounded-circle bg-warning bg-opacity-10 text-warning p-3">
                        <i class="bi bi-mortarboard fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted">Pengajuan Dinas</small>
                        <h3 class="fw-bold mb-0">0</h3>
                    </div>
                    <div class="rounded-circle bg-info bg-opacity-10 text-info p-3">
                        <i class="bi bi-briefcase fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- penjelasan: Bagian kedua berisi statistik tambahan. --}}
    <div class="row g-3 mb-4">

        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <small class="text-muted">Absensi Pegawai Hari Ini</small>
                    <h4 class="fw-bold mb-1">0</h4>
                    <span class="badge bg-success-subtle text-success">Hadir / Terlambat / Dinas</span>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <small class="text-muted">Nilai Terinput</small>
                    <h4 class="fw-bold mb-1">0</h4>
                    <span class="badge bg-primary-subtle text-primary">Semester Aktif</span>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <small class="text-muted">WhatsApp Terkirim</small>
                    <h4 class="fw-bold mb-1">0</h4>
                    <span class="badge bg-info-subtle text-info">Fonnte Log</span>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <small class="text-muted">Status Sistem</small>
                    <h4 class="fw-bold mb-1">Aktif</h4>
                    <span class="badge bg-success-subtle text-success">Local Development</span>
                </div>
            </div>
        </div>

    </div>

    {{-- penjelasan: Bagian panel utama dashboard. --}}
    <div class="row g-3">

        {{-- penjelasan: Panel kiri berisi tabel pengajuan dinas terbaru. --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="fw-bold mb-0">Pengajuan Dinas Terbaru</h6>
                        <small class="text-muted">Daftar pengajuan dinas yang perlu dipantau</small>
                    </div>
                    <span class="badge bg-warning-subtle text-warning">Menunggu</span>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Nama</th>
                                    <th>Tanggal</th>
                                    <th>Tujuan</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- penjelasan: Data masih contoh sementara. --}}
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">
                                        Belum ada pengajuan dinas terbaru.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- penjelasan: Panel kanan berisi ringkasan sistem. --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0">
                    <h6 class="fw-bold mb-0">Ringkasan Sistem</h6>
                    <small class="text-muted">Status konfigurasi utama</small>
                </div>

                <div class="card-body">

                    <div class="d-flex justify-content-between border-bottom pb-2 mb-2">
                        <span class="text-muted">Tahun Ajaran</span>
                        <span class="fw-semibold">Belum diset</span>
                    </div>

                    <div class="d-flex justify-content-between border-bottom pb-2 mb-2">
                        <span class="text-muted">Semester</span>
                        <span class="fw-semibold">Belum diset</span>
                    </div>

                    <div class="d-flex justify-content-between border-bottom pb-2 mb-2">
                        <span class="text-muted">Fonnte</span>
                        <span class="badge bg-secondary">Belum aktif</span>
                    </div>

                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Validasi Lokasi</span>
                        <span class="badge bg-secondary">Belum diset</span>
                    </div>

                </div>
            </div>
        </div>

    </div>

@endsection
