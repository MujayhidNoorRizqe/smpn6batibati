<?php

// penjelasan: File ini adalah controller untuk modul Data Pegawai.
// penjelasan: Controller ini dipakai oleh Super Admin dan Admin.
// penjelasan: Sistem saat ini hanya memakai jenis pegawai Guru.
// penjelasan: Jenis pegawai Staff sudah tidak ditampilkan dan tidak bisa ditambahkan dari sistem.
// penjelasan: Jika akun login dipilih, nama pegawai otomatis mengikuti nama akun login tersebut.
// penjelasan: Controller ini mengatur daftar pegawai, tambah pegawai, detail, edit, update, dan ubah status pegawai.
// penjelasan: Controller ini juga mengatur upload foto pegawai ke storage Laravel.

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pegawai;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class PegawaiController extends Controller
{
    private function routePrefix(): string
    {
        return auth()->user()->role === 'super_admin' ? 'super-admin' : 'admin';
    }

    public function index(Request $request)
    {
        $query = Pegawai::with('user')
            ->where('jenis_pegawai', 'guru');

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('nama_pegawai', 'like', '%' . $search . '%')
                    ->orWhere('nip', 'like', '%' . $search . '%')
                    ->orWhere('jabatan', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $pegawais = $query->latest()->paginate(10)->withQueryString();

        $routePrefix = $this->routePrefix();

        return view('admin.pages.pegawai.index', compact('pegawais', 'routePrefix'));
    }

    public function create()
    {
        $users = User::where('role', 'guru')
            ->whereDoesntHave('pegawai')
            ->where('status', 'aktif')
            ->orderBy('name')
            ->get();

        $routePrefix = $this->routePrefix();

        return view('admin.pages.pegawai.create', compact('users', 'routePrefix'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => ['nullable', 'exists:users,id', 'unique:pegawais,user_id'],
            'nip' => ['nullable', 'string', 'max:50', 'unique:pegawais,nip'],
            'nama_pegawai' => ['required_without:user_id', 'nullable', 'string', 'max:150'],
            'jenis_pegawai' => ['required', Rule::in(['guru'])],
            'jabatan' => ['required', 'string', 'max:150'],
            'jenis_kelamin' => ['nullable', Rule::in(['L', 'P'])],
            'no_hp' => ['nullable', 'string', 'max:30'],
            'alamat' => ['required', 'string'],
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'status' => ['required', Rule::in(['aktif', 'nonaktif'])],
        ], $this->validationMessages());

        if (! empty($validated['user_id'])) {
            $user = User::find($validated['user_id']);

            if (! $user || $user->role !== 'guru' || $user->status !== 'aktif') {
                return back()
                    ->withErrors(['user_id' => 'Akun login yang dipilih harus akun guru aktif.'])
                    ->withInput();
            }

            $validated['nama_pegawai'] = $user->name;
            $validated['jenis_pegawai'] = 'guru';
        } else {
            $validated['jenis_pegawai'] = 'guru';
        }

        $validated = $this->normalizeNullableFields($validated);

        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')->store('foto/pegawai', 'public');
        }

        Pegawai::create($validated);

        return redirect()
            ->route($this->routePrefix() . '.pegawai.index')
            ->with('success', 'Data guru berhasil ditambahkan.');
    }

    public function show(Pegawai $pegawai)
    {
        if ($pegawai->jenis_pegawai !== 'guru') {
            return redirect()
                ->route($this->routePrefix() . '.pegawai.index')
                ->with('error', 'Data staff sudah tidak ditampilkan pada sistem.');
        }

        $pegawai->load('user');

        $routePrefix = $this->routePrefix();

        return view('admin.pages.pegawai.show', compact('pegawai', 'routePrefix'));
    }

    public function edit(Pegawai $pegawai)
    {
        if ($pegawai->jenis_pegawai !== 'guru') {
            return redirect()
                ->route($this->routePrefix() . '.pegawai.index')
                ->with('error', 'Data staff sudah tidak bisa diedit pada sistem.');
        }

        $users = User::where('role', 'guru')
            ->where('status', 'aktif')
            ->where(function ($query) use ($pegawai) {
                $query->whereDoesntHave('pegawai')
                    ->orWhere('id', $pegawai->user_id);
            })
            ->orderBy('name')
            ->get();

        $routePrefix = $this->routePrefix();

        return view('admin.pages.pegawai.edit', compact('pegawai', 'users', 'routePrefix'));
    }

    public function update(Request $request, Pegawai $pegawai)
    {
        if ($pegawai->jenis_pegawai !== 'guru') {
            return redirect()
                ->route($this->routePrefix() . '.pegawai.index')
                ->with('error', 'Data staff sudah tidak bisa diperbarui pada sistem.');
        }

        $validated = $request->validate([
            'user_id' => [
                'nullable',
                'exists:users,id',
                Rule::unique('pegawais', 'user_id')->ignore($pegawai->id),
            ],
            'nip' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('pegawais', 'nip')->ignore($pegawai->id),
            ],
            'nama_pegawai' => ['required_without:user_id', 'nullable', 'string', 'max:150'],
            'jenis_pegawai' => ['required', Rule::in(['guru'])],
            'jabatan' => ['nullable', 'string', 'max:150'],
            'jenis_kelamin' => ['nullable', Rule::in(['L', 'P'])],
            'no_hp' => ['nullable', 'string', 'max:30'],
            'alamat' => ['nullable', 'string'],
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'status' => ['required', Rule::in(['aktif', 'nonaktif'])],
        ], $this->validationMessages());

        if (! empty($validated['user_id'])) {
            $user = User::find($validated['user_id']);

            if (! $user || $user->role !== 'guru' || $user->status !== 'aktif') {
                return back()
                    ->withErrors(['user_id' => 'Akun login yang dipilih harus akun guru aktif.'])
                    ->withInput();
            }

            $validated['nama_pegawai'] = $user->name;
            $validated['jenis_pegawai'] = 'guru';
        } else {
            $validated['jenis_pegawai'] = 'guru';
        }

        $validated = $this->normalizeNullableFields($validated);

        if ($request->hasFile('foto')) {
            if ($pegawai->foto && Storage::disk('public')->exists($pegawai->foto)) {
                Storage::disk('public')->delete($pegawai->foto);
            }

            $validated['foto'] = $request->file('foto')->store('foto/pegawai', 'public');
        }

        $pegawai->update($validated);

        return redirect()
            ->route($this->routePrefix() . '.pegawai.index')
            ->with('success', 'Data guru berhasil diperbarui.');
    }

    public function toggleStatus(Pegawai $pegawai)
    {
        if ($pegawai->jenis_pegawai !== 'guru') {
            $pegawai->update([
                'status' => 'nonaktif',
            ]);

            return back()->with('success', 'Data staff lama berhasil dinonaktifkan.');
        }

        $newStatus = $pegawai->status === 'aktif' ? 'nonaktif' : 'aktif';

        $pegawai->update([
            'status' => $newStatus,
        ]);

        $message = $newStatus === 'aktif'
            ? 'Guru berhasil diaktifkan.'
            : 'Guru berhasil dinonaktifkan.';

        return back()->with('success', $message);
    }

    private function validationMessages(): array
    {
        return [
            'user_id.exists' => 'Akun login tidak valid.',
            'user_id.unique' => 'Akun login sudah terhubung dengan data guru lain.',

            'nip.string' => 'NIP harus berupa teks.',
            'nip.max' => 'NIP maksimal 50 karakter.',
            'nip.unique' => 'NIP sudah digunakan.',

            'nama_pegawai.required_without' => 'Nama guru wajib diisi jika belum memilih akun login.',
            'nama_pegawai.string' => 'Nama guru harus berupa teks.',
            'nama_pegawai.max' => 'Nama guru maksimal 150 karakter.',

            'jenis_pegawai.required' => 'Jenis pegawai wajib dipilih.',
            'jenis_pegawai.in' => 'Jenis pegawai yang tersedia hanya Guru.',

            'jabatan.required' => 'Jabatan wajib diisi.',
            'jabatan.string' => 'Jabatan harus berupa teks.',
            'jabatan.max' => 'Jabatan maksimal 150 karakter.',

            'jenis_kelamin.in' => 'Jenis kelamin yang dipilih tidak valid.',

            'no_hp.string' => 'Nomor HP harus berupa teks.',
            'no_hp.max' => 'Nomor HP maksimal 30 karakter.',

            'alamat.required' => 'Alamat wajib diisi.',
            'alamat.string' => 'Alamat harus berupa teks.',

            'foto.image' => 'File foto harus berupa gambar.',
            'foto.mimes' => 'Format foto harus jpg, jpeg, png, atau webp.',
            'foto.max' => 'Ukuran foto maksimal 2MB.',

            'status.required' => 'Status wajib dipilih.',
            'status.in' => 'Status yang dipilih tidak valid.',
        ];
    }

    private function normalizeNullableFields(array $validated): array
    {
        $nullableFields = [
            'user_id',
            'nip',
            'jenis_kelamin',
            'no_hp',
        ];

        foreach ($nullableFields as $field) {
            if (array_key_exists($field, $validated) && $validated[$field] === '') {
                $validated[$field] = null;
            }
        }

        if (isset($validated['nama_pegawai'])) {
            $validated['nama_pegawai'] = trim($validated['nama_pegawai']);
        }

        if (isset($validated['nip']) && $validated['nip'] !== null) {
            $validated['nip'] = trim($validated['nip']);
        }

        if (isset($validated['jabatan']) && $validated['jabatan'] !== null) {
            $validated['jabatan'] = trim($validated['jabatan']);
        }

        if (isset($validated['no_hp']) && $validated['no_hp'] !== null) {
            $validated['no_hp'] = trim($validated['no_hp']);
        }

        if (isset($validated['alamat']) && $validated['alamat'] !== null) {
            $validated['alamat'] = trim($validated['alamat']);
        }

        return $validated;
    }
}
