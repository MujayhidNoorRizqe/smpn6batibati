<?php

// penjelasan: File ini adalah controller untuk Modul Tahun Ajaran.
// penjelasan: Controller ini dipakai oleh Super Admin dan Admin.
// penjelasan: Controller ini mengatur daftar, tambah, detail, edit, update, dan aktif/nonaktif tahun ajaran.
// penjelasan: Saat satu tahun ajaran dibuat aktif, tahun ajaran lain otomatis dibuat nonaktif.
// penjelasan: Semua validasi memakai pesan Bahasa Indonesia agar selaras dengan UI global.
// penjelasan: Nama tahun ajaran dan status wajib diisi.
// penjelasan: Tanggal mulai dan tanggal selesai bersifat opsional, tetapi jika diisi harus valid.

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
// penjelasan: Controller adalah class dasar Laravel untuk membuat controller.

use App\Models\TahunAjaran;
// penjelasan: Model TahunAjaran digunakan untuk mengambil, menyimpan, dan mengubah data tahun ajaran.

use Illuminate\Http\Request;
// penjelasan: Request digunakan untuk mengambil data dari form.

use Illuminate\Support\Facades\DB;
// penjelasan: DB digunakan untuk menjalankan transaksi database agar proses aktif/nonaktif tetap aman.

use Illuminate\Validation\Rule;
// penjelasan: Rule digunakan untuk validasi unique dan pilihan nilai tertentu.

class TahunAjaranController extends Controller
{
    /**
     * penjelasan: routePrefix digunakan agar view bisa dipakai oleh super admin dan admin.
     * penjelasan: Jika role user super_admin, routePrefix menjadi super-admin.
     * penjelasan: Jika role user admin, routePrefix menjadi admin.
     */
    private function routePrefix(): string
    {
        return auth()->user()->role === 'super_admin' ? 'super-admin' : 'admin';
    }

    /**
     * penjelasan: Method index menampilkan daftar tahun ajaran.
     * penjelasan: Method ini juga memproses pencarian dan filter status.
     */
    public function index(Request $request)
    {
        $query = TahunAjaran::withCount('semesters');

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where('nama_tahun_ajaran', 'like', '%' . $search . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $tahunAjarans = $query->latest()->paginate(10)->withQueryString();

        $routePrefix = $this->routePrefix();

        return view('admin.pages.tahun-ajaran.index', compact('tahunAjarans', 'routePrefix'));
    }

    /**
     * penjelasan: Method create menampilkan form tambah tahun ajaran.
     */
    public function create()
    {
        $routePrefix = $this->routePrefix();

        return view('admin.pages.tahun-ajaran.create', compact('routePrefix'));
    }

    /**
     * penjelasan: Method store menyimpan data tahun ajaran baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_tahun_ajaran' => ['required', 'string', 'max:50', 'unique:tahun_ajarans,nama_tahun_ajaran'],
            'tanggal_mulai' => ['nullable', 'date'],
            'tanggal_selesai' => ['nullable', 'date', 'after_or_equal:tanggal_mulai'],
            'status' => ['required', Rule::in(['aktif', 'nonaktif'])],
        ], $this->validationMessages());

        $validated = $this->normalizeFields($validated);

        DB::transaction(function () use ($validated) {
            if ($validated['status'] === 'aktif') {
                TahunAjaran::where('status', 'aktif')->update(['status' => 'nonaktif']);
            }

            TahunAjaran::create($validated);
        });

        return redirect()
            ->route($this->routePrefix() . '.tahun-ajaran.index')
            ->with('success', 'Data tahun ajaran berhasil ditambahkan.');
    }

    /**
     * penjelasan: Method show menampilkan detail tahun ajaran.
     */
    public function show(TahunAjaran $tahunAjaran)
    {
        $tahunAjaran->load('semesters');

        $routePrefix = $this->routePrefix();

        return view('admin.pages.tahun-ajaran.show', compact('tahunAjaran', 'routePrefix'));
    }

    /**
     * penjelasan: Method edit menampilkan form edit tahun ajaran.
     */
    public function edit(TahunAjaran $tahunAjaran)
    {
        $routePrefix = $this->routePrefix();

        return view('admin.pages.tahun-ajaran.edit', compact('tahunAjaran', 'routePrefix'));
    }

    /**
     * penjelasan: Method update menyimpan perubahan data tahun ajaran.
     */
    public function update(Request $request, TahunAjaran $tahunAjaran)
    {
        $validated = $request->validate([
            'nama_tahun_ajaran' => [
                'required',
                'string',
                'max:50',
                Rule::unique('tahun_ajarans', 'nama_tahun_ajaran')->ignore($tahunAjaran->id),
            ],
            'tanggal_mulai' => ['nullable', 'date'],
            'tanggal_selesai' => ['nullable', 'date', 'after_or_equal:tanggal_mulai'],
            'status' => ['required', Rule::in(['aktif', 'nonaktif'])],
        ], $this->validationMessages());

        $validated = $this->normalizeFields($validated);

        DB::transaction(function () use ($tahunAjaran, $validated) {
            if ($validated['status'] === 'aktif') {
                TahunAjaran::where('id', '!=', $tahunAjaran->id)->update(['status' => 'nonaktif']);
            }

            $tahunAjaran->update($validated);
        });

        return redirect()
            ->route($this->routePrefix() . '.tahun-ajaran.index')
            ->with('success', 'Data tahun ajaran berhasil diperbarui.');
    }

    /**
     * penjelasan: Method toggleStatus mengubah status tahun ajaran.
     * penjelasan: Jika tahun ajaran diaktifkan, tahun ajaran lain otomatis menjadi nonaktif.
     */
    public function toggleStatus(TahunAjaran $tahunAjaran)
    {
        DB::transaction(function () use ($tahunAjaran) {
            if ($tahunAjaran->status === 'aktif') {
                $tahunAjaran->update(['status' => 'nonaktif']);
                return;
            }

            TahunAjaran::where('id', '!=', $tahunAjaran->id)->update(['status' => 'nonaktif']);

            $tahunAjaran->update(['status' => 'aktif']);
        });

        $message = $tahunAjaran->fresh()->status === 'aktif'
            ? 'Tahun ajaran berhasil diaktifkan. Tahun ajaran aktif lainnya otomatis dinonaktifkan.'
            : 'Tahun ajaran berhasil dinonaktifkan.';

        return back()->with('success', $message);
    }

    /**
     * penjelasan: Method validationMessages menyimpan pesan validasi Bahasa Indonesia.
     */
    private function validationMessages(): array
    {
        return [
            'nama_tahun_ajaran.required' => 'Nama tahun ajaran wajib diisi.',
            'nama_tahun_ajaran.string' => 'Nama tahun ajaran harus berupa teks.',
            'nama_tahun_ajaran.max' => 'Nama tahun ajaran maksimal 50 karakter.',
            'nama_tahun_ajaran.unique' => 'Nama tahun ajaran sudah digunakan.',

            'tanggal_mulai.date' => 'Tanggal mulai tidak valid.',

            'tanggal_selesai.date' => 'Tanggal selesai tidak valid.',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai tidak boleh lebih kecil dari tanggal mulai.',

            'status.required' => 'Status wajib dipilih.',
            'status.in' => 'Status yang dipilih tidak valid.',
        ];
    }

    /**
     * penjelasan: Method normalizeFields membersihkan input sebelum disimpan.
     */
    private function normalizeFields(array $validated): array
    {
        $validated['nama_tahun_ajaran'] = trim($validated['nama_tahun_ajaran']);

        if (array_key_exists('tanggal_mulai', $validated) && $validated['tanggal_mulai'] === '') {
            $validated['tanggal_mulai'] = null;
        }

        if (array_key_exists('tanggal_selesai', $validated) && $validated['tanggal_selesai'] === '') {
            $validated['tanggal_selesai'] = null;
        }

        return $validated;
    }
}
