<?php

// penjelasan: File ini adalah Controller Jadwal Mengajar untuk role Guru.
// penjelasan: Controller ini hanya menampilkan jadwal mengajar milik guru yang sedang login.
// penjelasan: Guru tidak bisa tambah, edit, atau menghapus jadwal dari halaman ini.
// penjelasan: Data jadwal diambil dari tabel jadwal_pelajarans berdasarkan guru_id.
// penjelasan: Halaman ini dipakai sebagai dasar untuk melihat jadwal, absen murid, input nilai, dan rekap nilai.

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\JadwalPelajaran;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Semester;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class JadwalMengajarController extends Controller
{
    /**
     * penjelasan: Fungsi ini mengambil data pegawai guru dari akun user login.
     * penjelasan: Jika user belum terhubung dengan data pegawai, akses ditolak.
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
     * penjelasan: Menampilkan halaman Jadwal Mengajar Guru.
     * penjelasan: Jadwal difilter berdasarkan tahun ajaran, semester, kelas, mata pelajaran, hari, dan status.
     * penjelasan: Data yang tampil hanya jadwal milik guru login.
     */
    public function index(Request $request): View
    {
        $guru = $this->guruLogin();

        $query = JadwalPelajaran::with([
                'tahunAjaran',
                'semester',
                'kelas',
                'mataPelajaran',
                'guru',
            ])
            ->where('guru_id', $guru->id);

        if ($request->filled('tahun_ajaran_id')) {
            $query->where('tahun_ajaran_id', $request->tahun_ajaran_id);
        }

        if ($request->filled('semester_id')) {
            $query->where('semester_id', $request->semester_id);
        }

        if ($request->filled('kelas_id')) {
            $query->where('kelas_id', $request->kelas_id);
        }

        if ($request->filled('mata_pelajaran_id')) {
            $query->where('mata_pelajaran_id', $request->mata_pelajaran_id);
        }

        if ($request->filled('hari')) {
            $query->where('hari', $request->hari);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $jadwalMengajars = $query
            ->orderByRaw("FIELD(hari, 'senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu')")
            ->orderBy('jam_mulai')
            ->paginate(10)
            ->withQueryString();

        $jadwalGuru = JadwalPelajaran::where('guru_id', $guru->id)->get();

        $tahunAjarans = TahunAjaran::whereIn('id', $jadwalGuru->pluck('tahun_ajaran_id')->unique())
            ->orderByDesc('id')
            ->get();

        $semesters = Semester::with('tahunAjaran')
            ->whereIn('id', $jadwalGuru->pluck('semester_id')->unique())
            ->orderByDesc('id')
            ->get();

        $kelasList = Kelas::whereIn('id', $jadwalGuru->pluck('kelas_id')->unique())
            ->orderBy('tingkat')
            ->orderBy('nama_kelas')
            ->get();

        $mataPelajarans = MataPelajaran::whereIn('id', $jadwalGuru->pluck('mata_pelajaran_id')->unique())
            ->orderBy('nama_mapel')
            ->get();

        $totalJadwal = JadwalPelajaran::where('guru_id', $guru->id)->count();

        $totalJadwalAktif = JadwalPelajaran::where('guru_id', $guru->id)
            ->where('status', 'aktif')
            ->count();

        $totalKelas = $jadwalGuru->pluck('kelas_id')->unique()->count();

        $totalMapel = $jadwalGuru->pluck('mata_pelajaran_id')->unique()->count();

        $statistik = [
            'total_jadwal' => $totalJadwal,
            'total_jadwal_aktif' => $totalJadwalAktif,
            'total_kelas' => $totalKelas,
            'total_mapel' => $totalMapel,
        ];

        return view('admin.pages.guru.jadwal-mengajar.index', compact(
            'guru',
            'jadwalMengajars',
            'tahunAjarans',
            'semesters',
            'kelasList',
            'mataPelajarans',
            'statistik'
        ));
    }
}
