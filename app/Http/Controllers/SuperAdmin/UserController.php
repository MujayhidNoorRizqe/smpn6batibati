<?php

// penjelasan: File ini adalah controller untuk fitur Manajemen User.
// penjelasan: Controller ini hanya dipakai oleh Super Admin.
// penjelasan: File ini dipanggil dari route dengan prefix /super-admin/users.
// penjelasan: Controller ini memakai fitur bawaan Laravel seperti Request, Hash, Auth, dan Model User.

namespace App\Http\Controllers\SuperAdmin;

// penjelasan: Controller adalah class dasar bawaan Laravel.
use App\Http\Controllers\Controller;

// penjelasan: Model User digunakan untuk mengambil, membuat, dan mengubah data pada tabel users.
use App\Models\User;

// penjelasan: Request digunakan untuk mengambil data dari form tambah/edit/reset password.
use Illuminate\Http\Request;

// penjelasan: Auth digunakan untuk mengambil user yang sedang login.
use Illuminate\Support\Facades\Auth;

// penjelasan: Hash digunakan untuk mengubah password biasa menjadi password hash sebelum disimpan.
use Illuminate\Support\Facades\Hash;

// penjelasan: Rule digunakan untuk validasi unique email saat update data.
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    // penjelasan: Method index digunakan untuk menampilkan daftar user.
    // penjelasan: Method ini dipanggil oleh route GET /super-admin/users.
    public function index(Request $request)
    {
        // penjelasan: query digunakan untuk memulai pencarian data user.
        $query = User::query();

        // penjelasan: Jika ada keyword pencarian, sistem mencari berdasarkan nama atau email.
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        // penjelasan: Jika ada filter role, sistem hanya menampilkan user sesuai role.
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // penjelasan: Jika ada filter status, sistem hanya menampilkan user sesuai status.
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // penjelasan: Data user diurutkan dari yang terbaru dan dibuat pagination 10 data per halaman.
        $users = $query->latest()->paginate(10)->withQueryString();

        // penjelasan: View ini menampilkan halaman daftar user.
        return view('admin.pages.users.index', compact('users'));
    }

    // penjelasan: Method create digunakan untuk menampilkan form tambah user.
    // penjelasan: Method ini dipanggil oleh route GET /super-admin/users/create.
    public function create()
    {
        return view('admin.pages.users.create');
    }

    // penjelasan: Method store digunakan untuk menyimpan user baru.
    // penjelasan: Method ini dipanggil oleh form tambah user melalui route POST /super-admin/users.
    public function store(Request $request)
    {
        // penjelasan: Validasi memastikan data yang masuk sesuai aturan.
        // penjelasan: confirmed berarti password harus sama dengan password_confirmation.
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', Rule::in(['admin', 'guru', 'staff'])],
            'status' => ['required', Rule::in(['aktif', 'nonaktif'])],
        ], [
            'name.required' => 'Nama user wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak sama.',
            'role.required' => 'Role wajib dipilih.',
            'status.required' => 'Status wajib dipilih.',
        ]);

        // penjelasan: Password di-hash agar tidak tersimpan sebagai teks asli.
        $validated['password'] = Hash::make($validated['password']);

        // penjelasan: Membuat data user baru ke tabel users.
        User::create($validated);

        // penjelasan: Setelah berhasil, user diarahkan kembali ke daftar user.
        return redirect()
            ->route('super-admin.users.index')
            ->with('success', 'User berhasil ditambahkan.');
    }

    // penjelasan: Method edit digunakan untuk menampilkan form edit user.
    // penjelasan: Parameter User $user otomatis diambil dari id user pada URL.
    public function edit(User $user)
    {
        return view('admin.pages.users.edit', compact('user'));
    }

    // penjelasan: Method update digunakan untuk menyimpan perubahan data user.
    // penjelasan: Method ini dipanggil oleh form edit user melalui route PUT /super-admin/users/{user}.
    public function update(Request $request, User $user)
    {
        // penjelasan: Validasi email unique mengabaikan email milik user yang sedang diedit.
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => [
                'required',
                'email',
                'max:150',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'role' => ['required', Rule::in(['admin', 'guru', 'staff'])],
            'status' => ['required', Rule::in(['aktif', 'nonaktif'])],
        ], [
            'name.required' => 'Nama user wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan.',
            'role.required' => 'Role wajib dipilih.',
            'status.required' => 'Status wajib dipilih.',
        ]);

        // penjelasan: Akun super admin tidak boleh diubah role-nya dari halaman ini.
        if ($user->role === 'super_admin') {
            $validated['role'] = 'super_admin';
        }

        // penjelasan: User yang sedang login tidak boleh menonaktifkan akunnya sendiri.
        if ($user->id === Auth::id()) {
            $validated['status'] = 'aktif';
        }

        // penjelasan: Update data user ke tabel users.
        $user->update($validated);

        return redirect()
            ->route('super-admin.users.index')
            ->with('success', 'User berhasil diperbarui.');
    }

    // penjelasan: Method resetPassword digunakan untuk menampilkan form reset password.
    // penjelasan: Password lama tidak pernah ditampilkan demi keamanan.
    public function resetPassword(User $user)
    {
        return view('admin.pages.users.reset-password', compact('user'));
    }

    // penjelasan: Method updatePassword digunakan untuk menyimpan password baru.
    // penjelasan: Method ini dipanggil oleh form reset password.
    public function updatePassword(Request $request, User $user)
    {
        // penjelasan: Validasi password baru.
        $validated = $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'password.required' => 'Password baru wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak sama.',
        ]);

        // penjelasan: Password baru di-hash sebelum disimpan.
        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()
            ->route('super-admin.users.index')
            ->with('success', 'Password user berhasil direset.');
    }

    // penjelasan: Method toggleStatus digunakan untuk mengubah status aktif/nonaktif user.
    // penjelasan: Method ini dipanggil dari tombol aktif/nonaktif di daftar user.
    public function toggleStatus(User $user)
    {
        // penjelasan: Super admin tidak boleh menonaktifkan akunnya sendiri.
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Anda tidak bisa menonaktifkan akun sendiri.');
        }

        // penjelasan: Akun super admin utama tidak boleh dinonaktifkan dari tombol ini.
        if ($user->role === 'super_admin') {
            return back()->with('error', 'Akun super admin tidak bisa dinonaktifkan dari halaman ini.');
        }

        // penjelasan: Jika status aktif maka diubah menjadi nonaktif, dan sebaliknya.
        $user->update([
            'status' => $user->status === 'aktif' ? 'nonaktif' : 'aktif',
        ]);

        return back()->with('success', 'Status user berhasil diubah.');
    }
}
