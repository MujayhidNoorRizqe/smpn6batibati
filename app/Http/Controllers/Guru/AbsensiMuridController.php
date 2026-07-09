<?php

// penjelasan: Controller ini digunakan oleh guru.
// penjelasan: Controller ini menangani halaman Absen Murid, input absensi murid, simpan absensi, dan detail absensi.
// penjelasan: Guru hanya bisa mengabsen murid berdasarkan jadwal mengajar miliknya pada hari berjalan.
// penjelasan: Pada halaman index, jadwal mengajar guru hari ini dikelompokkan per kelas.
// penjelasan: Riwayat absensi murid juga dikelompokkan per kelas agar tampilan lebih rapi.

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\AbsensiMurid;
use App\Models\JadwalPelajaran;
use App\Models\Murid;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AbsensiMuridController extends Controller
{
    /**
     * penjelasan: Method ini mengambil data pegawai dari user yang sedang login.
     * penjelasan: Akun guru wajib terhubung dengan data pegawai agar bisa menginput absensi murid.
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
     * penjelasan: Jadwal dikelompokkan berdasarkan kelas agar guru memilih kelas terlebih dahulu.
     * penjelasan: Riwayat absensi juga dikelompokkan berdasarkan kelas.
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

            $jadwal->target_absensi = $jadwal->total_murid_aktif;

            $jadwal->status_absensi_hari_ini = $this->statusProgressAbsensi(
                (int) $jadwal->total_absensi_hari_ini,
                (int) $jadwal->target_absensi
            );
        }

        // penjelasan: Jadwal hari ini dikelompokkan berdasarkan kelas_id.
        $kelasJadwalHariIni = $jadwalHariIni
            ->groupBy('kelas_id')
            ->map(function ($jadwals) {
                $jadwalPertama = $jadwals->first();

                $totalMuridAktif = (int) ($jadwalPertama->total_murid_aktif ?? 0);
                $totalJadwal = $jadwals->count();

                $totalAbsensiHariIni = $jadwals->sum(function ($jadwal) {
                    return (int) ($jadwal->total_absensi_hari_ini ?? 0);
                });

                $targetAbsensi = $totalMuridAktif * $totalJadwal;

                return (object) [
                    'kelas' => $jadwalPertama->kelas,
                    'kelas_id' => $jadwalPertama->kelas_id,
                    'jadwals' => $jadwals->values(),
                    'total_jadwal' => $totalJadwal,
                    'total_murid_aktif' => $totalMuridAktif,
                    'total_absensi_hari_ini' => $totalAbsensiHariIni,
                    'target_absensi' => $targetAbsensi,
                    'status_absensi_hari_ini' => $this->statusProgressAbsensi($totalAbsensiHariIni, $targetAbsensi),
                ];
            })
            ->sortBy(fn ($item) => $item->kelas?->nama_kelas ?? '')
            ->values();

        $statusCounts = AbsensiMurid::where('guru_id', $guru->id)
            ->whereDate('tanggal_absen', $tanggalHariIni)
            ->select('status_absen', DB::raw('COUNT(*) as total'))
            ->groupBy('status_absen')
            ->pluck('total', 'status_absen');

        // penjelasan: Riwayat absensi milik guru diambil semua lalu dikelompokkan per kelas.
        // penjelasan: Ini membuat tampilan bawah halaman Absen Murid tidak lagi berupa tabel panjang per murid.
        $riwayatAbsensi = AbsensiMurid::with([
                'murid',
                'kelas',
                'mataPelajaran',
                'jadwalPelajaran',
            ])
            ->where('guru_id', $guru->id)
            ->latest('tanggal_absen')
            ->latest()
            ->get();

        $riwayatAbsensiTotal = $riwayatAbsensi->count();

        $riwayatAbsensiPerKelas = $riwayatAbsensi
            ->groupBy('kelas_id')
            ->map(function ($items) {
                $kelas = $items->first()?->kelas;
                $status = $items->groupBy('status_absen')->map->count();
                $tanggalTerakhir = $items->sortByDesc('tanggal_absen')->first()?->tanggal_absen;

                return (object) [
                    'kelas_id' => $kelas?->id,
                    'kelas' => $kelas,
                    'total_data' => $items->count(),
                    'total_murid' => $items->pluck('murid_id')->unique()->count(),
                    'total_mapel' => $items->pluck('mata_pelajaran_id')->unique()->count(),
                    'hadir' => $status['hadir'] ?? 0,
                    'izin' => $status['izin'] ?? 0,
                    'sakit' => $status['sakit'] ?? 0,
                    'alpha' => $status['alpha'] ?? 0,
                    'terlambat' => $status['terlambat'] ?? 0,
                    'tanggal_terakhir' => $tanggalTerakhir,
                    'absensis' => $items->take(10)->values(),
                ];
            })
            ->sortBy(fn ($item) => $item->kelas?->nama_kelas ?? '')
            ->values();

        return view('admin.pages.absensi-murid.index', compact(
            'guru',
            'tanggalHariIni',
            'hariHariIni',
            'jadwalHariIni',
            'kelasJadwalHariIni',
            'statusCounts',
            'riwayatAbsensiPerKelas',
            'riwayatAbsensiTotal'
        ));
    }

    /**
     * penjelasan: Method ini menampilkan form input absensi murid berdasarkan jadwal pelajaran.
     * penjelasan: Daftar murid yang tampil hanya murid aktif pada kelas dari jadwal tersebut.
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
     * penjelasan: Semua murid aktif pada kelas terkait wajib memiliki status absensi.
     * penjelasan: Jika data sudah ada pada jadwal dan tanggal yang sama, data diperbarui.
     */
    public function store(Request $request, JadwalPelajaran $jadwalPelajaran)
    {
        $guru = $this->currentPegawai();

        $redirect = $this->validateJadwalUntukHariIni($jadwalPelajaran, $guru);

        if ($redirect) {
            return $redirect;
        }

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
            'absensi.*.status_absen' => ['required', Rule::in($this->statusAbsensiMurid())],
            'absensi.*.keterangan' => ['nullable', 'string', 'max:255'],
        ], $this->validationMessages());

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
     * penjelasan: Method ini menyimpan daftar status absensi murid yang valid.
     */
    private function statusAbsensiMurid(): array
    {
        return ['hadir', 'izin', 'sakit', 'alpha', 'terlambat'];
    }

    /**
     * penjelasan: Method ini menentukan status progress absensi.
     */
    private function statusProgressAbsensi(int $totalAbsensi, int $targetAbsensi): string
    {
        if ($targetAbsensi < 1) {
            return 'tidak_ada_murid';
        }

        if ($totalAbsensi < 1) {
            return 'belum_diabsen';
        }

        if ($totalAbsensi < $targetAbsensi) {
            return 'belum_lengkap';
        }

        return 'sudah_lengkap';
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
