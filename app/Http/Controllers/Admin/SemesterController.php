<?php

// penjelasan: File ini adalah controller untuk Modul Semester.
// penjelasan: Controller ini dipakai oleh Super Admin dan Admin.
// penjelasan: Controller ini mengatur daftar, tambah, detail, edit, update, dan aktif/nonaktif semester.
// penjelasan: Saat satu semester dibuat aktif, semester lain otomatis dibuat nonaktif.
// penjelasan: Semua validasi memakai pesan Bahasa Indonesia agar selaras dengan UI global.
// penjelasan: Tahun ajaran, semester, tanggal mulai, tanggal selesai, dan status wajib diisi.
// penjelasan: Tanggal mulai dan tanggal selesai dipilih manual sesuai kalender akademik.

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
// penjelasan: Controller adalah class dasar Laravel untuk membuat controller.

use App\Models\Semester;
// penjelasan: Model Semester digunakan untuk mengambil, menyimpan, dan mengubah data semester.

use App\Models\TahunAjaran;
// penjelasan: Model TahunAjaran digunakan untuk mengambil pilihan tahun ajaran pada form semester.

use Illuminate\Http\Request;
// penjelasan: Request digunakan untuk mengambil data dari form.

use Illuminate\Support\Facades\DB;
// penjelasan: DB digunakan untuk transaksi database agar proses aktif/nonaktif tetap aman.

use Illuminate\Validation\Rule;
// penjelasan: Rule digunakan untuk validasi unique dan pilihan nilai tertentu.

class SemesterController extends Controller
{
    /**
     * penjelasan: routePrefix digunakan agar view bisa dipakai oleh super admin dan admin.
     */
    private function routePrefix(): string
    {
        return auth()->user()->role === 'super_admin' ? 'super-admin' : 'admin';
    }

    /**
     * penjelasan: Method index menampilkan daftar semester.
     */
    public function index(Request $request)
    {
        $query = Semester::with('tahunAjaran');

        if ($request->filled('tahun_ajaran_id')) {
            $query->where('tahun_ajaran_id', $request->tahun_ajaran_id);
        }

        if ($request->filled('nama_semester')) {
            $query->where('nama_semester', $request->nama_semester);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $semesters = $query->latest()->paginate(10)->withQueryString();

        $tahunAjarans = TahunAjaran::latest()->get();

        $routePrefix = $this->routePrefix();

        return view('admin.pages.semester.index', compact('semesters', 'tahunAjarans', 'routePrefix'));
    }

    /**
     * penjelasan: Method create menampilkan form tambah semester.
     */
    public function create()
    {
        $tahunAjarans = TahunAjaran::latest()->get();

        $routePrefix = $this->routePrefix();

        return view('admin.pages.semester.create', compact('tahunAjarans', 'routePrefix'));
    }

    /**
     * penjelasan: Method store menyimpan semester baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tahun_ajaran_id' => ['required', 'exists:tahun_ajarans,id'],
            'nama_semester' => [
                'required',
                Rule::in(['ganjil', 'genap']),
                Rule::unique('semesters', 'nama_semester')->where(function ($query) use ($request) {
                    return $query->where('tahun_ajaran_id', $request->tahun_ajaran_id);
                }),
            ],
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_selesai' => ['required', 'date', 'after_or_equal:tanggal_mulai'],
            'status' => ['required', Rule::in(['aktif', 'nonaktif'])],
        ], $this->validationMessages());

        if ($validated['status'] === 'aktif') {
            $this->ensureTahunAjaranAktif($validated['tahun_ajaran_id']);
        }

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

    /**
     * penjelasan: Method show menampilkan detail semester.
     */
    public function show(Semester $semester)
    {
        $semester->load('tahunAjaran');

        $routePrefix = $this->routePrefix();

        return view('admin.pages.semester.show', compact('semester', 'routePrefix'));
    }

    /**
     * penjelasan: Method edit menampilkan form edit semester.
     */
    public function edit(Semester $semester)
    {
        $tahunAjarans = TahunAjaran::latest()->get();

        $routePrefix = $this->routePrefix();

        return view('admin.pages.semester.edit', compact('semester', 'tahunAjarans', 'routePrefix'));
    }

    /**
     * penjelasan: Method update menyimpan perubahan semester.
     */
    public function update(Request $request, Semester $semester)
    {
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
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_selesai' => ['required', 'date', 'after_or_equal:tanggal_mulai'],
            'status' => ['required', Rule::in(['aktif', 'nonaktif'])],
        ], $this->validationMessages());

        if ($validated['status'] === 'aktif') {
            $this->ensureTahunAjaranAktif($validated['tahun_ajaran_id']);
        }

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

    /**
     * penjelasan: Method toggleStatus mengubah status semester.
     * penjelasan: Semester hanya boleh aktif jika tahun ajarannya aktif.
     */
    public function toggleStatus(Semester $semester)
    {
        if ($semester->status !== 'aktif') {
            $this->ensureTahunAjaranAktif($semester->tahun_ajaran_id);
        }

        DB::transaction(function () use ($semester) {
            if ($semester->status === 'aktif') {
                $semester->update(['status' => 'nonaktif']);
                return;
            }

            Semester::where('id', '!=', $semester->id)->update(['status' => 'nonaktif']);

            $semester->update(['status' => 'aktif']);
        });

        $message = $semester->fresh()->status === 'aktif'
            ? 'Semester berhasil diaktifkan. Semester aktif lainnya otomatis dinonaktifkan.'
            : 'Semester berhasil dinonaktifkan.';

        return back()->with('success', $message);
    }

    /**
     * penjelasan: Method ensureTahunAjaranAktif memastikan semester aktif berada pada tahun ajaran aktif.
     */
    private function ensureTahunAjaranAktif(int|string $tahunAjaranId): void
    {
        $tahunAjaranAktif = TahunAjaran::where('id', $tahunAjaranId)
            ->where('status', 'aktif')
            ->exists();

        if (! $tahunAjaranAktif) {
            back()
                ->withErrors(['tahun_ajaran_id' => 'Semester aktif harus berada pada tahun ajaran yang aktif.'])
                ->withInput()
                ->throwResponse();
        }
    }

    /**
     * penjelasan: Method validationMessages menyimpan pesan validasi Bahasa Indonesia.
     */
    private function validationMessages(): array
    {
        return [
            'tahun_ajaran_id.required' => 'Tahun ajaran wajib dipilih.',
            'tahun_ajaran_id.exists' => 'Tahun ajaran tidak valid.',

            'nama_semester.required' => 'Semester wajib dipilih.',
            'nama_semester.in' => 'Semester yang dipilih tidak valid.',
            'nama_semester.unique' => 'Semester tersebut sudah ada pada tahun ajaran yang dipilih.',

            'tanggal_mulai.required' => 'Tanggal mulai wajib diisi.',
            'tanggal_mulai.date' => 'Tanggal mulai tidak valid.',

            'tanggal_selesai.required' => 'Tanggal selesai wajib diisi.',
            'tanggal_selesai.date' => 'Tanggal selesai tidak valid.',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai tidak boleh lebih kecil dari tanggal mulai.',

            'status.required' => 'Status wajib dipilih.',
            'status.in' => 'Status yang dipilih tidak valid.',
        ];
    }
}
