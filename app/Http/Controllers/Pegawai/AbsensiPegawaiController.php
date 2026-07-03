<?php

// penjelasan: Controller ini digunakan oleh guru dan staff.
// penjelasan: Controller ini menangani halaman Absen Saya, absen masuk, dan absen pulang.
// penjelasan: Tanggal, jam masuk, dan jam pulang diambil otomatis dari sistem.
// penjelasan: Lokasi pegawai divalidasi berdasarkan titik koordinat sekolah dan radius dari .env.

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use App\Models\AbsensiPegawai;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AbsensiPegawaiController extends Controller
{
    private function routePrefix(): string
    {
        return auth()->user()->role === 'guru' ? 'guru' : 'staff';
    }

    private function currentPegawai()
    {
        $pegawai = auth()->user()->pegawai;

        if (! $pegawai) {
            abort(403, 'Akun ini belum terhubung dengan data pegawai.');
        }

        return $pegawai;
    }

    public function index()
    {
        $pegawai = $this->currentPegawai();

        $tanggalHariIni = now()->toDateString();

        $absensiHariIni = AbsensiPegawai::where('pegawai_id', $pegawai->id)
            ->whereDate('tanggal_absen', $tanggalHariIni)
            ->first();

        $riwayatAbsensi = AbsensiPegawai::where('pegawai_id', $pegawai->id)
            ->latest('tanggal_absen')
            ->paginate(10);

        $routePrefix = $this->routePrefix();

        $pengaturanAbsensi = [
            'jam_masuk' => config('sekolah.absensi.jam_masuk'),
            'batas_terlambat' => config('sekolah.absensi.batas_terlambat'),
            'jam_pulang_minimal' => config('sekolah.absensi.jam_pulang_minimal'),
            'radius_meter' => config('sekolah.absensi.radius_meter'),
        ];

        return view('admin.pages.absensi-pegawai.index', compact(
            'pegawai',
            'absensiHariIni',
            'riwayatAbsensi',
            'routePrefix',
            'pengaturanAbsensi'
        ));
    }

    public function absenMasuk(Request $request)
    {
        $pegawai = $this->currentPegawai();

        $validated = $request->validate([
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
        ], [
            'latitude.required' => 'Lokasi latitude wajib terbaca sebelum absen masuk.',
            'latitude.numeric' => 'Lokasi latitude tidak valid.',
            'latitude.between' => 'Lokasi latitude tidak valid.',
            'longitude.required' => 'Lokasi longitude wajib terbaca sebelum absen masuk.',
            'longitude.numeric' => 'Lokasi longitude tidak valid.',
            'longitude.between' => 'Lokasi longitude tidak valid.',
        ]);

        $jarakMeter = $this->validateLocation((float) $validated['latitude'], (float) $validated['longitude']);

        $tanggalHariIni = now()->toDateString();

        $absensiHariIni = AbsensiPegawai::where('pegawai_id', $pegawai->id)
            ->whereDate('tanggal_absen', $tanggalHariIni)
            ->first();

        if ($absensiHariIni && $absensiHariIni->isStatusPengajuan()) {
            return back()->withErrors([
                'absensi' => 'Anda sudah memiliki status ' . $absensiHariIni->status_absen_label . ' dari pengajuan yang disetujui.',
            ]);
        }

        if ($absensiHariIni && $absensiHariIni->jam_masuk) {
            return back()->withErrors([
                'absensi' => 'Anda sudah melakukan absen masuk hari ini.',
            ]);
        }

        $statusAbsen = $this->determineStatusMasuk();

        AbsensiPegawai::updateOrCreate(
            [
                'pegawai_id' => $pegawai->id,
                'tanggal_absen' => $tanggalHariIni,
            ],
            [
                'jam_masuk' => now()->format('H:i:s'),
                'status_absen' => $statusAbsen,
                'metode_absen' => 'lokasi',
                'latitude' => $validated['latitude'],
                'longitude' => $validated['longitude'],
                'keterangan' => 'Absen masuk melalui lokasi GPS. Jarak dari sekolah sekitar ' . round($jarakMeter) . ' meter.',
            ]
        );

        $message = $statusAbsen === 'hadir'
            ? 'Absen masuk berhasil. Status Anda: Hadir.'
            : 'Absen masuk berhasil. Status Anda: Terlambat.';

        return back()->with('success', $message);
    }

    public function absenPulang(Request $request)
    {
        $pegawai = $this->currentPegawai();

        $validated = $request->validate([
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
        ], [
            'latitude.required' => 'Lokasi latitude wajib terbaca sebelum absen pulang.',
            'latitude.numeric' => 'Lokasi latitude tidak valid.',
            'latitude.between' => 'Lokasi latitude tidak valid.',
            'longitude.required' => 'Lokasi longitude wajib terbaca sebelum absen pulang.',
            'longitude.numeric' => 'Lokasi longitude tidak valid.',
            'longitude.between' => 'Lokasi longitude tidak valid.',
        ]);

        $jarakMeter = $this->validateLocation((float) $validated['latitude'], (float) $validated['longitude']);

        $tanggalHariIni = now()->toDateString();

        $absensiHariIni = AbsensiPegawai::where('pegawai_id', $pegawai->id)
            ->whereDate('tanggal_absen', $tanggalHariIni)
            ->first();

        if (! $absensiHariIni || ! $absensiHariIni->jam_masuk) {
            return back()->withErrors([
                'absensi' => 'Anda belum melakukan absen masuk hari ini.',
            ]);
        }

        if ($absensiHariIni->isStatusPengajuan()) {
            return back()->withErrors([
                'absensi' => 'Status absensi dari pengajuan tidak memerlukan absen pulang.',
            ]);
        }

        if ($absensiHariIni->jam_pulang) {
            return back()->withErrors([
                'absensi' => 'Anda sudah melakukan absen pulang hari ini.',
            ]);
        }

        $this->validateJamPulangMinimal();

        $keteranganLama = $absensiHariIni->keterangan ? $absensiHariIni->keterangan . ' ' : '';

        $absensiHariIni->update([
            'jam_pulang' => now()->format('H:i:s'),
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'keterangan' => $keteranganLama . 'Absen pulang melalui lokasi GPS. Jarak dari sekolah sekitar ' . round($jarakMeter) . ' meter.',
        ]);

        return back()->with('success', 'Absen pulang berhasil disimpan.');
    }

    private function determineStatusMasuk(): string
    {
        $tanggalHariIni = now()->toDateString();

        $batasTerlambat = Carbon::createFromFormat(
            'Y-m-d H:i',
            $tanggalHariIni . ' ' . config('sekolah.absensi.batas_terlambat')
        );

        return now()->greaterThan($batasTerlambat) ? 'terlambat' : 'hadir';
    }

    private function validateJamPulangMinimal(): void
    {
        $tanggalHariIni = now()->toDateString();

        $jamPulangMinimal = Carbon::createFromFormat(
            'Y-m-d H:i',
            $tanggalHariIni . ' ' . config('sekolah.absensi.jam_pulang_minimal')
        );

        if (now()->lessThan($jamPulangMinimal)) {
            throw ValidationException::withMessages([
                'jam_pulang' => 'Absen pulang baru dapat dilakukan mulai pukul ' . config('sekolah.absensi.jam_pulang_minimal') . '.',
            ]);
        }
    }

    private function validateLocation(float $latitude, float $longitude): float
    {
        $schoolLatitude = config('sekolah.absensi.latitude');
        $schoolLongitude = config('sekolah.absensi.longitude');
        $radiusMeter = (int) config('sekolah.absensi.radius_meter', 100);

        if ($schoolLatitude === null || $schoolLongitude === null) {
            throw ValidationException::withMessages([
                'lokasi' => 'Koordinat sekolah belum diatur. Isi SEKOLAH_LATITUDE dan SEKOLAH_LONGITUDE pada file .env.',
            ]);
        }

        $distance = $this->calculateDistanceInMeters(
            $schoolLatitude,
            $schoolLongitude,
            $latitude,
            $longitude
        );

        if ($distance > $radiusMeter) {
            throw ValidationException::withMessages([
                'lokasi' => 'Anda berada di luar radius sekolah. Jarak Anda sekitar ' . round($distance) . ' meter dari titik sekolah.',
            ]);
        }

        return $distance;
    }

    private function calculateDistanceInMeters(float $fromLat, float $fromLng, float $toLat, float $toLng): float
    {
        $earthRadius = 6371000;

        $latFrom = deg2rad($fromLat);
        $lngFrom = deg2rad($fromLng);
        $latTo = deg2rad($toLat);
        $lngTo = deg2rad($toLng);

        $latDelta = $latTo - $latFrom;
        $lngDelta = $lngTo - $lngFrom;

        $a = sin($latDelta / 2) ** 2
            + cos($latFrom) * cos($latTo) * sin($lngDelta / 2) ** 2;

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}
