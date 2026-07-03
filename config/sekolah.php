<?php

// penjelasan: File konfigurasi ini menyimpan pengaturan sekolah.
// penjelasan: Pengaturan absensi memakai nilai dari .env agar mudah diubah tanpa mengedit kode.
// penjelasan: Koordinat sekolah dipakai untuk validasi radius GPS absen pegawai.

return [
    'absensi' => [
        'latitude' => env('SEKOLAH_LATITUDE') !== null && env('SEKOLAH_LATITUDE') !== ''
            ? (float) env('SEKOLAH_LATITUDE')
            : null,

        'longitude' => env('SEKOLAH_LONGITUDE') !== null && env('SEKOLAH_LONGITUDE') !== ''
            ? (float) env('SEKOLAH_LONGITUDE')
            : null,

        'radius_meter' => (int) env('SEKOLAH_RADIUS_METER', 100),

        'jam_masuk' => env('ABSENSI_JAM_MASUK', '08:00'),
        'batas_terlambat' => env('ABSENSI_BATAS_TERLAMBAT', '08:15'),
        'jam_pulang_minimal' => env('ABSENSI_JAM_PULANG_MINIMAL', '04:00'),
    ],
];
