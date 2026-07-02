<?php

// penjelasan: File ini adalah controller untuk menangani login dan logout.
// penjelasan: Controller adalah penghubung antara route, proses sistem, dan view.
// penjelasan: Controller ini mengatur validasi login, alert login gagal, alert akun nonaktif, dan redirect dashboard sesuai role.

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
// penjelasan: Controller adalah class dasar bawaan Laravel untuk membuat controller.

use Illuminate\Http\Request;
// penjelasan: Request digunakan untuk mengambil data yang dikirim dari form login.

use Illuminate\Support\Facades\Auth;
// penjelasan: Auth adalah fitur bawaan Laravel untuk proses login, logout, dan mengecek user.

use Illuminate\Validation\ValidationException;
// penjelasan: ValidationException digunakan untuk menampilkan pesan error validasi login.

class LoginController extends Controller
{
    /**
     * penjelasan: Method ini menampilkan halaman login.
     * penjelasan: Method ini dipanggil oleh route GET /login.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * penjelasan: Method ini memproses login user.
     * penjelasan: Method ini dipanggil oleh form login melalui route POST /login.
     */
    public function login(Request $request)
    {
        // penjelasan: Validasi memastikan email dan password wajib diisi.
        // penjelasan: Pesan dibuat Bahasa Indonesia agar tampilan validasi lebih jelas.
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
        ]);

        // penjelasan: Credentials adalah data email dan password dari form.
        // penjelasan: Data ini digunakan untuk mencocokkan akun di tabel users.
        $credentials = $request->only('email', 'password');

        // penjelasan: Auth::attempt mencoba login menggunakan email dan password.
        // penjelasan: Jika gagal, user dikembalikan ke login dengan alert Login gagal.
        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withErrors([
                    'email' => 'Email atau password salah. Periksa kembali data login Anda.',
                ])
                ->with('login_error_type', 'failed')
                ->onlyInput('email');
        }

        // penjelasan: regenerate digunakan untuk memperbarui session setelah login.
        // penjelasan: Ini penting untuk keamanan session user.
        $request->session()->regenerate();

        // penjelasan: Mengambil data user yang berhasil login.
        $user = Auth::user();

        // penjelasan: Jika akun user nonaktif, sistem langsung logout dan menolak akses.
        // penjelasan: Alert yang muncul adalah Akun nonaktif.
        if ($user->status !== 'aktif') {
            Auth::logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()
                ->withErrors([
                    'email' => 'Akun Anda sedang nonaktif. Silakan hubungi administrator.',
                ])
                ->with('login_error_type', 'inactive')
                ->onlyInput('email');
        }

        // penjelasan: Menyimpan waktu terakhir user login ke kolom last_login_at.
        $user->update([
            'last_login_at' => now(),
        ]);

        // penjelasan: Setelah login berhasil, user diarahkan ke dashboard sesuai role.
        return redirect()->intended($this->redirectByRole($user->role));
    }

    /**
     * penjelasan: Method ini digunakan untuk logout.
     * penjelasan: Method ini dipanggil oleh tombol logout di dashboard.
     */
    public function logout(Request $request)
    {
        // penjelasan: Auth::logout menghapus status login user.
        Auth::logout();

        // penjelasan: invalidate menghapus session lama.
        $request->session()->invalidate();

        // penjelasan: regenerateToken membuat token keamanan baru setelah logout.
        $request->session()->regenerateToken();

        // penjelasan: Setelah logout, user diarahkan kembali ke halaman login.
        return redirect()->route('login')->with('success', 'Anda berhasil logout.');
    }

    /**
     * penjelasan: Method private ini menentukan tujuan dashboard berdasarkan role user.
     * penjelasan: Method ini hanya dipakai di dalam LoginController.
     */
    private function redirectByRole(string $role): string
    {
        return match ($role) {
            'super_admin' => route('super-admin.dashboard'),
            'admin' => route('admin.dashboard'),
            'guru' => route('guru.dashboard'),
            'staff' => route('staff.dashboard'),
            default => route('login'),
        };
    }
}
