<?php

// penjelasan: Controller ini digunakan oleh admin dan super admin.
// penjelasan: Controller ini menangani rekap absensi pegawai.
// penjelasan: Data absensi berasal dari guru/staff yang melakukan Absen Saya, pengajuan disetujui, atau generate alpha.
// penjelasan: Controller ini menyediakan daftar rekap, detail rekap, filter, ringkasan status, dan generate alpha pegawai.
// penjelasan: Generate alpha digunakan untuk membuat status alpha bagi pegawai aktif yang tidak memiliki absensi pada tanggal tertentu.

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
// penjelasan: Controller adalah class dasar Laravel untuk membuat controller.

use App\Models\AbsensiPegawai;
// penjelasan: Model AbsensiPegawai digunakan untuk membaca dan membuat data absensi pegawai.

use App\Models\Pegawai;
// penjelasan: Model Pegawai digunakan untuk mengambil data guru/staff aktif.

use Illuminate\Http\Request;
// penjelasan: Request digunakan untuk mengambil data filter dari form.

use Illuminate\Support\Facades\DB;
// penjelasan: DB digunakan untuk transaksi database saat generate alpha.

use Illuminate\Validation\Rule;
// penjelasan: Rule digunakan untuk validasi pilihan status dan metode absensi.

class RekapAbsensiPegawaiController extends Controller
{
    private function routePrefix(): string
    {
        return auth()->user()->role === 'super_admin' ? 'super-admin' : 'admin';
    }

    public function index(Request $request)
    {
        $query = AbsensiPegawai::with([
            'pegawai.user',
            'pengajuanAbsensiPegawai',
        ])->whereHas('pegawai', function ($q) {
            $q->whereIn('jenis_pegawai', ['guru', 'staff']);
        });

        if ($request->filled('tanggal_mulai')) {
            $query->whereDate('tanggal_absen', '>=', $request->tanggal_mulai);
        }

        if ($request->filled('tanggal_selesai')) {
            $query->whereDate('tanggal_absen', '<=', $request->tanggal_selesai);
        }

        if ($request->filled('pegawai_id')) {
            $query->where('pegawai_id', $request->pegawai_id);
        }

        if ($request->filled('status_absen')) {
            $query->where('status_absen', $request->status_absen);
        }

        if ($request->filled('metode_absen')) {
            $query->where('metode_absen', $request->metode_absen);
        }

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->whereHas('pegawai', function ($q) use ($search) {
                $q->where('nama_pegawai', 'like', '%' . $search . '%')
                    ->orWhere('nip', 'like', '%' . $search . '%')
                    ->orWhere('jabatan', 'like', '%' . $search . '%');
            });
        }

        $summaryQuery = clone $query;

        $statusCounts = $summaryQuery
            ->select('status_absen', DB::raw('COUNT(*) as total'))
            ->groupBy('status_absen')
            ->pluck('total', 'status_absen');

        $totalRekap = array_sum($statusCounts->toArray());

        $absensiPegawais = $query
            ->orderByDesc('tanggal_absen')
            ->orderByDesc('jam_masuk')
            ->paginate(10)
            ->withQueryString();

        $pegawais = Pegawai::whereIn('jenis_pegawai', ['guru', 'staff'])
            ->orderBy('nama_pegawai')
            ->get();

        $tanggalAlpha = $request->input('tanggal_alpha', now()->toDateString());

        $pegawaiSudahAbsenIds = AbsensiPegawai::whereDate('tanggal_absen', $tanggalAlpha)
            ->pluck('pegawai_id')
            ->toArray();

        $jumlahBelumAbsen = Pegawai::whereIn('jenis_pegawai', ['guru', 'staff'])
            ->where('status', 'aktif')
            ->whereNotIn('id', $pegawaiSudahAbsenIds)
            ->count();

        $routePrefix = $this->routePrefix();

        return view('admin.pages.rekap-absensi-pegawai.index', compact(
            'absensiPegawais',
            'pegawais',
            'statusCounts',
            'totalRekap',
            'tanggalAlpha',
            'jumlahBelumAbsen',
            'routePrefix'
        ));
    }

    public function show(AbsensiPegawai $absensiPegawai)
    {
        $absensiPegawai->load([
            'pegawai.user',
            'pengajuanAbsensiPegawai',
        ]);

        $routePrefix = $this->routePrefix();

        return view('admin.pages.rekap-absensi-pegawai.show', compact(
            'absensiPegawai',
            'routePrefix'
        ));
    }

    public function generateAlpha(Request $request)
    {
        $validated = $request->validate([
            'tanggal_alpha' => ['required', 'date', 'before_or_equal:today'],
        ], [
            'tanggal_alpha.required' => 'Tanggal alpha wajib diisi.',
            'tanggal_alpha.date' => 'Tanggal alpha tidak valid.',
            'tanggal_alpha.before_or_equal' => 'Tanggal alpha tidak boleh lebih dari hari ini.',
        ]);

        $tanggalAlpha = $validated['tanggal_alpha'];

        $jumlahDibuat = 0;

        DB::transaction(function () use ($tanggalAlpha, &$jumlahDibuat) {
            $pegawaiSudahAbsenIds = AbsensiPegawai::whereDate('tanggal_absen', $tanggalAlpha)
                ->pluck('pegawai_id')
                ->toArray();

            $pegawaisBelumAbsen = Pegawai::whereIn('jenis_pegawai', ['guru', 'staff'])
                ->where('status', 'aktif')
                ->whereNotIn('id', $pegawaiSudahAbsenIds)
                ->get();

            foreach ($pegawaisBelumAbsen as $pegawai) {
                AbsensiPegawai::create([
                    'pegawai_id' => $pegawai->id,
                    'tanggal_absen' => $tanggalAlpha,
                    'jam_masuk' => null,
                    'jam_pulang' => null,
                    'status_absen' => 'alpha',
                    'metode_absen' => 'manual',
                    'latitude' => null,
                    'longitude' => null,
                    'keterangan' => 'Status alpha dibuat otomatis dari rekap admin karena pegawai tidak memiliki absensi atau pengajuan yang disetujui pada tanggal ini.',
                    'pengajuan_absensi_pegawai_id' => null,
                ]);

                $jumlahDibuat++;
            }
        });

        if ($jumlahDibuat < 1) {
            return back()->with('error', 'Tidak ada data alpha yang dibuat. Semua pegawai aktif sudah memiliki absensi pada tanggal tersebut.');
        }

        return back()->with('success', $jumlahDibuat . ' data alpha berhasil dibuat.');
    }
}
