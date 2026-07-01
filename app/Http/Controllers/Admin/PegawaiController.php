<?php

// penjelasan: File ini adalah controller untuk modul Data Pegawai.
// penjelasan: Controller ini dipakai oleh Super Admin dan Admin.
// penjelasan: Controller ini mengatur daftar pegawai, tambah pegawai, detail, edit, update, dan ubah status pegawai.
// penjelasan: Controller ini juga mengatur upload foto pegawai ke storage Laravel.

namespace App\Http\Controllers\Admin;

// penjelasan: Controller adalah class dasar bawaan Laravel.
use App\Http\Controllers\Controller;

// penjelasan: Model Pegawai digunakan untuk mengambil dan menyimpan data pada tabel pegawais.
use App\Models\Pegawai;

// penjelasan: Model User digunakan untuk mengambil akun guru/staff yang bisa dihubungkan ke data pegawai.
use App\Models\User;

// penjelasan: Request digunakan untuk mengambil data dari form tambah dan edit pegawai.
use Illuminate\Http\Request;

// penjelasan: Storage digunakan untuk menyimpan dan menghapus file foto pegawai.
use Illuminate\Support\Facades\Storage;

// penjelasan: Rule digunakan untuk validasi unique yang lebih fleksibel.
use Illuminate\Validation\Rule;

class PegawaiController extends Controller
{
    // penjelasan: Method routePrefix digunakan untuk menentukan prefix route berdasarkan role login.
    // penjelasan: Jika login sebagai super_admin maka route-nya super-admin.
    // penjelasan: Jika login sebagai admin maka route-nya admin.
    private function routePrefix(): string
    {
        return auth()->user()->role === 'super_admin' ? 'super-admin' : 'admin';
    }

    // penjelasan: Method index digunakan untuk menampilkan daftar pegawai.
    // penjelasan: Method ini dipanggil oleh route GET /super-admin/pegawai atau /admin/pegawai.
    public function index(Request $request)
    {
        // penjelasan: Query awal mengambil data pegawai beserta relasi user.
        // penjelasan: with('user') digunakan agar data akun user bisa ditampilkan tanpa query berulang.
        $query = Pegawai::with('user');

        // penjelasan: Jika input search diisi, sistem mencari berdasarkan nama pegawai, NIP, atau jabatan.
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('nama_pegawai', 'like', '%' . $search . '%')
                    ->orWhere('nip', 'like', '%' . $search . '%')
                    ->orWhere('jabatan', 'like', '%' . $search . '%');
            });
        }

        // penjelasan: Filter jenis_pegawai dipakai untuk menampilkan guru saja atau staff saja.
        if ($request->filled('jenis_pegawai')) {
            $query->where('jenis_pegawai', $request->jenis_pegawai);
        }

        // penjelasan: Filter status dipakai untuk menampilkan pegawai aktif atau nonaktif.
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // penjelasan: Data pegawai diurutkan dari yang terbaru dan ditampilkan 10 data per halaman.
        $pegawais = $query->latest()->paginate(10)->withQueryString();

        // penjelasan: routePrefix dikirim ke view agar tombol/link menyesuaikan role login.
        $routePrefix = $this->routePrefix();

        return view('admin.pages.pegawai.index', compact('pegawais', 'routePrefix'));
    }

    // penjelasan: Method create digunakan untuk menampilkan form tambah pegawai.
    // penjelasan: Method ini dipanggil oleh route GET /super-admin/pegawai/create atau /admin/pegawai/create.
    public function create()
    {
        // penjelasan: Mengambil akun guru/staff yang belum terhubung ke data pegawai.
        // penjelasan: Akun admin tidak wajib menjadi pegawai, jadi tidak ditampilkan.
        $users = User::whereIn('role', ['guru', 'staff'])
            ->whereDoesntHave('pegawai')
            ->where('status', 'aktif')
            ->orderBy('name')
            ->get();

        $routePrefix = $this->routePrefix();

        return view('admin.pages.pegawai.create', compact('users', 'routePrefix'));
    }

    // penjelasan: Method store digunakan untuk menyimpan data pegawai baru.
    // penjelasan: Method ini dipanggil oleh form tambah pegawai.
    public function store(Request $request)
    {
        // penjelasan: Validasi memastikan data pegawai sesuai aturan.
        // penjelasan: user_id nullable karena pegawai boleh dibuat tanpa akun login.
        // penjelasan: nip nullable tetapi jika diisi harus unik.
        $validated = $request->validate([
            'user_id' => ['nullable', 'exists:users,id', 'unique:pegawais,user_id'],
            'nip' => ['nullable', 'string', 'max:50', 'unique:pegawais,nip'],
            'nama_pegawai' => ['required', 'string', 'max:150'],
            'jenis_pegawai' => ['required', Rule::in(['guru', 'staff'])],
            'jabatan' => ['nullable', 'string', 'max:150'],
            'jenis_kelamin' => ['nullable', Rule::in(['L', 'P'])],
            'no_hp' => ['nullable', 'string', 'max:30'],
            'alamat' => ['nullable', 'string'],
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'status' => ['required', Rule::in(['aktif', 'nonaktif'])],
        ], [
            'user_id.exists' => 'Akun user tidak valid.',
            'user_id.unique' => 'Akun user sudah terhubung dengan data pegawai lain.',
            'nip.unique' => 'NIP sudah digunakan.',
            'nama_pegawai.required' => 'Nama pegawai wajib diisi.',
            'jenis_pegawai.required' => 'Jenis pegawai wajib dipilih.',
            'foto.image' => 'File foto harus berupa gambar.',
            'foto.mimes' => 'Format foto harus jpg, jpeg, png, atau webp.',
            'foto.max' => 'Ukuran foto maksimal 2MB.',
            'status.required' => 'Status wajib dipilih.',
        ]);

        // penjelasan: Jika ada upload foto, foto disimpan ke storage/app/public/foto/pegawai.
        // penjelasan: Path file disimpan ke kolom foto.
        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')->store('foto/pegawai', 'public');
        }

        // penjelasan: Membuat data pegawai baru ke tabel pegawais.
        Pegawai::create($validated);

        return redirect()
            ->route($this->routePrefix() . '.pegawai.index')
            ->with('success', 'Data pegawai berhasil ditambahkan.');
    }

    // penjelasan: Method show digunakan untuk menampilkan detail pegawai.
    // penjelasan: Parameter Pegawai $pegawai otomatis mengambil data berdasarkan id di URL.
    public function show(Pegawai $pegawai)
    {
        $pegawai->load('user');

        $routePrefix = $this->routePrefix();

        return view('admin.pages.pegawai.show', compact('pegawai', 'routePrefix'));
    }

    // penjelasan: Method edit digunakan untuk menampilkan form edit pegawai.
    public function edit(Pegawai $pegawai)
    {
        // penjelasan: Mengambil akun guru/staff aktif yang belum punya pegawai.
        // penjelasan: Jika pegawai sudah punya user_id, user tersebut tetap ditampilkan agar tidak hilang saat edit.
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

    // penjelasan: Method update digunakan untuk menyimpan perubahan data pegawai.
    public function update(Request $request, Pegawai $pegawai)
    {
        // penjelasan: Validasi unique mengabaikan data pegawai yang sedang diedit.
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
        ], [
            'user_id.exists' => 'Akun user tidak valid.',
            'user_id.unique' => 'Akun user sudah terhubung dengan data pegawai lain.',
            'nip.unique' => 'NIP sudah digunakan.',
            'nama_pegawai.required' => 'Nama pegawai wajib diisi.',
            'jenis_pegawai.required' => 'Jenis pegawai wajib dipilih.',
            'foto.image' => 'File foto harus berupa gambar.',
            'foto.mimes' => 'Format foto harus jpg, jpeg, png, atau webp.',
            'foto.max' => 'Ukuran foto maksimal 2MB.',
            'status.required' => 'Status wajib dipilih.',
        ]);

        // penjelasan: Jika ada foto baru, foto lama dihapus agar storage tidak penuh.
        if ($request->hasFile('foto')) {
            if ($pegawai->foto && Storage::disk('public')->exists($pegawai->foto)) {
                Storage::disk('public')->delete($pegawai->foto);
            }

            $validated['foto'] = $request->file('foto')->store('foto/pegawai', 'public');
        }

        // penjelasan: Update data pegawai pada tabel pegawais.
        $pegawai->update($validated);

        return redirect()
            ->route($this->routePrefix() . '.pegawai.index')
            ->with('success', 'Data pegawai berhasil diperbarui.');
    }

    // penjelasan: Method toggleStatus digunakan untuk mengubah status pegawai aktif/nonaktif.
    // penjelasan: Data tidak dihapus permanen agar riwayat absensi dan jadwal tetap aman.
    public function toggleStatus(Pegawai $pegawai)
    {
        $pegawai->update([
            'status' => $pegawai->status === 'aktif' ? 'nonaktif' : 'aktif',
        ]);

        return back()->with('success', 'Status pegawai berhasil diubah.');
    }
}
