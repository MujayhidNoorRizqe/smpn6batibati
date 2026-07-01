<?php

// penjelasan: File ini mengatur semua route atau URL aplikasi.
// penjelasan: Route adalah jalur alamat yang dibuka user di browser.
// penjelasan: Contoh route: /login, /super-admin/dashboard, /super-admin/pegawai, /super-admin/murid.
// penjelasan: File ini sekarang mengatur route public, login, logout, dashboard, manajemen user, data pegawai, data kelas, data wali murid, data murid, mata pelajaran, tahun ajaran, dan semester.

use App\Http\Controllers\Admin\KelasController;
// penjelasan: KelasController digunakan untuk modul Data Kelas.

use App\Http\Controllers\Admin\MataPelajaranController;
// penjelasan: MataPelajaranController digunakan untuk modul Data Mata Pelajaran.

use App\Http\Controllers\Admin\MuridController;
// penjelasan: MuridController digunakan untuk modul Data Murid.

use App\Http\Controllers\Admin\PegawaiController;
// penjelasan: PegawaiController digunakan untuk modul Data Pegawai.

use App\Http\Controllers\Admin\SemesterController;
// penjelasan: SemesterController digunakan untuk modul Semester.
// penjelasan: Controller ini mengatur daftar semester, tambah, edit, detail, dan aktif/nonaktif semester.

use App\Http\Controllers\Admin\TahunAjaranController;
// penjelasan: TahunAjaranController digunakan untuk modul Tahun Ajaran.
// penjelasan: Controller ini mengatur daftar tahun ajaran, tambah, edit, detail, dan aktif/nonaktif tahun ajaran.

use App\Http\Controllers\Admin\WaliMuridController;
// penjelasan: WaliMuridController digunakan untuk modul Data Wali Murid.

use App\Http\Controllers\Auth\LoginController;
// penjelasan: LoginController digunakan untuk fitur login dan logout.

use App\Http\Controllers\SuperAdmin\UserController;
// penjelasan: UserController digunakan untuk fitur Manajemen User oleh Super Admin.

use Illuminate\Support\Facades\Route;
// penjelasan: Route adalah facade bawaan Laravel untuk mendefinisikan URL aplikasi.


// =========================================================
// ROUTE PUBLIC / HALAMAN AWAL APLIKASI
// =========================================================

Route::get('/', function () {
    // penjelasan: auth()->check() digunakan untuk mengecek apakah user sudah login atau belum.
    if (! auth()->check()) {
        return redirect()->route('login');
    }

    // penjelasan: Mengambil data user login untuk diarahkan ke dashboard sesuai role.
    $user = auth()->user();

    if ($user->role === 'super_admin') {
        return redirect()->route('super-admin.dashboard');
    }

    if ($user->role === 'admin') {
        return redirect()->route('admin.dashboard');
    }

    if ($user->role === 'guru') {
        return redirect()->route('guru.dashboard');
    }

    if ($user->role === 'staff') {
        return redirect()->route('staff.dashboard');
    }

    return redirect()->route('login');
})->name('public.home');


// =========================================================
// ROUTE LOGIN UNTUK USER BELUM LOGIN
// =========================================================

Route::middleware('guest')->group(function () {
    // penjelasan: Menampilkan halaman login.
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');

    // penjelasan: Memproses form login.
    Route::post('/login', [LoginController::class, 'login'])->name('login.process');
});


// =========================================================
// ROUTE LOGOUT UNTUK USER SUDAH LOGIN
// =========================================================

Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');


// =========================================================
// ROUTE SUPER ADMIN
// =========================================================

Route::middleware(['auth', 'role:super_admin'])
    ->prefix('super-admin')
    ->name('super-admin.')
    ->group(function () {

        // =================================================
        // DASHBOARD SUPER ADMIN
        // =================================================

        Route::get('/dashboard', function () {
            return view('admin.pages.dashboard.super-admin.index');
        })->name('dashboard');


        // =================================================
        // MANAJEMEN USER
        // =================================================

        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::get('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
        Route::put('/users/{user}/reset-password', [UserController::class, 'updatePassword'])->name('users.update-password');
        Route::patch('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');


        // =================================================
        // DATA PEGAWAI
        // =================================================

        Route::get('/pegawai', [PegawaiController::class, 'index'])->name('pegawai.index');
        Route::get('/pegawai/create', [PegawaiController::class, 'create'])->name('pegawai.create');
        Route::post('/pegawai', [PegawaiController::class, 'store'])->name('pegawai.store');
        Route::get('/pegawai/{pegawai}', [PegawaiController::class, 'show'])->name('pegawai.show');
        Route::get('/pegawai/{pegawai}/edit', [PegawaiController::class, 'edit'])->name('pegawai.edit');
        Route::put('/pegawai/{pegawai}', [PegawaiController::class, 'update'])->name('pegawai.update');
        Route::patch('/pegawai/{pegawai}/toggle-status', [PegawaiController::class, 'toggleStatus'])->name('pegawai.toggle-status');


        // =================================================
        // DATA KELAS
        // =================================================

        Route::get('/kelas', [KelasController::class, 'index'])->name('kelas.index');
        Route::get('/kelas/create', [KelasController::class, 'create'])->name('kelas.create');
        Route::post('/kelas', [KelasController::class, 'store'])->name('kelas.store');
        Route::get('/kelas/{kelas}', [KelasController::class, 'show'])->name('kelas.show');
        Route::get('/kelas/{kelas}/edit', [KelasController::class, 'edit'])->name('kelas.edit');
        Route::put('/kelas/{kelas}', [KelasController::class, 'update'])->name('kelas.update');
        Route::patch('/kelas/{kelas}/toggle-status', [KelasController::class, 'toggleStatus'])->name('kelas.toggle-status');


        // =================================================
        // DATA WALI MURID
        // =================================================

        Route::get('/wali-murid', [WaliMuridController::class, 'index'])->name('wali-murid.index');
        Route::get('/wali-murid/create', [WaliMuridController::class, 'create'])->name('wali-murid.create');
        Route::post('/wali-murid', [WaliMuridController::class, 'store'])->name('wali-murid.store');
        Route::get('/wali-murid/{waliMurid}', [WaliMuridController::class, 'show'])->name('wali-murid.show');
        Route::get('/wali-murid/{waliMurid}/edit', [WaliMuridController::class, 'edit'])->name('wali-murid.edit');
        Route::put('/wali-murid/{waliMurid}', [WaliMuridController::class, 'update'])->name('wali-murid.update');
        Route::patch('/wali-murid/{waliMurid}/toggle-status', [WaliMuridController::class, 'toggleStatus'])->name('wali-murid.toggle-status');


        // =================================================
        // DATA MURID
        // =================================================

        Route::get('/murid', [MuridController::class, 'index'])->name('murid.index');
        Route::get('/murid/create', [MuridController::class, 'create'])->name('murid.create');
        Route::post('/murid', [MuridController::class, 'store'])->name('murid.store');
        Route::get('/murid/{murid}', [MuridController::class, 'show'])->name('murid.show');
        Route::get('/murid/{murid}/edit', [MuridController::class, 'edit'])->name('murid.edit');
        Route::put('/murid/{murid}', [MuridController::class, 'update'])->name('murid.update');
        Route::patch('/murid/{murid}/toggle-status', [MuridController::class, 'toggleStatus'])->name('murid.toggle-status');


        // =================================================
        // DATA MATA PELAJARAN
        // =================================================

        Route::get('/mata-pelajaran', [MataPelajaranController::class, 'index'])->name('mata-pelajaran.index');
        Route::get('/mata-pelajaran/create', [MataPelajaranController::class, 'create'])->name('mata-pelajaran.create');
        Route::post('/mata-pelajaran', [MataPelajaranController::class, 'store'])->name('mata-pelajaran.store');
        Route::get('/mata-pelajaran/{mataPelajaran}', [MataPelajaranController::class, 'show'])->name('mata-pelajaran.show');
        Route::get('/mata-pelajaran/{mataPelajaran}/edit', [MataPelajaranController::class, 'edit'])->name('mata-pelajaran.edit');
        Route::put('/mata-pelajaran/{mataPelajaran}', [MataPelajaranController::class, 'update'])->name('mata-pelajaran.update');
        Route::patch('/mata-pelajaran/{mataPelajaran}/toggle-status', [MataPelajaranController::class, 'toggleStatus'])->name('mata-pelajaran.toggle-status');


        // =================================================
        // TAHUN AJARAN
        // =================================================

        // penjelasan: Route GET /super-admin/tahun-ajaran digunakan untuk menampilkan daftar tahun ajaran.
        Route::get('/tahun-ajaran', [TahunAjaranController::class, 'index'])->name('tahun-ajaran.index');

        // penjelasan: Route GET /super-admin/tahun-ajaran/create digunakan untuk menampilkan form tambah tahun ajaran.
        Route::get('/tahun-ajaran/create', [TahunAjaranController::class, 'create'])->name('tahun-ajaran.create');

        // penjelasan: Route POST /super-admin/tahun-ajaran digunakan untuk menyimpan data tahun ajaran baru.
        Route::post('/tahun-ajaran', [TahunAjaranController::class, 'store'])->name('tahun-ajaran.store');

        // penjelasan: Route GET /super-admin/tahun-ajaran/{tahunAjaran} digunakan untuk menampilkan detail tahun ajaran.
        Route::get('/tahun-ajaran/{tahunAjaran}', [TahunAjaranController::class, 'show'])->name('tahun-ajaran.show');

        // penjelasan: Route GET /super-admin/tahun-ajaran/{tahunAjaran}/edit digunakan untuk menampilkan form edit tahun ajaran.
        Route::get('/tahun-ajaran/{tahunAjaran}/edit', [TahunAjaranController::class, 'edit'])->name('tahun-ajaran.edit');

        // penjelasan: Route PUT /super-admin/tahun-ajaran/{tahunAjaran} digunakan untuk menyimpan perubahan tahun ajaran.
        Route::put('/tahun-ajaran/{tahunAjaran}', [TahunAjaranController::class, 'update'])->name('tahun-ajaran.update');

        // penjelasan: Route PATCH ini digunakan untuk mengubah status tahun ajaran aktif/nonaktif.
        Route::patch('/tahun-ajaran/{tahunAjaran}/toggle-status', [TahunAjaranController::class, 'toggleStatus'])->name('tahun-ajaran.toggle-status');


        // =================================================
        // SEMESTER
        // =================================================

        // penjelasan: Route GET /super-admin/semester digunakan untuk menampilkan daftar semester.
        Route::get('/semester', [SemesterController::class, 'index'])->name('semester.index');

        // penjelasan: Route GET /super-admin/semester/create digunakan untuk menampilkan form tambah semester.
        Route::get('/semester/create', [SemesterController::class, 'create'])->name('semester.create');

        // penjelasan: Route POST /super-admin/semester digunakan untuk menyimpan data semester baru.
        Route::post('/semester', [SemesterController::class, 'store'])->name('semester.store');

        // penjelasan: Route GET /super-admin/semester/{semester} digunakan untuk menampilkan detail semester.
        Route::get('/semester/{semester}', [SemesterController::class, 'show'])->name('semester.show');

        // penjelasan: Route GET /super-admin/semester/{semester}/edit digunakan untuk menampilkan form edit semester.
        Route::get('/semester/{semester}/edit', [SemesterController::class, 'edit'])->name('semester.edit');

        // penjelasan: Route PUT /super-admin/semester/{semester} digunakan untuk menyimpan perubahan semester.
        Route::put('/semester/{semester}', [SemesterController::class, 'update'])->name('semester.update');

        // penjelasan: Route PATCH ini digunakan untuk mengubah status semester aktif/nonaktif.
        Route::patch('/semester/{semester}/toggle-status', [SemesterController::class, 'toggleStatus'])->name('semester.toggle-status');
    });


// =========================================================
// ROUTE ADMIN
// =========================================================

Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // =================================================
        // DASHBOARD ADMIN
        // =================================================

        Route::get('/dashboard', function () {
            return view('admin.pages.dashboard.admin.index');
        })->name('dashboard');


        // =================================================
        // DATA PEGAWAI
        // =================================================

        Route::get('/pegawai', [PegawaiController::class, 'index'])->name('pegawai.index');
        Route::get('/pegawai/create', [PegawaiController::class, 'create'])->name('pegawai.create');
        Route::post('/pegawai', [PegawaiController::class, 'store'])->name('pegawai.store');
        Route::get('/pegawai/{pegawai}', [PegawaiController::class, 'show'])->name('pegawai.show');
        Route::get('/pegawai/{pegawai}/edit', [PegawaiController::class, 'edit'])->name('pegawai.edit');
        Route::put('/pegawai/{pegawai}', [PegawaiController::class, 'update'])->name('pegawai.update');
        Route::patch('/pegawai/{pegawai}/toggle-status', [PegawaiController::class, 'toggleStatus'])->name('pegawai.toggle-status');


        // =================================================
        // DATA KELAS
        // =================================================

        Route::get('/kelas', [KelasController::class, 'index'])->name('kelas.index');
        Route::get('/kelas/create', [KelasController::class, 'create'])->name('kelas.create');
        Route::post('/kelas', [KelasController::class, 'store'])->name('kelas.store');
        Route::get('/kelas/{kelas}', [KelasController::class, 'show'])->name('kelas.show');
        Route::get('/kelas/{kelas}/edit', [KelasController::class, 'edit'])->name('kelas.edit');
        Route::put('/kelas/{kelas}', [KelasController::class, 'update'])->name('kelas.update');
        Route::patch('/kelas/{kelas}/toggle-status', [KelasController::class, 'toggleStatus'])->name('kelas.toggle-status');


        // =================================================
        // DATA WALI MURID
        // =================================================

        Route::get('/wali-murid', [WaliMuridController::class, 'index'])->name('wali-murid.index');
        Route::get('/wali-murid/create', [WaliMuridController::class, 'create'])->name('wali-murid.create');
        Route::post('/wali-murid', [WaliMuridController::class, 'store'])->name('wali-murid.store');
        Route::get('/wali-murid/{waliMurid}', [WaliMuridController::class, 'show'])->name('wali-murid.show');
        Route::get('/wali-murid/{waliMurid}/edit', [WaliMuridController::class, 'edit'])->name('wali-murid.edit');
        Route::put('/wali-murid/{waliMurid}', [WaliMuridController::class, 'update'])->name('wali-murid.update');
        Route::patch('/wali-murid/{waliMurid}/toggle-status', [WaliMuridController::class, 'toggleStatus'])->name('wali-murid.toggle-status');


        // =================================================
        // DATA MURID
        // =================================================

        Route::get('/murid', [MuridController::class, 'index'])->name('murid.index');
        Route::get('/murid/create', [MuridController::class, 'create'])->name('murid.create');
        Route::post('/murid', [MuridController::class, 'store'])->name('murid.store');
        Route::get('/murid/{murid}', [MuridController::class, 'show'])->name('murid.show');
        Route::get('/murid/{murid}/edit', [MuridController::class, 'edit'])->name('murid.edit');
        Route::put('/murid/{murid}', [MuridController::class, 'update'])->name('murid.update');
        Route::patch('/murid/{murid}/toggle-status', [MuridController::class, 'toggleStatus'])->name('murid.toggle-status');


        // =================================================
        // DATA MATA PELAJARAN
        // =================================================

        Route::get('/mata-pelajaran', [MataPelajaranController::class, 'index'])->name('mata-pelajaran.index');
        Route::get('/mata-pelajaran/create', [MataPelajaranController::class, 'create'])->name('mata-pelajaran.create');
        Route::post('/mata-pelajaran', [MataPelajaranController::class, 'store'])->name('mata-pelajaran.store');
        Route::get('/mata-pelajaran/{mataPelajaran}', [MataPelajaranController::class, 'show'])->name('mata-pelajaran.show');
        Route::get('/mata-pelajaran/{mataPelajaran}/edit', [MataPelajaranController::class, 'edit'])->name('mata-pelajaran.edit');
        Route::put('/mata-pelajaran/{mataPelajaran}', [MataPelajaranController::class, 'update'])->name('mata-pelajaran.update');
        Route::patch('/mata-pelajaran/{mataPelajaran}/toggle-status', [MataPelajaranController::class, 'toggleStatus'])->name('mata-pelajaran.toggle-status');


        // =================================================
        // TAHUN AJARAN
        // =================================================

        // penjelasan: Route GET /admin/tahun-ajaran digunakan untuk menampilkan daftar tahun ajaran.
        Route::get('/tahun-ajaran', [TahunAjaranController::class, 'index'])->name('tahun-ajaran.index');

        // penjelasan: Route GET /admin/tahun-ajaran/create digunakan untuk menampilkan form tambah tahun ajaran.
        Route::get('/tahun-ajaran/create', [TahunAjaranController::class, 'create'])->name('tahun-ajaran.create');

        // penjelasan: Route POST /admin/tahun-ajaran digunakan untuk menyimpan data tahun ajaran baru.
        Route::post('/tahun-ajaran', [TahunAjaranController::class, 'store'])->name('tahun-ajaran.store');

        // penjelasan: Route GET /admin/tahun-ajaran/{tahunAjaran} digunakan untuk menampilkan detail tahun ajaran.
        Route::get('/tahun-ajaran/{tahunAjaran}', [TahunAjaranController::class, 'show'])->name('tahun-ajaran.show');

        // penjelasan: Route GET /admin/tahun-ajaran/{tahunAjaran}/edit digunakan untuk menampilkan form edit tahun ajaran.
        Route::get('/tahun-ajaran/{tahunAjaran}/edit', [TahunAjaranController::class, 'edit'])->name('tahun-ajaran.edit');

        // penjelasan: Route PUT /admin/tahun-ajaran/{tahunAjaran} digunakan untuk menyimpan perubahan tahun ajaran.
        Route::put('/tahun-ajaran/{tahunAjaran}', [TahunAjaranController::class, 'update'])->name('tahun-ajaran.update');

        // penjelasan: Route PATCH ini digunakan untuk mengubah status tahun ajaran aktif/nonaktif.
        Route::patch('/tahun-ajaran/{tahunAjaran}/toggle-status', [TahunAjaranController::class, 'toggleStatus'])->name('tahun-ajaran.toggle-status');


        // =================================================
        // SEMESTER
        // =================================================

        // penjelasan: Route GET /admin/semester digunakan untuk menampilkan daftar semester.
        Route::get('/semester', [SemesterController::class, 'index'])->name('semester.index');

        // penjelasan: Route GET /admin/semester/create digunakan untuk menampilkan form tambah semester.
        Route::get('/semester/create', [SemesterController::class, 'create'])->name('semester.create');

        // penjelasan: Route POST /admin/semester digunakan untuk menyimpan data semester baru.
        Route::post('/semester', [SemesterController::class, 'store'])->name('semester.store');

        // penjelasan: Route GET /admin/semester/{semester} digunakan untuk menampilkan detail semester.
        Route::get('/semester/{semester}', [SemesterController::class, 'show'])->name('semester.show');

        // penjelasan: Route GET /admin/semester/{semester}/edit digunakan untuk menampilkan form edit semester.
        Route::get('/semester/{semester}/edit', [SemesterController::class, 'edit'])->name('semester.edit');

        // penjelasan: Route PUT /admin/semester/{semester} digunakan untuk menyimpan perubahan semester.
        Route::put('/semester/{semester}', [SemesterController::class, 'update'])->name('semester.update');

        // penjelasan: Route PATCH ini digunakan untuk mengubah status semester aktif/nonaktif.
        Route::patch('/semester/{semester}/toggle-status', [SemesterController::class, 'toggleStatus'])->name('semester.toggle-status');
    });


// =========================================================
// ROUTE GURU
// =========================================================

Route::middleware(['auth', 'role:guru'])
    ->prefix('guru')
    ->name('guru.')
    ->group(function () {
        Route::get('/dashboard', function () {
            return view('admin.pages.dashboard.guru.index');
        })->name('dashboard');
    });


// =========================================================
// ROUTE STAFF
// =========================================================

Route::middleware(['auth', 'role:staff'])
    ->prefix('staff')
    ->name('staff.')
    ->group(function () {
        Route::get('/dashboard', function () {
            return view('admin.pages.dashboard.staff.index');
        })->name('dashboard');
    });
