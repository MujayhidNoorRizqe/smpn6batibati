{{-- penjelasan: File ini adalah halaman dashboard untuk guru. --}}
{{-- penjelasan: File ini dipanggil oleh route /guru/dashboard. --}}
{{-- penjelasan: Halaman ini hanya bisa diakses user dengan role guru. --}}

@extends('admin.layouts.app')

@section('title', 'Dashboard Guru')

@section('content')

    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">

                    <h4 class="fw-bold mb-1">Selamat Datang, {{ auth()->user()->name }}</h4>

                    <p class="text-muted mb-0">
                        Anda login sebagai Guru. Anda dapat melakukan absensi, pengajuan dinas, absen murid, dan input nilai.
                    </p>

                </div>
            </div>
        </div>
    </div>

    {{-- penjelasan: Card ini masih tampilan awal. --}}
    {{-- penjelasan: Nanti status absen dan jadwal mengajar akan diambil dari database. --}}
    <div class="row g-3">

        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <small class="text-muted">Status Absen Hari Ini</small>
                    <h5 class="fw-bold mb-0">Belum Absen</h5>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <small class="text-muted">Jadwal Mengajar Hari Ini</small>
                    <h5 class="fw-bold mb-0">0 Jadwal</h5>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <small class="text-muted">Pengajuan Dinas</small>
                    <h5 class="fw-bold mb-0">0 Menunggu</h5>
                </div>
            </div>
        </div>

    </div>

@endsection
