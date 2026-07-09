<?php

// penjelasan: File ini adalah Model TahunAjaran.
// penjelasan: Model ini digunakan Laravel untuk berhubungan dengan tabel tahun_ajarans.
// penjelasan: Data tahun ajaran dipakai untuk mengelompokkan semester dan data akademik.
// penjelasan: Data ini nanti dipakai oleh Jadwal Pelajaran, Absensi Murid, dan Nilai.

namespace App\Models;

// penjelasan: HasFactory digunakan agar model bisa dipakai untuk factory atau data dummy/testing.
use Illuminate\Database\Eloquent\Factories\HasFactory;

// penjelasan: Model adalah class dasar Laravel untuk model database.
use Illuminate\Database\Eloquent\Model;

class TahunAjaran extends Model
{
    use HasFactory;

    /**
     * penjelasan: Fillable adalah daftar kolom yang boleh diisi dari controller.
     * penjelasan: Kolom ini harus sesuai dengan kolom tabel tahun_ajarans.
     */
    protected $fillable = [
        'nama_tahun_ajaran',
        'tanggal_mulai',
        'tanggal_selesai',
        'status',
    ];

    /**
     * penjelasan: casts mengubah tipe data otomatis.
     * penjelasan: tanggal_mulai dan tanggal_selesai dibuat sebagai date agar mudah diformat di view.
     */
    protected function casts(): array
    {
        return [
            'tanggal_mulai' => 'date',
            'tanggal_selesai' => 'date',
        ];
    }

    // penjelasan: Relasi ini menghubungkan satu tahun ajaran ke banyak semester.
    // penjelasan: Dipanggil misalnya $tahunAjaran->semesters.
    public function semesters()
    {
        return $this->hasMany(Semester::class, 'tahun_ajaran_id');
    }

    // penjelasan: Relasi ini menghubungkan tahun ajaran ke banyak nilai.
    // penjelasan: Dipakai untuk rekap nilai semester dan laporan nilai.
    // penjelasan: Dipanggil misalnya $tahunAjaran->nilais.
    public function nilais()
    {
        return $this->hasMany(Nilai::class, 'tahun_ajaran_id');
    }

    // penjelasan: Fungsi ini mengecek apakah tahun ajaran berstatus aktif.
    public function isAktif(): bool
    {
        return $this->status === 'aktif';
    }
}
