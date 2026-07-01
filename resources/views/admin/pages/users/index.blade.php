{{-- penjelasan: File ini adalah halaman daftar user. --}}
{{-- penjelasan: File ini dipanggil oleh UserController method index(). --}}
{{-- penjelasan: Halaman ini hanya bisa diakses oleh Super Admin melalui route /super-admin/users. --}}
{{-- penjelasan: Halaman ini berfungsi untuk menampilkan data user, filter user, tambah user, edit user, reset password, dan aktif/nonaktif akun. --}}
{{-- penjelasan: File ini memakai layout dashboard utama dari admin.layouts.app. --}}
{{-- penjelasan: Data $users dikirim dari UserController method index() menggunakan compact('users'). --}}
{{-- penjelasan: Tombol aksi pada tabel memakai class action-buttons dan action-btn yang style-nya diatur di public/assets/admin/css/admin.css. --}}

@extends('admin.layouts.app')

@section('title', 'Manajemen User')

@section('content')

    {{-- penjelasan: Bagian ini adalah header halaman Manajemen User. --}}
    {{-- penjelasan: Di bagian ini ada judul halaman, deskripsi singkat, dan tombol Tambah User. --}}
    <div class="row mb-4">
        <div class="col-12">

            <div class="card border-0 shadow-sm">
                <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">

                    <div>
                        <h4 class="fw-bold mb-1">Manajemen User</h4>

                        <p class="text-muted mb-0">
                            Kelola akun login admin, guru, dan staff.
                        </p>
                    </div>

                    {{-- penjelasan: Tombol ini mengarah ke halaman form tambah user. --}}
                    {{-- penjelasan: Route super-admin.users.create memanggil UserController method create(). --}}
                    <a href="{{ route('super-admin.users.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i>
                        Tambah User
                    </a>

                </div>
            </div>

        </div>
    </div>

    {{-- penjelasan: Alert success menampilkan pesan berhasil setelah user ditambah, diedit, password direset, atau status diubah. --}}
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    {{-- penjelasan: Alert error menampilkan pesan gagal, misalnya saat super admin mencoba menonaktifkan akunnya sendiri. --}}
    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    {{-- penjelasan: Card ini berisi form filter dan pencarian data user. --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">

            {{-- penjelasan: Form filter memakai method GET agar parameter filter tampil di URL. --}}
            {{-- penjelasan: Route super-admin.users.index memanggil UserController method index(). --}}
            {{-- penjelasan: Parameter search, role, dan status dibaca oleh controller untuk memfilter data user. --}}
            <form action="{{ route('super-admin.users.index') }}" method="GET" class="row g-3">

                <div class="col-md-4">
                    <label class="form-label">Cari User</label>

                    {{-- penjelasan: Input search digunakan untuk mencari user berdasarkan nama atau email. --}}
                    {{-- penjelasan: request('search') menjaga nilai pencarian tetap tampil setelah tombol Filter ditekan. --}}
                    <input
                        type="text"
                        name="search"
                        class="form-control"
                        placeholder="Cari nama atau email"
                        value="{{ request('search') }}"
                    >
                </div>

                <div class="col-md-3">
                    <label class="form-label">Role</label>

                    {{-- penjelasan: Select role digunakan untuk menampilkan user berdasarkan role tertentu. --}}
                    {{-- penjelasan: Pilihan role mengikuti role yang dipakai pada tabel users. --}}
                    <select name="role" class="form-select">
                        <option value="">Semua Role</option>
                        <option value="super_admin" {{ request('role') === 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                        <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="guru" {{ request('role') === 'guru' ? 'selected' : '' }}>Guru</option>
                        <option value="staff" {{ request('role') === 'staff' ? 'selected' : '' }}>Staff</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Status</label>

                    {{-- penjelasan: Select status digunakan untuk memfilter akun aktif dan nonaktif. --}}
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="aktif" {{ request('status') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="nonaktif" {{ request('status') === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>

                <div class="col-md-2 d-flex align-items-end">
                    {{-- penjelasan: Tombol ini mengirim form filter ke UserController method index(). --}}
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search me-1"></i>
                        Filter
                    </button>
                </div>

            </form>

        </div>
    </div>

    {{-- penjelasan: Card ini berisi tabel daftar user. --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
            <div>
                <h6 class="fw-bold mb-0">Daftar User</h6>
                <small class="text-muted">Data akun yang terdaftar pada sistem</small>
            </div>

            {{-- penjelasan: Badge ini menampilkan jumlah user pada halaman pagination saat ini. --}}
            <span class="badge bg-primary-subtle text-primary">
                {{ $users->count() }} data tampil
            </span>
        </div>

        <div class="card-body">

            {{-- penjelasan: table-responsive membuat tabel tetap bisa discroll horizontal jika layar kecil. --}}
            <div class="table-responsive">
                <table class="table table-hover align-middle admin-table">
                    <thead class="table-light">
                        <tr>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Login Terakhir</th>

                            {{-- penjelasan: Kolom aksi diberi class table-action-column agar lebarnya lebih stabil. --}}
                            <th class="text-end table-action-column">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        {{-- penjelasan: @forelse digunakan untuk menampilkan data jika ada, dan pesan kosong jika data tidak ada. --}}
                        @forelse ($users as $user)
                            <tr>
                                {{-- penjelasan: Menampilkan nama user dari tabel users kolom name. --}}
                                <td class="fw-semibold">
                                    {{ $user->name }}
                                </td>

                                {{-- penjelasan: Menampilkan email user dari tabel users kolom email. --}}
                                <td>
                                    {{ $user->email }}
                                </td>

                                <td>
                                    {{-- penjelasan: Role dari database seperti super_admin diubah tampilannya menjadi Super Admin. --}}
                                    <span class="badge bg-primary-subtle text-primary">
                                        {{ ucwords(str_replace('_', ' ', $user->role)) }}
                                    </span>
                                </td>

                                <td>
                                    {{-- penjelasan: Jika status aktif, badge berwarna hijau. --}}
                                    {{-- penjelasan: Jika status nonaktif, badge berwarna merah. --}}
                                    <span class="badge {{ $user->status === 'aktif' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }}">
                                        {{ ucfirst($user->status) }}
                                    </span>
                                </td>

                                <td>
                                    {{-- penjelasan: last_login_at menampilkan waktu login terakhir user. --}}
                                    {{-- penjelasan: Jika user belum pernah login, sistem menampilkan tanda strip. --}}
                                    {{ $user->last_login_at ? $user->last_login_at->format('d-m-Y H:i') : '-' }}
                                </td>

                                {{-- penjelasan: Bagian ini berisi tombol aksi user. --}}
                                {{-- penjelasan: class action-buttons dibuat agar tombol lebih rapi, punya jarak, dan tidak saling menempel. --}}
                                <td class="text-end">
                                    <div class="action-buttons">

                                        {{-- penjelasan: Tombol Edit mengarah ke form edit user. --}}
                                        {{-- penjelasan: Route super-admin.users.edit memanggil UserController method edit(). --}}
                                        <a
                                            href="{{ route('super-admin.users.edit', $user) }}"
                                            class="btn btn-sm btn-outline-primary action-btn"
                                        >
                                            <i class="bi bi-pencil-square"></i>
                                            <span>Edit</span>
                                        </a>

                                        {{-- penjelasan: Tombol Reset mengarah ke halaman reset password. --}}
                                        {{-- penjelasan: Route super-admin.users.reset-password memanggil UserController method resetPassword(). --}}
                                        <a
                                            href="{{ route('super-admin.users.reset-password', $user) }}"
                                            class="btn btn-sm btn-outline-warning action-btn"
                                        >
                                            <i class="bi bi-key"></i>
                                            <span>Reset</span>
                                        </a>

                                        {{-- penjelasan: Form ini digunakan untuk mengubah status aktif/nonaktif user. --}}
                                        {{-- penjelasan: Method PATCH dipakai karena hanya mengubah sebagian data, yaitu kolom status. --}}
                                        {{-- penjelasan: Route super-admin.users.toggle-status memanggil UserController method toggleStatus(). --}}
                                        <form
                                            action="{{ route('super-admin.users.toggle-status', $user) }}"
                                            method="POST"
                                            class="d-inline"
                                        >
                                            @csrf
                                            @method('PATCH')

                                            {{-- penjelasan: Tombol ini berubah teks dan warna sesuai status user. --}}
                                            {{-- penjelasan: Jika user aktif, tombolnya Nonaktifkan. --}}
                                            {{-- penjelasan: Jika user nonaktif, tombolnya Aktifkan. --}}
                                            {{-- penjelasan: Tombol dibuat disabled jika user adalah akun sendiri atau role super_admin. --}}
                                            <button
                                                type="submit"
                                                class="btn btn-sm action-btn {{ $user->status === 'aktif' ? 'btn-outline-danger' : 'btn-outline-success' }}"
                                                onclick="return confirm('Yakin ingin mengubah status user ini?')"
                                                {{ $user->id === auth()->id() || $user->role === 'super_admin' ? 'disabled' : '' }}
                                            >
                                                @if ($user->status === 'aktif')
                                                    <i class="bi bi-person-x"></i>
                                                    <span>Nonaktif</span>
                                                @else
                                                    <i class="bi bi-person-check"></i>
                                                    <span>Aktifkan</span>
                                                @endif
                                            </button>
                                        </form>

                                    </div>
                                </td>
                            </tr>
                        @empty
                            {{-- penjelasan: Baris ini tampil jika data user kosong. --}}
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    Data user belum tersedia.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- penjelasan: Pagination digunakan untuk berpindah halaman jika data user lebih dari 10. --}}
            <div class="mt-3">
                {{ $users->links() }}
            </div>

        </div>
    </div>

@endsection
