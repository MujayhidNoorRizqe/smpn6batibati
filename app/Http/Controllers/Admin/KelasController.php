<?php

// penjelasan: File ini adalah controller untuk modul Data Kelas.
// penjelasan: Controller ini dipakai oleh Super Admin dan Admin.
// penjelasan: Controller ini mengatur daftar kelas, tambah kelas, detail kelas, edit kelas, update kelas, dan aktif/nonaktif kelas.
// penjelasan: File ini dipanggil dari route /super-admin/kelas dan /admin/kelas.
// penjelasan: Semua validasi memakai pesan Bahasa Indonesia agar selaras dengan UI global.
// penjelasan: Nama kelas, tingkat, wali kelas, dan status kelas wajib diisi.
// penjelasan: Controller ini juga menghitung total jumlah siswa per kelas melalui relasi murids.
// penjelasan: Pada halaman index, data kelas dikelompokkan berdasarkan tingkat agar tampilan lebih rapi.

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Pegawai;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class KelasController extends Controller
{
    /**
     * penjelasan: Method routePrefix digunakan agar route bisa menyesuaikan role login.
     * penjelasan: Jika user login sebagai super_admin, route prefix-nya super-admin.
     * penjelasan: Jika user login sebagai admin, route prefix-nya admin.
     */
    private function routePrefix(): string
    {
        return auth()->user()->role === 'super_admin' ? 'super-admin' : 'admin';
    }

    /**
     * penjelasan: Method index digunakan untuk menampilkan daftar kelas.
     * penjelasan: Method ini dipanggil oleh route GET /super-admin/kelas atau /admin/kelas.
     * penjelasan: Data kelas ditampilkan berkelompok berdasarkan tingkat, contoh Tingkat 7, Tingkat 8, Tingkat 9.
     */
    public function index(Request $request)
    {
        $query = Kelas::with('waliKelas')
            ->withCount(['murids as total_siswa']);

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('nama_kelas', 'like', '%' . $search . '%')
                    ->orWhere('tingkat', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('tingkat')) {
            $query->where('tingkat', $request->tingkat);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $kelasList = $query
            ->orderBy('tingkat')
            ->orderBy('nama_kelas')
            ->get();

        $kelasPerTingkat = $kelasList->groupBy(function ($kelas) {
            return $kelas->tingkat ?: 'Lainnya';
        });

        $totalKelas = $kelasList->count();
        $totalSiswa = $kelasList->sum('total_siswa');

        $routePrefix = $this->routePrefix();

        return view('admin.pages.kelas.index', compact(
            'kelasList',
            'kelasPerTingkat',
            'totalKelas',
            'totalSiswa',
            'routePrefix'
        ));
    }

    /**
     * penjelasan: Method create digunakan untuk menampilkan form tambah kelas.
     * penjelasan: Method ini dipanggil oleh route GET /super-admin/kelas/create atau /admin/kelas/create.
     */
    public function create()
    {
        $gurus = Pegawai::where('jenis_pegawai', 'guru')
            ->where('status', 'aktif')
            ->orderBy('nama_pegawai')
            ->get();

        $routePrefix = $this->routePrefix();

        return view('admin.pages.kelas.create', compact('gurus', 'routePrefix'));
    }

    /**
     * penjelasan: Method store digunakan untuk menyimpan data kelas baru.
     * penjelasan: Method ini dipanggil oleh form tambah kelas.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_kelas' => ['required', 'string', 'max:50', 'unique:kelas,nama_kelas'],
            'tingkat' => ['required', Rule::in(['7', '8', '9'])],
            'wali_kelas_id' => ['required', 'exists:pegawais,id'],
            'status' => ['required', Rule::in(['aktif', 'nonaktif'])],
        ], $this->validationMessages());

        $this->ensureValidWaliKelas($validated['wali_kelas_id']);

        $validated['nama_kelas'] = strtoupper(trim($validated['nama_kelas']));

        Kelas::create($validated);

        return redirect()
            ->route($this->routePrefix() . '.kelas.index')
            ->with('success', 'Data kelas berhasil ditambahkan.');
    }

    /**
     * penjelasan: Method show digunakan untuk menampilkan detail kelas.
     * penjelasan: Parameter Kelas $kelas otomatis mengambil data berdasarkan id pada URL.
     */
    public function show(Kelas $kelas)
    {
        $kelas->load('waliKelas');
        $kelas->loadCount(['murids as total_siswa']);

        $routePrefix = $this->routePrefix();

        return view('admin.pages.kelas.show', compact('kelas', 'routePrefix'));
    }

    /**
     * penjelasan: Method edit digunakan untuk menampilkan form edit kelas.
     * penjelasan: Method ini dipanggil oleh route GET /kelas/{kelas}/edit.
     */
    public function edit(Kelas $kelas)
    {
        $gurus = Pegawai::where(function ($query) use ($kelas) {
                $query->where(function ($q) {
                    $q->where('jenis_pegawai', 'guru')
                        ->where('status', 'aktif');
                })
                ->orWhere('id', $kelas->wali_kelas_id);
            })
            ->orderBy('nama_pegawai')
            ->get();

        $routePrefix = $this->routePrefix();

        return view('admin.pages.kelas.edit', compact('kelas', 'gurus', 'routePrefix'));
    }

    /**
     * penjelasan: Method update digunakan untuk menyimpan perubahan data kelas.
     * penjelasan: Method ini dipanggil oleh form edit kelas.
     */
    public function update(Request $request, Kelas $kelas)
    {
        $validated = $request->validate([
            'nama_kelas' => [
                'required',
                'string',
                'max:50',
                Rule::unique('kelas', 'nama_kelas')->ignore($kelas->id),
            ],
            'tingkat' => ['required', Rule::in(['7', '8', '9'])],
            'wali_kelas_id' => ['required', 'exists:pegawais,id'],
            'status' => ['required', Rule::in(['aktif', 'nonaktif'])],
        ], $this->validationMessages());

        $this->ensureValidWaliKelas($validated['wali_kelas_id']);

        $validated['nama_kelas'] = strtoupper(trim($validated['nama_kelas']));

        $kelas->update($validated);

        return redirect()
            ->route($this->routePrefix() . '.kelas.index')
            ->with('success', 'Data kelas berhasil diperbarui.');
    }

    /**
     * penjelasan: Method toggleStatus digunakan untuk mengubah status kelas aktif/nonaktif.
     * penjelasan: Data kelas tidak dihapus permanen agar relasi murid, jadwal, dan nilai tetap aman.
     */
    public function toggleStatus(Kelas $kelas)
    {
        $newStatus = $kelas->status === 'aktif' ? 'nonaktif' : 'aktif';

        $kelas->update([
            'status' => $newStatus,
        ]);

        $message = $newStatus === 'aktif'
            ? 'Kelas berhasil diaktifkan.'
            : 'Kelas berhasil dinonaktifkan.';

        return back()->with('success', $message);
    }

    /**
     * penjelasan: Method ensureValidWaliKelas memastikan wali kelas berasal dari pegawai jenis guru yang aktif.
     * penjelasan: Ini penting karena wali kelas wajib dan tidak boleh berasal dari staff/nonaktif.
     */
    private function ensureValidWaliKelas(int|string $waliKelasId): void
    {
        $guruValid = Pegawai::where('id', $waliKelasId)
            ->where('jenis_pegawai', 'guru')
            ->where('status', 'aktif')
            ->exists();

        if (! $guruValid) {
            abort(
                redirect()
                    ->back()
                    ->withErrors(['wali_kelas_id' => 'Wali kelas wajib berasal dari pegawai guru yang aktif.'])
                    ->withInput()
            );
        }
    }

    /**
     * penjelasan: Method validationMessages menyimpan semua pesan validasi Bahasa Indonesia.
     * penjelasan: Pesan ini dipakai saat tambah dan edit kelas.
     */
    private function validationMessages(): array
    {
        return [
            'nama_kelas.required' => 'Nama kelas wajib diisi.',
            'nama_kelas.string' => 'Nama kelas harus berupa teks.',
            'nama_kelas.max' => 'Nama kelas maksimal 50 karakter.',
            'nama_kelas.unique' => 'Nama kelas sudah digunakan.',

            'tingkat.required' => 'Tingkat wajib dipilih.',
            'tingkat.in' => 'Tingkat yang dipilih tidak valid.',

            'wali_kelas_id.required' => 'Wali kelas wajib dipilih.',
            'wali_kelas_id.exists' => 'Wali kelas tidak valid.',

            'status.required' => 'Status kelas wajib dipilih.',
            'status.in' => 'Status kelas yang dipilih tidak valid.',
        ];
    }
}
