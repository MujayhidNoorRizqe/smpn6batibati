<?php

// penjelasan: File ini adalah konfigurasi utama untuk menjalankan aplikasi Laravel 11.
// penjelasan: Di Laravel 11, pendaftaran route, middleware, dan error handler dilakukan di file bootstrap/app.php.
// penjelasan: File ini berbeda dengan config/app.php. File config/app.php berisi konfigurasi nama aplikasi, timezone, locale, dan APP_KEY.

// penjelasan: RoleMiddleware adalah middleware buatan kita.
// penjelasan: Middleware ini dipakai untuk membatasi akses halaman berdasarkan role user, misalnya super_admin, admin, guru, dan staff.
use App\Http\Middleware\RoleMiddleware;

// penjelasan: Application adalah class utama Laravel yang digunakan untuk membangun aplikasi.
use Illuminate\Foundation\Application;

// penjelasan: Exceptions digunakan untuk mengatur penanganan error aplikasi.
use Illuminate\Foundation\Configuration\Exceptions;

// penjelasan: Middleware digunakan untuk mendaftarkan middleware ke aplikasi Laravel.
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))

    // penjelasan: Bagian ini mengatur file route yang digunakan Laravel.
    // penjelasan: web.php adalah file route utama untuk halaman website.
    // penjelasan: console.php adalah file route untuk perintah console Laravel.
    // penjelasan: health '/up' adalah route bawaan Laravel untuk mengecek aplikasi hidup atau tidak.
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )

    // penjelasan: Bagian ini digunakan untuk mendaftarkan middleware.
    // penjelasan: Middleware adalah penyaring request sebelum user masuk ke halaman tertentu.
    ->withMiddleware(function (Middleware $middleware) {

        // penjelasan: Di sini kita membuat alias middleware bernama 'role'.
        // penjelasan: Alias 'role' ini akan dipakai di routes/web.php.
        // penjelasan: Contoh penggunaan di route: middleware(['auth', 'role:super_admin']).
        // penjelasan: Artinya halaman tersebut hanya boleh dibuka oleh user login dengan role super_admin.
        $middleware->alias([
            'role' => RoleMiddleware::class,
        ]);
    })

    // penjelasan: Bagian ini digunakan untuk konfigurasi error Laravel.
    // penjelasan: Untuk sekarang dikosongkan karena kita belum membuat pengaturan error khusus.
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })

    // penjelasan: create() digunakan untuk membuat dan menjalankan aplikasi Laravel berdasarkan konfigurasi di atas.
    ->create();
