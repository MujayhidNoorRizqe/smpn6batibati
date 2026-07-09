<?php

// penjelasan: File ini adalah controller untuk fitur Manajemen User.
// penjelasan: Role aktif pada sistem hanya super_admin, admin, dan guru.
// penjelasan: Role staff sudah tidak bisa dibuat, tidak bisa diedit, dan tidak ditampilkan di daftar user.

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query()
            ->whereIn('role', ['super_admin', 'admin', 'guru']);

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('role')) {
            $query->whereIn('role', ['super_admin', 'admin', 'guru'])
                ->where('role', $request->role);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $users = $query->latest()->paginate(10)->withQueryString();

        return view('admin.pages.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.pages.users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', Rule::in(['admin', 'guru'])],
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
            'role.in' => 'Role yang dipilih tidak valid. Role yang tersedia hanya Admin dan Guru.',
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

    public function edit(User $user)
    {
        if ($user->role === 'staff') {
            return redirect()
                ->route('super-admin.users.index')
                ->with('error', 'User staff sudah dinonaktifkan dari sistem dan tidak bisa diedit.');
        }

        return view('admin.pages.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        if ($user->role === 'staff') {
            return redirect()
                ->route('super-admin.users.index')
                ->with('error', 'User staff sudah dinonaktifkan dari sistem dan tidak bisa diperbarui.');
        }

        $allowedRoles = $user->role === 'super_admin'
            ? ['super_admin']
            : ['admin', 'guru'];

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
            'role.in' => 'Role yang dipilih tidak valid. Role yang tersedia hanya Admin dan Guru.',
            'status.required' => 'Status wajib dipilih.',
            'status.in' => 'Status yang dipilih tidak valid.',
        ]);

        $validated['name'] = trim($validated['name']);
        $validated['email'] = strtolower(trim($validated['email']));

        if ($user->role === 'super_admin') {
            $validated['role'] = 'super_admin';
        }

        if ($user->id === Auth::id()) {
            $validated['status'] = 'aktif';
        }

        if ($user->role === 'super_admin') {
            $validated['status'] = 'aktif';
        }

        $user->update($validated);

        return redirect()
            ->route('super-admin.users.index')
            ->with('success', 'User berhasil diperbarui.');
    }

    public function resetPassword(User $user)
    {
        if ($user->role === 'staff') {
            return redirect()
                ->route('super-admin.users.index')
                ->with('error', 'User staff sudah dinonaktifkan dari sistem dan password tidak bisa direset.');
        }

        return view('admin.pages.users.reset-password', compact('user'));
    }

    public function updatePassword(Request $request, User $user)
    {
        if ($user->role === 'staff') {
            return redirect()
                ->route('super-admin.users.index')
                ->with('error', 'User staff sudah dinonaktifkan dari sistem dan password tidak bisa diperbarui.');
        }

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

    public function toggleStatus(User $user)
    {
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Anda tidak bisa menonaktifkan akun sendiri.');
        }

        if ($user->role === 'super_admin') {
            return back()->with('error', 'Akun super admin tidak bisa dinonaktifkan dari halaman ini.');
        }

        if ($user->role === 'staff') {
            $user->update([
                'status' => 'nonaktif',
            ]);

            return back()->with('success', 'User staff berhasil dinonaktifkan.');
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
