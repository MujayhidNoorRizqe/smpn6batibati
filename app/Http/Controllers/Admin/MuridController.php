<?php

// penjelasan: File ini adalah controller untuk modul Data Murid.
// penjelasan: Controller ini dipakai oleh Super Admin dan Admin.
// penjelasan: Controller ini mengatur daftar murid, tambah murid, detail murid, edit murid, update murid, upload foto, dan aktif/nonaktif murid.
// penjelasan: Controller ini memanggil Model Murid, Kelas, dan WaliMurid.

namespace App\Http\Controllers\Admin;

// penjelasan: Controller adalah class dasar bawaan Laravel untuk membuat controller.
use App\Http\Controllers\Controller;

// penjelasan: Model Kelas digunakan untuk memilih kelas pada form murid.
use App\Models\Kelas;

// penjelasan: Model Murid digunakan untuk mengambil, menyimpan, dan mengubah data pada tabel murids.
use App\Models\Murid;

// penjelasan: Model WaliMurid digunakan untuk memilih wali murid pada form murid.
use App\Models\WaliMurid;

// penjelasan: Request digunakan untuk mengambil data dari form tambah dan edit murid.
use Illuminate\Http\Request;

// penjelasan: Storage digunakan untuk menyimpan dan menghapus foto murid.
use Illuminate\Support\Facades\Storage;

// penjelasan: Rule digunakan untuk validasi unique dan pilihan nilai tertentu.
use Illuminate\Validation\Rule;

class MuridController extends Controller
{
    // penjelasan: Method routePrefix digunakan agar route bisa menyesuaikan role user yang sedang login.
    // penjelasan: Jika user login sebagai super_admin, maka prefix route adalah super-admin.
    // penjelasan: Jika user login sebagai admin, maka prefix route adalah admin.
    private function routePrefix(): string
    {
        return auth()->user()->role === 'super_admin' ? 'super-admin' : 'admin';
    }

    // penjelasan: Method index digunakan untuk menampilkan daftar murid.
    // penjelasan: Method ini dipanggil oleh route GET /super-admin/murid atau /admin/murid.
    // penjelasan: Method ini juga memproses pencarian dan filter kelas/status.
    public function index(Request $request)
    {
        // penjelasan: Query awal mengambil data murid beserta relasi kelas dan waliMurid.
        // penjelasan: with(['kelas', 'waliMurid']) mencegah query berulang saat data ditampilkan di tabel.
        $query = Murid::with(['kelas', 'waliMurid']);

        // penjelasan: Jika search diisi, sistem mencari berdasarkan nama murid, NIS, atau NISN.
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('nama_murid', 'like', '%' . $search . '%')
                    ->orWhere('nis', 'like', '%' . $search . '%')
                    ->orWhere('nisn', 'like', '%' . $search . '%');
            });
        }

        // penjelasan: Filter kelas_id digunakan untuk menampilkan murid berdasarkan kelas tertentu.
        if ($request->filled('kelas_id')) {
            $query->where('kelas_id', $request->kelas_id);
        }

        // penjelasan: Filter jenis_kelamin digunakan untuk menampilkan murid laki-laki atau perempuan.
        if ($request->filled('jenis_kelamin')) {
            $query->where('jenis_kelamin', $request->jenis_kelamin);
        }

        // penjelasan: Filter status digunakan untuk menampilkan murid aktif atau nonaktif.
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // penjelasan: Data murid diurutkan dari terbaru dan ditampilkan 10 data per halaman.
        $murids = $query->latest()->paginate(10)->withQueryString();

        // penjelasan: kelasList digunakan untuk pilihan filter kelas pada halaman index.
        $kelasList = Kelas::where('status', 'aktif')
            ->orderBy('tingkat')
            ->orderBy('nama_kelas')
            ->get();

        // penjelasan: routePrefix dikirim ke view agar tombol/link mengikuti role login.
        $routePrefix = $this->routePrefix();

        return view('admin.pages.murid.index', compact('murids', 'kelasList', 'routePrefix'));
    }

    // penjelasan: Method create digunakan untuk menampilkan form tambah murid.
    // penjelasan: Method ini dipanggil oleh route GET /murid/create.
    public function create()
    {
        // penjelasan: Mengambil kelas aktif untuk pilihan kelas murid.
        $kelasList = Kelas::where('status', 'aktif')
            ->orderBy('tingkat')
            ->orderBy('nama_kelas')
            ->get();

        // penjelasan: Mengambil wali murid aktif untuk pilihan wali murid.
        $waliMurids = WaliMurid::where('status', 'aktif')
            ->orderBy('nama_wali')
            ->get();

        $routePrefix = $this->routePrefix();

        return view('admin.pages.murid.create', compact('kelasList', 'waliMurids', 'routePrefix'));
    }

    // penjelasan: Method store digunakan untuk menyimpan data murid baru.
    // penjelasan: Method ini dipanggil oleh form tambah murid melalui route POST /murid.
    public function store(Request $request)
    {
        // penjelasan: Validasi memastikan data murid sesuai aturan sebelum disimpan.
        // penjelasan: kelas_id wajib karena setiap murid harus memiliki kelas.
        // penjelasan: wali_murid_id wajib dipilih agar data murid terhubung dengan wali.
        // penjelasan: nis dan nisn boleh kosong, tetapi jika diisi harus unik.
        $validated = $request->validate([
            'kelas_id' => ['required', 'exists:kelas,id'],
            'wali_murid_id' => ['required', 'exists:wali_murids,id'],
            'nis' => ['nullable', 'string', 'max:50', 'unique:murids,nis'],
            'nisn' => ['nullable', 'string', 'max:50', 'unique:murids,nisn'],
            'nama_murid' => ['required', 'string', 'max:150'],
            'jenis_kelamin' => ['required', Rule::in(['L', 'P'])],
            'tempat_lahir' => ['nullable', 'string', 'max:100'],
            'tanggal_lahir' => ['nullable', 'date'],
            'agama' => ['nullable', 'string', 'max:50'],
            'alamat' => ['nullable', 'string'],
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'status' => ['required', Rule::in(['aktif', 'nonaktif'])],
        ], [
            'kelas_id.required' => 'Kelas wajib dipilih.',
            'kelas_id.exists' => 'Kelas tidak valid.',
            'wali_murid_id.required' => 'Wali murid wajib dipilih.',
            'wali_murid_id.exists' => 'Wali murid tidak valid.',
            'nis.unique' => 'NIS sudah digunakan.',
            'nisn.unique' => 'NISN sudah digunakan.',
            'nama_murid.required' => 'Nama murid wajib diisi.',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih.',
            'foto.image' => 'File foto harus berupa gambar.',
            'foto.mimes' => 'Format foto harus jpg, jpeg, png, atau webp.',
            'foto.max' => 'Ukuran foto maksimal 2MB.',
            'status.required' => 'Status wajib dipilih.',
        ]);

        // penjelasan: Sistem memastikan kelas yang dipilih masih aktif.
        $kelasAktif = Kelas::where('id', $validated['kelas_id'])
            ->where('status', 'aktif')
            ->exists();

        if (! $kelasAktif) {
            return back()
                ->withErrors(['kelas_id' => 'Kelas yang dipilih harus berstatus aktif.'])
                ->withInput();
        }

        // penjelasan: Sistem memastikan wali murid yang dipilih masih aktif.
        $waliAktif = WaliMurid::where('id', $validated['wali_murid_id'])
            ->where('status', 'aktif')
            ->exists();

        if (! $waliAktif) {
            return back()
                ->withErrors(['wali_murid_id' => 'Wali murid yang dipilih harus berstatus aktif.'])
                ->withInput();
        }

        // penjelasan: Jika ada upload foto, foto disimpan ke storage/app/public/foto/murid.
        // penjelasan: Path foto disimpan ke kolom foto di tabel murids.
        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')->store('foto/murid', 'public');
        }

        // penjelasan: Membuat data murid baru ke tabel murids.
        Murid::create($validated);

        return redirect()
            ->route($this->routePrefix() . '.murid.index')
            ->with('success', 'Data murid berhasil ditambahkan.');
    }

    // penjelasan: Method show digunakan untuk menampilkan detail murid.
    // penjelasan: Parameter Murid $murid otomatis mengambil data murid berdasarkan id pada URL.
    public function show(Murid $murid)
    {
        // penjelasan: load mengambil data relasi kelas dan wali murid untuk ditampilkan di halaman detail.
        $murid->load(['kelas', 'waliMurid']);

        $routePrefix = $this->routePrefix();

        return view('admin.pages.murid.show', compact('murid', 'routePrefix'));
    }

    // penjelasan: Method edit digunakan untuk menampilkan form edit murid.
    // penjelasan: Method ini dipanggil oleh route GET /murid/{murid}/edit.
    public function edit(Murid $murid)
    {
        // penjelasan: Mengambil kelas aktif atau kelas yang sedang dipakai murid.
        // penjelasan: Ini mencegah pilihan kelas hilang saat kelas lama sedang nonaktif.
        $kelasList = Kelas::where('status', 'aktif')
            ->orWhere('id', $murid->kelas_id)
            ->orderBy('tingkat')
            ->orderBy('nama_kelas')
            ->get();

        // penjelasan: Mengambil wali aktif atau wali yang sedang dipakai murid.
        // penjelasan: Ini mencegah pilihan wali hilang saat wali lama sedang nonaktif.
        $waliMurids = WaliMurid::where('status', 'aktif')
            ->orWhere('id', $murid->wali_murid_id)
            ->orderBy('nama_wali')
            ->get();

        $routePrefix = $this->routePrefix();

        return view('admin.pages.murid.edit', compact('murid', 'kelasList', 'waliMurids', 'routePrefix'));
    }

    // penjelasan: Method update digunakan untuk menyimpan perubahan data murid.
    // penjelasan: Method ini dipanggil oleh form edit murid melalui route PUT /murid/{murid}.
    public function update(Request $request, Murid $murid)
    {
        // penjelasan: Validasi unique pada nis dan nisn mengabaikan data murid yang sedang diedit.
        $validated = $request->validate([
            'kelas_id' => ['required', 'exists:kelas,id'],
            'wali_murid_id' => ['required', 'exists:wali_murids,id'],
            'nis' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('murids', 'nis')->ignore($murid->id),
            ],
            'nisn' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('murids', 'nisn')->ignore($murid->id),
            ],
            'nama_murid' => ['required', 'string', 'max:150'],
            'jenis_kelamin' => ['required', Rule::in(['L', 'P'])],
            'tempat_lahir' => ['nullable', 'string', 'max:100'],
            'tanggal_lahir' => ['nullable', 'date'],
            'agama' => ['nullable', 'string', 'max:50'],
            'alamat' => ['nullable', 'string'],
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'status' => ['required', Rule::in(['aktif', 'nonaktif'])],
        ], [
            'kelas_id.required' => 'Kelas wajib dipilih.',
            'kelas_id.exists' => 'Kelas tidak valid.',
            'wali_murid_id.required' => 'Wali murid wajib dipilih.',
            'wali_murid_id.exists' => 'Wali murid tidak valid.',
            'nis.unique' => 'NIS sudah digunakan.',
            'nisn.unique' => 'NISN sudah digunakan.',
            'nama_murid.required' => 'Nama murid wajib diisi.',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih.',
            'foto.image' => 'File foto harus berupa gambar.',
            'foto.mimes' => 'Format foto harus jpg, jpeg, png, atau webp.',
            'foto.max' => 'Ukuran foto maksimal 2MB.',
            'status.required' => 'Status wajib dipilih.',
        ]);

        // penjelasan: Jika ada foto baru, sistem menghapus foto lama dari storage agar tidak menumpuk.
        if ($request->hasFile('foto')) {
            if ($murid->foto && Storage::disk('public')->exists($murid->foto)) {
                Storage::disk('public')->delete($murid->foto);
            }

            $validated['foto'] = $request->file('foto')->store('foto/murid', 'public');
        }

        // penjelasan: Update data murid pada tabel murids.
        $murid->update($validated);

        return redirect()
            ->route($this->routePrefix() . '.murid.index')
            ->with('success', 'Data murid berhasil diperbarui.');
    }

    // penjelasan: Method toggleStatus digunakan untuk mengubah status murid aktif/nonaktif.
    // penjelasan: Data murid tidak dihapus permanen agar riwayat absensi dan nilai tetap aman.
    public function toggleStatus(Murid $murid)
    {
        $murid->update([
            'status' => $murid->status === 'aktif' ? 'nonaktif' : 'aktif',
        ]);

        return back()->with('success', 'Status murid berhasil diubah.');
    }
}
