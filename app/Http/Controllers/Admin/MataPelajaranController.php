<?php

// penjelasan: File ini adalah controller untuk modul Data Mata Pelajaran.
// penjelasan: Controller ini dipakai oleh Super Admin dan Admin.
// penjelasan: Controller ini mengatur daftar mata pelajaran, tambah, detail, edit, update, dan aktif/nonaktif.
// penjelasan: File ini dipanggil dari route /super-admin/mata-pelajaran dan /admin/mata-pelajaran.

namespace App\Http\Controllers\Admin;

// penjelasan: Controller adalah class dasar bawaan Laravel untuk membuat controller.
use App\Http\Controllers\Controller;

// penjelasan: Model MataPelajaran digunakan untuk mengambil, menyimpan, dan mengubah data pada tabel mata_pelajarans.
use App\Models\MataPelajaran;

// penjelasan: Request digunakan untuk mengambil data dari form tambah dan edit mata pelajaran.
use Illuminate\Http\Request;

// penjelasan: Rule digunakan untuk validasi unique dan pilihan nilai tertentu.
use Illuminate\Validation\Rule;

class MataPelajaranController extends Controller
{
    // penjelasan: Method routePrefix digunakan agar route bisa menyesuaikan role user yang sedang login.
    // penjelasan: Jika user login sebagai super_admin, maka prefix route adalah super-admin.
    // penjelasan: Jika user login sebagai admin, maka prefix route adalah admin.
    // penjelasan: Dengan method ini, view yang sama bisa dipakai oleh Super Admin dan Admin.
    private function routePrefix(): string
    {
        return auth()->user()->role === 'super_admin' ? 'super-admin' : 'admin';
    }

    // penjelasan: Method index digunakan untuk menampilkan daftar mata pelajaran.
    // penjelasan: Method ini dipanggil oleh route GET /super-admin/mata-pelajaran atau /admin/mata-pelajaran.
    // penjelasan: Method ini juga memproses pencarian dan filter kelompok/status.
    public function index(Request $request)
    {
        // penjelasan: Query awal dibuat dari model MataPelajaran.
        // penjelasan: Query dibuat bertahap agar mudah ditambah filter.
        $query = MataPelajaran::query();

        // penjelasan: Jika input search diisi, sistem mencari berdasarkan kode_mapel atau nama_mapel.
        // penjelasan: where(function) digunakan agar kondisi pencarian dikelompokkan dengan benar.
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('kode_mapel', 'like', '%' . $search . '%')
                    ->orWhere('nama_mapel', 'like', '%' . $search . '%');
            });
        }

        // penjelasan: Filter kelompok digunakan untuk menampilkan mapel umum, muatan lokal, atau ekstrakurikuler.
        if ($request->filled('kelompok')) {
            $query->where('kelompok', $request->kelompok);
        }

        // penjelasan: Filter status digunakan untuk menampilkan mata pelajaran aktif atau nonaktif.
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // penjelasan: Data diurutkan dari terbaru dan ditampilkan 10 data per halaman.
        // penjelasan: withQueryString() menjaga filter tetap aktif saat pindah halaman pagination.
        $mataPelajarans = $query->latest()->paginate(10)->withQueryString();

        // penjelasan: routePrefix dikirim ke view agar tombol tambah/edit/detail/status sesuai role login.
        $routePrefix = $this->routePrefix();

        // penjelasan: View ini menampilkan daftar mata pelajaran.
        return view('admin.pages.mata-pelajaran.index', compact('mataPelajarans', 'routePrefix'));
    }

    // penjelasan: Method create digunakan untuk menampilkan form tambah mata pelajaran.
    // penjelasan: Method ini dipanggil oleh route GET /mata-pelajaran/create.
    public function create()
    {
        // penjelasan: routePrefix dikirim agar form action menyesuaikan role login.
        $routePrefix = $this->routePrefix();

        // penjelasan: View ini menampilkan form tambah mata pelajaran.
        return view('admin.pages.mata-pelajaran.create', compact('routePrefix'));
    }

    // penjelasan: Method store digunakan untuk menyimpan mata pelajaran baru.
    // penjelasan: Method ini dipanggil oleh form tambah mata pelajaran melalui method POST.
    public function store(Request $request)
    {
        // penjelasan: Validasi memastikan data yang masuk sesuai aturan.
        // penjelasan: kode_mapel wajib unik agar kode mata pelajaran tidak dobel.
        // penjelasan: kelompok wajib salah satu dari umum, muatan_lokal, atau ekstrakurikuler.
        // penjelasan: status wajib salah satu dari aktif atau nonaktif.
        $validated = $request->validate([
            'kode_mapel' => ['required', 'string', 'max:30', 'unique:mata_pelajarans,kode_mapel'],
            'nama_mapel' => ['required', 'string', 'max:150'],
            'kelompok' => ['required', Rule::in(['umum', 'muatan_lokal', 'ekstrakurikuler'])],
            'deskripsi' => ['nullable', 'string'],
            'status' => ['required', Rule::in(['aktif', 'nonaktif'])],
        ], [
            'kode_mapel.required' => 'Kode mata pelajaran wajib diisi.',
            'kode_mapel.unique' => 'Kode mata pelajaran sudah digunakan.',
            'nama_mapel.required' => 'Nama mata pelajaran wajib diisi.',
            'kelompok.required' => 'Kelompok mata pelajaran wajib dipilih.',
            'status.required' => 'Status wajib dipilih.',
        ]);

        // penjelasan: Kode mapel dibuat huruf besar agar format data konsisten.
        // penjelasan: Contoh input mtk akan disimpan menjadi MTK.
        $validated['kode_mapel'] = strtoupper($validated['kode_mapel']);

        // penjelasan: Data yang sudah valid disimpan ke tabel mata_pelajarans.
        MataPelajaran::create($validated);

        // penjelasan: Setelah berhasil simpan, user diarahkan kembali ke daftar mata pelajaran.
        return redirect()
            ->route($this->routePrefix() . '.mata-pelajaran.index')
            ->with('success', 'Data mata pelajaran berhasil ditambahkan.');
    }

    // penjelasan: Method show digunakan untuk menampilkan detail mata pelajaran.
    // penjelasan: Parameter MataPelajaran $mataPelajaran otomatis diambil berdasarkan id pada URL.
    public function show(MataPelajaran $mataPelajaran)
    {
        // penjelasan: routePrefix dikirim ke view untuk tombol kembali dan edit.
        $routePrefix = $this->routePrefix();

        // penjelasan: View ini menampilkan detail data mata pelajaran.
        return view('admin.pages.mata-pelajaran.show', compact('mataPelajaran', 'routePrefix'));
    }

    // penjelasan: Method edit digunakan untuk menampilkan form edit mata pelajaran.
    // penjelasan: Parameter MataPelajaran $mataPelajaran otomatis mengambil data berdasarkan id pada URL.
    public function edit(MataPelajaran $mataPelajaran)
    {
        // penjelasan: routePrefix dikirim agar form update menyesuaikan role login.
        $routePrefix = $this->routePrefix();

        // penjelasan: View ini menampilkan form edit mata pelajaran.
        return view('admin.pages.mata-pelajaran.edit', compact('mataPelajaran', 'routePrefix'));
    }

    // penjelasan: Method update digunakan untuk menyimpan perubahan data mata pelajaran.
    // penjelasan: Method ini dipanggil oleh form edit melalui route PUT /mata-pelajaran/{mataPelajaran}.
    public function update(Request $request, MataPelajaran $mataPelajaran)
    {
        // penjelasan: Validasi unique pada kode_mapel mengabaikan data mata pelajaran yang sedang diedit.
        // penjelasan: Ini penting agar kode milik data yang sama tidak dianggap duplikat.
        $validated = $request->validate([
            'kode_mapel' => [
                'required',
                'string',
                'max:30',
                Rule::unique('mata_pelajarans', 'kode_mapel')->ignore($mataPelajaran->id),
            ],
            'nama_mapel' => ['required', 'string', 'max:150'],
            'kelompok' => ['required', Rule::in(['umum', 'muatan_lokal', 'ekstrakurikuler'])],
            'deskripsi' => ['nullable', 'string'],
            'status' => ['required', Rule::in(['aktif', 'nonaktif'])],
        ], [
            'kode_mapel.required' => 'Kode mata pelajaran wajib diisi.',
            'kode_mapel.unique' => 'Kode mata pelajaran sudah digunakan.',
            'nama_mapel.required' => 'Nama mata pelajaran wajib diisi.',
            'kelompok.required' => 'Kelompok mata pelajaran wajib dipilih.',
            'status.required' => 'Status wajib dipilih.',
        ]);

        // penjelasan: Kode mapel dibuat huruf besar agar formatnya konsisten.
        $validated['kode_mapel'] = strtoupper($validated['kode_mapel']);

        // penjelasan: Data mata pelajaran diperbarui pada tabel mata_pelajarans.
        $mataPelajaran->update($validated);

        // penjelasan: Setelah berhasil update, user diarahkan ke daftar mata pelajaran.
        return redirect()
            ->route($this->routePrefix() . '.mata-pelajaran.index')
            ->with('success', 'Data mata pelajaran berhasil diperbarui.');
    }

    // penjelasan: Method toggleStatus digunakan untuk mengubah status mata pelajaran aktif/nonaktif.
    // penjelasan: Data mata pelajaran tidak dihapus permanen agar nanti relasi jadwal dan nilai tetap aman.
    // penjelasan: Method ini dipanggil oleh tombol Aktifkan/Nonaktif pada halaman daftar mata pelajaran.
    public function toggleStatus(MataPelajaran $mataPelajaran)
    {
        // penjelasan: Jika status sekarang aktif, maka diubah menjadi nonaktif.
        // penjelasan: Jika status sekarang nonaktif, maka diubah menjadi aktif.
        $mataPelajaran->update([
            'status' => $mataPelajaran->status === 'aktif' ? 'nonaktif' : 'aktif',
        ]);

        // penjelasan: Setelah status berubah, user dikembalikan ke halaman sebelumnya.
        return back()->with('success', 'Status mata pelajaran berhasil diubah.');
    }
}
