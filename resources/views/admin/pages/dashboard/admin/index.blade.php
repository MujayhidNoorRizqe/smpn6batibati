{{-- penjelasan: File ini adalah halaman dashboard untuk admin. --}}
{{-- penjelasan: File ini dipanggil oleh route /admin/dashboard. --}}
{{-- penjelasan: Halaman ini hanya bisa diakses user dengan role admin. --}}

@extends('admin.layouts.app')

@section('title', 'Dashboard Admin')

@section('content')

    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">

                    <h4 class="fw-bold mb-1">Selamat Datang, {{ auth()->user()->name }}</h4>

                    <p class="text-muted mb-0">
                        Anda login sebagai Admin. Anda dapat mengelola data akademik, absensi, laporan, dan konten website.
                    </p>

                </div>
            </div>
        </div>
    </div>

    {{-- penjelasan: Card statistik masih memakai angka sementara. --}}
    {{-- penjelasan: Nanti angka akan diambil dari database menggunakan controller. --}}
    <div class="row g-3">

        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <small class="text-muted">Total Pegawai</small>
                    <h3 class="fw-bold mb-0">0</h3>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <small class="text-muted">Total Murid</small>
                    <h3 class="fw-bold mb-0">0</h3>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <small class="text-muted">Absensi Hari Ini</small>
                    <h3 class="fw-bold mb-0">0</h3>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <small class="text-muted">Nilai Terinput</small>
                    <h3 class="fw-bold mb-0">0</h3>
                </div>
            </div>
        </div>

    </div>

@endsection
