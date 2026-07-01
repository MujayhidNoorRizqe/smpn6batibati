{{-- penjelasan: File ini adalah halaman daftar wali murid. --}}
{{-- penjelasan: File ini dipanggil oleh WaliMuridController method index(). --}}
{{-- penjelasan: Halaman ini bisa diakses oleh Super Admin dan Admin. --}}
{{-- penjelasan: Data $waliMurids dikirim dari controller menggunakan compact('waliMurids', 'routePrefix'). --}}
{{-- penjelasan: routePrefix dipakai agar link bisa otomatis menjadi super-admin atau admin sesuai role login. --}}

@extends('admin.layouts.app')

@section('title', 'Data Wali Murid')

@section('content')

    {{-- penjelasan: Bagian header halaman berisi judul, deskripsi, dan tombol tambah wali murid. --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                    <div>
                        <h4 class="fw-bold mb-1">Data Wali Murid</h4>
                        <p class="text-muted mb-0">
                            Kelola data orang tua atau wali murid untuk kebutuhan data murid dan notifikasi WhatsApp.
                        </p>
                    </div>

                    {{-- penjelasan: Tombol ini mengarah ke halaman tambah wali murid. --}}
                    {{-- penjelasan: routePrefix membuat route otomatis sesuai role login. --}}
                    <a href="{{ route($routePrefix . '.wali-murid.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i>
                        Tambah Wali Murid
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- penjelasan: Alert sukses tampil setelah data berhasil ditambah, diedit, atau status diubah. --}}
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    {{-- penjelasan: Card ini berisi form pencarian dan filter wali murid. --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">

            {{-- penjelasan: Form memakai method GET agar filter tampil di URL. --}}
            {{-- penjelasan: Filter ini diproses oleh WaliMuridController method index(). --}}
            <form action="{{ route($routePrefix . '.wali-murid.index') }}" method="GET" class="row g-3">

                <div class="col-md-4">
                    <label class="form-label">Cari Wali Murid</label>

                    {{-- penjelasan: Input search digunakan untuk mencari berdasarkan nama, NIK, nomor HP, atau nomor WhatsApp. --}}
                    {{-- penjelasan: request('search') membuat nilai pencarian tetap muncul setelah filter dijalankan. --}}
                    <input
                        type="text"
                        name="search"
                        class="form-control"
                        placeholder="Cari nama, NIK, HP, atau WhatsApp"
                        value="{{ request('search') }}"
                    >
                </div>

                <div class="col-md-3">
                    <label class="form-label">Hubungan</label>

                    {{-- penjelasan: Filter hubungan dipakai untuk menampilkan data ayah, ibu, atau wali saja. --}}
                    <select name="hubungan" class="form-select">
                        <option value="">Semua Hubungan</option>
                        <option value="ayah" {{ request('hubungan') === 'ayah' ? 'selected' : '' }}>Ayah</option>
                        <option value="ibu" {{ request('hubungan') === 'ibu' ? 'selected' : '' }}>Ibu</option>
                        <option value="wali" {{ request('hubungan') === 'wali' ? 'selected' : '' }}>Wali</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Status</label>

                    {{-- penjelasan: Filter status dipakai untuk menampilkan wali murid aktif atau nonaktif. --}}
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="aktif" {{ request('status') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="nonaktif" {{ request('status') === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>

                <div class="col-md-2 d-flex align-items-end">
                    {{-- penjelasan: Tombol ini mengirim parameter filter ke controller. --}}
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search me-1"></i>
                        Filter
                    </button>
                </div>

            </form>
        </div>
    </div>

    {{-- penjelasan: Card ini berisi tabel daftar wali murid. --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
            <div>
                <h6 class="fw-bold mb-0">Daftar Wali Murid</h6>
                <small class="text-muted">Data orang tua/wali yang terdaftar pada sistem</small>
            </div>

            {{-- penjelasan: Badge ini menampilkan jumlah data yang sedang tampil pada halaman pagination saat ini. --}}
            <span class="badge bg-primary-subtle text-primary">
                {{ $waliMurids->count() }} data tampil
            </span>
        </div>

        <div class="card-body">

            {{-- penjelasan: table-responsive membuat tabel bisa discroll horizontal pada layar kecil. --}}
            <div class="table-responsive">
                <table class="table table-hover align-middle admin-table">
                    <thead class="table-light">
                        <tr>
                            <th>Nama Wali</th>
                            <th>Hubungan</th>
                            <th>No. WhatsApp</th>
                            <th>Pekerjaan</th>
                            <th>Status</th>
                            <th class="text-end table-action-column">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        {{-- penjelasan: @forelse menampilkan data jika ada, dan pesan kosong jika data belum tersedia. --}}
                        @forelse ($waliMurids as $waliMurid)
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $waliMurid->nama_wali }}</div>
                                    <small class="text-muted">NIK: {{ $waliMurid->nik ?? '-' }}</small>
                                </td>

                                <td>
                                    <span class="badge bg-primary-subtle text-primary">
                                        {{ ucfirst($waliMurid->hubungan) }}
                                    </span>
                                </td>

                                <td>
                                    @if ($waliMurid->no_whatsapp)
                                        <span class="badge bg-success-subtle text-success">
                                            {{ $waliMurid->no_whatsapp }}
                                        </span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary">
                                            Belum ada
                                        </span>
                                    @endif
                                </td>

                                <td>{{ $waliMurid->pekerjaan ?? '-' }}</td>

                                <td>
                                    <span class="badge {{ $waliMurid->status === 'aktif' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }}">
                                        {{ ucfirst($waliMurid->status) }}
                                    </span>
                                </td>

                                <td class="text-end">
                                    <div class="action-buttons">

                                        {{-- penjelasan: Tombol Detail membuka halaman detail wali murid. --}}
                                        <a href="{{ route($routePrefix . '.wali-murid.show', $waliMurid) }}" class="btn btn-sm btn-outline-secondary action-btn">
                                            <i class="bi bi-eye"></i>
                                            <span>Detail</span>
                                        </a>

                                        {{-- penjelasan: Tombol Edit membuka halaman edit wali murid. --}}
                                        <a href="{{ route($routePrefix . '.wali-murid.edit', $waliMurid) }}" class="btn btn-sm btn-outline-primary action-btn">
                                            <i class="bi bi-pencil-square"></i>
                                            <span>Edit</span>
                                        </a>

                                        {{-- penjelasan: Form ini digunakan untuk mengubah status aktif/nonaktif wali murid. --}}
                                        {{-- penjelasan: Method PATCH dipakai karena hanya mengubah satu bagian data yaitu status. --}}
                                        <form action="{{ route($routePrefix . '.wali-murid.toggle-status', $waliMurid) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')

                                            <button
                                                type="submit"
                                                class="btn btn-sm action-btn {{ $waliMurid->status === 'aktif' ? 'btn-outline-danger' : 'btn-outline-success' }}"
                                                onclick="return confirm('Yakin ingin mengubah status wali murid ini?')"
                                            >
                                                @if ($waliMurid->status === 'aktif')
                                                    <i class="bi bi-x-circle"></i>
                                                    <span>Nonaktif</span>
                                                @else
                                                    <i class="bi bi-check-circle"></i>
                                                    <span>Aktifkan</span>
                                                @endif
                                            </button>
                                        </form>

                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    Data wali murid belum tersedia.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- penjelasan: Pagination dipakai jika data wali murid lebih dari 10. --}}
            <div class="mt-3">
                {{ $waliMurids->links() }}
            </div>

        </div>
    </div>

@endsection
