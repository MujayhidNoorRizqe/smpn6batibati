{{-- penjelasan: File ini adalah halaman dashboard awal untuk guru. --}}
{{-- penjelasan: File ini dipanggil oleh route /guru/dashboard. --}}
{{-- penjelasan: Halaman ini hanya bisa dibuka jika user login dan memiliki role guru. --}}

{{-- penjelasan: Baris ini memakai layout utama dashboard. --}}
@extends('admin.layouts.app')

{{-- penjelasan: Section title mengisi judul halaman pada layout. --}}
@section('title', 'Dashboard Guru')

{{-- penjelasan: Section content adalah isi utama halaman dashboard guru. --}}
@section('content')

    <div class="row">
        <div class="col-12">

            {{-- penjelasan: Card ini adalah tampilan awal dashboard guru. --}}
            <div class="card border-0 shadow-sm">
                <div class="card-body">

                    <h3 class="fw-bold mb-2">Dashboard Guru</h3>

                    {{-- penjelasan: auth()->user()->name menampilkan nama guru yang sedang login. --}}
                    <p class="text-muted mb-0">
                        Selamat datang, {{ auth()->user()->name }}.
                        Anda login sebagai Guru.
                    </p>

                </div>
            </div>

        </div>
    </div>

@endsection
