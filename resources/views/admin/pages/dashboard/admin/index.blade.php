{{-- penjelasan: File ini adalah halaman dashboard utama untuk admin. --}}
{{-- penjelasan: File ini dipanggil oleh route /admin/dashboard. --}}
{{-- penjelasan: Halaman ini hanya bisa dibuka oleh user login dengan role admin. --}}
{{-- penjelasan: Data pada halaman ini masih statis sementara. --}}

@extends('admin.layouts.app')

@section('title', 'Dashboard Admin')

@section('content')

    {{-- penjelasan: Welcome card untuk admin. --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">

                    <div>
                        <h4 class="fw-bold mb-1">Selamat Datang, {{ auth()->user()->name }}</h4>

                        <p class="text-muted mb-0">
                            Anda login sebagai Admin. Anda dapat mengelola data akademik, absensi, laporan, dan konten website.
                        </p>
                    </div>

                    <div class="text-md-end">
                        <span class="badge bg-primary px-3 py-2">
                            <i class="bi bi-person-check me-1"></i>
                            Admin
                        </span>
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- penjelasan: Card statistik utama untuk admin. --}}
    <div class="row g-3 mb-4">

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
                        <small class="text-muted">Absensi Hari Ini</small>
                        <h3 class="fw-bold mb-0">0</h3>
                    </div>
                    <div class="rounded-circle bg-info bg-opacity-10 text-info p-3">
                        <i class="bi bi-fingerprint fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted">Dinas Menunggu</small>
                        <h3 class="fw-bold mb-0">0</h3>
                    </div>
                    <div class="rounded-circle bg-primary bg-opacity-10 text-primary p-3">
                        <i class="bi bi-briefcase fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- penjelasan: Panel informasi admin. --}}
    <div class="row g-3">

        <div class="col-lg-7">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0">
                    <h6 class="fw-bold mb-0">Monitoring Absensi Pegawai</h6>
                    <small class="text-muted">Ringkasan kehadiran pegawai hari ini</small>
                </div>

                <div class="card-body">
                    <div class="row g-3">

                        <div class="col-md-3">
                            <div class="border rounded-3 p-3 text-center">
                                <small class="text-muted">Hadir</small>
                                <h4 class="fw-bold text-success mb-0">0</h4>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="border rounded-3 p-3 text-center">
                                <small class="text-muted">Terlambat</small>
                                <h4 class="fw-bold text-warning mb-0">0</h4>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="border rounded-3 p-3 text-center">
                                <small class="text-muted">Dinas</small>
                                <h4 class="fw-bold text-primary mb-0">0</h4>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="border rounded-3 p-3 text-center">
                                <small class="text-muted">Belum Absen</small>
                                <h4 class="fw-bold text-danger mb-0">0</h4>
                            </div>
                        </div>

                    </div>

                    <hr>

                    <p class="text-muted mb-0">
                        Data monitoring ini nanti otomatis diambil dari tabel absensi pegawai.
                    </p>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0">
                    <h6 class="fw-bold mb-0">Tugas Admin Hari Ini</h6>
                    <small class="text-muted">Daftar pekerjaan yang perlu dipantau</small>
                </div>

                <div class="card-body">

                    <div class="d-flex align-items-center gap-3 border-bottom pb-3 mb-3">
                        <div class="rounded-circle bg-warning bg-opacity-10 text-warning p-2">
                            <i class="bi bi-briefcase"></i>
                        </div>
                        <div>
                            <div class="fw-semibold">Cek Pengajuan Dinas</div>
                            <small class="text-muted">Persetujuan dinas guru dan staff</small>
                        </div>
                    </div>

                    <div class="d-flex align-items-center gap-3 border-bottom pb-3 mb-3">
                        <div class="rounded-circle bg-primary bg-opacity-10 text-primary p-2">
                            <i class="bi bi-calendar-check"></i>
                        </div>
                        <div>
                            <div class="fw-semibold">Pantau Jadwal Pelajaran</div>
                            <small class="text-muted">Pastikan jadwal aktif sudah benar</small>
                        </div>
                    </div>

                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-success bg-opacity-10 text-success p-2">
                            <i class="bi bi-whatsapp"></i>
                        </div>
                        <div>
                            <div class="fw-semibold">Kirim Laporan Wali</div>
                            <small class="text-muted">Absen dan nilai melalui Fonnte</small>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>

@endsection
