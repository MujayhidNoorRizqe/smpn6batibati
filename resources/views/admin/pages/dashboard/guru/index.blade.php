{{-- penjelasan: File ini adalah halaman dashboard utama untuk guru. --}}
{{-- penjelasan: File ini dipanggil oleh route /guru/dashboard. --}}
{{-- penjelasan: Halaman ini hanya bisa dibuka oleh user login dengan role guru. --}}
{{-- penjelasan: Data pada halaman ini masih statis sementara. --}}

@extends('admin.layouts.app')

@section('title', 'Dashboard Guru')

@section('content')

    {{-- penjelasan: Welcome card untuk guru. --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">

                    <div>
                        <h4 class="fw-bold mb-1">Selamat Datang, {{ auth()->user()->name }}</h4>

                        <p class="text-muted mb-0">
                            Anda login sebagai Guru. Anda dapat melakukan absensi, pengajuan dinas, absen murid, dan input nilai.
                        </p>
                    </div>

                    <div>
                        <span class="badge bg-success px-3 py-2">
                            <i class="bi bi-person-workspace me-1"></i>
                            Guru
                        </span>
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- penjelasan: Card statistik khusus guru. --}}
    <div class="row g-3 mb-4">

        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <small class="text-muted">Status Absen Hari Ini</small>
                    <h5 class="fw-bold mb-1">Belum Absen</h5>
                    <span class="badge bg-secondary-subtle text-secondary">Menunggu absen masuk</span>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <small class="text-muted">Jadwal Hari Ini</small>
                    <h5 class="fw-bold mb-1">0 Jadwal</h5>
                    <span class="badge bg-primary-subtle text-primary">Berdasarkan jadwal aktif</span>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <small class="text-muted">Pengajuan Dinas</small>
                    <h5 class="fw-bold mb-1">0 Menunggu</h5>
                    <span class="badge bg-warning-subtle text-warning">Status pengajuan terbaru</span>
                </div>
            </div>
        </div>

    </div>

    {{-- penjelasan: Panel akademik guru. --}}
    <div class="row g-3">

        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0">
                    <h6 class="fw-bold mb-0">Jadwal Mengajar Hari Ini</h6>
                    <small class="text-muted">Jadwal akan diambil dari tabel jadwal_pelajarans</small>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Jam</th>
                                    <th>Kelas</th>
                                    <th>Mata Pelajaran</th>
                                    <th>Status Absen Murid</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">
                                        Belum ada jadwal mengajar hari ini.
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
                    <h6 class="fw-bold mb-0">Absensi Saya</h6>
                    <small class="text-muted">Status absensi pegawai hari ini</small>
                </div>

                <div class="card-body">

                    <div class="d-flex justify-content-between border-bottom pb-2 mb-2">
                        <span class="text-muted">Jam Masuk</span>
                        <span class="fw-semibold">-</span>
                    </div>

                    <div class="d-flex justify-content-between border-bottom pb-2 mb-2">
                        <span class="text-muted">Jam Pulang</span>
                        <span class="fw-semibold">-</span>
                    </div>

                    <div class="d-flex justify-content-between border-bottom pb-2 mb-2">
                        <span class="text-muted">Status</span>
                        <span class="badge bg-secondary">Belum Absen</span>
                    </div>

                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Lokasi</span>
                        <span class="fw-semibold">Belum dicek</span>
                    </div>

                </div>
            </div>
        </div>

    </div>

    {{-- penjelasan: Panel tambahan guru. --}}
    <div class="row g-3 mt-1">

        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <h6 class="fw-bold mb-0">Kelas yang Perlu Diabsen</h6>
                    <small class="text-muted">Daftar kelas berdasarkan jadwal guru</small>
                </div>
                <div class="card-body text-muted">
                    Belum ada kelas yang perlu diabsen.
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <h6 class="fw-bold mb-0">Nilai Belum Lengkap</h6>
                    <small class="text-muted">Rekap input nilai semester aktif</small>
                </div>
                <div class="card-body text-muted">
                    Belum ada data nilai yang perlu dilengkapi.
                </div>
            </div>
        </div>

    </div>

@endsection
