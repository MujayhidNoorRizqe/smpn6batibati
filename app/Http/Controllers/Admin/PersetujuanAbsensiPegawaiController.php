<?php

// penjelasan: Controller ini digunakan oleh Admin dan Super Admin.
// penjelasan: Admin/Super Admin dapat melihat detail pengajuan, membuka bukti, menyetujui, atau menolak.
// penjelasan: Jika pengajuan disetujui, sistem membuat data absensi resmi di tabel absensi_pegawais.

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AbsensiPegawai;
use App\Models\PengajuanAbsensiPegawai;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PersetujuanAbsensiPegawaiController extends Controller
{
    private function routePrefix(): string
    {
        return auth()->user()->role === 'super_admin' ? 'super-admin' : 'admin';
    }

    public function index(Request $request)
    {
        $query = PengajuanAbsensiPegawai::with(['pegawai', 'disetujuiOleh']);

        if ($request->filled('jenis_pengajuan')) {
            $query->where('jenis_pengajuan', $request->jenis_pengajuan);
        }

        if ($request->filled('status_pengajuan')) {
            $query->where('status_pengajuan', $request->status_pengajuan);
        }

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->whereHas('pegawai', function ($q) use ($search) {
                $q->where('nama_pegawai', 'like', '%' . $search . '%')
                    ->orWhere('nip', 'like', '%' . $search . '%');
            });
        }

        $pengajuans = $query->latest()->paginate(10)->withQueryString();

        $routePrefix = $this->routePrefix();

        return view('admin.pages.persetujuan-absensi-pegawai.index', compact('pengajuans', 'routePrefix'));
    }

    public function show(PengajuanAbsensiPegawai $pengajuanAbsensiPegawai)
    {
        $pengajuanAbsensiPegawai->load(['pegawai', 'disetujuiOleh', 'absensiPegawais']);

        $routePrefix = $this->routePrefix();

        return view('admin.pages.persetujuan-absensi-pegawai.show', compact('pengajuanAbsensiPegawai', 'routePrefix'));
    }

    public function approve(Request $request, PengajuanAbsensiPegawai $pengajuanAbsensiPegawai)
    {
        $validated = $request->validate([
            'catatan_admin' => ['nullable', 'string'],
        ], [
            'catatan_admin.string' => 'Catatan admin harus berupa teks.',
        ]);

        $this->ensureMenunggu($pengajuanAbsensiPegawai);
        $this->validateApprovalRequirements($pengajuanAbsensiPegawai);

        DB::transaction(function () use ($pengajuanAbsensiPegawai, $validated) {
            $pengajuanAbsensiPegawai->update([
                'status_pengajuan' => 'disetujui',
                'disetujui_oleh' => auth()->id(),
                'disetujui_pada' => now(),
                'catatan_admin' => $validated['catatan_admin'] ?? null,
            ]);

            $period = CarbonPeriod::create(
                $pengajuanAbsensiPegawai->tanggal_mulai,
                $pengajuanAbsensiPegawai->tanggal_selesai
            );

            foreach ($period as $tanggal) {
                AbsensiPegawai::updateOrCreate(
                    [
                        'pegawai_id' => $pengajuanAbsensiPegawai->pegawai_id,
                        'tanggal_absen' => $tanggal->format('Y-m-d'),
                    ],
                    [
                        'status_absen' => $pengajuanAbsensiPegawai->jenis_pengajuan,
                        'metode_absen' => 'pengajuan',
                        'keterangan' => $pengajuanAbsensiPegawai->alasan,
                        'pengajuan_absensi_pegawai_id' => $pengajuanAbsensiPegawai->id,
                    ]
                );
            }
        });

        return redirect()
            ->route($this->routePrefix() . '.persetujuan-absensi-pegawai.index')
            ->with('success', 'Pengajuan berhasil disetujui dan absensi resmi berhasil dibuat.');
    }

    public function reject(Request $request, PengajuanAbsensiPegawai $pengajuanAbsensiPegawai)
    {
        $validated = $request->validate([
            'catatan_admin' => ['required', 'string'],
        ], [
            'catatan_admin.required' => 'Catatan penolakan wajib diisi.',
            'catatan_admin.string' => 'Catatan penolakan harus berupa teks.',
        ]);

        $this->ensureMenunggu($pengajuanAbsensiPegawai);

        $pengajuanAbsensiPegawai->update([
            'status_pengajuan' => 'ditolak',
            'disetujui_oleh' => auth()->id(),
            'disetujui_pada' => now(),
            'catatan_admin' => trim($validated['catatan_admin']),
        ]);

        return redirect()
            ->route($this->routePrefix() . '.persetujuan-absensi-pegawai.index')
            ->with('success', 'Pengajuan berhasil ditolak.');
    }

    private function ensureMenunggu(PengajuanAbsensiPegawai $pengajuan): void
    {
        if (! $pengajuan->isMenunggu()) {
            throw ValidationException::withMessages([
                'pengajuan' => 'Pengajuan ini sudah diproses.',
            ]);
        }
    }

    private function validateApprovalRequirements(PengajuanAbsensiPegawai $pengajuan): void
    {
        if (in_array($pengajuan->jenis_pengajuan, ['dinas', 'sakit'], true) && ! $pengajuan->hasBuktiFile()) {
            throw ValidationException::withMessages([
                'bukti_file' => 'Pengajuan dinas atau sakit tidak dapat disetujui karena belum memiliki bukti.',
            ]);
        }

        if ($pengajuan->jenis_pengajuan === 'dinas') {
            if (! $pengajuan->judul_pengajuan) {
                throw ValidationException::withMessages([
                    'judul_pengajuan' => 'Pengajuan dinas tidak dapat disetujui karena judul kegiatan belum diisi.',
                ]);
            }

            if (! $pengajuan->lokasi_kegiatan) {
                throw ValidationException::withMessages([
                    'lokasi_kegiatan' => 'Pengajuan dinas tidak dapat disetujui karena lokasi kegiatan belum diisi.',
                ]);
            }
        }
    }
}
