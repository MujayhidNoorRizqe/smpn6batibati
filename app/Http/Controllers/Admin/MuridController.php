<?php

// penjelasan: File ini adalah controller untuk modul Data Murid.
// penjelasan: Controller ini dipakai oleh Super Admin dan Admin.
// penjelasan: Controller ini mengatur daftar murid, tambah murid, detail murid, edit murid, update murid, upload foto, dan aktif/nonaktif murid.
// penjelasan: Controller ini memanggil Model Murid, Kelas, dan WaliMurid.
// penjelasan: Semua validasi memakai pesan Bahasa Indonesia agar selaras dengan UI global.
// penjelasan: Nama murid, kelas, jenis kelamin, wali murid, NISN, tanggal lahir, dan status wajib diisi.
// penjelasan: NIS dan foto bersifat opsional.
// penjelasan: Pada halaman index, data murid tidak langsung tampil sebelum user melakukan filter.
// penjelasan: Setelah filter dilakukan, daftar murid diurutkan berdasarkan nama murid secara alfabetis seperti daftar absen.

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
// penjelasan: Controller adalah class dasar bawaan Laravel untuk membuat controller.

use App\Models\Kelas;
// penjelasan: Model Kelas digunakan untuk memilih kelas pada form murid.

use App\Models\Murid;
// penjelasan: Model Murid digunakan untuk mengambil, menyimpan, dan mengubah data pada tabel murids.

use App\Models\WaliMurid;
// penjelasan: Model WaliMurid digunakan untuk memilih wali murid pada form murid.

use Illuminate\Http\Request;
// penjelasan: Request digunakan untuk mengambil data dari form tambah, edit, dan filter murid.

use Illuminate\Pagination\LengthAwarePaginator;
// penjelasan: LengthAwarePaginator digunakan agar variabel $murids tetap aman walaupun data belum difilter.

use Illuminate\Support\Facades\Storage;
// penjelasan: Storage digunakan untuk menyimpan dan menghapus foto murid.

use Illuminate\Validation\Rule;
// penjelasan: Rule digunakan untuk validasi unique dan pilihan nilai tertentu.

use Illuminate\Validation\ValidationException;
// penjelasan: ValidationException digunakan untuk mengembalikan error validasi manual dalam Bahasa Indonesia.

class MuridController extends Controller
{
    /**
     * penjelasan: Method routePrefix digunakan agar route bisa menyesuaikan role user yang sedang login.
     * penjelasan: Jika user login sebagai super_admin, maka prefix route adalah super-admin.
     * penjelasan: Jika user login sebagai admin, maka prefix route adalah admin.
     */
    private function routePrefix(): string
    {
        return auth()->user()->role === 'super_admin' ? 'super-admin' : 'admin';
    }

    /**
     * penjelasan: Method index digunakan untuk menampilkan halaman Data Murid.
     * penjelasan: Method ini dipanggil oleh route GET /super-admin/murid atau /admin/murid.
     * penjelasan: Sebelum filter dilakukan, daftar murid tidak ditampilkan.
     * penjelasan: Setelah filter dilakukan, daftar murid tampil urut berdasarkan nama murid secara alfabetis.
     */
    public function index(Request $request)
    {
        $hasFilter = $request->filled('search')
            || $request->filled('kelas_id')
            || $request->filled('jenis_kelamin')
            || $request->filled('status');

        $kelasList = Kelas::where('status', 'aktif')
            ->orderBy('tingkat')
            ->orderBy('nama_kelas')
            ->get();

        $routePrefix = $this->routePrefix();

        if (! $hasFilter) {
            $murids = new LengthAwarePaginator(
                [],
                0,
                10,
                LengthAwarePaginator::resolveCurrentPage(),
                [
                    'path' => $request->url(),
                    'query' => $request->query(),
                ]
            );

            return view('admin.pages.murid.index', compact(
                'murids',
                'kelasList',
                'routePrefix',
                'hasFilter'
            ));
        }

        $query = Murid::with(['kelas', 'waliMurid']);

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('nama_murid', 'like', '%' . $search . '%')
                    ->orWhere('nis', 'like', '%' . $search . '%')
                    ->orWhere('nisn', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('kelas_id')) {
            $query->where('kelas_id', $request->kelas_id);
        }

        if ($request->filled('jenis_kelamin')) {
            $query->where('jenis_kelamin', $request->jenis_kelamin);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $murids = $query
            ->orderByRaw('LOWER(nama_murid) ASC')
            ->orderBy('nama_murid')
            ->orderBy('id')
            ->paginate(10)
            ->withQueryString();

        return view('admin.pages.murid.index', compact(
            'murids',
            'kelasList',
            'routePrefix',
            'hasFilter'
        ));
    }

    /**
     * penjelasan: Method create digunakan untuk menampilkan form tambah murid.
     * penjelasan: Method ini dipanggil oleh route GET /murid/create.
     */
    public function create()
    {
        $kelasList = Kelas::where('status', 'aktif')
            ->orderBy('tingkat')
            ->orderBy('nama_kelas')
            ->get();

        $waliMurids = WaliMurid::where('status', 'aktif')
            ->orderBy('nama_wali')
            ->get();

        $routePrefix = $this->routePrefix();

        return view('admin.pages.murid.create', compact('kelasList', 'waliMurids', 'routePrefix'));
    }

    /**
     * penjelasan: Method store digunakan untuk menyimpan data murid baru.
     * penjelasan: Method ini dipanggil oleh form tambah murid.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kelas_id' => ['required', 'exists:kelas,id'],
            'wali_murid_id' => ['required', 'exists:wali_murids,id'],
            'nis' => ['nullable', 'string', 'max:50', 'unique:murids,nis'],
            'nisn' => ['required', 'string', 'max:50', 'unique:murids,nisn'],
            'nama_murid' => ['required', 'string', 'max:150'],
            'jenis_kelamin' => ['required', Rule::in(['L', 'P'])],
            'tempat_lahir' => ['nullable', 'string', 'max:100'],
            'tanggal_lahir' => ['required', 'date'],
            'agama' => ['nullable', 'string', 'max:50'],
            'alamat' => ['nullable', 'string'],
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'status' => ['required', Rule::in(['aktif', 'nonaktif'])],
        ], $this->validationMessages());

        $this->ensureActiveKelas($validated['kelas_id']);
        $this->ensureActiveWaliMurid($validated['wali_murid_id']);

        $validated = $this->normalizeFields($validated);

        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')->store('foto/murid', 'public');
        }

        Murid::create($validated);

        return redirect()
            ->route($this->routePrefix() . '.murid.index')
            ->with('success', 'Data murid berhasil ditambahkan.');
    }

    /**
     * penjelasan: Method show digunakan untuk menampilkan detail murid.
     * penjelasan: Parameter Murid $murid otomatis mengambil data murid berdasarkan id pada URL.
     */
    public function show(Murid $murid)
    {
        $murid->load(['kelas', 'waliMurid']);

        $routePrefix = $this->routePrefix();

        return view('admin.pages.murid.show', compact('murid', 'routePrefix'));
    }

    /**
     * penjelasan: Method edit digunakan untuk menampilkan form edit murid.
     * penjelasan: Method ini dipanggil oleh route GET /murid/{murid}/edit.
     */
    public function edit(Murid $murid)
    {
        $kelasList = Kelas::where(function ($query) use ($murid) {
                $query->where('status', 'aktif')
                    ->orWhere('id', $murid->kelas_id);
            })
            ->orderBy('tingkat')
            ->orderBy('nama_kelas')
            ->get();

        $waliMurids = WaliMurid::where(function ($query) use ($murid) {
                $query->where('status', 'aktif')
                    ->orWhere('id', $murid->wali_murid_id);
            })
            ->orderBy('nama_wali')
            ->get();

        $routePrefix = $this->routePrefix();

        return view('admin.pages.murid.edit', compact('murid', 'kelasList', 'waliMurids', 'routePrefix'));
    }

    /**
     * penjelasan: Method update digunakan untuk menyimpan perubahan data murid.
     * penjelasan: Method ini dipanggil oleh form edit murid.
     */
    public function update(Request $request, Murid $murid)
    {
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
                'required',
                'string',
                'max:50',
                Rule::unique('murids', 'nisn')->ignore($murid->id),
            ],
            'nama_murid' => ['required', 'string', 'max:150'],
            'jenis_kelamin' => ['required', Rule::in(['L', 'P'])],
            'tempat_lahir' => ['nullable', 'string', 'max:100'],
            'tanggal_lahir' => ['required', 'date'],
            'agama' => ['nullable', 'string', 'max:50'],
            'alamat' => ['nullable', 'string'],
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'status' => ['required', Rule::in(['aktif', 'nonaktif'])],
        ], $this->validationMessages());

        $this->ensureActiveKelas($validated['kelas_id'], $murid->kelas_id);
        $this->ensureActiveWaliMurid($validated['wali_murid_id'], $murid->wali_murid_id);

        $validated = $this->normalizeFields($validated);

        if ($request->hasFile('foto')) {
            if ($murid->foto && Storage::disk('public')->exists($murid->foto)) {
                Storage::disk('public')->delete($murid->foto);
            }

            $validated['foto'] = $request->file('foto')->store('foto/murid', 'public');
        }

        $murid->update($validated);

        return redirect()
            ->route($this->routePrefix() . '.murid.index')
            ->with('success', 'Data murid berhasil diperbarui.');
    }

    /**
     * penjelasan: Method toggleStatus digunakan untuk mengubah status murid aktif/nonaktif.
     * penjelasan: Data murid tidak dihapus permanen agar riwayat absensi dan nilai tetap aman.
     */
    public function toggleStatus(Murid $murid)
    {
        $newStatus = $murid->status === 'aktif' ? 'nonaktif' : 'aktif';

        $murid->update([
            'status' => $newStatus,
        ]);

        $message = $newStatus === 'aktif'
            ? 'Murid berhasil diaktifkan.'
            : 'Murid berhasil dinonaktifkan.';

        return back()->with('success', $message);
    }

    /**
     * penjelasan: Method validationMessages menyimpan semua pesan validasi Bahasa Indonesia.
     * penjelasan: Pesan ini dipakai saat tambah dan edit murid.
     */
    private function validationMessages(): array
    {
        return [
            'kelas_id.required' => 'Kelas wajib dipilih.',
            'kelas_id.exists' => 'Kelas tidak valid.',

            'wali_murid_id.required' => 'Wali murid wajib dipilih.',
            'wali_murid_id.exists' => 'Wali murid tidak valid.',

            'nis.string' => 'NIS harus berupa teks.',
            'nis.max' => 'NIS maksimal 50 karakter.',
            'nis.unique' => 'NIS sudah digunakan.',

            'nisn.required' => 'NISN wajib diisi.',
            'nisn.string' => 'NISN harus berupa teks.',
            'nisn.max' => 'NISN maksimal 50 karakter.',
            'nisn.unique' => 'NISN sudah digunakan.',

            'nama_murid.required' => 'Nama murid wajib diisi.',
            'nama_murid.string' => 'Nama murid harus berupa teks.',
            'nama_murid.max' => 'Nama murid maksimal 150 karakter.',

            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih.',
            'jenis_kelamin.in' => 'Jenis kelamin yang dipilih tidak valid.',

            'tempat_lahir.string' => 'Tempat lahir harus berupa teks.',
            'tempat_lahir.max' => 'Tempat lahir maksimal 100 karakter.',

            'tanggal_lahir.required' => 'Tanggal lahir wajib dipilih.',
            'tanggal_lahir.date' => 'Tanggal lahir tidak valid.',

            'agama.string' => 'Agama harus berupa teks.',
            'agama.max' => 'Agama maksimal 50 karakter.',

            'alamat.string' => 'Alamat harus berupa teks.',

            'foto.image' => 'File foto harus berupa gambar.',
            'foto.mimes' => 'Format foto harus jpg, jpeg, png, atau webp.',
            'foto.max' => 'Ukuran foto maksimal 2MB.',

            'status.required' => 'Status wajib dipilih.',
            'status.in' => 'Status yang dipilih tidak valid.',
        ];
    }

    /**
     * penjelasan: Method ensureActiveKelas memastikan kelas yang dipilih aktif.
     * penjelasan: Saat edit, kelas lama yang sedang terhubung tetap diperbolehkan agar data lama tidak rusak.
     */
    private function ensureActiveKelas(int|string $kelasId, int|string|null $currentKelasId = null): void
    {
        if ($currentKelasId !== null && (string) $kelasId === (string) $currentKelasId) {
            return;
        }

        $kelasAktif = Kelas::where('id', $kelasId)
            ->where('status', 'aktif')
            ->exists();

        if (! $kelasAktif) {
            throw ValidationException::withMessages([
                'kelas_id' => 'Kelas yang dipilih harus berstatus aktif.',
            ]);
        }
    }

    /**
     * penjelasan: Method ensureActiveWaliMurid memastikan wali murid yang dipilih aktif.
     * penjelasan: Saat edit, wali lama yang sedang terhubung tetap diperbolehkan agar data lama tidak rusak.
     */
    private function ensureActiveWaliMurid(int|string $waliMuridId, int|string|null $currentWaliMuridId = null): void
    {
        if ($currentWaliMuridId !== null && (string) $waliMuridId === (string) $currentWaliMuridId) {
            return;
        }

        $waliAktif = WaliMurid::where('id', $waliMuridId)
            ->where('status', 'aktif')
            ->exists();

        if (! $waliAktif) {
            throw ValidationException::withMessages([
                'wali_murid_id' => 'Wali murid yang dipilih harus berstatus aktif.',
            ]);
        }
    }

    /**
     * penjelasan: Method normalizeFields membersihkan input sebelum disimpan.
     * penjelasan: Field opsional kosong akan disimpan sebagai null.
     */
    private function normalizeFields(array $validated): array
    {
        $nullableFields = [
            'nis',
            'tempat_lahir',
            'agama',
            'alamat',
        ];

        foreach ($nullableFields as $field) {
            if (array_key_exists($field, $validated) && $validated[$field] === '') {
                $validated[$field] = null;
            }
        }

        if (isset($validated['nama_murid'])) {
            $validated['nama_murid'] = trim($validated['nama_murid']);
        }

        if (isset($validated['nis']) && $validated['nis'] !== null) {
            $validated['nis'] = trim($validated['nis']);
        }

        if (isset($validated['nisn'])) {
            $validated['nisn'] = trim($validated['nisn']);
        }

        if (isset($validated['tempat_lahir']) && $validated['tempat_lahir'] !== null) {
            $validated['tempat_lahir'] = trim($validated['tempat_lahir']);
        }

        if (isset($validated['agama']) && $validated['agama'] !== null) {
            $validated['agama'] = trim($validated['agama']);
        }

        if (isset($validated['alamat']) && $validated['alamat'] !== null) {
            $validated['alamat'] = trim($validated['alamat']);
        }

        return $validated;
    }
}
