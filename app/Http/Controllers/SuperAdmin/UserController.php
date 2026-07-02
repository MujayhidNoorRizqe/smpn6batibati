<?php

// penjelasan: File ini adalah controller untuk fitur Manajemen User.
// penjelasan: Controller ini hanya dipakai oleh Super Admin.
// penjelasan: File ini dipanggil dari route dengan prefix /super-admin/users.
// penjelasan: Controller ini memakai fitur bawaan Laravel seperti Request, Hash, Auth, dan Model User.
// penjelasan: Controller ini mengatur daftar user, tambah user, edit user, reset password, dan aktif/nonaktif user.

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
// penjelasan: Controller adalah class dasar bawaan Laravel.

use App\Models\User;
// penjelasan: Model User digunakan untuk mengambil, membuat, dan mengubah data pada tabel users.

use Illuminate\Http\Request;
// penjelasan: Request digunakan untuk mengambil data dari form tambah/edit/reset password.

use Illuminate\Support\Facades\Auth;
// penjelasan: Auth digunakan untuk mengambil user yang sedang login.

use Illuminate\Support\Facades\Hash;
// penjelasan: Hash digunakan untuk mengubah password biasa menjadi password hash sebelum disimpan.

use Illuminate\Validation\Rule;
// penjelasan: Rule digunakan untuk validasi pilihan role/status dan unique email saat update data.

class UserController extends Controller
{
    /**
     * penjelasan: Method index digunakan untuk menampilkan daftar user.
     * penjelasan: Method ini dipanggil oleh route GET /super-admin/users.
     */
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $users = $query->latest()->paginate(10)->withQueryString();

        return view('admin.pages.users.index', compact('users'));
    }

    /**
     * penjelasan: Method create digunakan untuk menampilkan form tambah user.
     * penjelasan: Method ini dipanggil oleh route GET /super-admin/users/create.
     */
    public function create()
    {
        return view('admin.pages.users.create');
    }

    /**
     * penjelasan: Method store digunakan untuk menyimpan user baru.
     * penjelasan: Method ini dipanggil oleh form tambah user melalui route POST /super-admin/users.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', Rule::in(['admin', 'guru', 'staff'])],
            'status' => ['required', Rule::in(['aktif', 'nonaktif'])],
        ], [
            'name.required' => 'Nama user wajib diisi.',
            'name.max' => 'Nama user maksimal 100 karakter.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.max' => 'Email maksimal 150 karakter.',
            'email.unique' => 'Email sudah digunakan.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak sama.',
            'role.required' => 'Role wajib dipilih.',
            'role.in' => 'Role yang dipilih tidak valid.',
            'status.required' => 'Status wajib dipilih.',
            'status.in' => 'Status yang dipilih tidak valid.',
        ]);

        $validated['name'] = trim($validated['name']);
        $validated['email'] = strtolower(trim($validated['email']));
        $validated['password'] = Hash::make($validated['password']);

        User::create($validated);

        return redirect()
            ->route('super-admin.users.index')
            ->with('success', 'User berhasil ditambahkan.');
    }

    /**
     * penjelasan: Method edit digunakan untuk menampilkan form edit user.
     * penjelasan: Parameter User $user otomatis diambil dari id user pada URL.
     */
    public function edit(User $user)
    {
        return view('admin.pages.users.edit', compact('user'));
    }

    /**
     * penjelasan: Method update digunakan untuk menyimpan perubahan data user.
     * penjelasan: Method ini dipanggil oleh form edit user melalui route PUT /super-admin/users/{user}.
     */
    public function update(Request $request, User $user)
    {
        $allowedRoles = $user->role === 'super_admin'
            ? ['super_admin']
            : ['admin', 'guru', 'staff'];

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => [
                'required',
                'email',
                'max:150',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'role' => ['required', Rule::in($allowedRoles)],
            'status' => ['required', Rule::in(['aktif', 'nonaktif'])],
        ], [
            'name.required' => 'Nama user wajib diisi.',
            'name.max' => 'Nama user maksimal 100 karakter.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.max' => 'Email maksimal 150 karakter.',
            'email.unique' => 'Email sudah digunakan.',
            'role.required' => 'Role wajib dipilih.',
            'role.in' => 'Role yang dipilih tidak valid.',
            'status.required' => 'Status wajib dipilih.',
            'status.in' => 'Status yang dipilih tidak valid.',
        ]);

        $validated['name'] = trim($validated['name']);
        $validated['email'] = strtolower(trim($validated['email']));

        // penjelasan: Akun super admin tidak boleh diubah role-nya dari halaman ini.
        if ($user->role === 'super_admin') {
            $validated['role'] = 'super_admin';
        }

        // penjelasan: User yang sedang login tidak boleh menonaktifkan akunnya sendiri.
        if ($user->id === Auth::id()) {
            $validated['status'] = 'aktif';
        }

        // penjelasan: Akun super admin juga dipaksa tetap aktif agar sistem tidak kehilangan akses utama.
        if ($user->role === 'super_admin') {
            $validated['status'] = 'aktif';
        }

        $user->update($validated);

        return redirect()
            ->route('super-admin.users.index')
            ->with('success', 'User berhasil diperbarui.');
    }

    /**
     * penjelasan: Method resetPassword digunakan untuk menampilkan form reset password.
     * penjelasan: Password lama tidak pernah ditampilkan demi keamanan.
     */
    public function resetPassword(User $user)
    {
        return view('admin.pages.users.reset-password', compact('user'));
    }

    /**
     * penjelasan: Method updatePassword digunakan untuk menyimpan password baru.
     * penjelasan: Method ini dipanggil oleh form reset password.
     */
    public function updatePassword(Request $request, User $user)
    {
        $validated = $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'password.required' => 'Password baru wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak sama.',
        ]);

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()
            ->route('super-admin.users.index')
            ->with('success', 'Password user berhasil direset.');
    }

    /**
     * penjelasan: Method toggleStatus digunakan untuk mengubah status aktif/nonaktif user.
     * penjelasan: Method ini dipanggil dari tombol aktif/nonaktif di daftar user.
     */
    public function toggleStatus(User $user)
    {
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Anda tidak bisa menonaktifkan akun sendiri.');
        }

        if ($user->role === 'super_admin') {
            return back()->with('error', 'Akun super admin tidak bisa dinonaktifkan dari halaman ini.');
        }

        $newStatus = $user->status === 'aktif' ? 'nonaktif' : 'aktif';

        $user->update([
            'status' => $newStatus,
        ]);

        $message = $newStatus === 'aktif'
            ? 'User berhasil diaktifkan.'
            : 'User berhasil dinonaktifkan.';

        return back()->with('success', $message);
    }
}
