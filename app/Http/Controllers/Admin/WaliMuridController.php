<?php

// penjelasan: File ini adalah controller untuk modul Data Wali Murid.
// penjelasan: Controller ini dipakai oleh Super Admin dan Admin.
// penjelasan: Controller ini mengatur daftar wali murid, tambah wali murid, detail wali murid, edit, update, dan aktif/nonaktif.
// penjelasan: File ini dipanggil dari route /super-admin/wali-murid dan /admin/wali-murid.

namespace App\Http\Controllers\Admin;

// penjelasan: Controller adalah class dasar bawaan Laravel untuk membuat controller.
use App\Http\Controllers\Controller;

// penjelasan: Model WaliMurid digunakan untuk mengambil, menyimpan, dan mengubah data pada tabel wali_murids.
use App\Models\WaliMurid;

// penjelasan: Request digunakan untuk mengambil data dari form tambah dan edit wali murid.
use Illuminate\Http\Request;

// penjelasan: Rule digunakan untuk validasi unique dan pilihan nilai tertentu.
use Illuminate\Validation\Rule;

class WaliMuridController extends Controller
{
    // penjelasan: Method routePrefix digunakan agar route bisa menyesuaikan role user yang sedang login.
    // penjelasan: Jika user login sebagai super_admin, maka prefix route adalah super-admin.
    // penjelasan: Jika user login sebagai admin, maka prefix route adalah admin.
    // penjelasan: Method ini membuat view bisa memakai satu file yang sama untuk Super Admin dan Admin.
    private function routePrefix(): string
    {
        return auth()->user()->role === 'super_admin' ? 'super-admin' : 'admin';
    }

    // penjelasan: Method index digunakan untuk menampilkan daftar wali murid.
    // penjelasan: Method ini dipanggil oleh route GET /super-admin/wali-murid atau /admin/wali-murid.
    // penjelasan: Method ini juga memproses pencarian dan filter status/hubungan.
    public function index(Request $request)
    {
        // penjelasan: Query awal untuk mengambil data wali murid.
        // penjelasan: Query dibuat terpisah agar bisa ditambahkan filter secara bertahap.
        $query = WaliMurid::query();

        // penjelasan: Jika input search diisi, sistem mencari data berdasarkan nama_wali, nik, no_hp, atau no_whatsapp.
        // penjelasan: where(function) dipakai agar kondisi pencarian dikelompokkan dengan benar.
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('nama_wali', 'like', '%' . $search . '%')
                    ->orWhere('nik', 'like', '%' . $search . '%')
                    ->orWhere('no_hp', 'like', '%' . $search . '%')
                    ->orWhere('no_whatsapp', 'like', '%' . $search . '%');
            });
        }

        // penjelasan: Filter hubungan digunakan untuk menampilkan wali berdasarkan hubungan ayah, ibu, atau wali.
        if ($request->filled('hubungan')) {
            $query->where('hubungan', $request->hubungan);
        }

        // penjelasan: Filter status digunakan untuk menampilkan wali murid aktif atau nonaktif.
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // penjelasan: latest() mengurutkan data terbaru di atas.
        // penjelasan: paginate(10) menampilkan 10 data per halaman.
        // penjelasan: withQueryString() menjaga filter tetap ada saat pindah halaman pagination.
        $waliMurids = $query->latest()->paginate(10)->withQueryString();

        // penjelasan: routePrefix dikirim ke view agar link tombol tambah, edit, detail, dan status sesuai role login.
        $routePrefix = $this->routePrefix();

        // penjelasan: View ini menampilkan halaman daftar wali murid.
        return view('admin.pages.wali-murid.index', compact('waliMurids', 'routePrefix'));
    }

    // penjelasan: Method create digunakan untuk menampilkan form tambah wali murid.
    // penjelasan: Method ini dipanggil oleh route GET /super-admin/wali-murid/create atau /admin/wali-murid/create.
    public function create()
    {
        // penjelasan: routePrefix dikirim agar form action bisa menyesuaikan role user.
        $routePrefix = $this->routePrefix();

        // penjelasan: View ini menampilkan form tambah wali murid.
        return view('admin.pages.wali-murid.create', compact('routePrefix'));
    }

    // penjelasan: Method store digunakan untuk menyimpan data wali murid baru.
    // penjelasan: Method ini dipanggil oleh form tambah wali murid.
    // penjelasan: Data yang masuk harus lolos validasi terlebih dahulu.
    public function store(Request $request)
    {
        // penjelasan: Validasi memastikan data yang dikirim dari form sesuai aturan.
        // penjelasan: nik boleh kosong, tetapi jika diisi harus unik.
        // penjelasan: hubungan wajib salah satu dari ayah, ibu, atau wali.
        // penjelasan: status wajib salah satu dari aktif atau nonaktif.
        $validated = $request->validate([
            'nama_wali' => ['required', 'string', 'max:150'],
            'nik' => ['nullable', 'string', 'max:30', 'unique:wali_murids,nik'],
            'hubungan' => ['required', Rule::in(['ayah', 'ibu', 'wali'])],
            'pekerjaan' => ['nullable', 'string', 'max:150'],
            'no_hp' => ['nullable', 'string', 'max:30'],
            'no_whatsapp' => ['nullable', 'string', 'max:30'],
            'alamat' => ['nullable', 'string'],
            'status' => ['required', Rule::in(['aktif', 'nonaktif'])],
        ], [
            'nama_wali.required' => 'Nama wali murid wajib diisi.',
            'nik.unique' => 'NIK sudah digunakan oleh wali murid lain.',
            'hubungan.required' => 'Hubungan wali wajib dipilih.',
            'status.required' => 'Status wajib dipilih.',
        ]);

        // penjelasan: Data yang sudah valid disimpan ke tabel wali_murids.
        WaliMurid::create($validated);

        // penjelasan: Setelah berhasil, user diarahkan kembali ke halaman daftar wali murid.
        return redirect()
            ->route($this->routePrefix() . '.wali-murid.index')
            ->with('success', 'Data wali murid berhasil ditambahkan.');
    }

    // penjelasan: Method show digunakan untuk menampilkan detail wali murid.
    // penjelasan: Parameter WaliMurid $waliMurid otomatis diisi Laravel berdasarkan id dari URL.
    // penjelasan: Method ini dipanggil oleh route GET /wali-murid/{waliMurid}.
    public function show(WaliMurid $waliMurid)
    {
        // penjelasan: routePrefix dikirim ke view untuk tombol kembali dan edit.
        $routePrefix = $this->routePrefix();

        // penjelasan: View ini menampilkan detail data wali murid.
        return view('admin.pages.wali-murid.show', compact('waliMurid', 'routePrefix'));
    }

    // penjelasan: Method edit digunakan untuk menampilkan form edit wali murid.
    // penjelasan: Parameter WaliMurid $waliMurid otomatis mengambil data wali murid berdasarkan id pada URL.
    public function edit(WaliMurid $waliMurid)
    {
        // penjelasan: routePrefix dikirim agar form update menyesuaikan role login.
        $routePrefix = $this->routePrefix();

        // penjelasan: View ini menampilkan form edit wali murid.
        return view('admin.pages.wali-murid.edit', compact('waliMurid', 'routePrefix'));
    }

    // penjelasan: Method update digunakan untuk menyimpan perubahan data wali murid.
    // penjelasan: Method ini dipanggil oleh form edit melalui route PUT /wali-murid/{waliMurid}.
    public function update(Request $request, WaliMurid $waliMurid)
    {
        // penjelasan: Validasi unique pada nik mengabaikan data wali murid yang sedang diedit.
        // penjelasan: Ini penting agar NIK milik data yang sama tidak dianggap duplikat.
        $validated = $request->validate([
            'nama_wali' => ['required', 'string', 'max:150'],
            'nik' => [
                'nullable',
                'string',
                'max:30',
                Rule::unique('wali_murids', 'nik')->ignore($waliMurid->id),
            ],
            'hubungan' => ['required', Rule::in(['ayah', 'ibu', 'wali'])],
            'pekerjaan' => ['nullable', 'string', 'max:150'],
            'no_hp' => ['nullable', 'string', 'max:30'],
            'no_whatsapp' => ['nullable', 'string', 'max:30'],
            'alamat' => ['nullable', 'string'],
            'status' => ['required', Rule::in(['aktif', 'nonaktif'])],
        ], [
            'nama_wali.required' => 'Nama wali murid wajib diisi.',
            'nik.unique' => 'NIK sudah digunakan oleh wali murid lain.',
            'hubungan.required' => 'Hubungan wali wajib dipilih.',
            'status.required' => 'Status wajib dipilih.',
        ]);

        // penjelasan: Data wali murid diperbarui pada tabel wali_murids.
        $waliMurid->update($validated);

        // penjelasan: Setelah berhasil update, user diarahkan ke halaman daftar wali murid.
        return redirect()
            ->route($this->routePrefix() . '.wali-murid.index')
            ->with('success', 'Data wali murid berhasil diperbarui.');
    }

    // penjelasan: Method toggleStatus digunakan untuk mengubah status wali murid aktif/nonaktif.
    // penjelasan: Data wali murid tidak dihapus permanen agar nanti relasi dengan murid tetap aman.
    // penjelasan: Method ini dipanggil oleh tombol Aktifkan/Nonaktif pada halaman daftar wali murid.
    public function toggleStatus(WaliMurid $waliMurid)
    {
        // penjelasan: Jika status sekarang aktif maka diubah menjadi nonaktif.
        // penjelasan: Jika status sekarang nonaktif maka diubah menjadi aktif.
        $waliMurid->update([
            'status' => $waliMurid->status === 'aktif' ? 'nonaktif' : 'aktif',
        ]);

        // penjelasan: Setelah status berubah, user kembali ke halaman sebelumnya.
        return back()->with('success', 'Status wali murid berhasil diubah.');
    }
}
