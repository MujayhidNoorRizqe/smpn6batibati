<?php

// penjelasan: Controller ini mengatur fitur WhatsApp Fonnte.
// penjelasan: Fitur ini mengambil nomor WhatsApp dari Data Wali Murid.
// penjelasan: Alur halaman dibuat wajib memilih kelas terlebih dahulu.
// penjelasan: Setelah kelas dipilih, baru tampil daftar wali murid yang memiliki murid aktif pada kelas tersebut.
// penjelasan: Admin/Super Admin memilih wali murid, memilih murid, lalu mengirim pesan absen murid dan/atau nilai ujian semester.
// penjelasan: Pengiriman memakai API Fonnte dengan token dari env FONNTE_TOKEN atau storage/app/whatsapp-fonnte-settings.json.
// penjelasan: Jika pesan terkirim, halaman menampilkan validasi berhasil. Jika gagal, halaman menampilkan validasi gagal.

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AbsensiMurid;
use App\Models\Kelas;
use App\Models\Murid;
use App\Models\Nilai;
use App\Models\WaliMurid;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppFonnteController extends Controller
{
    private string $settingPath = 'app/whatsapp-fonnte-settings.json';

    public function index(Request $request)
    {
        $keyword = trim((string) $request->input('keyword', ''));
        $kelasId = $request->input('kelas_id');
        $hasKelasFilter = $request->filled('kelas_id');

        $kelasList = Kelas::where('status', 'aktif')
            ->withCount([
                'murids as total_murid_aktif' => function ($query) {
                    $query->where('status', 'aktif');
                },
            ])
            ->orderBy('tingkat')
            ->orderBy('nama_kelas')
            ->get();

        $kelasPerTingkat = $kelasList->groupBy(fn ($kelas) => $kelas->tingkat ?: 'Lainnya');

        $kelasTerpilih = null;

        if ($hasKelasFilter) {
            $kelasTerpilih = $kelasList->firstWhere('id', (int) $kelasId);

            if (! $kelasTerpilih) {
                return redirect()
                    ->route($this->routePrefix() . '.whatsapp-fonnte.index')
                    ->with('error', 'Kelas yang dipilih tidak ditemukan atau sudah nonaktif.');
            }
        }

        if (! $hasKelasFilter) {
            $waliMurids = $this->emptyPaginator($request);
            $totalWaliWhatsapp = 0;
            $totalMuridTerhubung = 0;
        } else {
            $waliQuery = WaliMurid::with([
                    'murids' => function ($query) use ($kelasId) {
                        $query->with('kelas')
                            ->where('status', 'aktif')
                            ->where('kelas_id', $kelasId)
                            ->orderByRaw('LOWER(nama_murid) ASC')
                            ->orderBy('nama_murid');
                    },
                ])
                ->withCount([
                    'murids as murids_count' => function ($query) use ($kelasId) {
                        $query->where('status', 'aktif')
                            ->where('kelas_id', $kelasId);
                    },
                ])
                ->where('status', 'aktif')
                ->whereNotNull('no_whatsapp')
                ->where('no_whatsapp', '!=', '')
                ->whereHas('murids', function ($query) use ($kelasId) {
                    $query->where('status', 'aktif')
                        ->where('kelas_id', $kelasId);
                })
                ->when($keyword !== '', function ($query) use ($keyword, $kelasId) {
                    $query->where(function ($q) use ($keyword, $kelasId) {
                        $q->where('nama_wali', 'like', '%' . $keyword . '%')
                            ->orWhere('no_whatsapp', 'like', '%' . $keyword . '%')
                            ->orWhere('no_hp', 'like', '%' . $keyword . '%')
                            ->orWhereHas('murids', function ($muridQuery) use ($keyword, $kelasId) {
                                $muridQuery->where('status', 'aktif')
                                    ->where('kelas_id', $kelasId)
                                    ->where(function ($searchQuery) use ($keyword) {
                                        $searchQuery->where('nama_murid', 'like', '%' . $keyword . '%')
                                            ->orWhere('nis', 'like', '%' . $keyword . '%')
                                            ->orWhere('nisn', 'like', '%' . $keyword . '%');
                                    });
                            });
                    });
                });

            $totalWaliWhatsapp = (clone $waliQuery)->count();

            $waliMurids = $waliQuery
                ->orderByRaw('LOWER(nama_wali) ASC')
                ->orderBy('nama_wali')
                ->paginate(12)
                ->withQueryString();

            $totalMuridTerhubung = Murid::where('status', 'aktif')
                ->where('kelas_id', $kelasId)
                ->whereHas('waliMurid', function ($query) {
                    $query->where('status', 'aktif')
                        ->whereNotNull('no_whatsapp')
                        ->where('no_whatsapp', '!=', '');
                })
                ->count();
        }

        $settings = $this->getSettings();
        $tokenTersedia = $this->hasToken();

        return view('admin.pages.whatsapp-fonnte.index', compact(
            'waliMurids',
            'keyword',
            'kelasId',
            'kelasList',
            'kelasPerTingkat',
            'kelasTerpilih',
            'hasKelasFilter',
            'totalWaliWhatsapp',
            'totalMuridTerhubung',
            'settings',
            'tokenTersedia'
        ));
    }

    public function show(Request $request, WaliMurid $waliMurid)
    {
        $kelasId = $request->input('kelas_id');

        if (! $request->filled('kelas_id')) {
            return redirect()
                ->route($this->routePrefix() . '.whatsapp-fonnte.index')
                ->with('error', 'Pilih kelas terlebih dahulu sebelum memilih wali murid.');
        }

        $kelasTerpilih = Kelas::where('status', 'aktif')->find($kelasId);

        if (! $kelasTerpilih) {
            return redirect()
                ->route($this->routePrefix() . '.whatsapp-fonnte.index')
                ->with('error', 'Kelas yang dipilih tidak ditemukan atau sudah nonaktif.');
        }

        $waliMurid->load([
            'murids' => function ($query) use ($kelasId) {
                $query->with('kelas')
                    ->where('status', 'aktif')
                    ->where('kelas_id', $kelasId)
                    ->orderByRaw('LOWER(nama_murid) ASC')
                    ->orderBy('nama_murid');
            },
        ]);

        if (! $waliMurid->hasWhatsapp()) {
            return redirect()
                ->route($this->routePrefix() . '.whatsapp-fonnte.index', [
                    'kelas_id' => $kelasId,
                ])
                ->with('error', 'Wali murid ini belum memiliki nomor WhatsApp.');
        }

        if ($waliMurid->murids->isEmpty()) {
            return redirect()
                ->route($this->routePrefix() . '.whatsapp-fonnte.index', [
                    'kelas_id' => $kelasId,
                ])
                ->with('error', 'Wali murid ini tidak memiliki murid aktif pada kelas ' . $kelasTerpilih->nama_kelas . '.');
        }

        $selectedMurid = null;

        if ($request->filled('murid_id')) {
            $selectedMurid = $waliMurid->murids
                ->firstWhere('id', (int) $request->murid_id);
        }

        if (! $selectedMurid) {
            $selectedMurid = $waliMurid->murids->first();
        }

        $latestAbsensi = collect();
        $latestNilais = collect();

        if ($selectedMurid) {
            $latestAbsensi = AbsensiMurid::with(['kelas', 'mataPelajaran', 'guru'])
                ->where('murid_id', $selectedMurid->id)
                ->where('kelas_id', $kelasId)
                ->orderByDesc('tanggal_absen')
                ->limit(5)
                ->get();

            $latestNilai = Nilai::where('murid_id', $selectedMurid->id)
                ->where('kelas_id', $kelasId)
                ->orderByDesc('created_at')
                ->first();

            if ($latestNilai) {
                $latestNilais = Nilai::with(['kelas', 'mataPelajaran', 'tahunAjaran', 'semester', 'pegawai'])
                    ->where('murid_id', $selectedMurid->id)
                    ->where('kelas_id', $kelasId)
                    ->where('tahun_ajaran_id', $latestNilai->tahun_ajaran_id)
                    ->where('semester_id', $latestNilai->semester_id)
                    ->orderBy('mata_pelajaran_id')
                    ->get();
            }
        }

        $settings = $this->getSettings();
        $tokenTersedia = $this->hasToken();

        return view('admin.pages.whatsapp-fonnte.show', compact(
            'waliMurid',
            'selectedMurid',
            'latestAbsensi',
            'latestNilais',
            'settings',
            'tokenTersedia',
            'kelasId',
            'kelasTerpilih'
        ));
    }

    public function send(Request $request, WaliMurid $waliMurid)
    {
        $validated = $request->validate([
            'murid_id' => ['required', 'integer', 'exists:murids,id'],
            'kelas_id' => ['required', 'integer', 'exists:kelas,id'],
            'send_absensi' => ['nullable', 'boolean'],
            'send_nilai' => ['nullable', 'boolean'],
        ], [
            'murid_id.required' => 'Murid wajib dipilih.',
            'murid_id.exists' => 'Murid yang dipilih tidak ditemukan.',
            'kelas_id.required' => 'Kelas wajib dipilih.',
            'kelas_id.exists' => 'Kelas yang dipilih tidak ditemukan.',
        ]);

        $kelasId = $validated['kelas_id'];

        $sendAbsensi = $request->boolean('send_absensi');
        $sendNilai = $request->boolean('send_nilai');

        if (! $sendAbsensi && ! $sendNilai) {
            return back()
                ->withInput()
                ->with('error', 'Pilih minimal satu jenis pesan: absen murid atau nilai ujian semester.');
        }

        if (! $waliMurid->hasWhatsapp()) {
            return back()
                ->withInput()
                ->with('error', 'Pesan tidak terkirim karena nomor WhatsApp wali murid belum tersedia.');
        }

        if (! $this->hasToken()) {
            return back()
                ->withInput()
                ->with('error', 'Pesan tidak terkirim karena token Fonnte belum diatur. Isi FONNTE_TOKEN pada file .env atau simpan token pada pengaturan Fonnte.');
        }

        $murid = Murid::with(['kelas', 'waliMurid'])
            ->where('wali_murid_id', $waliMurid->id)
            ->where('kelas_id', $kelasId)
            ->findOrFail($validated['murid_id']);

        $target = $this->normalizeNomorWhatsapp($waliMurid->no_whatsapp);

        $hasilKirim = [];

        if ($sendAbsensi) {
            $messageAbsensi = $this->buildAbsensiMessage($murid, $waliMurid);
            $hasilKirim['absensi'] = $this->sendFonnte($target, $messageAbsensi);
        }

        if ($sendNilai) {
            $messageNilai = $this->buildNilaiMessage($murid, $waliMurid);
            $hasilKirim['nilai'] = $this->sendFonnte($target, $messageNilai);
        }

        $gagal = collect($hasilKirim)
            ->filter(fn ($result) => $result['success'] === false);

        if ($gagal->isNotEmpty()) {
            $pesanGagal = $gagal
                ->map(fn ($result, $jenis) => ucfirst($jenis) . ': ' . $result['message'])
                ->implode(' | ');

            return redirect()
                ->route($this->routePrefix() . '.whatsapp-fonnte.show', [
                    'waliMurid' => $waliMurid->id,
                    'kelas_id' => $kelasId,
                    'murid_id' => $murid->id,
                ])
                ->with('error', 'Pesan tidak terkirim. ' . $pesanGagal);
        }

        $jenisTerkirim = collect($hasilKirim)
            ->keys()
            ->map(fn ($jenis) => $jenis === 'absensi' ? 'absen murid' : 'nilai ujian semester')
            ->implode(' dan ');

        return redirect()
            ->route($this->routePrefix() . '.whatsapp-fonnte.show', [
                'waliMurid' => $waliMurid->id,
                'kelas_id' => $kelasId,
                'murid_id' => $murid->id,
            ])
            ->with('success', 'Pesan ' . $jenisTerkirim . ' berhasil terkirim ke ' . $waliMurid->nama_wali . '.');
    }

    private function buildAbsensiMessage(Murid $murid, WaliMurid $waliMurid): string
    {
        $absensis = AbsensiMurid::with(['kelas', 'mataPelajaran', 'guru'])
            ->where('murid_id', $murid->id)
            ->orderByDesc('tanggal_absen')
            ->limit(5)
            ->get();

        $message = "Assalamu'alaikum Bapak/Ibu {$waliMurid->nama_wali}.\n\n";
        $message .= "Berikut informasi absensi terbaru murid:\n";
        $message .= "Nama: {$murid->nama_murid}\n";
        $message .= 'Kelas: ' . ($murid->kelas?->nama_kelas ?? '-') . "\n\n";

        if ($absensis->isEmpty()) {
            $message .= "Belum ada data absensi murid yang tercatat pada sistem.\n\n";
        } else {
            $message .= "Data Absensi Terbaru:\n";

            foreach ($absensis as $index => $absensi) {
                $nomor = $index + 1;
                $tanggal = $absensi->tanggal_absen ? $absensi->tanggal_absen->format('d-m-Y') : '-';
                $mapel = $absensi->mataPelajaran?->nama_mapel ?? '-';
                $status = $absensi->status_absen_label;
                $keterangan = $absensi->keterangan ?: '-';

                $message .= "{$nomor}. {$tanggal} | {$mapel} | {$status}";
                $message .= $keterangan !== '-' ? " | {$keterangan}\n" : "\n";
            }

            $message .= "\n";
        }

        $message .= "Pesan ini dikirim melalui Sistem Informasi Akademik SMPN 6 Bati-Bati.";

        return $message;
    }

    private function buildNilaiMessage(Murid $murid, WaliMurid $waliMurid): string
    {
        $latestNilai = Nilai::where('murid_id', $murid->id)
            ->orderByDesc('created_at')
            ->first();

        $message = "Assalamu'alaikum Bapak/Ibu {$waliMurid->nama_wali}.\n\n";
        $message .= "Berikut informasi nilai ujian semester murid:\n";
        $message .= "Nama: {$murid->nama_murid}\n";
        $message .= 'Kelas: ' . ($murid->kelas?->nama_kelas ?? '-') . "\n\n";

        if (! $latestNilai) {
            $message .= "Belum ada data nilai ujian semester yang tercatat pada sistem.\n\n";
            $message .= "Pesan ini dikirim melalui Sistem Informasi Akademik SMPN 6 Bati-Bati.";

            return $message;
        }

        $nilais = Nilai::with(['mataPelajaran', 'tahunAjaran', 'semester'])
            ->where('murid_id', $murid->id)
            ->where('tahun_ajaran_id', $latestNilai->tahun_ajaran_id)
            ->where('semester_id', $latestNilai->semester_id)
            ->orderBy('mata_pelajaran_id')
            ->get();

        $tahunAjaran = $nilais->first()?->tahunAjaran?->nama_tahun_ajaran
            ?? $nilais->first()?->tahunAjaran?->tahun_ajaran
            ?? '-';

        $semester = $nilais->first()?->semester?->nama_semester_label
            ?? $nilais->first()?->semester?->nama_semester
            ?? '-';

        $message .= "Tahun Ajaran: {$tahunAjaran}\n";
        $message .= "Semester: {$semester}\n\n";

        if ($nilais->isEmpty()) {
            $message .= "Belum ada data nilai ujian semester yang tercatat pada sistem.\n\n";
        } else {
            $message .= "Daftar Nilai:\n";

            foreach ($nilais as $index => $nilai) {
                $nomor = $index + 1;
                $mapel = $nilai->mataPelajaran?->nama_mapel ?? '-';
                $message .= "{$nomor}. {$mapel}: {$nilai->nilai_ujian} ({$nilai->predikat} - {$nilai->keterangan_predikat})\n";
            }

            $rataRata = round($nilais->avg('nilai_ujian'), 2);
            $message .= "\nRata-rata: {$rataRata}\n\n";
        }

        $message .= "Pesan ini dikirim melalui Sistem Informasi Akademik SMPN 6 Bati-Bati.";

        return $message;
    }

    private function sendFonnte(string $target, string $message): array
    {
        try {
            $response = Http::asForm()
                ->withHeaders([
                    'Authorization' => $this->fonnteToken(),
                ])
                ->timeout(30)
                ->post('https://api.fonnte.com/send', [
                    'target' => $target,
                    'message' => $message,
                ]);

            $json = $response->json();

            if ($response->successful() && (($json['status'] ?? true) !== false)) {
                return [
                    'success' => true,
                    'message' => 'Terkirim',
                    'response' => $json,
                ];
            }

            return [
                'success' => false,
                'message' => $json['reason'] ?? $json['message'] ?? 'Fonnte menolak pengiriman pesan.',
                'response' => $json,
            ];
        } catch (\Throwable $e) {
            Log::error('Gagal mengirim WhatsApp Fonnte', [
                'message' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Terjadi kesalahan koneksi ke Fonnte.',
                'response' => null,
            ];
        }
    }

    private function routePrefix(): string
    {
        return auth()->user()->role === 'super_admin' ? 'super-admin' : 'admin';
    }

    private function emptyPaginator(Request $request): LengthAwarePaginator
    {
        return new LengthAwarePaginator(
            [],
            0,
            12,
            LengthAwarePaginator::resolveCurrentPage(),
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );
    }

    private function normalizeNomorWhatsapp(?string $nomor): string
    {
        $nomor = preg_replace('/[^0-9]/', '', (string) $nomor);

        if (str_starts_with($nomor, '0')) {
            return '62' . substr($nomor, 1);
        }

        if (str_starts_with($nomor, '8')) {
            return '62' . $nomor;
        }

        return $nomor;
    }

    private function hasToken(): bool
    {
        return ! empty($this->fonnteToken());
    }

    private function fonnteToken(): string
    {
        $envToken = env('FONNTE_TOKEN');

        if (! empty($envToken)) {
            return $envToken;
        }

        $settings = $this->getSettings();

        return $settings['token'] ?? '';
    }

    private function getSettings(): array
    {
        $default = [
            'token' => '',
            'updated_at' => null,
            'updated_by' => null,
        ];

        $path = storage_path($this->settingPath);

        if (! File::exists($path)) {
            return $default;
        }

        $content = File::get($path);
        $stored = json_decode($content, true);

        if (! is_array($stored)) {
            return $default;
        }

        return array_merge($default, $stored);
    }
}
