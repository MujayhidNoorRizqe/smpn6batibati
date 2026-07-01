<?php

// penjelasan: File ini mengatur semua route atau URL aplikasi.
// penjelasan: Route public, login, dashboard, manajemen user, data pegawai, dan data kelas diatur di file ini.

use App\Http\Controllers\Admin\KelasController;
// penjelasan: KelasController digunakan untuk modul Data Kelas.

use App\Http\Controllers\Admin\PegawaiController;
// penjelasan: PegawaiController digunakan untuk modul Data Pegawai.

use App\Http\Controllers\Auth\LoginController;
// penjelasan: LoginController digunakan untuk login dan logout.

use App\Http\Controllers\SuperAdmin\UserController;
// penjelasan: UserController digunakan untuk Manajemen User oleh Super Admin.

use Illuminate\Support\Facades\Route;
// penjelasan: Route adalah fitur Laravel untuk membuat alamat URL.

Route::get('/', function () {
    return view('welcome');
})->name('public.home');
// penjelasan: Route public utama untuk halaman awal website.

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    // penjelasan: Menampilkan halaman login.

    Route::post('/login', [LoginController::class, 'login'])->name('login.process');
    // penjelasan: Memproses form login.
});

Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');
// penjelasan: Memproses logout user yang sudah login.

Route::middleware(['auth', 'role:super_admin'])
    ->prefix('super-admin')
    ->name('super-admin.')
    ->group(function () {
        Route::get('/dashboard', function () {
            return view('admin.pages.dashboard.super-admin.index');
        })->name('dashboard');
        // penjelasan: Dashboard super admin.

        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::get('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
        Route::put('/users/{user}/reset-password', [UserController::class, 'updatePassword'])->name('users.update-password');
        Route::patch('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
        // penjelasan: Route manajemen user khusus super admin.

        Route::get('/pegawai', [PegawaiController::class, 'index'])->name('pegawai.index');
        Route::get('/pegawai/create', [PegawaiController::class, 'create'])->name('pegawai.create');
        Route::post('/pegawai', [PegawaiController::class, 'store'])->name('pegawai.store');
        Route::get('/pegawai/{pegawai}', [PegawaiController::class, 'show'])->name('pegawai.show');
        Route::get('/pegawai/{pegawai}/edit', [PegawaiController::class, 'edit'])->name('pegawai.edit');
        Route::put('/pegawai/{pegawai}', [PegawaiController::class, 'update'])->name('pegawai.update');
        Route::patch('/pegawai/{pegawai}/toggle-status', [PegawaiController::class, 'toggleStatus'])->name('pegawai.toggle-status');
        // penjelasan: Route data pegawai untuk super admin.

        Route::get('/kelas', [KelasController::class, 'index'])->name('kelas.index');
        Route::get('/kelas/create', [KelasController::class, 'create'])->name('kelas.create');
        Route::post('/kelas', [KelasController::class, 'store'])->name('kelas.store');
        Route::get('/kelas/{kelas}', [KelasController::class, 'show'])->name('kelas.show');
        Route::get('/kelas/{kelas}/edit', [KelasController::class, 'edit'])->name('kelas.edit');
        Route::put('/kelas/{kelas}', [KelasController::class, 'update'])->name('kelas.update');
        Route::patch('/kelas/{kelas}/toggle-status', [KelasController::class, 'toggleStatus'])->name('kelas.toggle-status');
        // penjelasan: Route data kelas untuk super admin.
    });

Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', function () {
            return view('admin.pages.dashboard.admin.index');
        })->name('dashboard');
        // penjelasan: Dashboard admin.

        Route::get('/pegawai', [PegawaiController::class, 'index'])->name('pegawai.index');
        Route::get('/pegawai/create', [PegawaiController::class, 'create'])->name('pegawai.create');
        Route::post('/pegawai', [PegawaiController::class, 'store'])->name('pegawai.store');
        Route::get('/pegawai/{pegawai}', [PegawaiController::class, 'show'])->name('pegawai.show');
        Route::get('/pegawai/{pegawai}/edit', [PegawaiController::class, 'edit'])->name('pegawai.edit');
        Route::put('/pegawai/{pegawai}', [PegawaiController::class, 'update'])->name('pegawai.update');
        Route::patch('/pegawai/{pegawai}/toggle-status', [PegawaiController::class, 'toggleStatus'])->name('pegawai.toggle-status');
        // penjelasan: Route data pegawai untuk admin.

        Route::get('/kelas', [KelasController::class, 'index'])->name('kelas.index');
        Route::get('/kelas/create', [KelasController::class, 'create'])->name('kelas.create');
        Route::post('/kelas', [KelasController::class, 'store'])->name('kelas.store');
        Route::get('/kelas/{kelas}', [KelasController::class, 'show'])->name('kelas.show');
        Route::get('/kelas/{kelas}/edit', [KelasController::class, 'edit'])->name('kelas.edit');
        Route::put('/kelas/{kelas}', [KelasController::class, 'update'])->name('kelas.update');
        Route::patch('/kelas/{kelas}/toggle-status', [KelasController::class, 'toggleStatus'])->name('kelas.toggle-status');
        // penjelasan: Route data kelas untuk admin.
    });

Route::middleware(['auth', 'role:guru'])
    ->prefix('guru')
    ->name('guru.')
    ->group(function () {
        Route::get('/dashboard', function () {
            return view('admin.pages.dashboard.guru.index');
        })->name('dashboard');
        // penjelasan: Dashboard guru.
    });

Route::middleware(['auth', 'role:staff'])
    ->prefix('staff')
    ->name('staff.')
    ->group(function () {
        Route::get('/dashboard', function () {
            return view('admin.pages.dashboard.staff.index');
        })->name('dashboard');
        // penjelasan: Dashboard staff.
    });
