<?php

// penjelasan: Model ini digunakan untuk tabel absensi_pegawais.
// penjelasan: Data di tabel ini adalah absensi resmi guru/staff.

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

    protected function casts(): array
    {
        return [
            'tanggal_absen' => 'date',
        ];
    }

    // penjelasan: Relasi ke pegawai pemilik absensi.
    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }

    // penjelasan: Relasi ke pengajuan absensi pegawai jika absensi dibuat dari pengajuan.
    public function pengajuanAbsensiPegawai()
    {
        return $this->belongsTo(PengajuanAbsensiPegawai::class);
    }

    // penjelasan: Label status absen untuk tampilan.
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
}
