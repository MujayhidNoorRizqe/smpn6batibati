{{-- penjelasan: File ini adalah halaman dashboard untuk super admin. --}}
{{-- penjelasan: File ini dipanggil oleh route /super-admin/dashboard. --}}
{{-- penjelasan: Halaman ini hanya bisa diakses user dengan role super_admin. --}}

{{-- penjelasan: Halaman ini memakai layout utama dashboard. --}}
@extends('admin.layouts.app')

{{-- penjelasan: Section title akan mengisi judul halaman di layout dan topbar. --}}
@section('title', 'Dashboard Super Admin')

{{-- penjelasan: Section content adalah isi utama halaman dashboard. --}}
@section('content')

    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">

                    {{-- penjelasan: auth()->user()->name digunakan untuk menampilkan nama user yang sedang login. --}}
                    <h4 class="fw-bold mb-1">Selamat Datang, {{ auth()->user()->name }}</h4>

                    <p class="text-muted mb-0">
                        Anda login sebagai Super Admin. Anda memiliki akses penuh terhadap sistem.
                    </p>

                </div>
            </div>
        </div>
    </div>

    {{-- penjelasan: Row ini menampilkan card ringkasan awal. --}}
    {{-- penjelasan: Angka masih statis sementara, nanti akan dihubungkan ke database. --}}
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
                    <small class="text-muted">Pengajuan Dinas</small>
                    <h3 class="fw-bold mb-0">0</h3>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <small class="text-muted">WhatsApp Terkirim</small>
                    <h3 class="fw-bold mb-0">0</h3>
                </div>
            </div>
        </div>

    </div>

@endsection
