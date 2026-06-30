<?php

// penjelasan: File ini adalah controller untuk menangani login dan logout.
// penjelasan: Controller adalah penghubung antara route, proses sistem, dan view.

namespace App\Http\Controllers\Auth;

// penjelasan: Controller adalah class dasar bawaan Laravel untuk membuat controller.
use App\Http\Controllers\Controller;

// penjelasan: Request digunakan untuk mengambil data yang dikirim dari form login.
use Illuminate\Http\Request;

// penjelasan: Auth adalah fitur bawaan Laravel untuk proses login, logout, dan mengecek user.
use Illuminate\Support\Facades\Auth;

// penjelasan: ValidationException digunakan untuk menampilkan pesan error validasi login.
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    // penjelasan: Method ini menampilkan halaman login.
    // penjelasan: Method ini dipanggil oleh route GET /login.
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // penjelasan: Method ini memproses login user.
    // penjelasan: Method ini dipanggil oleh form login melalui route POST /login.
    public function login(Request $request)
    {
        // penjelasan: Validasi memastikan email dan password wajib diisi.
        // penjelasan: Jika kosong atau format email salah, Laravel akan menampilkan error.
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
        // penjelasan: Jika gagal, pesan error akan ditampilkan.
        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => 'Email atau password salah.',
            ]);
        }

        // penjelasan: regenerate digunakan untuk memperbarui session setelah login.
        // penjelasan: Ini penting untuk keamanan session user.
        $request->session()->regenerate();

        // penjelasan: Mengambil data user yang berhasil login.
        $user = Auth::user();

        // penjelasan: Jika akun user nonaktif, sistem langsung logout dan menolak akses.
        if ($user->status !== 'aktif') {
            Auth::logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()->withErrors([
                'email' => 'Akun Anda sedang nonaktif. Silakan hubungi administrator.',
            ])->onlyInput('email');
        }

        // penjelasan: Menyimpan waktu terakhir user login ke kolom last_login_at.
        $user->update([
            'last_login_at' => now(),
        ]);

        // penjelasan: Setelah login berhasil, user diarahkan ke dashboard sesuai role.
        return redirect()->intended($this->redirectByRole($user->role));
    }

    // penjelasan: Method ini digunakan untuk logout.
    // penjelasan: Method ini dipanggil oleh tombol logout di dashboard.
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

    // penjelasan: Method private ini menentukan tujuan dashboard berdasarkan role user.
    // penjelasan: Method ini hanya dipakai di dalam LoginController.
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
