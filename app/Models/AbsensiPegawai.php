<?php

// penjelasan: Model ini mewakili tabel absensi_pegawais.
// penjelasan: Tabel ini menyimpan absensi resmi guru/staff.
// penjelasan: Data bisa berasal dari lokasi, WiFi, pengajuan, atau manual.

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AbsensiPegawai extends Model
{
    use HasFactory;

    protected $fillable = [
        'pegawai_id',
        'tanggal_absen',
        'jam_masuk',
        'jam_pulang',
        'status_absen',
        'metode_absen',
        'latitude',
        'longitude',
        'keterangan',
        'pengajuan_absensi_pegawai_id',
    ];

    protected $casts = [
        'tanggal_absen' => 'date',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }

    public function pengajuanAbsensiPegawai()
    {
        return $this->belongsTo(PengajuanAbsensiPegawai::class);
    }

    public function getStatusAbsenLabelAttribute(): string
    {
        return match ($this->status_absen) {
            'hadir' => 'Hadir',
            'terlambat' => 'Terlambat',
            'dinas' => 'Dinas',
            'izin' => 'Izin',
            'sakit' => 'Sakit',
            'alpha' => 'Alpha',
            default => '-',
        };
    }

    public function getMetodeAbsenLabelAttribute(): string
    {
        return match ($this->metode_absen) {
            'lokasi' => 'Lokasi GPS',
            'wifi' => 'WiFi Sekolah',
            'pengajuan' => 'Pengajuan',
            'manual' => 'Manual',
            default => '-',
        };
    }

    public function getJamMasukFormatAttribute(): string
    {
        return $this->jam_masuk ? substr($this->jam_masuk, 0, 5) : '-';
    }

    public function getJamPulangFormatAttribute(): string
    {
        return $this->jam_pulang ? substr($this->jam_pulang, 0, 5) : '-';
    }

    public function isStatusPengajuan(): bool
    {
        return in_array($this->status_absen, ['dinas', 'sakit', 'izin'], true);
    }

    public function isHadirAtauTerlambat(): bool
    {
        return in_array($this->status_absen, ['hadir', 'terlambat'], true);
    }
}
