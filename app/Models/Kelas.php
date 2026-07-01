<?php

// penjelasan: File ini adalah Model Kelas.
// penjelasan: Model Kelas digunakan untuk berhubungan dengan tabel kelas.
// penjelasan: Tabel kelas menyimpan data kelas seperti 7A, 8A, dan 9A.
// penjelasan: Model ini punya relasi ke Pegawai sebagai wali kelas.

namespace App\Models;

// penjelasan: HasFactory digunakan agar model bisa dipakai untuk data dummy/testing.
use Illuminate\Database\Eloquent\Factories\HasFactory;

// penjelasan: Model adalah class dasar Laravel untuk model database.
use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    use HasFactory;

    // penjelasan: Laravel biasanya menebak nama tabel dari nama model.
    // penjelasan: Karena nama tabel kita adalah kelas, maka kita set manual agar tidak salah.
    protected $table = 'kelas';

    /**
     * penjelasan: Fillable adalah daftar kolom yang boleh diisi dari controller.
     * penjelasan: Kolom ini sesuai dengan struktur tabel kelas.
     */
    protected $fillable = [
        'nama_kelas',
        'tingkat',
        'wali_kelas_id',
        'status',
    ];

    // penjelasan: Relasi ini menghubungkan kelas ke pegawai sebagai wali kelas.
    // penjelasan: wali_kelas_id pada tabel kelas mengarah ke id pada tabel pegawais.
    // penjelasan: Relasi ini dipanggil misalnya $kelas->waliKelas.
    public function waliKelas()
    {
        return $this->belongsTo(Pegawai::class, 'wali_kelas_id');
    }

    // penjelasan: Fungsi ini mengecek apakah kelas masih aktif.
    public function isAktif(): bool
    {
        return $this->status === 'aktif';
    }
}
