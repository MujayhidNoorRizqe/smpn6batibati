<?php

// penjelasan: Controller ini digunakan oleh admin dan super admin.
// penjelasan: Controller ini menampilkan rekap absensi murid yang sudah diinput oleh guru.
// penjelasan: Admin dan super admin hanya melihat data, tidak bisa mengedit data absensi murid.
// penjelasan: Alur halaman dibuat sama seperti dashboard guru:
// penjelasan: 1. Halaman index menampilkan rekap per kelas.
// penjelasan: 2. Klik kelas menampilkan list murid per kelas.
// penjelasan: 3. Klik murid menampilkan riwayat absensi murid.
// penjelasan: Filter rekap absensi murid berdasarkan tanggal, kelas, mata pelajaran, dan guru.

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AbsensiMurid;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Murid;
use App\Models\Pegawai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RekapAbsensiMuridController extends Controller
{
    public function index(Request $request)
    {
        $validated = $this->validateFilter($request);

        $query = $this->baseQuery($validated);

        $statusCounts = (clone $query)
            ->select('status_absen', DB::raw('COUNT(*) as total'))
            ->groupBy('status_absen')
            ->pluck('total', 'status_absen');

        $totalData = (clone $query)->count();

        $semuaAbsensi = $query
            ->orderBy('tanggal_absen', 'desc')
            ->orderBy('kelas_id')
            ->get();

        $rekapPerKelas = $semuaAbsensi
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
                    'total_guru' => $items->pluck('guru_id')->unique()->count(),
                    'hadir' => $status['hadir'] ?? 0,
                    'izin' => $status['izin'] ?? 0,
                    'sakit' => $status['sakit'] ?? 0,
                    'alpha' => ($status['alpha'] ?? 0) + ($status['alpa'] ?? 0),
                    'terlambat' => $status['terlambat'] ?? 0,
                    'tanggal_terakhir' => $tanggalTerakhir,
                ];
            })
            ->sortBy(fn ($item) => $item->kelas?->tingkat . $item->kelas?->nama_kelas)
            ->values();

        $kelasList = Kelas::orderBy('tingkat')
            ->orderBy('nama_kelas')
            ->get();

        $mataPelajaranList = MataPelajaran::orderBy('nama_mapel')->get();

        $guruList = Pegawai::where('jenis_pegawai', 'guru')
            ->orderBy('nama_pegawai')
            ->get();

        $routePrefix = $this->routePrefix();

        return view('admin.pages.rekap-absensi-murid.index', compact(
            'validated',
            'statusCounts',
            'totalData',
            'rekapPerKelas',
            'kelasList',
            'mataPelajaranList',
            'guruList',
            'routePrefix'
        ));
    }

    public function kelas(Request $request, Kelas $kelas)
    {
        $validated = $this->validateFilter($request);

        $kelas->load('waliKelas');

        $query = $this->baseQuery($validated)
            ->where('kelas_id', $kelas->id);

        $statusCounts = (clone $query)
            ->select('status_absen', DB::raw('COUNT(*) as total'))
            ->groupBy('status_absen')
            ->pluck('total', 'status_absen');

        $totalData = (clone $query)->count();

        $absensiKelas = $query
            ->orderBy('tanggal_absen', 'desc')
            ->get();

        $murids = Murid::with('waliMurid')
            ->where('kelas_id', $kelas->id)
            ->orderBy('nama_murid')
            ->get();

        $absensiPerMurid = $absensiKelas->groupBy('murid_id');

        $rekapPerMurid = $murids
            ->map(function ($murid) use ($absensiPerMurid) {
                $items = $absensiPerMurid->get($murid->id, collect());
                $status = $items->groupBy('status_absen')->map->count();
                $tanggalTerakhir = $items->sortByDesc('tanggal_absen')->first()?->tanggal_absen;

                return (object) [
                    'murid' => $murid,
                    'total_data' => $items->count(),
                    'total_mapel' => $items->pluck('mata_pelajaran_id')->unique()->count(),
                    'total_guru' => $items->pluck('guru_id')->unique()->count(),
                    'hadir' => $status['hadir'] ?? 0,
                    'izin' => $status['izin'] ?? 0,
                    'sakit' => $status['sakit'] ?? 0,
                    'alpha' => ($status['alpha'] ?? 0) + ($status['alpa'] ?? 0),
                    'terlambat' => $status['terlambat'] ?? 0,
                    'tanggal_terakhir' => $tanggalTerakhir,
                ];
            })
            ->values();

        $routePrefix = $this->routePrefix();

        return view('admin.pages.rekap-absensi-murid.kelas', compact(
            'validated',
            'kelas',
            'statusCounts',
            'totalData',
            'rekapPerMurid',
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

        $query = $this->baseQuery($validated)
            ->where('murid_id', $murid->id);

        $statusCounts = (clone $query)
            ->select('status_absen', DB::raw('COUNT(*) as total'))
            ->groupBy('status_absen')
            ->pluck('total', 'status_absen');

        $totalData = (clone $query)->count();

        $absensiMurids = $query
            ->with([
                'murid.waliMurid',
                'kelas',
                'mataPelajaran',
                'guru',
                'jadwalPelajaran.tahunAjaran',
                'jadwalPelajaran.semester',
                'pembuat',
            ])
            ->orderBy('tanggal_absen', 'desc')
            ->orderBy('mata_pelajaran_id')
            ->paginate(15)
            ->withQueryString();

        $routePrefix = $this->routePrefix();

        return view('admin.pages.rekap-absensi-murid.murid', compact(
            'validated',
            'murid',
            'statusCounts',
            'totalData',
            'absensiMurids',
            'routePrefix'
        ));
    }

    private function baseQuery(array $validated)
    {
        $query = AbsensiMurid::with([
            'murid.waliMurid',
            'kelas',
            'mataPelajaran',
            'guru',
            'jadwalPelajaran',
            'pembuat',
        ]);

        if (! empty($validated['tanggal_mulai'])) {
            $query->whereDate('tanggal_absen', '>=', $validated['tanggal_mulai']);
        }

        if (! empty($validated['tanggal_selesai'])) {
            $query->whereDate('tanggal_absen', '<=', $validated['tanggal_selesai']);
        }

        if (! empty($validated['kelas_id'])) {
            $query->where('kelas_id', $validated['kelas_id']);
        }

        if (! empty($validated['mata_pelajaran_id'])) {
            $query->where('mata_pelajaran_id', $validated['mata_pelajaran_id']);
        }

        if (! empty($validated['guru_id'])) {
            $query->where('guru_id', $validated['guru_id']);
        }

        return $query;
    }

    private function validateFilter(Request $request): array
    {
        return $request->validate([
            'tanggal_mulai' => ['nullable', 'date'],
            'tanggal_selesai' => ['nullable', 'date', 'after_or_equal:tanggal_mulai'],
            'kelas_id' => ['nullable', 'integer', 'exists:kelas,id'],
            'mata_pelajaran_id' => ['nullable', 'integer', 'exists:mata_pelajarans,id'],
            'guru_id' => ['nullable', 'integer', 'exists:pegawais,id'],
        ], $this->validationMessages());
    }

    private function routePrefix(): string
    {
        return auth()->user()->role === 'super_admin' ? 'super-admin' : 'admin';
    }

    private function validationMessages(): array
    {
        return [
            'tanggal_mulai.date' => 'Tanggal mulai tidak valid.',
            'tanggal_selesai.date' => 'Tanggal selesai tidak valid.',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai tidak boleh lebih awal dari tanggal mulai.',

            'kelas_id.integer' => 'Data kelas tidak valid.',
            'kelas_id.exists' => 'Kelas yang dipilih tidak ditemukan.',

            'mata_pelajaran_id.integer' => 'Data mata pelajaran tidak valid.',
            'mata_pelajaran_id.exists' => 'Mata pelajaran yang dipilih tidak ditemukan.',

            'guru_id.integer' => 'Data guru tidak valid.',
            'guru_id.exists' => 'Guru yang dipilih tidak ditemukan.',
        ];
    }
}
