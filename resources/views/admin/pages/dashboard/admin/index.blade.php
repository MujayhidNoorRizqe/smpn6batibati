{{-- penjelasan: File ini adalah halaman dashboard awal untuk admin. --}}
{{-- penjelasan: File ini dipanggil oleh route /admin/dashboard. --}}
{{-- penjelasan: Halaman ini hanya bisa dibuka jika user login dan memiliki role admin. --}}

{{-- penjelasan: Baris ini memakai layout utama dashboard. --}}
@extends('admin.layouts.app')

{{-- penjelasan: Section title mengisi judul halaman pada layout. --}}
@section('title', 'Dashboard Admin')

{{-- penjelasan: Section content adalah isi utama halaman dashboard admin. --}}
@section('content')

    <div class="row">
        <div class="col-12">

            {{-- penjelasan: Card ini adalah tampilan awal dashboard admin. --}}
            <div class="card border-0 shadow-sm">
                <div class="card-body">

                    <h3 class="fw-bold mb-2">Dashboard Admin</h3>

                    {{-- penjelasan: auth()->user()->name menampilkan nama admin yang sedang login. --}}
                    <p class="text-muted mb-0">
                        Selamat datang, {{ auth()->user()->name }}.
                        Anda login sebagai Admin.
                    </p>

                </div>
            </div>

        </div>
    </div>

@endsection
