<?php

// penjelasan: File ini adalah controller untuk Modul Tahun Ajaran.
// penjelasan: Controller ini dipakai oleh Super Admin dan Admin.
// penjelasan: Controller ini mengatur daftar, tambah, detail, edit, update, dan aktif/nonaktif tahun ajaran.
// penjelasan: Saat satu tahun ajaran dibuat aktif, tahun ajaran lain otomatis dibuat nonaktif.

namespace App\Http\Controllers\Admin;

// penjelasan: Controller adalah class dasar Laravel untuk membuat controller.
use App\Http\Controllers\Controller;

// penjelasan: Model TahunAjaran digunakan untuk mengambil, menyimpan, dan mengubah data tahun ajaran.
use App\Models\TahunAjaran;

// penjelasan: DB digunakan untuk menjalankan transaksi database agar proses aktif/nonaktif tetap aman.
use Illuminate\Support\Facades\DB;

// penjelasan: Request digunakan untuk mengambil data dari form.
use Illuminate\Http\Request;

// penjelasan: Rule digunakan untuk validasi unique dan pilihan nilai tertentu.
use Illuminate\Validation\Rule;

class TahunAjaranController extends Controller
{
    // penjelasan: routePrefix digunakan agar view bisa dipakai oleh super admin dan admin.
    // penjelasan: Jika role user super_admin, routePrefix menjadi super-admin.
    // penjelasan: Jika role user admin, routePrefix menjadi admin.
    private function routePrefix(): string
    {
        return auth()->user()->role === 'super_admin' ? 'super-admin' : 'admin';
    }

    // penjelasan: Method index menampilkan daftar tahun ajaran.
    // penjelasan: Method ini juga memproses pencarian dan filter status.
    public function index(Request $request)
    {
        // penjelasan: Query dibuat dari model TahunAjaran.
        // penjelasan: withCount('semesters') digunakan untuk menghitung jumlah semester dalam tahun ajaran.
        $query = TahunAjaran::withCount('semesters');

        // penjelasan: Jika search diisi, sistem mencari berdasarkan nama_tahun_ajaran.
        if ($request->filled('search')) {
            $query->where('nama_tahun_ajaran', 'like', '%' . $request->search . '%');
        }

        // penjelasan: Filter status digunakan untuk menampilkan tahun ajaran aktif atau nonaktif.
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // penjelasan: Data ditampilkan 10 per halaman.
        $tahunAjarans = $query->latest()->paginate(10)->withQueryString();

        $routePrefix = $this->routePrefix();

        return view('admin.pages.tahun-ajaran.index', compact('tahunAjarans', 'routePrefix'));
    }

    // penjelasan: Method create menampilkan form tambah tahun ajaran.
    public function create()
    {
        $routePrefix = $this->routePrefix();

        return view('admin.pages.tahun-ajaran.create', compact('routePrefix'));
    }

    // penjelasan: Method store menyimpan data tahun ajaran baru.
    public function store(Request $request)
    {
        // penjelasan: Validasi memastikan nama tahun ajaran unik dan tanggal selesai tidak lebih kecil dari tanggal mulai.
        $validated = $request->validate([
            'nama_tahun_ajaran' => ['required', 'string', 'max:50', 'unique:tahun_ajarans,nama_tahun_ajaran'],
            'tanggal_mulai' => ['nullable', 'date'],
            'tanggal_selesai' => ['nullable', 'date', 'after_or_equal:tanggal_mulai'],
            'status' => ['required', Rule::in(['aktif', 'nonaktif'])],
        ], [
            'nama_tahun_ajaran.required' => 'Nama tahun ajaran wajib diisi.',
            'nama_tahun_ajaran.unique' => 'Nama tahun ajaran sudah digunakan.',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai tidak boleh lebih kecil dari tanggal mulai.',
            'status.required' => 'Status wajib dipilih.',
        ]);

        // penjelasan: Transaksi digunakan agar jika status aktif dipilih, tahun ajaran lain otomatis nonaktif.
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

    // penjelasan: Method show menampilkan detail tahun ajaran.
    public function show(TahunAjaran $tahunAjaran)
    {
        // penjelasan: load('semesters') mengambil data semester yang terhubung ke tahun ajaran.
        $tahunAjaran->load('semesters');

        $routePrefix = $this->routePrefix();

        return view('admin.pages.tahun-ajaran.show', compact('tahunAjaran', 'routePrefix'));
    }

    // penjelasan: Method edit menampilkan form edit tahun ajaran.
    public function edit(TahunAjaran $tahunAjaran)
    {
        $routePrefix = $this->routePrefix();

        return view('admin.pages.tahun-ajaran.edit', compact('tahunAjaran', 'routePrefix'));
    }

    // penjelasan: Method update menyimpan perubahan data tahun ajaran.
    public function update(Request $request, TahunAjaran $tahunAjaran)
    {
        // penjelasan: Validasi unique mengabaikan data yang sedang diedit.
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
        ], [
            'nama_tahun_ajaran.required' => 'Nama tahun ajaran wajib diisi.',
            'nama_tahun_ajaran.unique' => 'Nama tahun ajaran sudah digunakan.',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai tidak boleh lebih kecil dari tanggal mulai.',
            'status.required' => 'Status wajib dipilih.',
        ]);

        // penjelasan: Jika data ini dibuat aktif, data tahun ajaran lain otomatis nonaktif.
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

    // penjelasan: Method toggleStatus mengubah status tahun ajaran.
    // penjelasan: Jika tahun ajaran diaktifkan, tahun ajaran lain otomatis menjadi nonaktif.
    public function toggleStatus(TahunAjaran $tahunAjaran)
    {
        DB::transaction(function () use ($tahunAjaran) {
            if ($tahunAjaran->status === 'aktif') {
                $tahunAjaran->update(['status' => 'nonaktif']);
            } else {
                TahunAjaran::where('id', '!=', $tahunAjaran->id)->update(['status' => 'nonaktif']);

                $tahunAjaran->update(['status' => 'aktif']);
            }
        });

        return back()->with('success', 'Status tahun ajaran berhasil diubah.');
    }
}
