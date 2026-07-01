<?php

// penjelasan: File ini adalah controller untuk modul Data Kelas.
// penjelasan: Controller ini dipakai oleh Super Admin dan Admin.
// penjelasan: Controller ini mengatur daftar kelas, tambah kelas, detail kelas, edit kelas, update kelas, dan aktif/nonaktif kelas.
// penjelasan: File ini dipanggil dari route /super-admin/kelas dan /admin/kelas.

namespace App\Http\Controllers\Admin;

// penjelasan: Controller adalah class dasar bawaan Laravel untuk membuat controller.
use App\Http\Controllers\Controller;

// penjelasan: Model Kelas digunakan untuk mengambil, menyimpan, dan mengubah data pada tabel kelas.
use App\Models\Kelas;

// penjelasan: Model Pegawai digunakan untuk mengambil data guru aktif sebagai pilihan wali kelas.
use App\Models\Pegawai;

// penjelasan: Request digunakan untuk mengambil data dari form tambah/edit kelas.
use Illuminate\Http\Request;

// penjelasan: Rule digunakan untuk validasi unique dan pilihan nilai tertentu.
use Illuminate\Validation\Rule;

class KelasController extends Controller
{
    // penjelasan: Method routePrefix digunakan agar route bisa menyesuaikan role login.
    // penjelasan: Jika user login sebagai super_admin, route prefix-nya super-admin.
    // penjelasan: Jika user login sebagai admin, route prefix-nya admin.
    private function routePrefix(): string
    {
        return auth()->user()->role === 'super_admin' ? 'super-admin' : 'admin';
    }

    // penjelasan: Method index digunakan untuk menampilkan daftar kelas.
    // penjelasan: Method ini dipanggil oleh route GET /super-admin/kelas atau /admin/kelas.
    public function index(Request $request)
    {
        // penjelasan: Query awal mengambil data kelas beserta relasi waliKelas.
        // penjelasan: with('waliKelas') digunakan agar nama wali kelas bisa tampil di tabel.
        $query = Kelas::with('waliKelas');

        // penjelasan: Jika input search diisi, sistem mencari berdasarkan nama_kelas atau tingkat.
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('nama_kelas', 'like', '%' . $search . '%')
                    ->orWhere('tingkat', 'like', '%' . $search . '%');
            });
        }

        // penjelasan: Filter tingkat digunakan untuk menampilkan kelas tingkat tertentu.
        if ($request->filled('tingkat')) {
            $query->where('tingkat', $request->tingkat);
        }

        // penjelasan: Filter status digunakan untuk menampilkan kelas aktif atau nonaktif.
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // penjelasan: Data kelas diurutkan dari yang terbaru dan ditampilkan 10 data per halaman.
        $kelasList = $query->latest()->paginate(10)->withQueryString();

        // penjelasan: routePrefix dikirim ke view agar tombol/link menyesuaikan role login.
        $routePrefix = $this->routePrefix();

        return view('admin.pages.kelas.index', compact('kelasList', 'routePrefix'));
    }

    // penjelasan: Method create digunakan untuk menampilkan form tambah kelas.
    // penjelasan: Method ini dipanggil oleh route GET /super-admin/kelas/create atau /admin/kelas/create.
    public function create()
    {
        // penjelasan: Mengambil data pegawai yang jenisnya guru dan statusnya aktif.
        // penjelasan: Data ini digunakan sebagai pilihan wali kelas.
        $gurus = Pegawai::where('jenis_pegawai', 'guru')
            ->where('status', 'aktif')
            ->orderBy('nama_pegawai')
            ->get();

        $routePrefix = $this->routePrefix();

        return view('admin.pages.kelas.create', compact('gurus', 'routePrefix'));
    }

    // penjelasan: Method store digunakan untuk menyimpan data kelas baru.
    // penjelasan: Method ini dipanggil oleh form tambah kelas melalui route POST /kelas.
    public function store(Request $request)
    {
        // penjelasan: Validasi memastikan data kelas sesuai aturan.
        // penjelasan: nama_kelas wajib unik agar kelas tidak dobel.
        // penjelasan: wali_kelas_id nullable karena kelas boleh dibuat tanpa wali kelas dulu.
        $validated = $request->validate([
            'nama_kelas' => ['required', 'string', 'max:50', 'unique:kelas,nama_kelas'],
            'tingkat' => ['required', 'string', 'max:10'],
            'wali_kelas_id' => ['nullable', 'exists:pegawais,id'],
            'status' => ['required', Rule::in(['aktif', 'nonaktif'])],
        ], [
            'nama_kelas.required' => 'Nama kelas wajib diisi.',
            'nama_kelas.unique' => 'Nama kelas sudah digunakan.',
            'tingkat.required' => 'Tingkat kelas wajib diisi.',
            'wali_kelas_id.exists' => 'Wali kelas tidak valid.',
            'status.required' => 'Status wajib dipilih.',
        ]);

        // penjelasan: Jika wali kelas dipilih, sistem memastikan pegawai tersebut adalah guru aktif.
        if (! empty($validated['wali_kelas_id'])) {
            $guruValid = Pegawai::where('id', $validated['wali_kelas_id'])
                ->where('jenis_pegawai', 'guru')
                ->where('status', 'aktif')
                ->exists();

            if (! $guruValid) {
                return back()
                    ->withErrors(['wali_kelas_id' => 'Wali kelas harus berasal dari pegawai guru yang aktif.'])
                    ->withInput();
            }
        }

        // penjelasan: Membuat data kelas baru ke tabel kelas.
        Kelas::create($validated);

        return redirect()
            ->route($this->routePrefix() . '.kelas.index')
            ->with('success', 'Data kelas berhasil ditambahkan.');
    }

    // penjelasan: Method show digunakan untuk menampilkan detail kelas.
    // penjelasan: Parameter Kelas $kelas otomatis mengambil data berdasarkan id pada URL.
    public function show(Kelas $kelas)
    {
        // penjelasan: load('waliKelas') mengambil data wali kelas yang terhubung.
        $kelas->load('waliKelas');

        $routePrefix = $this->routePrefix();

        return view('admin.pages.kelas.show', compact('kelas', 'routePrefix'));
    }

    // penjelasan: Method edit digunakan untuk menampilkan form edit kelas.
    // penjelasan: Method ini dipanggil oleh route GET /kelas/{kelas}/edit.
    public function edit(Kelas $kelas)
    {
        // penjelasan: Mengambil guru aktif untuk pilihan wali kelas.
        $gurus = Pegawai::where('jenis_pegawai', 'guru')
            ->where('status', 'aktif')
            ->orderBy('nama_pegawai')
            ->get();

        $routePrefix = $this->routePrefix();

        return view('admin.pages.kelas.edit', compact('kelas', 'gurus', 'routePrefix'));
    }

    // penjelasan: Method update digunakan untuk menyimpan perubahan data kelas.
    // penjelasan: Method ini dipanggil oleh form edit kelas melalui route PUT /kelas/{kelas}.
    public function update(Request $request, Kelas $kelas)
    {
        // penjelasan: Validasi unique pada nama_kelas mengabaikan data kelas yang sedang diedit.
        $validated = $request->validate([
            'nama_kelas' => [
                'required',
                'string',
                'max:50',
                Rule::unique('kelas', 'nama_kelas')->ignore($kelas->id),
            ],
            'tingkat' => ['required', 'string', 'max:10'],
            'wali_kelas_id' => ['nullable', 'exists:pegawais,id'],
            'status' => ['required', Rule::in(['aktif', 'nonaktif'])],
        ], [
            'nama_kelas.required' => 'Nama kelas wajib diisi.',
            'nama_kelas.unique' => 'Nama kelas sudah digunakan.',
            'tingkat.required' => 'Tingkat kelas wajib diisi.',
            'wali_kelas_id.exists' => 'Wali kelas tidak valid.',
            'status.required' => 'Status wajib dipilih.',
        ]);

        // penjelasan: Jika wali kelas dipilih, sistem memastikan pegawai tersebut guru aktif.
        if (! empty($validated['wali_kelas_id'])) {
            $guruValid = Pegawai::where('id', $validated['wali_kelas_id'])
                ->where('jenis_pegawai', 'guru')
                ->where('status', 'aktif')
                ->exists();

            if (! $guruValid) {
                return back()
                    ->withErrors(['wali_kelas_id' => 'Wali kelas harus berasal dari pegawai guru yang aktif.'])
                    ->withInput();
            }
        }

        // penjelasan: Update data kelas pada tabel kelas.
        $kelas->update($validated);

        return redirect()
            ->route($this->routePrefix() . '.kelas.index')
            ->with('success', 'Data kelas berhasil diperbarui.');
    }

    // penjelasan: Method toggleStatus digunakan untuk mengubah status kelas aktif/nonaktif.
    // penjelasan: Data kelas tidak dihapus permanen agar nanti relasi murid, jadwal, dan nilai tetap aman.
    public function toggleStatus(Kelas $kelas)
    {
        $kelas->update([
            'status' => $kelas->status === 'aktif' ? 'nonaktif' : 'aktif',
        ]);

        return back()->with('success', 'Status kelas berhasil diubah.');
    }
}
