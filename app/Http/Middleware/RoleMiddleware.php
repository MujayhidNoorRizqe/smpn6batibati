<?php

// penjelasan: File ini adalah middleware role.
// penjelasan: Middleware ini berfungsi untuk membatasi akses halaman berdasarkan role user.

namespace App\Http\Middleware;

// penjelasan: Closure digunakan untuk melanjutkan request jika user lolos pengecekan.
use Closure;

// penjelasan: Request digunakan untuk membaca data request dari user.
use Illuminate\Http\Request;

// penjelasan: Auth digunakan untuk mengecek user sedang login atau tidak.
use Illuminate\Support\Facades\Auth;

// penjelasan: Response digunakan sebagai tipe hasil dari middleware.
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    // penjelasan: Method handle() otomatis dipanggil Laravel saat middleware digunakan di route.
    // penjelasan: Parameter ...$roles berisi daftar role yang diizinkan membuka route.
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // penjelasan: Jika user belum login, diarahkan ke halaman login.
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        // penjelasan: Mengambil data user yang sedang login.
        $user = Auth::user();

        // penjelasan: Jika akun user nonaktif, user dipaksa logout.
        if ($user->status !== 'aktif') {
            Auth::logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors([
                'email' => 'Akun Anda sedang nonaktif. Silakan hubungi administrator.',
            ]);
        }

        // penjelasan: Jika role user tidak ada dalam daftar role yang diizinkan, tampilkan error 403.
        // penjelasan: Contoh, staff tidak boleh membuka halaman guru atau admin.
        if (! in_array($user->role, $roles, true)) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        // penjelasan: Jika semua pengecekan lolos, request dilanjutkan ke halaman tujuan.
        return $next($request);
    }
}
