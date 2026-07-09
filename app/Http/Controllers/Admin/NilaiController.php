<?php

// penjelasan: File ini adalah Controller Nilai untuk Admin dan Super Admin.
// penjelasan: Admin/Super Admin hanya bisa melihat nilai, tidak bisa input atau edit nilai.
// penjelasan: Nilai yang ditampilkan adalah nilai ujian semester ganjil/genap sesuai data yang diinput guru.
// penjelasan: Alur halaman:
// penjelasan: 1. Halaman index menampilkan rekap nilai per kelas.
// penjelasan: 2. Klik kelas menampilkan list murid dalam kelas tersebut.
// penjelasan: 3. Klik murid menampilkan detail nilai murid seperti format rapor.
// penjelasan: Controller ini dipanggil dari route super-admin.nilai.* dan admin.nilai.*.

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Murid;
use App\Models\Nilai;
use App\Models\Semester;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;

class NilaiController extends Controller
{
    private function routePrefix(): string
    {
        return auth()->user()->role === 'super_admin' ? 'super-admin' : 'admin';
    }

    public function index(Request $request)
    {
        $validated = $this->validateFilter($request);

        $query = $this->baseQuery($validated);

        $semuaNilai = $query
            ->get()
            ->sortBy(function ($nilai) {
                return ($nilai->kelas?->tingkat ?? '99') . '|' . ($nilai->kelas?->nama_kelas ?? '') . '|' . ($nilai->murid?->nama_murid ?? '');
            })
            ->values();

        $totalData = $semuaNilai->count();
        $totalKelas = $semuaNilai->pluck('kelas_id')->unique()->count();
        $totalMurid = $semuaNilai->pluck('murid_id')->unique()->count();
        $totalMapel = $semuaNilai->pluck('mata_pelajaran_id')->unique()->count();

        $rataRataUmum = $semuaNilai->count() > 0
            ? round($semuaNilai->avg('nilai_ujian'), 2)
            : null;

        $rekapPerKelas = $semuaNilai
            ->groupBy('kelas_id')
            ->map(function ($items) {
                $kelas = $items->first()?->kelas;

                $rataRata = $items->count() > 0
                    ? round($items->avg('nilai_ujian'), 2)
                    : null;

                return (object) [
                    'kelas_id' => $kelas?->id,
                    'kelas' => $kelas,
                    'total_data' => $items->count(),
                    'total_murid' => $items->pluck('murid_id')->unique()->count(),
                    'total_mapel' => $items->pluck('mata_pelajaran_id')->unique()->count(),
                    'total_guru' => $items->pluck('pegawai_id')->unique()->count(),
                    'nilai_terendah' => $items->min('nilai_ujian'),
                    'nilai_tertinggi' => $items->max('nilai_ujian'),
                    'rata_rata' => $rataRata,
                    'predikat' => $rataRata === null ? '-' : $this->predikatDariNilai($rataRata),
                    'keterangan_predikat' => $rataRata === null ? '-' : $this->keteranganPredikatDariNilai($rataRata),
                ];
            })
            ->sortBy(function ($item) {
                return ($item->kelas?->tingkat ?? '99') . '|' . ($item->kelas?->nama_kelas ?? '');
            })
            ->values();

        $kelasList = Kelas::where('status', 'aktif')
            ->orderBy('tingkat')
            ->orderBy('nama_kelas')
            ->get();

        $tahunAjarans = TahunAjaran::orderByDesc('id')->get();

        $semesters = Semester::with('tahunAjaran')
            ->orderByDesc('id')
            ->get();

        $mataPelajarans = MataPelajaran::where('status', 'aktif')
            ->orderBy('nama_mapel')
            ->get();

        $routePrefix = $this->routePrefix();

        return view('admin.pages.nilai.index', compact(
            'validated',
            'totalData',
            'totalKelas',
            'totalMurid',
            'totalMapel',
            'rataRataUmum',
            'rekapPerKelas',
            'kelasList',
            'tahunAjarans',
            'semesters',
            'mataPelajarans',
            'routePrefix'
        ));
    }

    public function kelas(Request $request, Kelas $kelas)
    {
        $validated = $this->validateFilter($request);

        $kelas->load('waliKelas');

        $validatedUntukKelas = $validated;
        $validatedUntukKelas['kelas_id'] = null;

        $nilaiKelas = $this->baseQuery($validatedUntukKelas)
            ->where('kelas_id', $kelas->id)
            ->get();

        $murids = Murid::with(['kelas', 'waliMurid'])
            ->where('kelas_id', $kelas->id)
            ->where('status', 'aktif')
            ->orderBy('nama_murid')
            ->get();

        $nilaiPerMurid = $nilaiKelas->groupBy('murid_id');

        $rekapPerMurid = $murids
            ->map(function ($murid) use ($nilaiPerMurid) {
                $items = $nilaiPerMurid->get($murid->id, collect());

                $rataRata = $items->count() > 0
                    ? round($items->avg('nilai_ujian'), 2)
                    : null;

                return (object) [
                    'murid' => $murid,
                    'total_nilai' => $items->count(),
                    'total_mapel' => $items->pluck('mata_pelajaran_id')->unique()->count(),
                    'total_guru' => $items->pluck('pegawai_id')->unique()->count(),
                    'nilai_terendah' => $items->count() > 0 ? $items->min('nilai_ujian') : null,
                    'nilai_tertinggi' => $items->count() > 0 ? $items->max('nilai_ujian') : null,
                    'rata_rata' => $rataRata,
                    'predikat' => $rataRata === null ? '-' : $this->predikatDariNilai($rataRata),
                    'keterangan_predikat' => $rataRata === null ? '-' : $this->keteranganPredikatDariNilai($rataRata),
                    'status_nilai' => $this->statusNilai($items->count()),
                ];
            })
            ->values();

        $totalData = $nilaiKelas->count();
        $totalMurid = $murids->count();
        $totalMapel = $nilaiKelas->pluck('mata_pelajaran_id')->unique()->count();

        $rataRataKelas = $nilaiKelas->count() > 0
            ? round($nilaiKelas->avg('nilai_ujian'), 2)
            : null;

        $routePrefix = $this->routePrefix();

        return view('admin.pages.nilai.kelas', compact(
            'validated',
            'kelas',
            'rekapPerMurid',
            'totalData',
            'totalMurid',
            'totalMapel',
            'rataRataKelas',
            'routePrefix'
        ));
    }

    public function murid(Request $request, Murid $murid)
    {
        $validated = $this->validateFilter($request);

        $murid->load([
            'kelas.waliKelas',
            'waliMurid',
        ]);

        $validatedUntukMurid = $validated;
        $validatedUntukMurid['kelas_id'] = null;

        $nilaiMurids = $this->baseQuery($validatedUntukMurid)
            ->where('murid_id', $murid->id)
            ->get()
            ->sortBy(function ($nilai) {
                return $nilai->mataPelajaran?->nama_mapel ?? '';
            })
            ->values();

        $tahunAjaranDipilih = ! empty($validated['tahun_ajaran_id'])
            ? TahunAjaran::find($validated['tahun_ajaran_id'])
            : null;

        $semesterDipilih = ! empty($validated['semester_id'])
            ? Semester::with('tahunAjaran')->find($validated['semester_id'])
            : null;

        $rataRata = $nilaiMurids->count() > 0
            ? round($nilaiMurids->avg('nilai_ujian'), 2)
            : null;

        $ringkasan = [
            'total_nilai' => $nilaiMurids->count(),
            'total_mapel' => $nilaiMurids->pluck('mata_pelajaran_id')->unique()->count(),
            'nilai_terendah' => $nilaiMurids->count() > 0 ? $nilaiMurids->min('nilai_ujian') : null,
            'nilai_tertinggi' => $nilaiMurids->count() > 0 ? $nilaiMurids->max('nilai_ujian') : null,
            'rata_rata' => $rataRata,
            'predikat' => $rataRata === null ? '-' : $this->predikatDariNilai($rataRata),
            'keterangan_predikat' => $rataRata === null ? '-' : $this->keteranganPredikatDariNilai($rataRata),
        ];

        $routePrefix = $this->routePrefix();

        return view('admin.pages.nilai.murid', compact(
            'validated',
            'murid',
            'nilaiMurids',
            'tahunAjaranDipilih',
            'semesterDipilih',
            'ringkasan',
            'routePrefix'
        ));
    }

    private function baseQuery(array $validated)
    {
        $query = Nilai::with([
            'murid.waliMurid',
            'kelas.waliKelas',
            'mataPelajaran',
            'tahunAjaran',
            'semester.tahunAjaran',
            'pegawai',
        ]);

        if (! empty($validated['tahun_ajaran_id'])) {
            $query->where('tahun_ajaran_id', $validated['tahun_ajaran_id']);
        }

        if (! empty($validated['semester_id'])) {
            $query->where('semester_id', $validated['semester_id']);
        }

        if (! empty($validated['kelas_id'])) {
            $query->where('kelas_id', $validated['kelas_id']);
        }

        if (! empty($validated['mata_pelajaran_id'])) {
            $query->where('mata_pelajaran_id', $validated['mata_pelajaran_id']);
        }

        return $query;
    }

    private function validateFilter(Request $request): array
    {
        return $request->validate([
            'tahun_ajaran_id' => ['nullable', 'integer', 'exists:tahun_ajarans,id'],
            'semester_id' => ['nullable', 'integer', 'exists:semesters,id'],
            'kelas_id' => ['nullable', 'integer', 'exists:kelas,id'],
            'mata_pelajaran_id' => ['nullable', 'integer', 'exists:mata_pelajarans,id'],
        ], $this->validationMessages());
    }

    private function validationMessages(): array
    {
        return [
            'tahun_ajaran_id.integer' => 'Tahun ajaran tidak valid.',
            'tahun_ajaran_id.exists' => 'Tahun ajaran yang dipilih tidak ditemukan.',

            'semester_id.integer' => 'Semester tidak valid.',
            'semester_id.exists' => 'Semester yang dipilih tidak ditemukan.',

            'kelas_id.integer' => 'Kelas tidak valid.',
            'kelas_id.exists' => 'Kelas yang dipilih tidak ditemukan.',

            'mata_pelajaran_id.integer' => 'Mata pelajaran tidak valid.',
            'mata_pelajaran_id.exists' => 'Mata pelajaran yang dipilih tidak ditemukan.',
        ];
    }

    private function statusNilai(int $totalNilai): string
    {
        if ($totalNilai <= 0) {
            return 'Belum Ada';
        }

        return 'Tersedia';
    }

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
