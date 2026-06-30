<?php

// penjelasan: File ini digunakan untuk mengatur semua URL atau route website Laravel.
// penjelasan: Route adalah alamat yang dibuka di browser, misalnya /login atau /super-admin/dashboard.

// penjelasan: LoginController dipakai untuk mengatur halaman login, proses login, dan logout.
use App\Http\Controllers\Auth\LoginController;

// penjelasan: Route adalah fitur bawaan Laravel untuk membuat alamat URL.
use Illuminate\Support\Facades\Route;

// penjelasan: Route ini adalah halaman utama website public.
// penjelasan: Saat user membuka http://127.0.0.1:8000/, Laravel akan menampilkan file resources/views/welcome.blade.php.
Route::get('/', function () {
    return view('welcome');
})->name('public.home');

// penjelasan: Middleware guest artinya route ini hanya boleh dibuka oleh user yang belum login.
// penjelasan: Jika user sudah login, halaman login tidak perlu dibuka lagi.
Route::middleware('guest')->group(function () {

    // penjelasan: Route GET /login digunakan untuk menampilkan halaman login.
    // penjelasan: Route ini memanggil method showLoginForm() di LoginController.
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');

    // penjelasan: Route POST /login digunakan untuk memproses data login dari form.
    // penjelasan: Route ini dipanggil saat tombol Login diklik.
    Route::post('/login', [LoginController::class, 'login'])->name('login.process');
});

// penjelasan: Route POST /logout digunakan untuk keluar dari sistem.
// penjelasan: Middleware auth artinya route ini hanya bisa digunakan oleh user yang sudah login.
Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// penjelasan: Group route ini khusus untuk role super_admin.
// penjelasan: Prefix super-admin membuat URL menjadi /super-admin/dashboard.
// penjelasan: name super-admin. membuat nama route menjadi super-admin.dashboard.
Route::middleware(['auth', 'role:super_admin'])->prefix('super-admin')->name('super-admin.')->group(function () {

    // penjelasan: Route ini menampilkan dashboard super admin.
    // penjelasan: View yang dipanggil ada di resources/views/admin/pages/dashboard/super-admin/index.blade.php.
    Route::get('/dashboard', function () {
        return view('admin.pages.dashboard.super-admin.index');
    })->name('dashboard');
});

// penjelasan: Group route ini khusus untuk role admin.
// penjelasan: URL dashboard admin adalah /admin/dashboard.
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {

    // penjelasan: Route ini menampilkan dashboard admin.
    Route::get('/dashboard', function () {
        return view('admin.pages.dashboard.admin.index');
    })->name('dashboard');
});

// penjelasan: Group route ini khusus untuk role guru.
// penjelasan: URL dashboard guru adalah /guru/dashboard.
Route::middleware(['auth', 'role:guru'])->prefix('guru')->name('guru.')->group(function () {

    // penjelasan: Route ini menampilkan dashboard guru.
    Route::get('/dashboard', function () {
        return view('admin.pages.dashboard.guru.index');
    })->name('dashboard');
});

// penjelasan: Group route ini khusus untuk role staff.
// penjelasan: URL dashboard staff adalah /staff/dashboard.
Route::middleware(['auth', 'role:staff'])->prefix('staff')->name('staff.')->group(function () {

    // penjelasan: Route ini menampilkan dashboard staff.
    Route::get('/dashboard', function () {
        return view('admin.pages.dashboard.staff.index');
    })->name('dashboard');
});
