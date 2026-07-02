<?php

// penjelasan: File ini adalah controller untuk modul Data Pegawai.
// penjelasan: Controller ini dipakai oleh Super Admin dan Admin.
// penjelasan: Controller ini mengatur daftar pegawai, tambah pegawai, detail, edit, update, dan ubah status pegawai.
// penjelasan: Controller ini juga mengatur upload foto pegawai ke storage Laravel.
// penjelasan: Semua validasi memakai pesan Bahasa Indonesia agar selaras dengan UI global.
// penjelasan: Pada proses tambah pegawai, jabatan dan alamat dibuat wajib sesuai kebutuhan sistem.

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
// penjelasan: Controller adalah class dasar bawaan Laravel.

use App\Models\Pegawai;
// penjelasan: Model Pegawai digunakan untuk mengambil dan menyimpan data pada tabel pegawais.

use App\Models\User;
// penjelasan: Model User digunakan untuk mengambil akun guru/staff yang bisa dihubungkan ke data pegawai.

use Illuminate\Http\Request;
// penjelasan: Request digunakan untuk mengambil data dari form tambah dan edit pegawai.

use Illuminate\Support\Facades\Storage;
// penjelasan: Storage digunakan untuk menyimpan dan menghapus file foto pegawai.

use Illuminate\Validation\Rule;
// penjelasan: Rule digunakan untuk validasi unique yang lebih fleksibel.

class PegawaiController extends Controller
{
    /**
     * penjelasan: Method routePrefix digunakan untuk menentukan prefix route berdasarkan role login.
     * penjelasan: Jika login sebagai super_admin maka route-nya super-admin.
     * penjelasan: Jika login sebagai admin maka route-nya admin.
     */
    private function routePrefix(): string
    {
        return auth()->user()->role === 'super_admin' ? 'super-admin' : 'admin';
    }

    /**
     * penjelasan: Method index digunakan untuk menampilkan daftar pegawai.
     * penjelasan: Method ini dipanggil oleh route GET /super-admin/pegawai atau /admin/pegawai.
     */
    public function index(Request $request)
    {
        $query = Pegawai::with('user');

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('nama_pegawai', 'like', '%' . $search . '%')
                    ->orWhere('nip', 'like', '%' . $search . '%')
                    ->orWhere('jabatan', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('jenis_pegawai')) {
            $query->where('jenis_pegawai', $request->jenis_pegawai);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $pegawais = $query->latest()->paginate(10)->withQueryString();

        $routePrefix = $this->routePrefix();

        return view('admin.pages.pegawai.index', compact('pegawais', 'routePrefix'));
    }

    /**
     * penjelasan: Method create digunakan untuk menampilkan form tambah pegawai.
     * penjelasan: Method ini dipanggil oleh route GET /super-admin/pegawai/create atau /admin/pegawai/create.
     */
    public function create()
    {
        $users = User::whereIn('role', ['guru', 'staff'])
            ->whereDoesntHave('pegawai')
            ->where('status', 'aktif')
            ->orderBy('name')
            ->get();

        $routePrefix = $this->routePrefix();

        return view('admin.pages.pegawai.create', compact('users', 'routePrefix'));
    }

    /**
     * penjelasan: Method store digunakan untuk menyimpan data pegawai baru.
     * penjelasan: Method ini dipanggil oleh form tambah pegawai.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => ['nullable', 'exists:users,id', 'unique:pegawais,user_id'],
            'nip' => ['nullable', 'string', 'max:50', 'unique:pegawais,nip'],
            'nama_pegawai' => ['required', 'string', 'max:150'],
            'jenis_pegawai' => ['required', Rule::in(['guru', 'staff'])],
            'jabatan' => ['required', 'string', 'max:150'],
            'jenis_kelamin' => ['nullable', Rule::in(['L', 'P'])],
            'no_hp' => ['nullable', 'string', 'max:30'],
            'alamat' => ['required', 'string'],
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'status' => ['required', Rule::in(['aktif', 'nonaktif'])],
        ], $this->validationMessages());

        // penjelasan: Jika akun login dipilih, pastikan role akun sesuai dengan jenis pegawai.
        // penjelasan: Contoh jenis pegawai guru harus memakai user role guru.
        if (! empty($validated['user_id'])) {
            $user = User::find($validated['user_id']);

            if (! $user || $user->role !== $validated['jenis_pegawai']) {
                return back()
                    ->withErrors(['user_id' => 'Akun login harus sesuai dengan jenis pegawai yang dipilih.'])
                    ->withInput();
            }
        }

        $validated = $this->normalizeNullableFields($validated);

        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')->store('foto/pegawai', 'public');
        }

        Pegawai::create($validated);

        return redirect()
            ->route($this->routePrefix() . '.pegawai.index')
            ->with('success', 'Data pegawai berhasil ditambahkan.');
    }

    /**
     * penjelasan: Method show digunakan untuk menampilkan detail pegawai.
     * penjelasan: Parameter Pegawai $pegawai otomatis mengambil data berdasarkan id di URL.
     */
    public function show(Pegawai $pegawai)
    {
        $pegawai->load('user');

        $routePrefix = $this->routePrefix();

        return view('admin.pages.pegawai.show', compact('pegawai', 'routePrefix'));
    }

    /**
     * penjelasan: Method edit digunakan untuk menampilkan form edit pegawai.
     */
    public function edit(Pegawai $pegawai)
    {
        $users = User::whereIn('role', ['guru', 'staff'])
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

    /**
     * penjelasan: Method update digunakan untuk menyimpan perubahan data pegawai.
     * penjelasan: Untuk edit pegawai, jabatan dan alamat tetap mengikuti aturan sebelumnya.
     * penjelasan: Jika ingin edit juga wajib jabatan/alamat, aturan nullable bisa diganti menjadi required.
     */
    public function update(Request $request, Pegawai $pegawai)
    {
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
            'nama_pegawai' => ['required', 'string', 'max:150'],
            'jenis_pegawai' => ['required', Rule::in(['guru', 'staff'])],
            'jabatan' => ['nullable', 'string', 'max:150'],
            'jenis_kelamin' => ['nullable', Rule::in(['L', 'P'])],
            'no_hp' => ['nullable', 'string', 'max:30'],
            'alamat' => ['nullable', 'string'],
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'status' => ['required', Rule::in(['aktif', 'nonaktif'])],
        ], $this->validationMessages());

        if (! empty($validated['user_id'])) {
            $user = User::find($validated['user_id']);

            if (! $user || $user->role !== $validated['jenis_pegawai']) {
                return back()
                    ->withErrors(['user_id' => 'Akun login harus sesuai dengan jenis pegawai yang dipilih.'])
                    ->withInput();
            }
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
            ->with('success', 'Data pegawai berhasil diperbarui.');
    }

    /**
     * penjelasan: Method toggleStatus digunakan untuk mengubah status pegawai aktif/nonaktif.
     * penjelasan: Data tidak dihapus permanen agar riwayat absensi dan jadwal tetap aman.
     */
    public function toggleStatus(Pegawai $pegawai)
    {
        $newStatus = $pegawai->status === 'aktif' ? 'nonaktif' : 'aktif';

        $pegawai->update([
            'status' => $newStatus,
        ]);

        $message = $newStatus === 'aktif'
            ? 'Pegawai berhasil diaktifkan.'
            : 'Pegawai berhasil dinonaktifkan.';

        return back()->with('success', $message);
    }

    /**
     * penjelasan: Method validationMessages menyimpan semua pesan validasi Bahasa Indonesia.
     * penjelasan: Pesan ini dipakai saat tambah dan edit pegawai.
     */
    private function validationMessages(): array
    {
        return [
            'user_id.exists' => 'Akun login tidak valid.',
            'user_id.unique' => 'Akun login sudah terhubung dengan data pegawai lain.',

            'nip.string' => 'NIP harus berupa teks.',
            'nip.max' => 'NIP maksimal 50 karakter.',
            'nip.unique' => 'NIP sudah digunakan.',

            'nama_pegawai.required' => 'Nama pegawai wajib diisi.',
            'nama_pegawai.string' => 'Nama pegawai harus berupa teks.',
            'nama_pegawai.max' => 'Nama pegawai maksimal 150 karakter.',

            'jenis_pegawai.required' => 'Jenis pegawai wajib dipilih.',
            'jenis_pegawai.in' => 'Jenis pegawai yang dipilih tidak valid.',

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

    /**
     * penjelasan: Method normalizeNullableFields membersihkan input kosong menjadi null.
     * penjelasan: Ini menjaga data database lebih rapi.
     */
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
