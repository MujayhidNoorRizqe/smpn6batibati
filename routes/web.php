<?php

// penjelasan: File ini mengatur semua route atau URL aplikasi.
// penjelasan: Route adalah jalur alamat yang dibuka user di browser.
// penjelasan: Contoh route: /login, /super-admin/dashboard, /super-admin/pegawai, /super-admin/wali-murid.
// penjelasan: File ini sekarang mengatur route public, login, logout, dashboard, manajemen user, data pegawai, data kelas, dan data wali murid.

use App\Http\Controllers\Admin\KelasController;
// penjelasan: KelasController digunakan untuk modul Data Kelas.
// penjelasan: Controller ini menangani daftar kelas, tambah kelas, edit kelas, detail kelas, dan aktif/nonaktif kelas.

use App\Http\Controllers\Admin\PegawaiController;
// penjelasan: PegawaiController digunakan untuk modul Data Pegawai.
// penjelasan: Controller ini menangani data guru dan staff.

use App\Http\Controllers\Admin\WaliMuridController;
// penjelasan: WaliMuridController digunakan untuk modul Data Wali Murid.
// penjelasan: Controller ini menangani data orang tua/wali murid.
// penjelasan: Route wali murid nanti dipakai sebelum membuat Data Murid.

use App\Http\Controllers\Auth\LoginController;
// penjelasan: LoginController digunakan untuk fitur login dan logout.
// penjelasan: Controller ini menampilkan halaman login, memproses login, dan memproses logout.

use App\Http\Controllers\SuperAdmin\UserController;
// penjelasan: UserController digunakan untuk fitur Manajemen User oleh Super Admin.
// penjelasan: Controller ini menangani tambah user, edit user, reset password, dan aktif/nonaktif akun.

use Illuminate\Support\Facades\Route;
// penjelasan: Route adalah facade bawaan Laravel untuk mendefinisikan URL aplikasi.


// =========================================================
// ROUTE PUBLIC
// =========================================================

// penjelasan: Route ini adalah halaman utama public website.
// penjelasan: Saat user membuka URL utama aplikasi, Laravel akan menampilkan file welcome.blade.php.
// penjelasan: Nanti route ini bisa diganti ke halaman public sekolah.
Route::get('/', function () {
    return view('welcome');
})->name('public.home');


// =========================================================
// ROUTE LOGIN UNTUK USER BELUM LOGIN
// =========================================================

// penjelasan: Middleware guest artinya route di dalam group ini hanya untuk user yang belum login.
// penjelasan: Jika user sudah login lalu membuka /login, biasanya Laravel akan mengarahkan ke halaman dashboard.
Route::middleware('guest')->group(function () {

    // penjelasan: Route GET /login digunakan untuk menampilkan halaman form login.
    // penjelasan: Route ini memanggil LoginController method showLoginForm().
    // penjelasan: Nama route-nya adalah login.
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');

    // penjelasan: Route POST /login digunakan untuk memproses data login dari form.
    // penjelasan: Route ini memanggil LoginController method login().
    // penjelasan: Nama route-nya adalah login.process.
    Route::post('/login', [LoginController::class, 'login'])->name('login.process');
});


// =========================================================
// ROUTE LOGOUT UNTUK USER SUDAH LOGIN
// =========================================================

// penjelasan: Route POST /logout digunakan untuk keluar dari sistem.
// penjelasan: Middleware auth memastikan hanya user yang sudah login yang bisa logout.
// penjelasan: Route ini memanggil LoginController method logout().
Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');


// =========================================================
// ROUTE SUPER ADMIN
// =========================================================

// penjelasan: Group ini khusus untuk role super_admin.
// penjelasan: Middleware auth memastikan user sudah login.
// penjelasan: Middleware role:super_admin memastikan hanya super admin yang bisa membuka route ini.
// penjelasan: Prefix super-admin membuat semua URL di group ini diawali /super-admin.
// penjelasan: Name super-admin. membuat semua nama route diawali super-admin.
Route::middleware(['auth', 'role:super_admin'])
    ->prefix('super-admin')
    ->name('super-admin.')
    ->group(function () {

        // =================================================
        // DASHBOARD SUPER ADMIN
        // =================================================

        // penjelasan: Route ini menampilkan dashboard super admin.
        // penjelasan: URL-nya adalah /super-admin/dashboard.
        // penjelasan: Nama route-nya adalah super-admin.dashboard.
        Route::get('/dashboard', function () {
            return view('admin.pages.dashboard.super-admin.index');
        })->name('dashboard');


        // =================================================
        // MANAJEMEN USER
        // =================================================

        // penjelasan: Route GET /super-admin/users digunakan untuk menampilkan daftar user.
        // penjelasan: Method yang dipanggil adalah UserController@index.
        Route::get('/users', [UserController::class, 'index'])->name('users.index');

        // penjelasan: Route GET /super-admin/users/create digunakan untuk menampilkan form tambah user.
        // penjelasan: Method yang dipanggil adalah UserController@create.
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create');

        // penjelasan: Route POST /super-admin/users digunakan untuk menyimpan user baru.
        // penjelasan: Method yang dipanggil adalah UserController@store.
        Route::post('/users', [UserController::class, 'store'])->name('users.store');

        // penjelasan: Route GET /super-admin/users/{user}/edit digunakan untuk menampilkan form edit user.
        // penjelasan: Parameter {user} otomatis mengambil data dari model User berdasarkan id.
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');

        // penjelasan: Route PUT /super-admin/users/{user} digunakan untuk menyimpan perubahan data user.
        // penjelasan: Method yang dipanggil adalah UserController@update.
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');

        // penjelasan: Route GET ini digunakan untuk menampilkan form reset password user.
        // penjelasan: Password lama tidak ditampilkan karena disimpan dalam bentuk hash.
        Route::get('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');

        // penjelasan: Route PUT ini digunakan untuk menyimpan password baru user.
        // penjelasan: Method yang dipanggil adalah UserController@updatePassword.
        Route::put('/users/{user}/reset-password', [UserController::class, 'updatePassword'])->name('users.update-password');

        // penjelasan: Route PATCH ini digunakan untuk mengubah status user aktif/nonaktif.
        // penjelasan: PATCH dipakai karena hanya mengubah sebagian data, yaitu kolom status.
        Route::patch('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');


        // =================================================
        // DATA PEGAWAI
        // =================================================

        // penjelasan: Route GET /super-admin/pegawai digunakan untuk menampilkan daftar pegawai.
        Route::get('/pegawai', [PegawaiController::class, 'index'])->name('pegawai.index');

        // penjelasan: Route GET /super-admin/pegawai/create digunakan untuk menampilkan form tambah pegawai.
        Route::get('/pegawai/create', [PegawaiController::class, 'create'])->name('pegawai.create');

        // penjelasan: Route POST /super-admin/pegawai digunakan untuk menyimpan data pegawai baru.
        Route::post('/pegawai', [PegawaiController::class, 'store'])->name('pegawai.store');

        // penjelasan: Route GET /super-admin/pegawai/{pegawai} digunakan untuk menampilkan detail pegawai.
        // penjelasan: Route detail diletakkan setelah /create agar tidak bentrok dengan kata create.
        Route::get('/pegawai/{pegawai}', [PegawaiController::class, 'show'])->name('pegawai.show');

        // penjelasan: Route GET /super-admin/pegawai/{pegawai}/edit digunakan untuk menampilkan form edit pegawai.
        Route::get('/pegawai/{pegawai}/edit', [PegawaiController::class, 'edit'])->name('pegawai.edit');

        // penjelasan: Route PUT /super-admin/pegawai/{pegawai} digunakan untuk menyimpan perubahan data pegawai.
        Route::put('/pegawai/{pegawai}', [PegawaiController::class, 'update'])->name('pegawai.update');

        // penjelasan: Route PATCH ini digunakan untuk mengubah status pegawai aktif/nonaktif.
        Route::patch('/pegawai/{pegawai}/toggle-status', [PegawaiController::class, 'toggleStatus'])->name('pegawai.toggle-status');


        // =================================================
        // DATA KELAS
        // =================================================

        // penjelasan: Route GET /super-admin/kelas digunakan untuk menampilkan daftar kelas.
        Route::get('/kelas', [KelasController::class, 'index'])->name('kelas.index');

        // penjelasan: Route GET /super-admin/kelas/create digunakan untuk menampilkan form tambah kelas.
        Route::get('/kelas/create', [KelasController::class, 'create'])->name('kelas.create');

        // penjelasan: Route POST /super-admin/kelas digunakan untuk menyimpan data kelas baru.
        Route::post('/kelas', [KelasController::class, 'store'])->name('kelas.store');

        // penjelasan: Route GET /super-admin/kelas/{kelas} digunakan untuk menampilkan detail kelas.
        Route::get('/kelas/{kelas}', [KelasController::class, 'show'])->name('kelas.show');

        // penjelasan: Route GET /super-admin/kelas/{kelas}/edit digunakan untuk menampilkan form edit kelas.
        Route::get('/kelas/{kelas}/edit', [KelasController::class, 'edit'])->name('kelas.edit');

        // penjelasan: Route PUT /super-admin/kelas/{kelas} digunakan untuk menyimpan perubahan data kelas.
        Route::put('/kelas/{kelas}', [KelasController::class, 'update'])->name('kelas.update');

        // penjelasan: Route PATCH ini digunakan untuk mengubah status kelas aktif/nonaktif.
        Route::patch('/kelas/{kelas}/toggle-status', [KelasController::class, 'toggleStatus'])->name('kelas.toggle-status');


        // =================================================
        // DATA WALI MURID
        // =================================================

        // penjelasan: Route GET /super-admin/wali-murid digunakan untuk menampilkan daftar wali murid.
        // penjelasan: Method yang dipanggil adalah WaliMuridController@index.
        Route::get('/wali-murid', [WaliMuridController::class, 'index'])->name('wali-murid.index');

        // penjelasan: Route GET /super-admin/wali-murid/create digunakan untuk menampilkan form tambah wali murid.
        // penjelasan: Method yang dipanggil adalah WaliMuridController@create.
        Route::get('/wali-murid/create', [WaliMuridController::class, 'create'])->name('wali-murid.create');

        // penjelasan: Route POST /super-admin/wali-murid digunakan untuk menyimpan data wali murid baru.
        // penjelasan: Method yang dipanggil adalah WaliMuridController@store.
        Route::post('/wali-murid', [WaliMuridController::class, 'store'])->name('wali-murid.store');

        // penjelasan: Route GET /super-admin/wali-murid/{waliMurid} digunakan untuk menampilkan detail wali murid.
        // penjelasan: Parameter {waliMurid} otomatis dibaca Laravel sebagai model WaliMurid.
        Route::get('/wali-murid/{waliMurid}', [WaliMuridController::class, 'show'])->name('wali-murid.show');

        // penjelasan: Route GET /super-admin/wali-murid/{waliMurid}/edit digunakan untuk menampilkan form edit wali murid.
        Route::get('/wali-murid/{waliMurid}/edit', [WaliMuridController::class, 'edit'])->name('wali-murid.edit');

        // penjelasan: Route PUT /super-admin/wali-murid/{waliMurid} digunakan untuk menyimpan perubahan data wali murid.
        Route::put('/wali-murid/{waliMurid}', [WaliMuridController::class, 'update'])->name('wali-murid.update');

        // penjelasan: Route PATCH ini digunakan untuk mengubah status wali murid aktif/nonaktif.
        Route::patch('/wali-murid/{waliMurid}/toggle-status', [WaliMuridController::class, 'toggleStatus'])->name('wali-murid.toggle-status');
    });


// =========================================================
// ROUTE ADMIN
// =========================================================

// penjelasan: Group ini khusus untuk role admin.
// penjelasan: Admin bisa mengelola data pegawai, kelas, dan wali murid.
// penjelasan: Admin tidak diberi akses Manajemen User karena Manajemen User hanya untuk Super Admin.
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // =================================================
        // DASHBOARD ADMIN
        // =================================================

        // penjelasan: Route ini menampilkan dashboard admin.
        // penjelasan: URL-nya adalah /admin/dashboard.
        // penjelasan: Nama route-nya adalah admin.dashboard.
        Route::get('/dashboard', function () {
            return view('admin.pages.dashboard.admin.index');
        })->name('dashboard');


        // =================================================
        // DATA PEGAWAI
        // =================================================

        // penjelasan: Route GET /admin/pegawai digunakan untuk menampilkan daftar pegawai.
        Route::get('/pegawai', [PegawaiController::class, 'index'])->name('pegawai.index');

        // penjelasan: Route GET /admin/pegawai/create digunakan untuk menampilkan form tambah pegawai.
        Route::get('/pegawai/create', [PegawaiController::class, 'create'])->name('pegawai.create');

        // penjelasan: Route POST /admin/pegawai digunakan untuk menyimpan data pegawai baru.
        Route::post('/pegawai', [PegawaiController::class, 'store'])->name('pegawai.store');

        // penjelasan: Route GET /admin/pegawai/{pegawai} digunakan untuk menampilkan detail pegawai.
        Route::get('/pegawai/{pegawai}', [PegawaiController::class, 'show'])->name('pegawai.show');

        // penjelasan: Route GET /admin/pegawai/{pegawai}/edit digunakan untuk menampilkan form edit pegawai.
        Route::get('/pegawai/{pegawai}/edit', [PegawaiController::class, 'edit'])->name('pegawai.edit');

        // penjelasan: Route PUT /admin/pegawai/{pegawai} digunakan untuk menyimpan perubahan data pegawai.
        Route::put('/pegawai/{pegawai}', [PegawaiController::class, 'update'])->name('pegawai.update');

        // penjelasan: Route PATCH ini digunakan untuk mengubah status pegawai aktif/nonaktif.
        Route::patch('/pegawai/{pegawai}/toggle-status', [PegawaiController::class, 'toggleStatus'])->name('pegawai.toggle-status');


        // =================================================
        // DATA KELAS
        // =================================================

        // penjelasan: Route GET /admin/kelas digunakan untuk menampilkan daftar kelas.
        Route::get('/kelas', [KelasController::class, 'index'])->name('kelas.index');

        // penjelasan: Route GET /admin/kelas/create digunakan untuk menampilkan form tambah kelas.
        Route::get('/kelas/create', [KelasController::class, 'create'])->name('kelas.create');

        // penjelasan: Route POST /admin/kelas digunakan untuk menyimpan data kelas baru.
        Route::post('/kelas', [KelasController::class, 'store'])->name('kelas.store');

        // penjelasan: Route GET /admin/kelas/{kelas} digunakan untuk menampilkan detail kelas.
        Route::get('/kelas/{kelas}', [KelasController::class, 'show'])->name('kelas.show');

        // penjelasan: Route GET /admin/kelas/{kelas}/edit digunakan untuk menampilkan form edit kelas.
        Route::get('/kelas/{kelas}/edit', [KelasController::class, 'edit'])->name('kelas.edit');

        // penjelasan: Route PUT /admin/kelas/{kelas} digunakan untuk menyimpan perubahan data kelas.
        Route::put('/kelas/{kelas}', [KelasController::class, 'update'])->name('kelas.update');

        // penjelasan: Route PATCH ini digunakan untuk mengubah status kelas aktif/nonaktif.
        Route::patch('/kelas/{kelas}/toggle-status', [KelasController::class, 'toggleStatus'])->name('kelas.toggle-status');


        // =================================================
        // DATA WALI MURID
        // =================================================

        // penjelasan: Route GET /admin/wali-murid digunakan untuk menampilkan daftar wali murid.
        Route::get('/wali-murid', [WaliMuridController::class, 'index'])->name('wali-murid.index');

        // penjelasan: Route GET /admin/wali-murid/create digunakan untuk menampilkan form tambah wali murid.
        Route::get('/wali-murid/create', [WaliMuridController::class, 'create'])->name('wali-murid.create');

        // penjelasan: Route POST /admin/wali-murid digunakan untuk menyimpan data wali murid baru.
        Route::post('/wali-murid', [WaliMuridController::class, 'store'])->name('wali-murid.store');

        // penjelasan: Route GET /admin/wali-murid/{waliMurid} digunakan untuk menampilkan detail wali murid.
        Route::get('/wali-murid/{waliMurid}', [WaliMuridController::class, 'show'])->name('wali-murid.show');

        // penjelasan: Route GET /admin/wali-murid/{waliMurid}/edit digunakan untuk menampilkan form edit wali murid.
        Route::get('/wali-murid/{waliMurid}/edit', [WaliMuridController::class, 'edit'])->name('wali-murid.edit');

        // penjelasan: Route PUT /admin/wali-murid/{waliMurid} digunakan untuk menyimpan perubahan data wali murid.
        Route::put('/wali-murid/{waliMurid}', [WaliMuridController::class, 'update'])->name('wali-murid.update');

        // penjelasan: Route PATCH ini digunakan untuk mengubah status wali murid aktif/nonaktif.
        Route::patch('/wali-murid/{waliMurid}/toggle-status', [WaliMuridController::class, 'toggleStatus'])->name('wali-murid.toggle-status');
    });


// =========================================================
// ROUTE GURU
// =========================================================

// penjelasan: Group ini khusus untuk role guru.
// penjelasan: Untuk saat ini guru baru memiliki dashboard.
// penjelasan: Nanti akan ditambahkan route absensi guru, pengajuan dinas, jadwal mengajar, absen murid, dan input nilai.
Route::middleware(['auth', 'role:guru'])
    ->prefix('guru')
    ->name('guru.')
    ->group(function () {

        // penjelasan: Route ini menampilkan dashboard guru.
        // penjelasan: URL-nya adalah /guru/dashboard.
        // penjelasan: Nama route-nya adalah guru.dashboard.
        Route::get('/dashboard', function () {
            return view('admin.pages.dashboard.guru.index');
        })->name('dashboard');
    });


// =========================================================
// ROUTE STAFF
// =========================================================

// penjelasan: Group ini khusus untuk role staff.
// penjelasan: Untuk saat ini staff baru memiliki dashboard.
// penjelasan: Nanti akan ditambahkan route absensi staff, pengajuan dinas, dan riwayat absensi.
Route::middleware(['auth', 'role:staff'])
    ->prefix('staff')
    ->name('staff.')
    ->group(function () {

        // penjelasan: Route ini menampilkan dashboard staff.
        // penjelasan: URL-nya adalah /staff/dashboard.
        // penjelasan: Nama route-nya adalah staff.dashboard.
        Route::get('/dashboard', function () {
            return view('admin.pages.dashboard.staff.index');
        })->name('dashboard');
    });
