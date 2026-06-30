{{-- penjelasan: File ini adalah halaman dashboard awal untuk staff. --}}
{{-- penjelasan: File ini dipanggil oleh route /staff/dashboard. --}}
{{-- penjelasan: Halaman ini hanya bisa dibuka jika user login dan memiliki role staff. --}}

{{-- penjelasan: Baris ini memakai layout utama dashboard. --}}
@extends('admin.layouts.app')

{{-- penjelasan: Section title mengisi judul halaman pada layout. --}}
@section('title', 'Dashboard Staff')

{{-- penjelasan: Section content adalah isi utama halaman dashboard staff. --}}
@section('content')

    <div class="row">
        <div class="col-12">

            {{-- penjelasan: Card ini adalah tampilan awal dashboard staff. --}}
            <div class="card border-0 shadow-sm">
                <div class="card-body">

                    <h3 class="fw-bold mb-2">Dashboard Staff</h3>

                    {{-- penjelasan: auth()->user()->name menampilkan nama staff yang sedang login. --}}
                    <p class="text-muted mb-0">
                        Selamat datang, {{ auth()->user()->name }}.
                        Anda login sebagai Staff.
                    </p>

                </div>
            </div>

        </div>
    </div>

@endsection
