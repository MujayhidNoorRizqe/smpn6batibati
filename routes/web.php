<?php

// penjelasan: File ini mengatur semua route atau URL aplikasi.
// penjelasan: Route adalah jalur alamat yang dibuka user di browser.
// penjelasan: Contoh route: /login, /super-admin/dashboard, /super-admin/pegawai, /super-admin/murid.
// penjelasan: File ini sekarang mengatur route public, login, logout, dashboard, manajemen user, data pegawai, data kelas, data wali murid, data murid, dan mata pelajaran.

use App\Http\Controllers\Admin\KelasController;
// penjelasan: KelasController digunakan untuk modul Data Kelas.
// penjelasan: Controller ini menangani daftar kelas, tambah kelas, edit kelas, detail kelas, dan aktif/nonaktif kelas.

use App\Http\Controllers\Admin\MataPelajaranController;
// penjelasan: MataPelajaranController digunakan untuk modul Data Mata Pelajaran.
// penjelasan: Controller ini menangani daftar mata pelajaran, tambah, edit, detail, dan aktif/nonaktif mata pelajaran.
// penjelasan: Data mata pelajaran nanti digunakan untuk Jadwal Pelajaran dan Nilai Murid.

use App\Http\Controllers\Admin\MuridController;
// penjelasan: MuridController digunakan untuk modul Data Murid.
// penjelasan: Controller ini menangani daftar murid, tambah murid, edit murid, detail murid, upload foto murid, dan aktif/nonaktif murid.
// penjelasan: Modul Data Murid membutuhkan data kelas dan data wali murid.

use App\Http\Controllers\Admin\PegawaiController;
// penjelasan: PegawaiController digunakan untuk modul Data Pegawai.
// penjelasan: Controller ini menangani data guru dan staff.

use App\Http\Controllers\Admin\WaliMuridController;
// penjelasan: WaliMuridController digunakan untuk modul Data Wali Murid.
// penjelasan: Controller ini menangani data orang tua/wali murid.
// penjelasan: Route wali murid dipakai sebelum membuat Data Murid karena murid harus dihubungkan ke wali murid.

use App\Http\Controllers\Auth\LoginController;
// penjelasan: LoginController digunakan untuk fitur login dan logout.
// penjelasan: Controller ini menampilkan halaman login, memproses login, dan memproses logout.

use App\Http\Controllers\SuperAdmin\UserController;
// penjelasan: UserController digunakan untuk fitur Manajemen User oleh Super Admin.
// penjelasan: Controller ini menangani tambah user, edit user, reset password, dan aktif/nonaktif akun.

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

        // penjelasan: Route GET /super-admin/mata-pelajaran digunakan untuk menampilkan daftar mata pelajaran.
        Route::get('/mata-pelajaran', [MataPelajaranController::class, 'index'])->name('mata-pelajaran.index');

        // penjelasan: Route GET /super-admin/mata-pelajaran/create digunakan untuk menampilkan form tambah mata pelajaran.
        Route::get('/mata-pelajaran/create', [MataPelajaranController::class, 'create'])->name('mata-pelajaran.create');

        // penjelasan: Route POST /super-admin/mata-pelajaran digunakan untuk menyimpan data mata pelajaran baru.
        Route::post('/mata-pelajaran', [MataPelajaranController::class, 'store'])->name('mata-pelajaran.store');

        // penjelasan: Route GET /super-admin/mata-pelajaran/{mataPelajaran} digunakan untuk menampilkan detail mata pelajaran.
        Route::get('/mata-pelajaran/{mataPelajaran}', [MataPelajaranController::class, 'show'])->name('mata-pelajaran.show');

        // penjelasan: Route GET /super-admin/mata-pelajaran/{mataPelajaran}/edit digunakan untuk menampilkan form edit mata pelajaran.
        Route::get('/mata-pelajaran/{mataPelajaran}/edit', [MataPelajaranController::class, 'edit'])->name('mata-pelajaran.edit');

        // penjelasan: Route PUT /super-admin/mata-pelajaran/{mataPelajaran} digunakan untuk menyimpan perubahan data mata pelajaran.
        Route::put('/mata-pelajaran/{mataPelajaran}', [MataPelajaranController::class, 'update'])->name('mata-pelajaran.update');

        // penjelasan: Route PATCH ini digunakan untuk mengubah status mata pelajaran aktif/nonaktif.
        Route::patch('/mata-pelajaran/{mataPelajaran}/toggle-status', [MataPelajaranController::class, 'toggleStatus'])->name('mata-pelajaran.toggle-status');
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

        // penjelasan: Route GET /admin/mata-pelajaran digunakan untuk menampilkan daftar mata pelajaran.
        Route::get('/mata-pelajaran', [MataPelajaranController::class, 'index'])->name('mata-pelajaran.index');

        // penjelasan: Route GET /admin/mata-pelajaran/create digunakan untuk menampilkan form tambah mata pelajaran.
        Route::get('/mata-pelajaran/create', [MataPelajaranController::class, 'create'])->name('mata-pelajaran.create');

        // penjelasan: Route POST /admin/mata-pelajaran digunakan untuk menyimpan data mata pelajaran baru.
        Route::post('/mata-pelajaran', [MataPelajaranController::class, 'store'])->name('mata-pelajaran.store');

        // penjelasan: Route GET /admin/mata-pelajaran/{mataPelajaran} digunakan untuk menampilkan detail mata pelajaran.
        Route::get('/mata-pelajaran/{mataPelajaran}', [MataPelajaranController::class, 'show'])->name('mata-pelajaran.show');

        // penjelasan: Route GET /admin/mata-pelajaran/{mataPelajaran}/edit digunakan untuk menampilkan form edit mata pelajaran.
        Route::get('/mata-pelajaran/{mataPelajaran}/edit', [MataPelajaranController::class, 'edit'])->name('mata-pelajaran.edit');

        // penjelasan: Route PUT /admin/mata-pelajaran/{mataPelajaran} digunakan untuk menyimpan perubahan data mata pelajaran.
        Route::put('/mata-pelajaran/{mataPelajaran}', [MataPelajaranController::class, 'update'])->name('mata-pelajaran.update');

        // penjelasan: Route PATCH ini digunakan untuk mengubah status mata pelajaran aktif/nonaktif.
        Route::patch('/mata-pelajaran/{mataPelajaran}/toggle-status', [MataPelajaranController::class, 'toggleStatus'])->name('mata-pelajaran.toggle-status');
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
