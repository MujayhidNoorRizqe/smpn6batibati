<?php

// penjelasan: File ini adalah controller untuk Modul Jadwal Pelajaran.
// penjelasan: Controller ini dipakai oleh Super Admin dan Admin.
// penjelasan: Controller ini mengatur daftar, tambah, detail, edit, update, dan aktif/nonaktif jadwal pelajaran.
// penjelasan: Controller ini juga memvalidasi agar jadwal guru dan kelas tidak bentrok.

namespace App\Http\Controllers\Admin;

// penjelasan: Controller adalah class dasar Laravel untuk membuat controller.
use App\Http\Controllers\Controller;

// penjelasan: JadwalPelajaran adalah model utama modul ini.
use App\Models\JadwalPelajaran;

// penjelasan: Kelas digunakan untuk pilihan kelas pada form jadwal.
use App\Models\Kelas;

// penjelasan: MataPelajaran digunakan untuk pilihan mata pelajaran pada form jadwal.
use App\Models\MataPelajaran;

// penjelasan: Pegawai digunakan untuk pilihan guru pada form jadwal.
use App\Models\Pegawai;

// penjelasan: Semester digunakan untuk pilihan semester pada form jadwal.
use App\Models\Semester;

// penjelasan: TahunAjaran digunakan untuk pilihan tahun ajaran pada form jadwal.
use App\Models\TahunAjaran;

// penjelasan: Request digunakan untuk mengambil data dari form dan filter.
use Illuminate\Http\Request;

// penjelasan: Rule digunakan untuk validasi pilihan enum.
use Illuminate\Validation\Rule;

// penjelasan: ValidationException digunakan untuk mengembalikan error validasi manual.
use Illuminate\Validation\ValidationException;

class JadwalPelajaranController extends Controller
{
    // penjelasan: routePrefix digunakan agar view yang sama bisa dipakai oleh Super Admin dan Admin.
    // penjelasan: Jika user login super_admin, routePrefix adalah super-admin.
    // penjelasan: Jika user login admin, routePrefix adalah admin.
    private function routePrefix(): string
    {
        return auth()->user()->role === 'super_admin' ? 'super-admin' : 'admin';
    }

    // penjelasan: Method index menampilkan daftar jadwal pelajaran.
    // penjelasan: Method ini juga memproses filter tahun ajaran, semester, kelas, guru, hari, dan status.
    public function index(Request $request)
    {
        // penjelasan: Query mengambil jadwal beserta relasi agar data tabel tidak melakukan query berulang.
        $query = JadwalPelajaran::with([
            'tahunAjaran',
            'semester',
            'kelas',
            'mataPelajaran',
            'guru',
        ]);

        // penjelasan: Filter tahun ajaran.
        if ($request->filled('tahun_ajaran_id')) {
            $query->where('tahun_ajaran_id', $request->tahun_ajaran_id);
        }

        // penjelasan: Filter semester.
        if ($request->filled('semester_id')) {
            $query->where('semester_id', $request->semester_id);
        }

        // penjelasan: Filter kelas.
        if ($request->filled('kelas_id')) {
            $query->where('kelas_id', $request->kelas_id);
        }

        // penjelasan: Filter guru.
        if ($request->filled('guru_id')) {
            $query->where('guru_id', $request->guru_id);
        }

        // penjelasan: Filter hari.
        if ($request->filled('hari')) {
            $query->where('hari', $request->hari);
        }

        // penjelasan: Filter status aktif/nonaktif.
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // penjelasan: Data diurutkan berdasarkan hari dan jam mulai agar mudah dibaca.
        $jadwalPelajarans = $query
            ->orderByRaw("FIELD(hari, 'senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu')")
            ->orderBy('jam_mulai')
            ->paginate(10)
            ->withQueryString();

        // penjelasan: Data berikut dipakai untuk pilihan filter pada halaman index.
        $tahunAjarans = TahunAjaran::latest()->get();

        $semesters = Semester::with('tahunAjaran')->latest()->get();

        $kelasList = Kelas::orderBy('tingkat')
            ->orderBy('nama_kelas')
            ->get();

        $gurus = Pegawai::where('jenis_pegawai', 'guru')
            ->orderBy('nama_pegawai')
            ->get();

        $routePrefix = $this->routePrefix();

        return view('admin.pages.jadwal-pelajaran.index', compact(
            'jadwalPelajarans',
            'tahunAjarans',
            'semesters',
            'kelasList',
            'gurus',
            'routePrefix'
        ));
    }

    // penjelasan: Method create menampilkan form tambah jadwal pelajaran.
    public function create()
    {
        // penjelasan: Tahun ajaran aktif dipakai agar jadwal dibuat pada periode akademik yang berjalan.
        $tahunAjarans = TahunAjaran::where('status', 'aktif')
            ->latest()
            ->get();

        // penjelasan: Semester aktif dipakai agar jadwal tidak dibuat pada semester lama/nonaktif.
        $semesters = Semester::with('tahunAjaran')
            ->where('status', 'aktif')
            ->latest()
            ->get();

        // penjelasan: Kelas aktif dipakai sebagai pilihan kelas.
        $kelasList = Kelas::where('status', 'aktif')
            ->orderBy('tingkat')
            ->orderBy('nama_kelas')
            ->get();

        // penjelasan: Mata pelajaran aktif dipakai sebagai pilihan mata pelajaran.
        $mataPelajarans = MataPelajaran::where('status', 'aktif')
            ->orderBy('nama_mapel')
            ->get();

        // penjelasan: Hanya pegawai jenis guru dan status aktif yang boleh menjadi guru pengajar.
        $gurus = Pegawai::where('jenis_pegawai', 'guru')
            ->where('status', 'aktif')
            ->orderBy('nama_pegawai')
            ->get();

        $routePrefix = $this->routePrefix();

        return view('admin.pages.jadwal-pelajaran.create', compact(
            'tahunAjarans',
            'semesters',
            'kelasList',
            'mataPelajarans',
            'gurus',
            'routePrefix'
        ));
    }

    // penjelasan: Method store menyimpan jadwal pelajaran baru.
    public function store(Request $request)
    {
        // penjelasan: Validasi dasar memastikan input form sesuai aturan.
        // penjelasan: Validasi bentrok jadwal dilakukan setelah validasi dasar.
        $validated = $request->validate([
            'tahun_ajaran_id' => ['required', 'exists:tahun_ajarans,id'],
            'semester_id' => ['required', 'exists:semesters,id'],
            'kelas_id' => ['required', 'exists:kelas,id'],
            'mata_pelajaran_id' => ['required', 'exists:mata_pelajarans,id'],
            'guru_id' => ['required', 'exists:pegawais,id'],
            'hari' => ['required', Rule::in(['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu'])],
            'jam_mulai' => ['required', 'date_format:H:i'],
            'jam_selesai' => ['required', 'date_format:H:i'],
            'status' => ['required', Rule::in(['aktif', 'nonaktif'])],
        ], [
            'tahun_ajaran_id.required' => 'Tahun ajaran wajib dipilih.',
            'semester_id.required' => 'Semester wajib dipilih.',
            'kelas_id.required' => 'Kelas wajib dipilih.',
            'mata_pelajaran_id.required' => 'Mata pelajaran wajib dipilih.',
            'guru_id.required' => 'Guru wajib dipilih.',
            'hari.required' => 'Hari wajib dipilih.',
            'jam_mulai.required' => 'Jam mulai wajib diisi.',
            'jam_mulai.date_format' => 'Format jam mulai tidak valid.',
            'jam_selesai.required' => 'Jam selesai wajib diisi.',
            'jam_selesai.date_format' => 'Format jam selesai tidak valid.',
            'status.required' => 'Status wajib dipilih.',
        ]);

        // penjelasan: Validasi manual agar jam selesai lebih besar dari jam mulai.
        if ($validated['jam_selesai'] <= $validated['jam_mulai']) {
            throw ValidationException::withMessages([
                'jam_selesai' => 'Jam selesai harus lebih besar dari jam mulai.',
            ]);
        }

        // penjelasan: Validasi data pendukung seperti tahun ajaran aktif, semester aktif, kelas aktif, mapel aktif, dan guru aktif.
        $this->validateSupportingData($validated);

        // penjelasan: Validasi jadwal bentrok untuk kelas dan guru.
        $this->validateNoScheduleConflict($validated);

        // penjelasan: Jadwal pelajaran disimpan ke tabel jadwal_pelajarans.
        JadwalPelajaran::create($validated);

        return redirect()
            ->route($this->routePrefix() . '.jadwal-pelajaran.index')
            ->with('success', 'Jadwal pelajaran berhasil ditambahkan.');
    }

    // penjelasan: Method show menampilkan detail jadwal pelajaran.
    public function show(JadwalPelajaran $jadwalPelajaran)
    {
        // penjelasan: load mengambil relasi yang diperlukan untuk halaman detail.
        $jadwalPelajaran->load([
            'tahunAjaran',
            'semester',
            'kelas',
            'mataPelajaran',
            'guru',
        ]);

        $routePrefix = $this->routePrefix();

        return view('admin.pages.jadwal-pelajaran.show', compact('jadwalPelajaran', 'routePrefix'));
    }

    // penjelasan: Method edit menampilkan form edit jadwal pelajaran.
    public function edit(JadwalPelajaran $jadwalPelajaran)
    {
        // penjelasan: Query berikut mengambil data aktif dan data yang sedang dipakai jadwal.
        // penjelasan: Ini mencegah pilihan lama hilang jika data lama sudah dinonaktifkan.
        $tahunAjarans = TahunAjaran::where('status', 'aktif')
            ->orWhere('id', $jadwalPelajaran->tahun_ajaran_id)
            ->latest()
            ->get();

        $semesters = Semester::with('tahunAjaran')
            ->where('status', 'aktif')
            ->orWhere('id', $jadwalPelajaran->semester_id)
            ->latest()
            ->get();

        $kelasList = Kelas::where('status', 'aktif')
            ->orWhere('id', $jadwalPelajaran->kelas_id)
            ->orderBy('tingkat')
            ->orderBy('nama_kelas')
            ->get();

        $mataPelajarans = MataPelajaran::where('status', 'aktif')
            ->orWhere('id', $jadwalPelajaran->mata_pelajaran_id)
            ->orderBy('nama_mapel')
            ->get();

        $gurus = Pegawai::where('jenis_pegawai', 'guru')
            ->where(function ($query) use ($jadwalPelajaran) {
                $query->where('status', 'aktif')
                    ->orWhere('id', $jadwalPelajaran->guru_id);
            })
            ->orderBy('nama_pegawai')
            ->get();

        $routePrefix = $this->routePrefix();

        return view('admin.pages.jadwal-pelajaran.edit', compact(
            'jadwalPelajaran',
            'tahunAjarans',
            'semesters',
            'kelasList',
            'mataPelajarans',
            'gurus',
            'routePrefix'
        ));
    }

    // penjelasan: Method update menyimpan perubahan jadwal pelajaran.
    public function update(Request $request, JadwalPelajaran $jadwalPelajaran)
    {
        $validated = $request->validate([
            'tahun_ajaran_id' => ['required', 'exists:tahun_ajarans,id'],
            'semester_id' => ['required', 'exists:semesters,id'],
            'kelas_id' => ['required', 'exists:kelas,id'],
            'mata_pelajaran_id' => ['required', 'exists:mata_pelajarans,id'],
            'guru_id' => ['required', 'exists:pegawais,id'],
            'hari' => ['required', Rule::in(['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu'])],
            'jam_mulai' => ['required', 'date_format:H:i'],
            'jam_selesai' => ['required', 'date_format:H:i'],
            'status' => ['required', Rule::in(['aktif', 'nonaktif'])],
        ], [
            'tahun_ajaran_id.required' => 'Tahun ajaran wajib dipilih.',
            'semester_id.required' => 'Semester wajib dipilih.',
            'kelas_id.required' => 'Kelas wajib dipilih.',
            'mata_pelajaran_id.required' => 'Mata pelajaran wajib dipilih.',
            'guru_id.required' => 'Guru wajib dipilih.',
            'hari.required' => 'Hari wajib dipilih.',
            'jam_mulai.required' => 'Jam mulai wajib diisi.',
            'jam_selesai.required' => 'Jam selesai wajib diisi.',
            'status.required' => 'Status wajib dipilih.',
        ]);

        if ($validated['jam_selesai'] <= $validated['jam_mulai']) {
            throw ValidationException::withMessages([
                'jam_selesai' => 'Jam selesai harus lebih besar dari jam mulai.',
            ]);
        }

        $this->validateSupportingData($validated);

        // penjelasan: Saat update, jadwal yang sedang diedit diabaikan dari pengecekan bentrok.
        $this->validateNoScheduleConflict($validated, $jadwalPelajaran);

        $jadwalPelajaran->update($validated);

        return redirect()
            ->route($this->routePrefix() . '.jadwal-pelajaran.index')
            ->with('success', 'Jadwal pelajaran berhasil diperbarui.');
    }

    // penjelasan: Method toggleStatus mengubah status jadwal aktif/nonaktif.
    // penjelasan: Jika jadwal akan diaktifkan, sistem tetap mengecek bentrok jadwal.
    public function toggleStatus(JadwalPelajaran $jadwalPelajaran)
    {
        if ($jadwalPelajaran->status === 'aktif') {
            $jadwalPelajaran->update(['status' => 'nonaktif']);

            return back()->with('success', 'Jadwal pelajaran berhasil dinonaktifkan.');
        }

        $validated = $jadwalPelajaran->only([
            'tahun_ajaran_id',
            'semester_id',
            'kelas_id',
            'mata_pelajaran_id',
            'guru_id',
            'hari',
            'jam_mulai',
            'jam_selesai',
            'status',
        ]);

        $validated['status'] = 'aktif';
        $validated['jam_mulai'] = substr($jadwalPelajaran->jam_mulai, 0, 5);
        $validated['jam_selesai'] = substr($jadwalPelajaran->jam_selesai, 0, 5);

        $this->validateSupportingData($validated);
        $this->validateNoScheduleConflict($validated, $jadwalPelajaran);

        $jadwalPelajaran->update(['status' => 'aktif']);

        return back()->with('success', 'Jadwal pelajaran berhasil diaktifkan.');
    }

    // penjelasan: Method ini memastikan data pendukung jadwal valid dan aktif.
    // penjelasan: Jadwal aktif wajib memakai tahun ajaran aktif, semester aktif, kelas aktif, mapel aktif, dan guru aktif.
    private function validateSupportingData(array $validated): void
    {
        if ($validated['status'] !== 'aktif') {
            return;
        }

        $tahunAjaran = TahunAjaran::find($validated['tahun_ajaran_id']);

        if (! $tahunAjaran || $tahunAjaran->status !== 'aktif') {
            throw ValidationException::withMessages([
                'tahun_ajaran_id' => 'Jadwal aktif harus memakai tahun ajaran yang aktif.',
            ]);
        }

        $semester = Semester::find($validated['semester_id']);

        if (! $semester || $semester->status !== 'aktif') {
            throw ValidationException::withMessages([
                'semester_id' => 'Jadwal aktif harus memakai semester yang aktif.',
            ]);
        }

        if ((int) $semester->tahun_ajaran_id !== (int) $validated['tahun_ajaran_id']) {
            throw ValidationException::withMessages([
                'semester_id' => 'Semester yang dipilih harus sesuai dengan tahun ajaran.',
            ]);
        }

        $kelasAktif = Kelas::where('id', $validated['kelas_id'])
            ->where('status', 'aktif')
            ->exists();

        if (! $kelasAktif) {
            throw ValidationException::withMessages([
                'kelas_id' => 'Kelas yang dipilih harus berstatus aktif.',
            ]);
        }

        $mataPelajaranAktif = MataPelajaran::where('id', $validated['mata_pelajaran_id'])
            ->where('status', 'aktif')
            ->exists();

        if (! $mataPelajaranAktif) {
            throw ValidationException::withMessages([
                'mata_pelajaran_id' => 'Mata pelajaran yang dipilih harus berstatus aktif.',
            ]);
        }

        $guruAktif = Pegawai::where('id', $validated['guru_id'])
            ->where('jenis_pegawai', 'guru')
            ->where('status', 'aktif')
            ->exists();

        if (! $guruAktif) {
            throw ValidationException::withMessages([
                'guru_id' => 'Guru yang dipilih harus pegawai jenis guru dan berstatus aktif.',
            ]);
        }
    }

    // penjelasan: Method ini mengecek bentrok jadwal untuk kelas dan guru.
    // penjelasan: Bentrok terjadi jika hari sama, semester sama, dan rentang jam saling tumpang tindih.
    // penjelasan: Jadwal nonaktif tidak dihitung sebagai bentrok.
    private function validateNoScheduleConflict(array $validated, ?JadwalPelajaran $currentJadwal = null): void
    {
        if ($validated['status'] !== 'aktif') {
            return;
        }

        $kelasConflictQuery = JadwalPelajaran::where('status', 'aktif')
            ->where('semester_id', $validated['semester_id'])
            ->where('kelas_id', $validated['kelas_id'])
            ->where('hari', $validated['hari'])
            ->where('jam_mulai', '<', $validated['jam_selesai'])
            ->where('jam_selesai', '>', $validated['jam_mulai']);

        if ($currentJadwal) {
            $kelasConflictQuery->where('id', '!=', $currentJadwal->id);
        }

        if ($kelasConflictQuery->exists()) {
            throw ValidationException::withMessages([
                'jam_mulai' => 'Jadwal kelas bentrok dengan jadwal lain pada hari dan jam tersebut.',
            ]);
        }

        $guruConflictQuery = JadwalPelajaran::where('status', 'aktif')
            ->where('semester_id', $validated['semester_id'])
            ->where('guru_id', $validated['guru_id'])
            ->where('hari', $validated['hari'])
            ->where('jam_mulai', '<', $validated['jam_selesai'])
            ->where('jam_selesai', '>', $validated['jam_mulai']);

        if ($currentJadwal) {
            $guruConflictQuery->where('id', '!=', $currentJadwal->id);
        }

        if ($guruConflictQuery->exists()) {
            throw ValidationException::withMessages([
                'guru_id' => 'Guru sudah memiliki jadwal lain pada hari dan jam tersebut.',
            ]);
        }
    }
}
