{{-- penjelasan: File ini adalah halaman dashboard utama untuk staff. --}}
{{-- penjelasan: File ini dipanggil oleh route /staff/dashboard. --}}
{{-- penjelasan: Halaman ini hanya bisa dibuka oleh user login dengan role staff. --}}
{{-- penjelasan: Data pada halaman ini masih statis sementara. --}}

@extends('admin.layouts.app')

@section('title', 'Dashboard Staff')

@section('content')

    {{-- penjelasan: Welcome card untuk staff. --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">

                    <div>
                        <h4 class="fw-bold mb-1">Selamat Datang, {{ auth()->user()->name }}</h4>

                        <p class="text-muted mb-0">
                            Anda login sebagai Staff. Anda dapat melakukan absensi dan pengajuan dinas.
                        </p>
                    </div>

                    <div>
                        <span class="badge bg-info px-3 py-2">
                            <i class="bi bi-person-badge me-1"></i>
                            Staff
                        </span>
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- penjelasan: Card statistik khusus staff. --}}
    <div class="row g-3 mb-4">

        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <small class="text-muted">Status Absen</small>
                    <h5 class="fw-bold mb-1">Belum Absen</h5>
                    <span class="badge bg-secondary-subtle text-secondary">Hari ini</span>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <small class="text-muted">Jam Masuk</small>
                    <h5 class="fw-bold mb-1">-</h5>
                    <span class="badge bg-primary-subtle text-primary">Absen masuk</span>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <small class="text-muted">Jam Pulang</small>
                    <h5 class="fw-bold mb-1">-</h5>
                    <span class="badge bg-success-subtle text-success">Absen pulang</span>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <small class="text-muted">Pengajuan Dinas</small>
                    <h5 class="fw-bold mb-1">0</h5>
                    <span class="badge bg-warning-subtle text-warning">Menunggu</span>
                </div>
            </div>
        </div>

    </div>

    {{-- penjelasan: Panel utama staff. --}}
    <div class="row g-3">

        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0">
                    <h6 class="fw-bold mb-0">Riwayat Absensi Terbaru</h6>
                    <small class="text-muted">Riwayat akan diambil dari tabel absensi_pegawais</small>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Jam Masuk</th>
                                    <th>Jam Pulang</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">
                                        Belum ada riwayat absensi.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0">
                    <h6 class="fw-bold mb-0">Informasi Absensi</h6>
                    <small class="text-muted">Aturan absensi pegawai</small>
                </div>

                <div class="card-body">

                    <div class="d-flex justify-content-between border-bottom pb-2 mb-2">
                        <span class="text-muted">Jam Masuk</span>
                        <span class="fw-semibold">Belum diset</span>
                    </div>

                    <div class="d-flex justify-content-between border-bottom pb-2 mb-2">
                        <span class="text-muted">Batas Terlambat</span>
                        <span class="fw-semibold">Belum diset</span>
                    </div>

                    <div class="d-flex justify-content-between border-bottom pb-2 mb-2">
                        <span class="text-muted">Validasi Lokasi</span>
                        <span class="badge bg-secondary">Belum diset</span>
                    </div>

                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Pengajuan Dinas</span>
                        <span class="badge bg-primary">Tersedia</span>
                    </div>

                </div>
            </div>
        </div>

    </div>

@endsection
