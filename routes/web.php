<?php

// penjelasan: File ini mengatur semua route atau URL pada aplikasi Laravel.
// penjelasan: Route public, login, logout, dashboard role, dan manajemen user diatur di file ini.

// penjelasan: LoginController digunakan untuk halaman login, proses login, dan logout.
use App\Http\Controllers\Auth\LoginController;

// penjelasan: UserController digunakan untuk fitur manajemen user oleh super admin.
use App\Http\Controllers\SuperAdmin\UserController;

// penjelasan: Route adalah fitur bawaan Laravel untuk membuat alamat URL.
use Illuminate\Support\Facades\Route;

// penjelasan: Route halaman utama public.
// penjelasan: Saat membuka / maka Laravel menampilkan welcome.blade.php.
Route::get('/', function () {
    return view('welcome');
})->name('public.home');

// penjelasan: Group guest hanya bisa dibuka oleh user yang belum login.
Route::middleware('guest')->group(function () {

    // penjelasan: Route ini menampilkan halaman login.
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');

    // penjelasan: Route ini memproses form login.
    Route::post('/login', [LoginController::class, 'login'])->name('login.process');
});

// penjelasan: Route logout hanya bisa dipakai user yang sudah login.
Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// penjelasan: Group route khusus super admin.
// penjelasan: auth memastikan user sudah login.
// penjelasan: role:super_admin memastikan hanya super admin yang bisa membuka route ini.
Route::middleware(['auth', 'role:super_admin'])
    ->prefix('super-admin')
    ->name('super-admin.')
    ->group(function () {

        // penjelasan: Dashboard super admin.
        Route::get('/dashboard', function () {
            return view('admin.pages.dashboard.super-admin.index');
        })->name('dashboard');

        // penjelasan: Route daftar user.
        Route::get('/users', [UserController::class, 'index'])->name('users.index');

        // penjelasan: Route form tambah user.
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create');

        // penjelasan: Route menyimpan user baru.
        Route::post('/users', [UserController::class, 'store'])->name('users.store');

        // penjelasan: Route form edit user.
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');

        // penjelasan: Route update data user.
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');

        // penjelasan: Route form reset password user.
        Route::get('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');

        // penjelasan: Route menyimpan password baru user.
        Route::put('/users/{user}/reset-password', [UserController::class, 'updatePassword'])->name('users.update-password');

        // penjelasan: Route mengubah status aktif/nonaktif user.
        Route::patch('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
    });

// penjelasan: Group route khusus admin.
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // penjelasan: Dashboard admin.
        Route::get('/dashboard', function () {
            return view('admin.pages.dashboard.admin.index');
        })->name('dashboard');
    });

// penjelasan: Group route khusus guru.
Route::middleware(['auth', 'role:guru'])
    ->prefix('guru')
    ->name('guru.')
    ->group(function () {

        // penjelasan: Dashboard guru.
        Route::get('/dashboard', function () {
            return view('admin.pages.dashboard.guru.index');
        })->name('dashboard');
    });

// penjelasan: Group route khusus staff.
Route::middleware(['auth', 'role:staff'])
    ->prefix('staff')
    ->name('staff.')
    ->group(function () {

        // penjelasan: Dashboard staff.
        Route::get('/dashboard', function () {
            return view('admin.pages.dashboard.staff.index');
        })->name('dashboard');
    });
