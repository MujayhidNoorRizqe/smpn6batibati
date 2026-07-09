<?php

// penjelasan: Controller ini digunakan oleh admin dan super admin.
// penjelasan: Controller ini menangani rekap absensi pegawai/guru.
// penjelasan: Data absensi berasal dari guru yang melakukan Absen Saya, pengajuan disetujui, atau generate alpha.
// penjelasan: Halaman awal tidak langsung menampilkan daftar rekap absensi.
// penjelasan: Data rekap absensi baru muncul setelah admin/super admin menggunakan filter.
// penjelasan: Generate alpha digunakan untuk membuat status alpha bagi guru aktif yang tidak memiliki absensi pada tanggal tertentu.
// penjelasan: Role staff sudah tidak digunakan, sehingga data yang dipakai hanya jenis pegawai guru.

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AbsensiPegawai;
use App\Models\Pegawai;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class RekapAbsensiPegawaiController extends Controller
{
    private function routePrefix(): string
    {
        return auth()->user()->role === 'super_admin' ? 'super-admin' : 'admin';
    }

    public function index(Request $request)
    {
        $validated = $request->validate([
            'tanggal_mulai' => ['nullable', 'date'],
            'tanggal_selesai' => ['nullable', 'date', 'after_or_equal:tanggal_mulai'],
            'pegawai_id' => ['nullable', 'integer', 'exists:pegawais,id'],
            'status_absen' => ['nullable', Rule::in(['hadir', 'terlambat', 'dinas', 'izin', 'sakit', 'alpha'])],
            'metode_absen' => ['nullable', Rule::in(['lokasi', 'wifi', 'pengajuan', 'manual'])],
            'search' => ['nullable', 'string', 'max:150'],
            'tanggal_alpha' => ['nullable', 'date'],
        ], $this->validationMessages());

        $hasFilter = $request->filled('tanggal_mulai')
            || $request->filled('tanggal_selesai')
            || $request->filled('pegawai_id')
            || $request->filled('status_absen')
            || $request->filled('metode_absen')
            || $request->filled('search');

        $pegawais = Pegawai::where('jenis_pegawai', 'guru')
            ->where('status', 'aktif')
            ->orderBy('nama_pegawai')
            ->get();

        $routePrefix = $this->routePrefix();

        if (! $hasFilter) {
            $absensiPegawais = $this->emptyPaginator($request);
            $statusCounts = collect();
            $totalRekap = 0;
        } else {
            $query = $this->baseQuery();

            $this->applyFilters($query, $validated);

            $statusCounts = (clone $query)
                ->select('status_absen', DB::raw('COUNT(*) as total'))
                ->groupBy('status_absen')
                ->pluck('total', 'status_absen');

            $totalRekap = (clone $query)->count();

            $absensiPegawais = $query
                ->orderByDesc('tanggal_absen')
                ->orderByDesc('jam_masuk')
                ->paginate(10)
                ->withQueryString();
        }

        $tanggalAlpha = $request->input('tanggal_alpha', now()->toDateString());

        $pegawaiSudahAbsenIds = AbsensiPegawai::whereDate('tanggal_absen', $tanggalAlpha)
            ->pluck('pegawai_id')
            ->toArray();

        $jumlahBelumAbsen = Pegawai::where('jenis_pegawai', 'guru')
            ->where('status', 'aktif')
            ->whereNotIn('id', $pegawaiSudahAbsenIds)
            ->count();

        return view('admin.pages.rekap-absensi-pegawai.index', compact(
            'validated',
            'hasFilter',
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

            $pegawaisBelumAbsen = Pegawai::where('jenis_pegawai', 'guru')
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
                    'keterangan' => 'Status alpha dibuat otomatis dari rekap admin karena guru tidak memiliki absensi atau pengajuan yang disetujui pada tanggal ini.',
                    'pengajuan_absensi_pegawai_id' => null,
                ]);

                $jumlahDibuat++;
            }
        });

        if ($jumlahDibuat < 1) {
            return back()->with('error', 'Tidak ada data alpha yang dibuat. Semua guru aktif sudah memiliki absensi pada tanggal tersebut.');
        }

        return back()->with('success', $jumlahDibuat . ' data alpha berhasil dibuat.');
    }

    private function baseQuery()
    {
        return AbsensiPegawai::with([
                'pegawai.user',
                'pengajuanAbsensiPegawai',
            ])
            ->whereHas('pegawai', function ($q) {
                $q->where('jenis_pegawai', 'guru');
            });
    }

    private function applyFilters($query, array $validated): void
    {
        if (! empty($validated['tanggal_mulai'])) {
            $query->whereDate('tanggal_absen', '>=', $validated['tanggal_mulai']);
        }

        if (! empty($validated['tanggal_selesai'])) {
            $query->whereDate('tanggal_absen', '<=', $validated['tanggal_selesai']);
        }

        if (! empty($validated['pegawai_id'])) {
            $query->where('pegawai_id', $validated['pegawai_id']);
        }

        if (! empty($validated['status_absen'])) {
            $query->where('status_absen', $validated['status_absen']);
        }

        if (! empty($validated['metode_absen'])) {
            $query->where('metode_absen', $validated['metode_absen']);
        }

        if (! empty($validated['search'])) {
            $search = trim($validated['search']);

            $query->whereHas('pegawai', function ($q) use ($search) {
                $q->where('nama_pegawai', 'like', '%' . $search . '%')
                    ->orWhere('nip', 'like', '%' . $search . '%')
                    ->orWhere('jabatan', 'like', '%' . $search . '%');
            });
        }
    }

    private function emptyPaginator(Request $request): LengthAwarePaginator
    {
        return new LengthAwarePaginator(
            [],
            0,
            10,
            LengthAwarePaginator::resolveCurrentPage(),
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );
    }

    private function validationMessages(): array
    {
        return [
            'tanggal_mulai.date' => 'Tanggal mulai tidak valid.',
            'tanggal_selesai.date' => 'Tanggal selesai tidak valid.',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai tidak boleh lebih awal dari tanggal mulai.',

            'pegawai_id.integer' => 'Data pegawai tidak valid.',
            'pegawai_id.exists' => 'Pegawai yang dipilih tidak ditemukan.',

            'status_absen.in' => 'Status absen yang dipilih tidak valid.',
            'metode_absen.in' => 'Metode absen yang dipilih tidak valid.',

            'search.string' => 'Pencarian harus berupa teks.',
            'search.max' => 'Pencarian maksimal 150 karakter.',

            'tanggal_alpha.date' => 'Tanggal alpha tidak valid.',
        ];
    }
}
