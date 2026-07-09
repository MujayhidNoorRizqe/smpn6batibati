<?php

// penjelasan: File ini adalah Controller Rekap Absensi Murid untuk role Guru.
// penjelasan: Alur halaman mengikuti format bertahap:
// penjelasan: 1. Guru filter tahun ajaran, semester, dan kelas.
// penjelasan: 2. Sistem menampilkan kelas yang sesuai filter.
// penjelasan: 3. Guru klik kelas untuk melihat list murid.
// penjelasan: 4. Guru klik murid untuk melihat riwayat absensi murid tersebut.
// penjelasan: Data dibatasi hanya untuk kelas dan jadwal yang diajar guru login.
// penjelasan: Controller ini memakai kolom absensi_murids: tanggal_absen, status_absen, guru_id.

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\AbsensiMurid;
use App\Models\JadwalPelajaran;
use App\Models\Kelas;
use App\Models\Murid;
use App\Models\Semester;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class RekapAbsensiMuridController extends Controller
{
    /**
     * penjelasan: Mengambil data pegawai milik akun guru yang sedang login.
     */
    private function guruLogin()
    {
        $user = Auth::user();

        if (! $user || ! $user->pegawai) {
            abort(403, 'Akun guru belum terhubung dengan data pegawai.');
        }

        return $user->pegawai;
    }

    /**
     * penjelasan: Mencari nama kolom guru pada tabel jadwal_pelajarans.
     * penjelasan: Pada project ini normalnya memakai guru_id.
     * penjelasan: Dibuat fleksibel agar tetap aman jika struktur berubah.
     */
    private function kolomGuruJadwal(): string
    {
        if (Schema::hasColumn('jadwal_pelajarans', 'guru_id')) {
            return 'guru_id';
        }

        if (Schema::hasColumn('jadwal_pelajarans', 'pegawai_id')) {
            return 'pegawai_id';
        }

        if (Schema::hasColumn('jadwal_pelajarans', 'user_id')) {
            return 'user_id';
        }

        abort(500, 'Kolom guru pada tabel jadwal_pelajarans tidak ditemukan. Gunakan guru_id, pegawai_id, atau user_id.');
    }

    /**
     * penjelasan: Menentukan ID guru untuk query jadwal.
     * penjelasan: Jika kolom jadwal adalah user_id, maka memakai Auth::id().
     * penjelasan: Jika kolom jadwal adalah guru_id/pegawai_id, maka memakai id pegawai.
     */
    private function idGuruUntukJadwal($guru): int
    {
        if ($this->kolomGuruJadwal() === 'user_id') {
            return Auth::id();
        }

        return $guru->id;
    }

    /**
     * penjelasan: Mengambil daftar ID jadwal guru berdasarkan kelas, tahun ajaran, dan semester.
     */
    private function jadwalIdsGuruKelas(
        $guru,
        int|string $kelasId,
        int|string $tahunAjaranId,
        int|string $semesterId
    ) {
        $kolomGuruJadwal = $this->kolomGuruJadwal();
        $idGuruJadwal = $this->idGuruUntukJadwal($guru);

        return JadwalPelajaran::where($kolomGuruJadwal, $idGuruJadwal)
            ->where('kelas_id', $kelasId)
            ->where('tahun_ajaran_id', $tahunAjaranId)
            ->where('semester_id', $semesterId)
            ->pluck('id')
            ->unique()
            ->values();
    }

    /**
     * penjelasan: Memastikan guru hanya bisa melihat kelas yang diajar.
     */
    private function pastikanGuruMengajarKelas($guru, int|string $kelasId): void
    {
        $kolomGuruJadwal = $this->kolomGuruJadwal();
        $idGuruJadwal = $this->idGuruUntukJadwal($guru);

        $boleh = JadwalPelajaran::where($kolomGuruJadwal, $idGuruJadwal)
            ->where('kelas_id', $kelasId)
            ->exists();

        if (! $boleh) {
            abort(403, 'Guru hanya boleh melihat rekap absensi murid untuk kelas yang diajar.');
        }
    }

    /**
     * penjelasan: Menghitung total status dari query absensi.
     * penjelasan: Kolom status yang dipakai adalah status_absen.
     */
    private function hitungStatus($query): array
    {
        $data = (clone $query)
            ->selectRaw('LOWER(status_absen) as status_key, COUNT(*) as total')
            ->groupBy('status_key')
            ->pluck('total', 'status_key');

        return [
            'hadir' => (int) ($data['hadir'] ?? 0),
            'izin' => (int) ($data['izin'] ?? 0),
            'sakit' => (int) ($data['sakit'] ?? 0),
            'alpha' => (int) (($data['alpha'] ?? 0) + ($data['alpa'] ?? 0)),
            'terlambat' => (int) ($data['terlambat'] ?? 0),
        ];
    }

    /**
     * penjelasan: Halaman awal rekap absen murid guru.
     * penjelasan: Guru memilih tahun ajaran, semester, dan kelas.
     * penjelasan: Setelah filter lengkap, sistem menampilkan kartu kelas yang ditemukan.
     */
    public function index(Request $request): View
    {
        $guru = $this->guruLogin();

        $kolomGuruJadwal = $this->kolomGuruJadwal();
        $idGuruJadwal = $this->idGuruUntukJadwal($guru);

        $jadwalGuru = JadwalPelajaran::where($kolomGuruJadwal, $idGuruJadwal)
            ->get();

        $kelasIds = $jadwalGuru->pluck('kelas_id')->unique()->values();

        $kelas = Kelas::whereIn('id', $kelasIds)
            ->where('status', 'aktif')
            ->orderBy('nama_kelas')
            ->get();

        $tahunAjarans = TahunAjaran::whereIn('id', $jadwalGuru->pluck('tahun_ajaran_id')->unique())
            ->orderByDesc('id')
            ->get();

        $semesters = Semester::whereIn('id', $jadwalGuru->pluck('semester_id')->unique())
            ->orderByDesc('id')
            ->get();

        $selectedKelasId = $request->kelas_id;
        $selectedTahunAjaranId = $request->tahun_ajaran_id;
        $selectedSemesterId = $request->semester_id;

        $kelasHasil = collect();
        $tahunAjaranDipilih = null;
        $semesterDipilih = null;

        if ($selectedKelasId && $selectedTahunAjaranId && $selectedSemesterId) {
            $this->pastikanGuruMengajarKelas($guru, $selectedKelasId);

            $tahunAjaranDipilih = TahunAjaran::find($selectedTahunAjaranId);
            $semesterDipilih = Semester::find($selectedSemesterId);

            $jadwalIds = $this->jadwalIdsGuruKelas(
                $guru,
                $selectedKelasId,
                $selectedTahunAjaranId,
                $selectedSemesterId
            );

            $kelasHasil = Kelas::with('waliKelas')
                ->where('id', $selectedKelasId)
                ->whereIn('id', $kelasIds)
                ->where('status', 'aktif')
                ->get()
                ->map(function ($kelasItem) use ($jadwalIds) {
                    $muridIds = Murid::where('kelas_id', $kelasItem->id)
                        ->where('status', 'aktif')
                        ->pluck('id');

                    $queryAbsensi = AbsensiMurid::whereIn('jadwal_pelajaran_id', $jadwalIds)
                        ->whereIn('murid_id', $muridIds)
                        ->where('kelas_id', $kelasItem->id);

                    $status = $this->hitungStatus($queryAbsensi);

                    $kelasItem->total_murid = $muridIds->count();
                    $kelasItem->total_jadwal = $jadwalIds->count();
                    $kelasItem->total_absensi = (clone $queryAbsensi)->count();
                    $kelasItem->hadir = $status['hadir'];
                    $kelasItem->izin = $status['izin'];
                    $kelasItem->sakit = $status['sakit'];
                    $kelasItem->alpha = $status['alpha'];
                    $kelasItem->terlambat = $status['terlambat'];

                    return $kelasItem;
                });
        }

        return view('admin.pages.guru.rekap-absen-murid.index', compact(
            'guru',
            'kelas',
            'tahunAjarans',
            'semesters',
            'selectedKelasId',
            'selectedTahunAjaranId',
            'selectedSemesterId',
            'kelasHasil',
            'tahunAjaranDipilih',
            'semesterDipilih'
        ));
    }

    /**
     * penjelasan: Menampilkan daftar murid dari kelas hasil filter.
     */
    public function kelas(Request $request, Kelas $kelas): View
    {
        $guru = $this->guruLogin();

        $validated = $request->validate([
            'tahun_ajaran_id' => ['required', 'exists:tahun_ajarans,id'],
            'semester_id' => ['required', 'exists:semesters,id'],
        ], [
            'tahun_ajaran_id.required' => 'Tahun ajaran wajib dipilih.',
            'semester_id.required' => 'Semester wajib dipilih.',
        ]);

        $this->pastikanGuruMengajarKelas($guru, $kelas->id);

        $tahunAjaran = TahunAjaran::findOrFail($validated['tahun_ajaran_id']);
        $semester = Semester::findOrFail($validated['semester_id']);
        $kelas->load('waliKelas');

        $jadwalIds = $this->jadwalIdsGuruKelas(
            $guru,
            $kelas->id,
            $tahunAjaran->id,
            $semester->id
        );

        $murids = Murid::where('kelas_id', $kelas->id)
            ->where('status', 'aktif')
            ->orderBy('nama_murid')
            ->get();

        $absensiPerMurid = AbsensiMurid::whereIn('jadwal_pelajaran_id', $jadwalIds)
            ->whereIn('murid_id', $murids->pluck('id'))
            ->where('kelas_id', $kelas->id)
            ->get()
            ->groupBy('murid_id');

        $murids = $murids->map(function ($murid) use ($absensiPerMurid) {
            $riwayat = $absensiPerMurid->get($murid->id, collect());

            $statusCounts = $riwayat
                ->groupBy(function ($item) {
                    return strtolower((string) $item->status_absen);
                })
                ->map(function ($items) {
                    return $items->count();
                });

            $murid->total_absensi = $riwayat->count();
            $murid->hadir = (int) ($statusCounts['hadir'] ?? 0);
            $murid->izin = (int) ($statusCounts['izin'] ?? 0);
            $murid->sakit = (int) ($statusCounts['sakit'] ?? 0);
            $murid->alpha = (int) (($statusCounts['alpha'] ?? 0) + ($statusCounts['alpa'] ?? 0));
            $murid->terlambat = (int) ($statusCounts['terlambat'] ?? 0);

            return $murid;
        });

        return view('admin.pages.guru.rekap-absen-murid.kelas', compact(
            'guru',
            'kelas',
            'tahunAjaran',
            'semester',
            'jadwalIds',
            'murids'
        ));
    }

    /**
     * penjelasan: Menampilkan riwayat absensi per murid.
     */
    public function murid(Request $request, Murid $murid): View
    {
        $guru = $this->guruLogin();

        $validated = $request->validate([
            'kelas_id' => ['required', 'exists:kelas,id'],
            'tahun_ajaran_id' => ['required', 'exists:tahun_ajarans,id'],
            'semester_id' => ['required', 'exists:semesters,id'],
        ], [
            'kelas_id.required' => 'Kelas wajib dipilih.',
            'tahun_ajaran_id.required' => 'Tahun ajaran wajib dipilih.',
            'semester_id.required' => 'Semester wajib dipilih.',
        ]);

        if ((int) $murid->kelas_id !== (int) $validated['kelas_id']) {
            abort(403, 'Murid tidak sesuai dengan kelas yang dipilih.');
        }

        $this->pastikanGuruMengajarKelas($guru, $validated['kelas_id']);

        $kelas = Kelas::with('waliKelas')->findOrFail($validated['kelas_id']);
        $tahunAjaran = TahunAjaran::findOrFail($validated['tahun_ajaran_id']);
        $semester = Semester::findOrFail($validated['semester_id']);

        $jadwalIds = $this->jadwalIdsGuruKelas(
            $guru,
            $kelas->id,
            $tahunAjaran->id,
            $semester->id
        );

        $baseQuery = AbsensiMurid::with([
                'jadwalPelajaran',
                'kelas',
                'mataPelajaran',
                'guru',
                'murid',
            ])
            ->where('murid_id', $murid->id)
            ->where('kelas_id', $kelas->id)
            ->whereIn('jadwal_pelajaran_id', $jadwalIds);

        $ringkasanStatus = $this->hitungStatus($baseQuery);
        $totalAbsensi = (clone $baseQuery)->count();

        $riwayatAbsensi = $baseQuery
            ->orderByDesc('tanggal_absen')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.pages.guru.rekap-absen-murid.murid', compact(
            'guru',
            'murid',
            'kelas',
            'tahunAjaran',
            'semester',
            'riwayatAbsensi',
            'ringkasanStatus',
            'totalAbsensi'
        ));
    }
}
