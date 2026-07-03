<?php

// penjelasan: Model ini mewakili tabel absensi_murids.
// penjelasan: Model ini digunakan untuk menyimpan dan membaca data absensi murid.
// penjelasan: Absensi murid terhubung dengan murid, jadwal pelajaran, guru, kelas, dan mata pelajaran.

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AbsensiMurid extends Model
{
    use HasFactory;

    protected $fillable = [
        'murid_id',
        'jadwal_pelajaran_id',
        'guru_id',
        'kelas_id',
        'mata_pelajaran_id',
        'tanggal_absen',
        'status_absen',
        'keterangan',
        'created_by',
    ];

    protected $casts = [
        'tanggal_absen' => 'date',
    ];

    public function murid()
    {
        return $this->belongsTo(Murid::class);
    }

    public function jadwalPelajaran()
    {
        return $this->belongsTo(JadwalPelajaran::class);
    }

    public function guru()
    {
        return $this->belongsTo(Pegawai::class, 'guru_id');
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    public function mataPelajaran()
    {
        return $this->belongsTo(MataPelajaran::class);
    }

    public function pembuat()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getStatusAbsenLabelAttribute(): string
    {
        return match ($this->status_absen) {
            'hadir' => 'Hadir',
            'izin' => 'Izin',
            'sakit' => 'Sakit',
            'alpha' => 'Alpha',
            default => '-',
        };
    }
}
