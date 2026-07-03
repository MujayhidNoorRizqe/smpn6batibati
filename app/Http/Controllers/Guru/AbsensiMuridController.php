<?php

// penjelasan: Controller ini digunakan oleh guru.
// penjelasan: Controller ini menangani daftar jadwal absen murid hari ini, form input absensi murid, simpan absensi, dan detail absensi.
// penjelasan: Guru hanya bisa mengabsen murid sesuai jadwal pelajaran miliknya pada hari berjalan.
// penjelasan: Tanggal absensi murid otomatis dari sistem untuk mengurangi manipulasi tanggal.
// penjelasan: Controller ini memakai kolom guru_id sesuai struktur tabel jadwal_pelajarans.

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
// penjelasan: Controller adalah class dasar Laravel untuk membuat controller.

use App\Models\AbsensiMurid;
// penjelasan: Model AbsensiMurid digunakan untuk menyimpan dan membaca data absensi murid.

use App\Models\JadwalPelajaran;
// penjelasan: Model JadwalPelajaran digunakan untuk mengambil jadwal mengajar guru.

use App\Models\Murid;
// penjelasan: Model Murid digunakan untuk mengambil daftar murid aktif pada kelas jadwal.

use Illuminate\Http\Request;
// penjelasan: Request digunakan untuk mengambil data dari form absensi murid.

use Illuminate\Support\Facades\DB;
// penjelasan: DB digunakan untuk transaksi agar penyimpanan absensi murid aman.

use Illuminate\Validation\Rule;
// penjelasan: Rule digunakan untuk validasi pilihan status absensi dan id murid.

use Illuminate\Validation\ValidationException;
// penjelasan: ValidationException digunakan untuk menampilkan error validasi khusus.

class AbsensiMuridController extends Controller
{
    /**
     * penjelasan: Method ini mengambil data pegawai dari user yang sedang login.
     * penjelasan: Guru wajib terhubung dengan data pegawai agar bisa menginput absensi murid.
     */
    private function currentPegawai()
    {
        $pegawai = auth()->user()->pegawai;

        if (! $pegawai) {
            abort(403, 'Akun ini belum terhubung dengan data pegawai.');
        }

        if ($pegawai->jenis_pegawai !== 'guru') {
            abort(403, 'Hanya guru yang dapat mengakses absensi murid.');
        }

        return $pegawai;
    }

    /**
     * penjelasan: Method ini menampilkan halaman utama Absen Murid.
     * penjelasan: Jadwal yang tampil hanya jadwal aktif milik guru pada hari ini.
     * penjelasan: Riwayat absensi yang tampil adalah absensi yang pernah diinput oleh guru tersebut.
     */
    public function index()
    {
        $guru = $this->currentPegawai();

        $tanggalHariIni = now()->toDateString();
        $hariHariIni = $this->hariIndonesia((int) now()->dayOfWeek);

        $jadwalHariIni = JadwalPelajaran::with([
                'tahunAjaran',
                'semester',
                'kelas',
                'mataPelajaran',
            ])
            ->where('guru_id', $guru->id)
            ->where('hari', $hariHariIni)
            ->where('status', 'aktif')
            ->orderBy('jam_mulai')
            ->get();

        foreach ($jadwalHariIni as $jadwal) {
            $jadwal->total_murid_aktif = Murid::where('kelas_id', $jadwal->kelas_id)
                ->where('status', 'aktif')
                ->count();

            $jadwal->total_absensi_hari_ini = AbsensiMurid::where('jadwal_pelajaran_id', $jadwal->id)
                ->whereDate('tanggal_absen', $tanggalHariIni)
                ->count();
        }

        $statusCounts = AbsensiMurid::where('guru_id', $guru->id)
            ->whereDate('tanggal_absen', $tanggalHariIni)
            ->select('status_absen', DB::raw('COUNT(*) as total'))
            ->groupBy('status_absen')
            ->pluck('total', 'status_absen');

        $riwayatAbsensi = AbsensiMurid::with([
                'murid',
                'kelas',
                'mataPelajaran',
                'jadwalPelajaran',
            ])
            ->where('guru_id', $guru->id)
            ->latest('tanggal_absen')
            ->latest()
            ->paginate(10);

        return view('admin.pages.absensi-murid.index', compact(
            'guru',
            'tanggalHariIni',
            'hariHariIni',
            'jadwalHariIni',
            'statusCounts',
            'riwayatAbsensi'
        ));
    }

    /**
     * penjelasan: Method ini menampilkan form input absensi murid berdasarkan jadwal pelajaran.
     * penjelasan: Absensi hanya bisa dilakukan jika jadwal tersebut milik guru yang login dan sesuai hari ini.
     */
    public function create(JadwalPelajaran $jadwalPelajaran)
    {
        $guru = $this->currentPegawai();

        $redirect = $this->validateJadwalUntukHariIni($jadwalPelajaran, $guru);

        if ($redirect) {
            return $redirect;
        }

        $jadwalPelajaran->load([
            'tahunAjaran',
            'semester',
            'kelas',
            'mataPelajaran',
        ]);

        $murids = Murid::where('kelas_id', $jadwalPelajaran->kelas_id)
            ->where('status', 'aktif')
            ->orderBy('nama_murid')
            ->get();

        $tanggalHariIni = now()->toDateString();

        $absensiTersimpan = AbsensiMurid::where('jadwal_pelajaran_id', $jadwalPelajaran->id)
            ->whereDate('tanggal_absen', $tanggalHariIni)
            ->get()
            ->keyBy('murid_id');

        return view('admin.pages.absensi-murid.create', compact(
            'guru',
            'jadwalPelajaran',
            'murids',
            'tanggalHariIni',
            'absensiTersimpan'
        ));
    }

    /**
     * penjelasan: Method ini menyimpan absensi murid.
     * penjelasan: Semua murid aktif pada kelas tersebut wajib memiliki status absensi.
     * penjelasan: Jika absensi sudah pernah diinput pada jadwal dan tanggal yang sama, data akan diperbarui.
     */
    public function store(Request $request, JadwalPelajaran $jadwalPelajaran)
    {
        $guru = $this->currentPegawai();

        $redirect = $this->validateJadwalUntukHariIni($jadwalPelajaran, $guru);

        if ($redirect) {
            return $redirect;
        }

        // penjelasan: Mengambil semua murid aktif berdasarkan kelas pada jadwal pelajaran.
        // penjelasan: ID murid langsung dikonversi ke integer agar konsisten dengan data dari form.
        $muridIds = Murid::where('kelas_id', $jadwalPelajaran->kelas_id)
            ->where('status', 'aktif')
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->toArray();

        if (count($muridIds) < 1) {
            return back()->with('error', 'Tidak ada murid aktif pada kelas ini.');
        }

        $validated = $request->validate([
            'absensi' => ['required', 'array'],
            'absensi.*.murid_id' => ['required', 'integer', Rule::in($muridIds)],
            'absensi.*.status_absen' => ['required', Rule::in(['hadir', 'izin', 'sakit', 'alpha'])],
            'absensi.*.keterangan' => ['nullable', 'string', 'max:255'],
        ], $this->validationMessages());

        // penjelasan: Data murid_id dari form kadang terbaca sebagai string.
        // penjelasan: Semua murid_id dari form dikonversi ke integer agar perbandingan tidak gagal.
        // penjelasan: Ini memperbaiki kasus murid hanya satu tetapi tetap dianggap belum lengkap.
        $submittedMuridIds = collect($validated['absensi'])
            ->pluck('murid_id')
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->sort()
            ->values()
            ->toArray();

        $muridIds = collect($muridIds)
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->sort()
            ->values()
            ->toArray();

        if ($submittedMuridIds !== $muridIds) {
            throw ValidationException::withMessages([
                'absensi' => 'Semua murid aktif pada kelas ini wajib diberi status absensi.',
            ]);
        }

        $tanggalHariIni = now()->toDateString();

        DB::transaction(function () use ($validated, $jadwalPelajaran, $guru, $tanggalHariIni) {
            foreach ($validated['absensi'] as $item) {
                $keterangan = isset($item['keterangan']) && trim((string) $item['keterangan']) !== ''
                    ? trim((string) $item['keterangan'])
                    : null;

                AbsensiMurid::updateOrCreate(
                    [
                        'murid_id' => (int) $item['murid_id'],
                        'jadwal_pelajaran_id' => $jadwalPelajaran->id,
                        'tanggal_absen' => $tanggalHariIni,
                    ],
                    [
                        'guru_id' => $guru->id,
                        'kelas_id' => $jadwalPelajaran->kelas_id,
                        'mata_pelajaran_id' => $jadwalPelajaran->mata_pelajaran_id,
                        'status_absen' => $item['status_absen'],
                        'keterangan' => $keterangan,
                        'created_by' => auth()->id(),
                    ]
                );
            }
        });

        return redirect()
            ->route('guru.absensi-murid.show', $jadwalPelajaran)
            ->with('success', 'Absensi murid berhasil disimpan.');
    }

    /**
     * penjelasan: Method ini menampilkan detail absensi murid pada jadwal hari ini.
     * penjelasan: Guru hanya bisa melihat detail absensi dari jadwal miliknya sendiri.
     */
    public function show(JadwalPelajaran $jadwalPelajaran)
    {
        $guru = $this->currentPegawai();

        if ((int) $jadwalPelajaran->guru_id !== (int) $guru->id) {
            abort(403, 'Anda tidak berhak melihat absensi jadwal ini.');
        }

        $jadwalPelajaran->load([
            'tahunAjaran',
            'semester',
            'kelas',
            'mataPelajaran',
        ]);

        $tanggalHariIni = now()->toDateString();

        $absensiMurids = AbsensiMurid::with('murid')
            ->where('jadwal_pelajaran_id', $jadwalPelajaran->id)
            ->whereDate('tanggal_absen', $tanggalHariIni)
            ->orderBy(
                Murid::select('nama_murid')
                    ->whereColumn('murids.id', 'absensi_murids.murid_id')
                    ->limit(1)
            )
            ->get();

        $statusCounts = AbsensiMurid::where('jadwal_pelajaran_id', $jadwalPelajaran->id)
            ->whereDate('tanggal_absen', $tanggalHariIni)
            ->select('status_absen', DB::raw('COUNT(*) as total'))
            ->groupBy('status_absen')
            ->pluck('total', 'status_absen');

        return view('admin.pages.absensi-murid.show', compact(
            'guru',
            'jadwalPelajaran',
            'tanggalHariIni',
            'absensiMurids',
            'statusCounts'
        ));
    }

    /**
     * penjelasan: Method ini mengecek apakah jadwal boleh diakses oleh guru yang login.
     * penjelasan: Jadwal harus milik guru tersebut, aktif, dan sesuai hari ini.
     */
    private function validateJadwalUntukHariIni(JadwalPelajaran $jadwalPelajaran, $guru)
    {
        if ((int) $jadwalPelajaran->guru_id !== (int) $guru->id) {
            abort(403, 'Anda tidak berhak mengakses jadwal ini.');
        }

        $hariHariIni = $this->hariIndonesia((int) now()->dayOfWeek);

        if ($jadwalPelajaran->hari !== $hariHariIni) {
            return redirect()
                ->route('guru.absensi-murid.index')
                ->with('error', 'Absensi murid hanya dapat dilakukan sesuai jadwal hari ini.');
        }

        if ($jadwalPelajaran->status !== 'aktif') {
            return redirect()
                ->route('guru.absensi-murid.index')
                ->with('error', 'Jadwal pelajaran ini sedang nonaktif.');
        }

        return null;
    }

    /**
     * penjelasan: Method ini mengubah angka hari dari Carbon menjadi nama hari Bahasa Indonesia.
     * penjelasan: 1 adalah Senin, 2 Selasa, dan seterusnya.
     */
    private function hariIndonesia(int $dayOfWeek): string
    {
        return match ($dayOfWeek) {
            1 => 'senin',
            2 => 'selasa',
            3 => 'rabu',
            4 => 'kamis',
            5 => 'jumat',
            6 => 'sabtu',
            default => 'minggu',
        };
    }

    /**
     * penjelasan: Method ini menyimpan pesan validasi custom dalam Bahasa Indonesia.
     */
    private function validationMessages(): array
    {
        return [
            'absensi.required' => 'Data absensi murid wajib diisi.',
            'absensi.array' => 'Format data absensi murid tidak valid.',

            'absensi.*.murid_id.required' => 'Data murid wajib tersedia.',
            'absensi.*.murid_id.integer' => 'Data murid tidak valid.',
            'absensi.*.murid_id.in' => 'Murid yang dipilih tidak valid untuk kelas ini.',

            'absensi.*.status_absen.required' => 'Status absensi setiap murid wajib dipilih.',
            'absensi.*.status_absen.in' => 'Status absensi murid tidak valid.',

            'absensi.*.keterangan.string' => 'Keterangan harus berupa teks.',
            'absensi.*.keterangan.max' => 'Keterangan maksimal 255 karakter.',
        ];
    }
}
