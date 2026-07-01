<?php

// penjelasan: File ini adalah controller untuk Modul Semester.
// penjelasan: Controller ini dipakai oleh Super Admin dan Admin.
// penjelasan: Controller ini mengatur daftar, tambah, detail, edit, update, dan aktif/nonaktif semester.
// penjelasan: Saat satu semester dibuat aktif, semester lain otomatis dibuat nonaktif.

namespace App\Http\Controllers\Admin;

// penjelasan: Controller adalah class dasar Laravel untuk membuat controller.
use App\Http\Controllers\Controller;

// penjelasan: Model Semester digunakan untuk mengambil, menyimpan, dan mengubah data semester.
use App\Models\Semester;

// penjelasan: Model TahunAjaran digunakan untuk mengambil pilihan tahun ajaran pada form semester.
use App\Models\TahunAjaran;

// penjelasan: Request digunakan untuk mengambil data dari form.
use Illuminate\Http\Request;

// penjelasan: DB digunakan untuk transaksi database.
use Illuminate\Support\Facades\DB;

// penjelasan: Rule digunakan untuk validasi unique dan pilihan nilai tertentu.
use Illuminate\Validation\Rule;

class SemesterController extends Controller
{
    // penjelasan: routePrefix digunakan agar view bisa dipakai oleh super admin dan admin.
    private function routePrefix(): string
    {
        return auth()->user()->role === 'super_admin' ? 'super-admin' : 'admin';
    }

    // penjelasan: Method index menampilkan daftar semester.
    public function index(Request $request)
    {
        // penjelasan: Query mengambil semester beserta relasi tahun ajaran.
        $query = Semester::with('tahunAjaran');

        // penjelasan: Filter tahun ajaran.
        if ($request->filled('tahun_ajaran_id')) {
            $query->where('tahun_ajaran_id', $request->tahun_ajaran_id);
        }

        // penjelasan: Filter nama semester, yaitu ganjil atau genap.
        if ($request->filled('nama_semester')) {
            $query->where('nama_semester', $request->nama_semester);
        }

        // penjelasan: Filter status aktif atau nonaktif.
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // penjelasan: Data ditampilkan 10 per halaman.
        $semesters = $query->latest()->paginate(10)->withQueryString();

        // penjelasan: Tahun ajaran dikirim ke view untuk filter.
        $tahunAjarans = TahunAjaran::latest()->get();

        $routePrefix = $this->routePrefix();

        return view('admin.pages.semester.index', compact('semesters', 'tahunAjarans', 'routePrefix'));
    }

    // penjelasan: Method create menampilkan form tambah semester.
    public function create()
    {
        // penjelasan: Semua tahun ajaran dikirim agar admin bisa membuat semester pada periode yang sesuai.
        $tahunAjarans = TahunAjaran::latest()->get();

        $routePrefix = $this->routePrefix();

        return view('admin.pages.semester.create', compact('tahunAjarans', 'routePrefix'));
    }

    // penjelasan: Method store menyimpan semester baru.
    public function store(Request $request)
    {
        // penjelasan: Validasi memastikan dalam satu tahun ajaran tidak ada semester yang sama dua kali.
        $validated = $request->validate([
            'tahun_ajaran_id' => ['required', 'exists:tahun_ajarans,id'],
            'nama_semester' => [
                'required',
                Rule::in(['ganjil', 'genap']),
                Rule::unique('semesters', 'nama_semester')->where(function ($query) use ($request) {
                    return $query->where('tahun_ajaran_id', $request->tahun_ajaran_id);
                }),
            ],
            'tanggal_mulai' => ['nullable', 'date'],
            'tanggal_selesai' => ['nullable', 'date', 'after_or_equal:tanggal_mulai'],
            'status' => ['required', Rule::in(['aktif', 'nonaktif'])],
        ], [
            'tahun_ajaran_id.required' => 'Tahun ajaran wajib dipilih.',
            'tahun_ajaran_id.exists' => 'Tahun ajaran tidak valid.',
            'nama_semester.required' => 'Semester wajib dipilih.',
            'nama_semester.unique' => 'Semester tersebut sudah ada pada tahun ajaran yang dipilih.',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai tidak boleh lebih kecil dari tanggal mulai.',
            'status.required' => 'Status wajib dipilih.',
        ]);

        // penjelasan: Jika semester dibuat aktif, tahun ajaran yang dipilih harus aktif.
        if ($validated['status'] === 'aktif') {
            $tahunAjaranAktif = TahunAjaran::where('id', $validated['tahun_ajaran_id'])
                ->where('status', 'aktif')
                ->exists();

            if (! $tahunAjaranAktif) {
                return back()
                    ->withErrors(['tahun_ajaran_id' => 'Semester aktif harus berada pada tahun ajaran yang aktif.'])
                    ->withInput();
            }
        }

        // penjelasan: Jika semester baru aktif, semester lain otomatis nonaktif.
        DB::transaction(function () use ($validated) {
            if ($validated['status'] === 'aktif') {
                Semester::where('status', 'aktif')->update(['status' => 'nonaktif']);
            }

            Semester::create($validated);
        });

        return redirect()
            ->route($this->routePrefix() . '.semester.index')
            ->with('success', 'Data semester berhasil ditambahkan.');
    }

    // penjelasan: Method show menampilkan detail semester.
    public function show(Semester $semester)
    {
        $semester->load('tahunAjaran');

        $routePrefix = $this->routePrefix();

        return view('admin.pages.semester.show', compact('semester', 'routePrefix'));
    }

    // penjelasan: Method edit menampilkan form edit semester.
    public function edit(Semester $semester)
    {
        $tahunAjarans = TahunAjaran::latest()->get();

        $routePrefix = $this->routePrefix();

        return view('admin.pages.semester.edit', compact('semester', 'tahunAjarans', 'routePrefix'));
    }

    // penjelasan: Method update menyimpan perubahan semester.
    public function update(Request $request, Semester $semester)
    {
        // penjelasan: Validasi unique mengabaikan data semester yang sedang diedit.
        $validated = $request->validate([
            'tahun_ajaran_id' => ['required', 'exists:tahun_ajarans,id'],
            'nama_semester' => [
                'required',
                Rule::in(['ganjil', 'genap']),
                Rule::unique('semesters', 'nama_semester')
                    ->where(function ($query) use ($request) {
                        return $query->where('tahun_ajaran_id', $request->tahun_ajaran_id);
                    })
                    ->ignore($semester->id),
            ],
            'tanggal_mulai' => ['nullable', 'date'],
            'tanggal_selesai' => ['nullable', 'date', 'after_or_equal:tanggal_mulai'],
            'status' => ['required', Rule::in(['aktif', 'nonaktif'])],
        ], [
            'tahun_ajaran_id.required' => 'Tahun ajaran wajib dipilih.',
            'tahun_ajaran_id.exists' => 'Tahun ajaran tidak valid.',
            'nama_semester.required' => 'Semester wajib dipilih.',
            'nama_semester.unique' => 'Semester tersebut sudah ada pada tahun ajaran yang dipilih.',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai tidak boleh lebih kecil dari tanggal mulai.',
            'status.required' => 'Status wajib dipilih.',
        ]);

        // penjelasan: Jika semester dibuat aktif, tahun ajaran yang dipilih harus aktif.
        if ($validated['status'] === 'aktif') {
            $tahunAjaranAktif = TahunAjaran::where('id', $validated['tahun_ajaran_id'])
                ->where('status', 'aktif')
                ->exists();

            if (! $tahunAjaranAktif) {
                return back()
                    ->withErrors(['tahun_ajaran_id' => 'Semester aktif harus berada pada tahun ajaran yang aktif.'])
                    ->withInput();
            }
        }

        // penjelasan: Jika semester ini aktif, semester lain otomatis nonaktif.
        DB::transaction(function () use ($semester, $validated) {
            if ($validated['status'] === 'aktif') {
                Semester::where('id', '!=', $semester->id)->update(['status' => 'nonaktif']);
            }

            $semester->update($validated);
        });

        return redirect()
            ->route($this->routePrefix() . '.semester.index')
            ->with('success', 'Data semester berhasil diperbarui.');
    }

    // penjelasan: Method toggleStatus mengubah status semester.
    public function toggleStatus(Semester $semester)
    {
        // penjelasan: Semester hanya boleh aktif jika tahun ajarannya aktif.
        if ($semester->status !== 'aktif' && $semester->tahunAjaran?->status !== 'aktif') {
            return back()->withErrors(['semester' => 'Semester tidak bisa diaktifkan karena tahun ajarannya belum aktif.']);
        }

        DB::transaction(function () use ($semester) {
            if ($semester->status === 'aktif') {
                $semester->update(['status' => 'nonaktif']);
            } else {
                Semester::where('id', '!=', $semester->id)->update(['status' => 'nonaktif']);

                $semester->update(['status' => 'aktif']);
            }
        });

        return back()->with('success', 'Status semester berhasil diubah.');
    }
}
