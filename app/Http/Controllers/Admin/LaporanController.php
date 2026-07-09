<?php

// penjelasan: Controller ini mengatur modul Laporan untuk Admin dan Super Admin.
// penjelasan: Halaman laporan dibuat bertingkat sesuai alur:
// penjelasan: 1. Halaman awal memilih jenis laporan.
// penjelasan: 2. Absensi Guru menampilkan list guru.
// penjelasan: 3. Absensi Murid menampilkan kelas, lalu list murid.
// penjelasan: 4. Laporan Nilai menampilkan kelas per tingkat, lalu list murid.
// penjelasan: 5. Setelah guru/murid dipilih, user memilih periode tahun ajaran, semester, atau minggu.
// penjelasan: 6. Dari periode tersebut user bisa melihat preview, export CSV, atau cetak/simpan PDF.
// penjelasan: Untuk Laporan Nilai periode minggu memakai tanggal input nilai yaitu created_at.

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AbsensiMurid;
use App\Models\AbsensiPegawai;
use App\Models\Kelas;
use App\Models\Murid;
use App\Models\Nilai;
use App\Models\Pegawai;
use App\Models\Semester;
use App\Models\TahunAjaran;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LaporanController extends Controller
{
    private array $jenisLaporan = [
        'absensi_guru' => 'Absensi Guru',
        'absensi_murid' => 'Absensi Murid',
        'nilai' => 'Laporan Nilai',
    ];

    private function routePrefix(): string
    {
        return auth()->user()->role === 'super_admin' ? 'super-admin' : 'admin';
    }

    public function index()
    {
        $routePrefix = $this->routePrefix();
        $jenisLaporan = $this->jenisLaporan;

        $totalGuru = Pegawai::where('jenis_pegawai', 'guru')
            ->where('status', 'aktif')
            ->count();

        $totalKelas = Kelas::where('status', 'aktif')->count();

        $totalMurid = Murid::where('status', 'aktif')->count();

        return view('admin.pages.laporan.index', compact(
            'routePrefix',
            'jenisLaporan',
            'totalGuru',
            'totalKelas',
            'totalMurid'
        ));
    }

    public function daftar(string $jenis)
    {
        $this->pastikanJenisValid($jenis);

        $routePrefix = $this->routePrefix();
        $jenisLabel = $this->jenisLaporan[$jenis];
        $mode = 'daftar';

        if ($jenis === 'absensi_guru') {
            $pegawaiList = Pegawai::where('jenis_pegawai', 'guru')
                ->where('status', 'aktif')
                ->orderBy('nama_pegawai')
                ->get();

            $rekapCounts = AbsensiPegawai::whereIn('pegawai_id', $pegawaiList->pluck('id'))
                ->select('pegawai_id', DB::raw('COUNT(*) as total'))
                ->groupBy('pegawai_id')
                ->pluck('total', 'pegawai_id');

            return view('admin.pages.laporan.daftar', compact(
                'routePrefix',
                'jenis',
                'jenisLabel',
                'mode',
                'pegawaiList',
                'rekapCounts'
            ));
        }

        $kelasList = Kelas::withCount(['murids as total_murid' => function ($query) {
                $query->where('status', 'aktif');
            }])
            ->where('status', 'aktif')
            ->orderBy('tingkat')
            ->orderBy('nama_kelas')
            ->get();

        if ($jenis === 'absensi_murid') {
            $rekapCounts = AbsensiMurid::whereIn('kelas_id', $kelasList->pluck('id'))
                ->select('kelas_id', DB::raw('COUNT(*) as total'))
                ->groupBy('kelas_id')
                ->pluck('total', 'kelas_id');
        } else {
            $rekapCounts = Nilai::whereIn('kelas_id', $kelasList->pluck('id'))
                ->select('kelas_id', DB::raw('COUNT(*) as total'))
                ->groupBy('kelas_id')
                ->pluck('total', 'kelas_id');
        }

        $kelasPerTingkat = $kelasList->groupBy(fn ($kelas) => $kelas->tingkat ?: 'Lainnya');

        return view('admin.pages.laporan.daftar', compact(
            'routePrefix',
            'jenis',
            'jenisLabel',
            'mode',
            'kelasList',
            'kelasPerTingkat',
            'rekapCounts'
        ));
    }

    public function daftarMurid(string $jenis, Kelas $kelas)
    {
        $this->pastikanJenisValid($jenis);

        if (! in_array($jenis, ['absensi_murid', 'nilai'], true)) {
            abort(404);
        }

        $routePrefix = $this->routePrefix();
        $jenisLabel = $this->jenisLaporan[$jenis];
        $mode = 'murid';

        $kelas->load('waliKelas');

        $muridList = Murid::with('waliMurid')
            ->where('kelas_id', $kelas->id)
            ->where('status', 'aktif')
            ->orderBy('nama_murid')
            ->get();

        if ($jenis === 'absensi_murid') {
            $rekapCounts = AbsensiMurid::whereIn('murid_id', $muridList->pluck('id'))
                ->select('murid_id', DB::raw('COUNT(*) as total'))
                ->groupBy('murid_id')
                ->pluck('total', 'murid_id');
        } else {
            $rekapCounts = Nilai::whereIn('murid_id', $muridList->pluck('id'))
                ->select('murid_id', DB::raw('COUNT(*) as total'))
                ->groupBy('murid_id')
                ->pluck('total', 'murid_id');
        }

        return view('admin.pages.laporan.daftar', compact(
            'routePrefix',
            'jenis',
            'jenisLabel',
            'mode',
            'kelas',
            'muridList',
            'rekapCounts'
        ));
    }

    public function periode(string $jenis, string $targetType, int $targetId)
    {
        $this->pastikanJenisValid($jenis);

        $target = $this->resolveTarget($jenis, $targetType, $targetId);

        $routePrefix = $this->routePrefix();
        $jenisLabel = $this->jenisLaporan[$jenis];

        $tahunAjarans = TahunAjaran::orderByDesc('id')->get();

        $semesters = Semester::with('tahunAjaran')
            ->orderByDesc('id')
            ->get();

        $targetTitle = $this->targetTitle($jenis, $target);
        $backUrl = $this->backUrlUntukTarget($routePrefix, $jenis, $target);

        return view('admin.pages.laporan.periode', compact(
            'routePrefix',
            'jenis',
            'jenisLabel',
            'targetType',
            'targetId',
            'target',
            'targetTitle',
            'tahunAjarans',
            'semesters',
            'backUrl'
        ));
    }

    public function preview(Request $request, string $jenis, string $targetType, int $targetId)
    {
        $this->pastikanJenisValid($jenis);

        $this->validatePeriode($request);

        $target = $this->resolveTarget($jenis, $targetType, $targetId);

        $report = $this->buildReport($request, $jenis, $targetType, $target, true);

        $routePrefix = $this->routePrefix();
        $jenisLabel = $this->jenisLaporan[$jenis];
        $targetTitle = $this->targetTitle($jenis, $target);

        return view('admin.pages.laporan.preview', compact(
            'routePrefix',
            'jenis',
            'jenisLabel',
            'targetType',
            'targetId',
            'target',
            'targetTitle',
            'report'
        ));
    }

    public function exportCsv(Request $request, string $jenis, string $targetType, int $targetId): StreamedResponse
    {
        $this->pastikanJenisValid($jenis);

        $this->validatePeriode($request);

        $target = $this->resolveTarget($jenis, $targetType, $targetId);

        $report = $this->buildReport($request, $jenis, $targetType, $target, false);

        $filename = str($report['title'])
            ->lower()
            ->replace(' ', '_')
            ->replace('/', '_')
            ->append('_' . now()->format('Ymd_His') . '.csv')
            ->toString();

        return response()->streamDownload(function () use ($report) {
            echo "\xEF\xBB\xBF";

            $handle = fopen('php://output', 'w');

            fputcsv($handle, $report['headers'], ';');

            foreach ($report['rows'] as $row) {
                fputcsv($handle, $row, ';');
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function cetak(Request $request, string $jenis, string $targetType, int $targetId)
    {
        $this->pastikanJenisValid($jenis);

        $this->validatePeriode($request);

        $target = $this->resolveTarget($jenis, $targetType, $targetId);

        $report = $this->buildReport($request, $jenis, $targetType, $target, false);

        $routePrefix = $this->routePrefix();
        $jenisLabel = $this->jenisLaporan[$jenis];
        $targetTitle = $this->targetTitle($jenis, $target);

        return view('admin.pages.laporan.cetak', compact(
            'routePrefix',
            'jenis',
            'jenisLabel',
            'targetType',
            'targetId',
            'target',
            'targetTitle',
            'report'
        ));
    }

    private function buildReport(Request $request, string $jenis, string $targetType, Pegawai|Murid $target, bool $paginate): array
    {
        return match ($jenis) {
            'absensi_guru' => $this->buildAbsensiGuruReport($request, $target, $paginate),
            'absensi_murid' => $this->buildAbsensiMuridReport($request, $target, $paginate),
            'nilai' => $this->buildNilaiReport($request, $target, $paginate),
        };
    }

    private function buildAbsensiGuruReport(Request $request, Pegawai $guru, bool $paginate): array
    {
        $periode = $this->periodeContext($request, 'absensi_guru');

        $query = AbsensiPegawai::with('pegawai')
            ->where('pegawai_id', $guru->id);

        $this->applyTanggalPeriode($query, $periode, 'tanggal_absen');

        $totalData = (clone $query)->count();

        $statusCounts = (clone $query)
            ->select('status_absen', DB::raw('COUNT(*) as total'))
            ->groupBy('status_absen')
            ->pluck('total', 'status_absen');

        $query->orderByDesc('tanggal_absen');

        $data = $paginate
            ? $query->paginate(15)->withQueryString()
            : $query->get();

        $items = $paginate ? $data->getCollection() : $data;

        $headers = [
            'Tanggal',
            'Nama Guru',
            'Jabatan',
            'Status',
            'Jam Masuk',
            'Jam Pulang',
            'Metode',
            'Keterangan',
        ];

        $rows = $items->map(function ($absensi) {
            return [
                $absensi->tanggal_absen ? $absensi->tanggal_absen->format('d-m-Y') : '-',
                $absensi->pegawai?->nama_pegawai ?? '-',
                $absensi->pegawai?->jabatan ?? '-',
                $absensi->status_absen_label,
                $absensi->jam_masuk_format,
                $absensi->jam_pulang_format,
                $absensi->metode_absen_label,
                $absensi->keterangan ?? '-',
            ];
        })->values();

        return [
            'title' => 'Laporan Absensi Guru',
            'description' => 'Laporan absensi guru berdasarkan periode yang dipilih.',
            'target_label' => $guru->nama_pegawai,
            'periode' => $periode,
            'headers' => $headers,
            'rows' => $rows,
            'data' => $data,
            'summary' => [
                ['label' => 'Total Data', 'value' => $totalData],
                ['label' => 'Hadir', 'value' => $statusCounts['hadir'] ?? 0],
                ['label' => 'Terlambat', 'value' => $statusCounts['terlambat'] ?? 0],
                ['label' => 'Dinas', 'value' => $statusCounts['dinas'] ?? 0],
                ['label' => 'Izin', 'value' => $statusCounts['izin'] ?? 0],
                ['label' => 'Sakit', 'value' => $statusCounts['sakit'] ?? 0],
                ['label' => 'Alpha', 'value' => $statusCounts['alpha'] ?? 0],
            ],
        ];
    }

    private function buildAbsensiMuridReport(Request $request, Murid $murid, bool $paginate): array
    {
        $periode = $this->periodeContext($request, 'absensi_murid');

        $query = AbsensiMurid::with([
                'murid.kelas',
                'kelas',
                'mataPelajaran',
                'guru',
                'jadwalPelajaran',
            ])
            ->where('murid_id', $murid->id);

        $this->applyTanggalPeriode($query, $periode, 'tanggal_absen');

        $totalData = (clone $query)->count();

        $statusCounts = (clone $query)
            ->select('status_absen', DB::raw('COUNT(*) as total'))
            ->groupBy('status_absen')
            ->pluck('total', 'status_absen');

        $query->orderByDesc('tanggal_absen')
            ->orderBy('mata_pelajaran_id');

        $data = $paginate
            ? $query->paginate(15)->withQueryString()
            : $query->get();

        $items = $paginate ? $data->getCollection() : $data;

        $headers = [
            'Tanggal',
            'Kelas',
            'Nama Murid',
            'Mata Pelajaran',
            'Guru',
            'Status',
            'Keterangan',
        ];

        $rows = $items->map(function ($absensi) {
            return [
                $absensi->tanggal_absen ? $absensi->tanggal_absen->format('d-m-Y') : '-',
                $absensi->kelas?->nama_kelas ?? '-',
                $absensi->murid?->nama_murid ?? '-',
                $absensi->mataPelajaran?->nama_mapel ?? '-',
                $absensi->guru?->nama_pegawai ?? '-',
                $absensi->status_absen_label,
                $absensi->keterangan ?? '-',
            ];
        })->values();

        return [
            'title' => 'Laporan Absensi Murid',
            'description' => 'Laporan absensi murid berdasarkan periode yang dipilih.',
            'target_label' => $murid->nama_murid,
            'periode' => $periode,
            'headers' => $headers,
            'rows' => $rows,
            'data' => $data,
            'summary' => [
                ['label' => 'Total Data', 'value' => $totalData],
                ['label' => 'Hadir', 'value' => $statusCounts['hadir'] ?? 0],
                ['label' => 'Izin', 'value' => $statusCounts['izin'] ?? 0],
                ['label' => 'Sakit', 'value' => $statusCounts['sakit'] ?? 0],
                ['label' => 'Alpha', 'value' => ($statusCounts['alpha'] ?? 0) + ($statusCounts['alpa'] ?? 0)],
                ['label' => 'Terlambat', 'value' => $statusCounts['terlambat'] ?? 0],
            ],
        ];
    }

    private function buildNilaiReport(Request $request, Murid $murid, bool $paginate): array
    {
        $periode = $this->periodeContext($request, 'nilai');

        $query = Nilai::with([
                'murid.kelas',
                'kelas',
                'mataPelajaran',
                'tahunAjaran',
                'semester.tahunAjaran',
                'pegawai',
            ])
            ->where('murid_id', $murid->id);

        if ($periode['tipe'] === 'tahun_ajaran') {
            $query->where('tahun_ajaran_id', $request->tahun_ajaran_id);
        }

        if ($periode['tipe'] === 'semester') {
            $query->where('semester_id', $request->semester_id);
        }

        if ($periode['tipe'] === 'minggu') {
            $this->applyTanggalPeriode($query, $periode, 'created_at');
        }

        $totalData = (clone $query)->count();
        $totalMapel = (clone $query)->distinct('mata_pelajaran_id')->count('mata_pelajaran_id');

        $nilaiTerendah = $totalData > 0 ? (clone $query)->min('nilai_ujian') : null;
        $nilaiTertinggi = $totalData > 0 ? (clone $query)->max('nilai_ujian') : null;
        $rataRata = $totalData > 0 ? round((clone $query)->avg('nilai_ujian'), 2) : null;

        $query->orderBy('mata_pelajaran_id');

        $data = $paginate
            ? $query->paginate(15)->withQueryString()
            : $query->get();

        $items = $paginate ? $data->getCollection() : $data;

        $headers = [
            'Kelas',
            'Nama Murid',
            'Mata Pelajaran',
            'Tahun Ajaran',
            'Semester',
            'Nilai',
            'Predikat',
            'Keterangan',
            'Guru Input',
            'Tanggal Input',
        ];

        $rows = $items->map(function ($nilai) {
            return [
                $nilai->kelas?->nama_kelas ?? '-',
                $nilai->murid?->nama_murid ?? '-',
                $nilai->mataPelajaran?->nama_mapel ?? '-',
                $nilai->tahunAjaran?->nama_tahun_ajaran ?? $nilai->tahunAjaran?->tahun_ajaran ?? '-',
                $nilai->semester?->nama_semester_label ?? $nilai->semester?->nama_semester ?? '-',
                $nilai->nilai_ujian,
                $nilai->predikat . ' - ' . $nilai->keterangan_predikat,
                $nilai->keterangan ?? '-',
                $nilai->pegawai?->nama_pegawai ?? '-',
                $nilai->created_at ? $nilai->created_at->format('d-m-Y H:i') : '-',
            ];
        })->values();

        return [
            'title' => 'Laporan Nilai',
            'description' => 'Laporan nilai ujian semester berdasarkan periode yang dipilih.',
            'target_label' => $murid->nama_murid,
            'periode' => $periode,
            'headers' => $headers,
            'rows' => $rows,
            'data' => $data,
            'summary' => [
                ['label' => 'Total Nilai', 'value' => $totalData],
                ['label' => 'Total Mapel', 'value' => $totalMapel],
                ['label' => 'Terendah', 'value' => $nilaiTerendah ?? '-'],
                ['label' => 'Tertinggi', 'value' => $nilaiTertinggi ?? '-'],
                ['label' => 'Rata-rata', 'value' => $rataRata ?? '-'],
                ['label' => 'Predikat', 'value' => $rataRata === null ? '-' : $this->predikatDariNilai($rataRata)],
            ],
        ];
    }

    private function validatePeriode(Request $request): void
    {
        $request->validate([
            'periode' => ['required', 'string', 'in:tahun_ajaran,semester,minggu'],
            'tahun_ajaran_id' => ['required_if:periode,tahun_ajaran', 'nullable', 'integer', 'exists:tahun_ajarans,id'],
            'semester_id' => ['required_if:periode,semester', 'nullable', 'integer', 'exists:semesters,id'],
            'tanggal_mulai' => ['required_if:periode,minggu', 'nullable', 'date'],
        ], [
            'periode.required' => 'Jenis periode wajib dipilih.',
            'periode.in' => 'Jenis periode tidak valid.',

            'tahun_ajaran_id.required_if' => 'Tahun ajaran wajib dipilih.',
            'tahun_ajaran_id.exists' => 'Tahun ajaran yang dipilih tidak ditemukan.',

            'semester_id.required_if' => 'Semester wajib dipilih.',
            'semester_id.exists' => 'Semester yang dipilih tidak ditemukan.',

            'tanggal_mulai.required_if' => 'Tanggal awal minggu wajib diisi.',
            'tanggal_mulai.date' => 'Tanggal awal minggu tidak valid.',
        ]);
    }

    private function periodeContext(Request $request, string $jenis): array
    {
        if ($request->periode === 'tahun_ajaran') {
            $tahunAjaran = TahunAjaran::findOrFail($request->tahun_ajaran_id);

            return [
                'tipe' => 'tahun_ajaran',
                'label' => 'Satu Tahun Ajaran',
                'nama' => $tahunAjaran->nama_tahun_ajaran ?? $tahunAjaran->tahun_ajaran ?? '-',
                'mulai' => $tahunAjaran->tanggal_mulai,
                'selesai' => $tahunAjaran->tanggal_selesai,
            ];
        }

        if ($request->periode === 'semester') {
            $semester = Semester::with('tahunAjaran')->findOrFail($request->semester_id);

            return [
                'tipe' => 'semester',
                'label' => 'Satu Semester',
                'nama' => ($semester->nama_semester_label ?? $semester->nama_semester ?? '-')
                    . ' - '
                    . ($semester->tahunAjaran?->nama_tahun_ajaran ?? $semester->tahunAjaran?->tahun_ajaran ?? '-'),
                'mulai' => $semester->tanggal_mulai,
                'selesai' => $semester->tanggal_selesai,
            ];
        }

        $tanggalMulai = Carbon::parse($request->tanggal_mulai)->startOfDay();
        $tanggalSelesai = $tanggalMulai->copy()->addDays(6)->endOfDay();

        return [
            'tipe' => 'minggu',
            'label' => 'Satu Minggu',
            'nama' => $tanggalMulai->format('d-m-Y') . ' s/d ' . $tanggalSelesai->format('d-m-Y'),
            'mulai' => $tanggalMulai,
            'selesai' => $tanggalSelesai,
        ];
    }

    private function applyTanggalPeriode($query, array $periode, string $kolom): void
    {
        if (! empty($periode['mulai']) && ! empty($periode['selesai'])) {
            if ($kolom === 'created_at') {
                $query->whereBetween($kolom, [
                    Carbon::parse($periode['mulai'])->startOfDay(),
                    Carbon::parse($periode['selesai'])->endOfDay(),
                ]);

                return;
            }

            $query->whereDate($kolom, '>=', Carbon::parse($periode['mulai'])->toDateString())
                ->whereDate($kolom, '<=', Carbon::parse($periode['selesai'])->toDateString());
        }
    }

    private function pastikanJenisValid(string $jenis): void
    {
        if (! array_key_exists($jenis, $this->jenisLaporan)) {
            abort(404, 'Jenis laporan tidak ditemukan.');
        }
    }

    private function resolveTarget(string $jenis, string $targetType, int $targetId): Pegawai|Murid
    {
        if ($jenis === 'absensi_guru' && $targetType === 'guru') {
            return Pegawai::where('jenis_pegawai', 'guru')
                ->findOrFail($targetId);
        }

        if (in_array($jenis, ['absensi_murid', 'nilai'], true) && $targetType === 'murid') {
            return Murid::with(['kelas.waliKelas', 'waliMurid'])
                ->findOrFail($targetId);
        }

        abort(404, 'Target laporan tidak valid.');
    }

    private function targetTitle(string $jenis, Pegawai|Murid $target): string
    {
        if ($jenis === 'absensi_guru') {
            return $target->nama_pegawai;
        }

        return $target->nama_murid;
    }

    private function backUrlUntukTarget(string $routePrefix, string $jenis, Pegawai|Murid $target): string
    {
        if ($jenis === 'absensi_guru') {
            return route($routePrefix . '.laporan.daftar', $jenis);
        }

        return route($routePrefix . '.laporan.daftar-murid', [
            'jenis' => $jenis,
            'kelas' => $target->kelas_id,
        ]);
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
}
