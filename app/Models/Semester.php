<?php

// penjelasan: File ini adalah Model Semester.
// penjelasan: Model ini digunakan Laravel untuk berhubungan dengan tabel semesters.
// penjelasan: Data semester terhubung ke tahun ajaran.
// penjelasan: Data semester nanti dipakai pada Jadwal Pelajaran, Absensi Murid, dan Nilai.

namespace App\Models;

// penjelasan: HasFactory digunakan agar model bisa dipakai untuk factory atau data dummy/testing.
use Illuminate\Database\Eloquent\Factories\HasFactory;

// penjelasan: Model adalah class dasar Laravel untuk model database.
use Illuminate\Database\Eloquent\Model;

class Semester extends Model
{
    use HasFactory;

    /**
     * penjelasan: Fillable adalah daftar kolom yang boleh diisi dari controller.
     * penjelasan: Kolom ini harus sesuai dengan kolom tabel semesters.
     */
    protected $fillable = [
        'tahun_ajaran_id',
        'nama_semester',
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

    // penjelasan: Relasi ini menghubungkan semester ke tahun ajaran.
    // penjelasan: Dipanggil misalnya $semester->tahunAjaran.
    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class);
    }

    // penjelasan: Fungsi ini mengecek apakah semester berstatus aktif.
    public function isAktif(): bool
    {
        return $this->status === 'aktif';
    }

    // penjelasan: Accessor ini mengubah nama_semester menjadi label yang rapi.
    // penjelasan: Contoh ganjil menjadi Ganjil.
    public function getNamaSemesterLabelAttribute(): string
    {
        return match ($this->nama_semester) {
            'ganjil' => 'Ganjil',
            'genap' => 'Genap',
            default => '-',
        };
    }
}
