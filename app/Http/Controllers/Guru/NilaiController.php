<?php

// penjelasan: File ini adalah Controller Nilai untuk role Guru.
// penjelasan: Controller ini mengatur input nilai ujian semester dan rekap nilai.
// penjelasan: Alur input nilai: guru pilih tahun ajaran, semester, kelas, lalu pilih murid.
// penjelasan: Alur rekap nilai: guru filter tahun ajaran, semester, kelas, lalu lihat kelas, list murid, dan detail nilai per murid.
// penjelasan: Nilai yang diinput hanya nilai ujian semester ganjil/genap, bukan nilai tugas atau nilai harian.

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\JadwalPelajaran;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Murid;
use App\Models\Nilai;
use App\Models\Semester;
use App\Models\TahunAjaran;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class NilaiController extends Controller
{
    /**
     * penjelasan: Mengambil data pegawai milik user guru yang sedang login.
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
     * penjelasan: Dibuat fleksibel karena bisa memakai pegawai_id, guru_id, atau user_id.
     */
    private function kolomGuruJadwal(): string
    {
        if (Schema::hasColumn('jadwal_pelajarans', 'pegawai_id')) {
            return 'pegawai_id';
        }

        if (Schema::hasColumn('jadwal_pelajarans', 'guru_id')) {
            return 'guru_id';
        }

        if (Schema::hasColumn('jadwal_pelajarans', 'user_id')) {
            return 'user_id';
        }

        abort(500, 'Kolom guru pada tabel jadwal_pelajarans tidak ditemukan. Gunakan pegawai_id, guru_id, atau user_id.');
    }

    /**
     * penjelasan: Menentukan ID yang dipakai untuk mencari jadwal guru.
     */
    private function idGuruUntukJadwal($guru): int
    {
        if ($this->kolomGuruJadwal() === 'user_id') {
            return Auth::id();
        }

        return $guru->id;
    }

    /**
     * penjelasan: Halaman awal input nilai.
     * penjelasan: Guru memilih tahun ajaran, semester, dan kelas.
     * penjelasan: Setelah filter dipilih, sistem menampilkan daftar murid pada kelas tersebut.
     */
    public function index(Request $request): View
    {
        $guru = $this->guruLogin();

        $kolomGuruJadwal = $this->kolomGuruJadwal();
        $idGuruJadwal = $this->idGuruUntukJadwal($guru);

        $jadwalGuru = JadwalPelajaran::with(['kelas', 'mataPelajaran'])
            ->where($kolomGuruJadwal, $idGuruJadwal)
            ->get();

        $kelasIds = $jadwalGuru->pluck('kelas_id')->unique()->values();

        $kelas = Kelas::whereIn('id', $kelasIds)
            ->where('status', 'aktif')
            ->orderBy('nama_kelas')
            ->get();

        $tahunAjarans = TahunAjaran::orderByDesc('id')->get();
        $semesters = Semester::orderByDesc('id')->get();

        $murids = collect();

        $selectedKelasId = $request->kelas_id;
        $selectedTahunAjaranId = $request->tahun_ajaran_id;
        $selectedSemesterId = $request->semester_id;

        if ($selectedKelasId && $selectedTahunAjaranId && $selectedSemesterId) {
            $this->pastikanGuruMengajarKelas($guru, $selectedKelasId);

            $mapelIds = $this->mapelIdsGuruPadaKelas($guru, $selectedKelasId);

            $murids = Murid::with('kelas')
                ->where('kelas_id', $selectedKelasId)
                ->where('status', 'aktif')
                ->orderBy('nama_murid')
                ->get();

            $nilaiPerMurid = Nilai::whereIn('murid_id', $murids->pluck('id'))
                ->where('tahun_ajaran_id', $selectedTahunAjaranId)
                ->where('semester_id', $selectedSemesterId)
                ->whereIn('mata_pelajaran_id', $mapelIds)
                ->get()
                ->groupBy('murid_id');

            $murids = $murids->map(function ($murid) use ($nilaiPerMurid) {
                $murid->jumlah_nilai_terisi = $nilaiPerMurid->get($murid->id, collect())->count();

                return $murid;
            });
        }

        return view('admin.pages.guru.input-nilai.index', compact(
            'guru',
            'kelas',
            'tahunAjarans',
            'semesters',
            'murids',
            'selectedKelasId',
            'selectedTahunAjaranId',
            'selectedSemesterId'
        ));
    }

    /**
     * penjelasan: Halaman input nilai per murid dengan format seperti rapor.
     */
    public function inputMurid(Request $request, Murid $murid): View
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

        $mapelIds = $this->mapelIdsGuruPadaKelas($guru, $validated['kelas_id']);

        $mataPelajarans = MataPelajaran::whereIn('id', $mapelIds)
            ->where('status', 'aktif')
            ->orderBy('nama_mapel')
            ->get();

        $nilaiTersimpan = Nilai::where('murid_id', $murid->id)
            ->where('tahun_ajaran_id', $validated['tahun_ajaran_id'])
            ->where('semester_id', $validated['semester_id'])
            ->whereIn('mata_pelajaran_id', $mapelIds)
            ->get()
            ->keyBy('mata_pelajaran_id');

        $kelas = Kelas::with('waliKelas')->findOrFail($validated['kelas_id']);
        $tahunAjaran = TahunAjaran::findOrFail($validated['tahun_ajaran_id']);
        $semester = Semester::findOrFail($validated['semester_id']);

        return view('admin.pages.guru.input-nilai.murid', compact(
            'guru',
            'murid',
            'kelas',
            'tahunAjaran',
            'semester',
            'mataPelajarans',
            'nilaiTersimpan'
        ));
    }

    /**
     * penjelasan: Menyimpan nilai ujian semester per murid.
     */
    public function storeMurid(Request $request, Murid $murid): RedirectResponse
    {
        $guru = $this->guruLogin();

        $validated = $request->validate([
            'kelas_id' => ['required', 'exists:kelas,id'],
            'tahun_ajaran_id' => ['required', 'exists:tahun_ajarans,id'],
            'semester_id' => ['required', 'exists:semesters,id'],
            'nilai' => ['required', 'array'],
            'nilai.*' => ['nullable', 'integer', 'min:0', 'max:100'],
            'keterangan' => ['nullable', 'array'],
            'keterangan.*' => ['nullable', 'string', 'max:255'],
        ], [
            'kelas_id.required' => 'Kelas wajib dipilih.',
            'tahun_ajaran_id.required' => 'Tahun ajaran wajib dipilih.',
            'semester_id.required' => 'Semester wajib dipilih.',
            'nilai.required' => 'Nilai wajib diisi.',
            'nilai.*.integer' => 'Nilai harus berupa angka.',
            'nilai.*.min' => 'Nilai minimal 0.',
            'nilai.*.max' => 'Nilai maksimal 100.',
        ]);

        if ((int) $murid->kelas_id !== (int) $validated['kelas_id']) {
            abort(403, 'Murid tidak sesuai dengan kelas yang dipilih.');
        }

        $this->pastikanGuruMengajarKelas($guru, $validated['kelas_id']);

        $mapelIds = $this->mapelIdsGuruPadaKelas($guru, $validated['kelas_id']);

        if ($mapelIds->isEmpty()) {
            return back()
                ->withInput()
                ->with('error', 'Belum ada mata pelajaran yang dapat diinput untuk kelas ini.');
        }

        foreach ($mapelIds as $mapelId) {
            $nilai = $validated['nilai'][$mapelId] ?? null;

            if ($nilai === null || $nilai === '') {
                return back()
                    ->withInput()
                    ->with('error', 'Nilai semua mata pelajaran wajib diisi sebelum disimpan.');
            }
        }

        DB::transaction(function () use ($validated, $mapelIds, $murid, $guru) {
            foreach ($mapelIds as $mapelId) {
                Nilai::updateOrCreate(
                    [
                        'murid_id' => $murid->id,
                        'mata_pelajaran_id' => $mapelId,
                        'semester_id' => $validated['semester_id'],
                        'tahun_ajaran_id' => $validated['tahun_ajaran_id'],
                    ],
                    [
                        'pegawai_id' => $guru->id,
                        'kelas_id' => $validated['kelas_id'],
                        'nilai_ujian' => $validated['nilai'][$mapelId],
                        'keterangan' => $validated['keterangan'][$mapelId] ?? null,
                    ]
                );
            }
        });

        return redirect()
            ->route('guru.input-nilai.index', [
                'kelas_id' => $validated['kelas_id'],
                'tahun_ajaran_id' => $validated['tahun_ajaran_id'],
                'semester_id' => $validated['semester_id'],
            ])
            ->with('success', 'Nilai ujian semester murid berhasil disimpan.');
    }

    /**
     * penjelasan: Halaman awal rekap nilai.
     * penjelasan: Guru filter tahun ajaran, semester, dan kelas.
     * penjelasan: Setelah filter, halaman hanya menampilkan kartu kelas yang ditemukan.
     */
    public function rekap(Request $request): View
    {
        $guru = $this->guruLogin();

        $kolomGuruJadwal = $this->kolomGuruJadwal();
        $idGuruJadwal = $this->idGuruUntukJadwal($guru);

        $jadwalGuru = JadwalPelajaran::where($kolomGuruJadwal, $idGuruJadwal)->get();

        $kelasIds = $jadwalGuru->pluck('kelas_id')->unique()->values();

        $kelas = Kelas::whereIn('id', $kelasIds)
            ->where('status', 'aktif')
            ->orderBy('nama_kelas')
            ->get();

        $tahunAjarans = TahunAjaran::orderByDesc('id')->get();
        $semesters = Semester::orderByDesc('id')->get();

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

            $mapelIds = $this->mapelIdsGuruPadaKelas($guru, $selectedKelasId);

            $kelasHasil = Kelas::with('waliKelas')
                ->where('id', $selectedKelasId)
                ->whereIn('id', $kelasIds)
                ->where('status', 'aktif')
                ->get()
                ->map(function ($kelasItem) use ($selectedTahunAjaranId, $selectedSemesterId, $mapelIds) {
                    $muridIds = Murid::where('kelas_id', $kelasItem->id)
                        ->where('status', 'aktif')
                        ->pluck('id');

                    $kelasItem->total_murid = $muridIds->count();
                    $kelasItem->total_mapel = $mapelIds->count();
                    $kelasItem->total_nilai_terisi = Nilai::whereIn('murid_id', $muridIds)
                        ->where('tahun_ajaran_id', $selectedTahunAjaranId)
                        ->where('semester_id', $selectedSemesterId)
                        ->whereIn('mata_pelajaran_id', $mapelIds)
                        ->count();

                    return $kelasItem;
                });
        }

        return view('admin.pages.guru.rekap-nilai.index', compact(
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
     * penjelasan: Menampilkan list murid dari kelas hasil filter.
     */
    public function rekapKelas(Request $request, Kelas $kelas): View
    {
        $guru = $this->guruLogin();

        $validated = $request->validate([
            'tahun_ajaran_id' => ['required', 'exists:tahun_ajarans,id'],
            'semester_id' => ['required', 'exists:semesters,id'],
        ]);

        $this->pastikanGuruMengajarKelas($guru, $kelas->id);

        $tahunAjaran = TahunAjaran::findOrFail($validated['tahun_ajaran_id']);
        $semester = Semester::findOrFail($validated['semester_id']);
        $kelas->load('waliKelas');

        $mapelIds = $this->mapelIdsGuruPadaKelas($guru, $kelas->id);

        $mataPelajarans = MataPelajaran::whereIn('id', $mapelIds)
            ->where('status', 'aktif')
            ->orderBy('nama_mapel')
            ->get();

        $murids = Murid::with('kelas')
            ->where('kelas_id', $kelas->id)
            ->where('status', 'aktif')
            ->orderBy('nama_murid')
            ->get();

        $nilaiPerMurid = Nilai::whereIn('murid_id', $murids->pluck('id'))
            ->where('tahun_ajaran_id', $tahunAjaran->id)
            ->where('semester_id', $semester->id)
            ->whereIn('mata_pelajaran_id', $mapelIds)
            ->get()
            ->groupBy('murid_id');

        $murids = $murids->map(function ($murid) use ($mataPelajarans, $nilaiPerMurid) {
            $nilaiMurid = $nilaiPerMurid->get($murid->id, collect());

            $jumlahMapel = $mataPelajarans->count();
            $jumlahNilai = $nilaiMurid->count();

            $rataRata = $jumlahNilai > 0
                ? round($nilaiMurid->avg('nilai_ujian'), 2)
                : null;

            if ($jumlahMapel > 0 && $jumlahNilai >= $jumlahMapel) {
                $statusNilai = 'Lengkap';
            } elseif ($jumlahNilai > 0) {
                $statusNilai = 'Sebagian';
            } else {
                $statusNilai = 'Belum Diisi';
            }

            $murid->jumlah_mapel = $jumlahMapel;
            $murid->jumlah_nilai = $jumlahNilai;
            $murid->rata_rata = $rataRata;
            $murid->predikat = $rataRata === null ? '-' : $this->predikatDariNilai($rataRata);
            $murid->keterangan_predikat = $rataRata === null ? '-' : $this->keteranganPredikatDariNilai($rataRata);
            $murid->status_nilai = $statusNilai;

            return $murid;
        });

        return view('admin.pages.guru.rekap-nilai.kelas', compact(
            'guru',
            'kelas',
            'tahunAjaran',
            'semester',
            'mataPelajarans',
            'murids'
        ));
    }

    /**
     * penjelasan: Menampilkan detail nilai per murid.
     */
    public function rekapMurid(Request $request, Murid $murid): View
    {
        $guru = $this->guruLogin();

        $validated = $request->validate([
            'kelas_id' => ['required', 'exists:kelas,id'],
            'tahun_ajaran_id' => ['required', 'exists:tahun_ajarans,id'],
            'semester_id' => ['required', 'exists:semesters,id'],
        ]);

        if ((int) $murid->kelas_id !== (int) $validated['kelas_id']) {
            abort(403, 'Murid tidak sesuai dengan kelas yang dipilih.');
        }

        $this->pastikanGuruMengajarKelas($guru, $validated['kelas_id']);

        $kelas = Kelas::with('waliKelas')->findOrFail($validated['kelas_id']);
        $tahunAjaran = TahunAjaran::findOrFail($validated['tahun_ajaran_id']);
        $semester = Semester::findOrFail($validated['semester_id']);

        $mapelIds = $this->mapelIdsGuruPadaKelas($guru, $kelas->id);

        $mataPelajarans = MataPelajaran::whereIn('id', $mapelIds)
            ->where('status', 'aktif')
            ->orderBy('nama_mapel')
            ->get();

        $nilaiTersimpan = Nilai::where('murid_id', $murid->id)
            ->where('tahun_ajaran_id', $tahunAjaran->id)
            ->where('semester_id', $semester->id)
            ->whereIn('mata_pelajaran_id', $mapelIds)
            ->get()
            ->keyBy('mata_pelajaran_id');

        $nilaiAngka = $nilaiTersimpan->pluck('nilai_ujian');

        $rataRata = $nilaiAngka->count() > 0
            ? round($nilaiAngka->avg(), 2)
            : null;

        $ringkasan = [
            'total_mapel' => $mataPelajarans->count(),
            'nilai_terisi' => $nilaiTersimpan->count(),
            'rata_rata' => $rataRata,
            'predikat' => $rataRata === null ? '-' : $this->predikatDariNilai($rataRata),
            'keterangan_predikat' => $rataRata === null ? '-' : $this->keteranganPredikatDariNilai($rataRata),
        ];

        return view('admin.pages.guru.rekap-nilai.murid', compact(
            'guru',
            'murid',
            'kelas',
            'tahunAjaran',
            'semester',
            'mataPelajarans',
            'nilaiTersimpan',
            'ringkasan'
        ));
    }

    /**
     * penjelasan: Mengecek apakah guru mengajar pada kelas tertentu.
     */
    private function pastikanGuruMengajarKelas($guru, int|string $kelasId): void
    {
        $kolomGuruJadwal = $this->kolomGuruJadwal();
        $idGuruJadwal = $this->idGuruUntukJadwal($guru);

        $boleh = JadwalPelajaran::where($kolomGuruJadwal, $idGuruJadwal)
            ->where('kelas_id', $kelasId)
            ->exists();

        if (! $boleh) {
            abort(403, 'Guru hanya boleh mengakses nilai untuk kelas yang diajar.');
        }
    }

    /**
     * penjelasan: Mengambil daftar mata pelajaran yang diajar guru pada kelas tertentu.
     */
    private function mapelIdsGuruPadaKelas($guru, int|string $kelasId)
    {
        $kolomGuruJadwal = $this->kolomGuruJadwal();
        $idGuruJadwal = $this->idGuruUntukJadwal($guru);

        return JadwalPelajaran::where($kolomGuruJadwal, $idGuruJadwal)
            ->where('kelas_id', $kelasId)
            ->pluck('mata_pelajaran_id')
            ->unique()
            ->values();
    }

    /**
     * penjelasan: Mengubah nilai angka menjadi predikat huruf.
     */
    private function predikatDariNilai(float|int $nilai): string
    {
        if ($nilai >= 90) {
            return 'A';
        }

        if ($nilai >= 80) {
            return 'B';
        }

        if ($nilai >= 70) {
            return 'C';
        }

        return 'D';
    }

    /**
     * penjelasan: Mengubah nilai angka menjadi keterangan predikat.
     */
    private function keteranganPredikatDariNilai(float|int $nilai): string
    {
        if ($nilai >= 90) {
            return 'Sangat Baik';
        }

        if ($nilai >= 80) {
            return 'Baik';
        }

        if ($nilai >= 70) {
            return 'Cukup';
        }

        return 'Perlu Bimbingan';
    }
}
