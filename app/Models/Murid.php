<?php

// penjelasan: File ini adalah Model Murid.
// penjelasan: Model Murid digunakan Laravel untuk berhubungan dengan tabel murids.
// penjelasan: Model ini memiliki relasi ke Kelas dan WaliMurid.
// penjelasan: Data murid nanti dipakai oleh modul absensi murid, nilai, laporan, dan WhatsApp Fonnte.

namespace App\Models;

// penjelasan: HasFactory digunakan agar model bisa dipakai untuk factory atau data dummy/testing.
use Illuminate\Database\Eloquent\Factories\HasFactory;

// penjelasan: Model adalah class dasar Laravel untuk model database.
use Illuminate\Database\Eloquent\Model;

class Murid extends Model
{
    use HasFactory;

    /**
     * penjelasan: Fillable adalah daftar kolom yang boleh diisi secara massal dari controller.
     * penjelasan: Kolom ini harus sesuai dengan struktur tabel murids.
     * penjelasan: Jika kolom tidak ada di fillable, Laravel tidak akan mengizinkan pengisian massal untuk kolom tersebut.
     */
    protected $fillable = [
        'kelas_id',
        'wali_murid_id',
        'nis',
        'nisn',
        'nama_murid',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'agama',
        'alamat',
        'foto',
        'status',
    ];

    /**
     * penjelasan: Casts digunakan untuk mengubah tipe data otomatis.
     * penjelasan: tanggal_lahir dibuat menjadi date agar mudah diformat pada view.
     */
    protected function casts(): array
    {
        return [
            'tanggal_lahir' => 'date',
        ];
    }

    // penjelasan: Relasi ini menghubungkan murid ke kelas.
    // penjelasan: Satu murid hanya berada pada satu kelas aktif.
    // penjelasan: Dipanggil misalnya $murid->kelas.
    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    // penjelasan: Relasi ini menghubungkan murid ke wali murid.
    // penjelasan: Satu murid memiliki satu wali utama pada data ini.
    // penjelasan: Dipanggil misalnya $murid->waliMurid.
    public function waliMurid()
    {
        return $this->belongsTo(WaliMurid::class, 'wali_murid_id');
    }

    // penjelasan: Fungsi ini mengecek apakah murid masih aktif.
    // penjelasan: Fungsi ini nanti berguna untuk absensi, nilai, dan laporan.
    public function isAktif(): bool
    {
        return $this->status === 'aktif';
    }
}
