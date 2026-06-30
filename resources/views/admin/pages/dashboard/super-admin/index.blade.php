{{-- penjelasan: File ini adalah halaman dashboard awal untuk super admin. --}}
{{-- penjelasan: File ini dipanggil oleh route /super-admin/dashboard. --}}
{{-- penjelasan: Halaman ini hanya bisa dibuka jika user login dan memiliki role super_admin. --}}

{{-- penjelasan: Baris ini memakai layout utama dashboard dari resources/views/admin/layouts/app.blade.php. --}}
@extends('admin.layouts.app')

{{-- penjelasan: Section title mengisi @yield('title') di layout dashboard. --}}
@section('title', 'Dashboard Super Admin')

{{-- penjelasan: Section content mengisi @yield('content') di layout dashboard. --}}
@section('content')

    <div class="row">
        <div class="col-12">

            {{-- penjelasan: Card ini adalah tampilan awal dashboard super admin. --}}
            <div class="card border-0 shadow-sm">
                <div class="card-body">

                    <h3 class="fw-bold mb-2">Dashboard Super Admin</h3>

                    {{-- penjelasan: auth()->user()->name menampilkan nama user yang sedang login. --}}
                    <p class="text-muted mb-0">
                        Selamat datang, {{ auth()->user()->name }}.
                        Anda login sebagai Super Admin.
                    </p>

                </div>
            </div>

        </div>
    </div>

@endsection
