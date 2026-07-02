<?php

// penjelasan: File ini adalah controller untuk modul Data Mata Pelajaran.
// penjelasan: Controller ini dipakai oleh Super Admin dan Admin.
// penjelasan: Controller ini mengatur daftar mata pelajaran, tambah, detail, edit, update, dan aktif/nonaktif.
// penjelasan: File ini dipanggil dari route /super-admin/mata-pelajaran dan /admin/mata-pelajaran.
// penjelasan: Semua validasi memakai pesan Bahasa Indonesia agar selaras dengan UI global.
// penjelasan: Kode mapel, nama mapel, kelompok, dan status wajib diisi.
// penjelasan: Deskripsi bersifat opsional.

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
// penjelasan: Controller adalah class dasar bawaan Laravel untuk membuat controller.

use App\Models\MataPelajaran;
// penjelasan: Model MataPelajaran digunakan untuk mengambil, menyimpan, dan mengubah data pada tabel mata_pelajarans.

use Illuminate\Http\Request;
// penjelasan: Request digunakan untuk mengambil data dari form tambah dan edit mata pelajaran.

use Illuminate\Validation\Rule;
// penjelasan: Rule digunakan untuk validasi unique dan pilihan nilai tertentu.

class MataPelajaranController extends Controller
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
     * penjelasan: Method index digunakan untuk menampilkan daftar mata pelajaran.
     * penjelasan: Method ini dipanggil oleh route GET /super-admin/mata-pelajaran atau /admin/mata-pelajaran.
     */
    public function index(Request $request)
    {
        $query = MataPelajaran::query();

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('kode_mapel', 'like', '%' . $search . '%')
                    ->orWhere('nama_mapel', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('kelompok')) {
            $query->where('kelompok', $request->kelompok);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $mataPelajarans = $query->latest()->paginate(10)->withQueryString();

        $routePrefix = $this->routePrefix();

        return view('admin.pages.mata-pelajaran.index', compact('mataPelajarans', 'routePrefix'));
    }

    /**
     * penjelasan: Method create digunakan untuk menampilkan form tambah mata pelajaran.
     */
    public function create()
    {
        $routePrefix = $this->routePrefix();

        return view('admin.pages.mata-pelajaran.create', compact('routePrefix'));
    }

    /**
     * penjelasan: Method store digunakan untuk menyimpan mata pelajaran baru.
     */
    public function store(Request $request)
    {
        // penjelasan: Kode mapel dirapikan sebelum validasi agar format database konsisten.
        $request->merge([
            'kode_mapel' => strtoupper(trim((string) $request->input('kode_mapel'))),
        ]);

        $validated = $request->validate([
            'kode_mapel' => ['required', 'string', 'max:30', 'unique:mata_pelajarans,kode_mapel'],
            'nama_mapel' => ['required', 'string', 'max:150'],
            'kelompok' => ['required', Rule::in(['umum', 'muatan_lokal', 'ekstrakurikuler'])],
            'deskripsi' => ['nullable', 'string'],
            'status' => ['required', Rule::in(['aktif', 'nonaktif'])],
        ], $this->validationMessages());

        $validated = $this->normalizeFields($validated);

        MataPelajaran::create($validated);

        return redirect()
            ->route($this->routePrefix() . '.mata-pelajaran.index')
            ->with('success', 'Data mata pelajaran berhasil ditambahkan.');
    }

    /**
     * penjelasan: Method show digunakan untuk menampilkan detail mata pelajaran.
     */
    public function show(MataPelajaran $mataPelajaran)
    {
        $routePrefix = $this->routePrefix();

        return view('admin.pages.mata-pelajaran.show', compact('mataPelajaran', 'routePrefix'));
    }

    /**
     * penjelasan: Method edit digunakan untuk menampilkan form edit mata pelajaran.
     */
    public function edit(MataPelajaran $mataPelajaran)
    {
        $routePrefix = $this->routePrefix();

        return view('admin.pages.mata-pelajaran.edit', compact('mataPelajaran', 'routePrefix'));
    }

    /**
     * penjelasan: Method update digunakan untuk menyimpan perubahan data mata pelajaran.
     */
    public function update(Request $request, MataPelajaran $mataPelajaran)
    {
        $request->merge([
            'kode_mapel' => strtoupper(trim((string) $request->input('kode_mapel'))),
        ]);

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
        ], $this->validationMessages());

        $validated = $this->normalizeFields($validated);

        $mataPelajaran->update($validated);

        return redirect()
            ->route($this->routePrefix() . '.mata-pelajaran.index')
            ->with('success', 'Data mata pelajaran berhasil diperbarui.');
    }

    /**
     * penjelasan: Method toggleStatus digunakan untuk mengubah status mata pelajaran aktif/nonaktif.
     * penjelasan: Data mata pelajaran tidak dihapus permanen agar nanti relasi jadwal dan nilai tetap aman.
     */
    public function toggleStatus(MataPelajaran $mataPelajaran)
    {
        $newStatus = $mataPelajaran->status === 'aktif' ? 'nonaktif' : 'aktif';

        $mataPelajaran->update([
            'status' => $newStatus,
        ]);

        $message = $newStatus === 'aktif'
            ? 'Mata pelajaran berhasil diaktifkan.'
            : 'Mata pelajaran berhasil dinonaktifkan.';

        return back()->with('success', $message);
    }

    /**
     * penjelasan: Method validationMessages menyimpan pesan validasi Bahasa Indonesia.
     */
    private function validationMessages(): array
    {
        return [
            'kode_mapel.required' => 'Kode mata pelajaran wajib diisi.',
            'kode_mapel.string' => 'Kode mata pelajaran harus berupa teks.',
            'kode_mapel.max' => 'Kode mata pelajaran maksimal 30 karakter.',
            'kode_mapel.unique' => 'Kode mata pelajaran sudah digunakan.',

            'nama_mapel.required' => 'Nama mata pelajaran wajib diisi.',
            'nama_mapel.string' => 'Nama mata pelajaran harus berupa teks.',
            'nama_mapel.max' => 'Nama mata pelajaran maksimal 150 karakter.',

            'kelompok.required' => 'Kelompok mata pelajaran wajib dipilih.',
            'kelompok.in' => 'Kelompok mata pelajaran yang dipilih tidak valid.',

            'deskripsi.string' => 'Deskripsi harus berupa teks.',

            'status.required' => 'Status wajib dipilih.',
            'status.in' => 'Status yang dipilih tidak valid.',
        ];
    }

    /**
     * penjelasan: Method normalizeFields membersihkan input sebelum disimpan.
     */
    private function normalizeFields(array $validated): array
    {
        $validated['kode_mapel'] = strtoupper(trim($validated['kode_mapel']));
        $validated['nama_mapel'] = trim($validated['nama_mapel']);

        if (array_key_exists('deskripsi', $validated)) {
            $validated['deskripsi'] = $validated['deskripsi'] === null || trim($validated['deskripsi']) === ''
                ? null
                : trim($validated['deskripsi']);
        }

        return $validated;
    }
}
