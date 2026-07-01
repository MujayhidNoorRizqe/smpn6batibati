<?php

// penjelasan: File ini adalah Model Pegawai.
// penjelasan: Model Pegawai digunakan untuk berhubungan dengan tabel pegawais.
// penjelasan: Tabel pegawais menyimpan data guru dan staff.

namespace App\Models;

// penjelasan: HasFactory digunakan agar model bisa dipakai untuk membuat data dummy/testing.
use Illuminate\Database\Eloquent\Factories\HasFactory;

// penjelasan: Model adalah class dasar Laravel untuk model database.
use Illuminate\Database\Eloquent\Model;

class Pegawai extends Model
{
    use HasFactory;

    /**
     * penjelasan: Fillable adalah daftar kolom yang boleh diisi dari controller.
     * penjelasan: Kolom ini mengikuti struktur tabel pegawais.
     */
    protected $fillable = [
        'user_id',
        'nip',
        'nama_pegawai',
        'jenis_pegawai',
        'jabatan',
        'jenis_kelamin',
        'no_hp',
        'alamat',
        'foto',
        'status',
    ];

    // penjelasan: Relasi ini menghubungkan pegawai ke akun user.
    // penjelasan: Satu pegawai boleh punya satu akun login.
    // penjelasan: Dipanggil misalnya $pegawai->user.
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // penjelasan: Fungsi ini mengecek apakah data pegawai adalah guru.
    // penjelasan: Guru nanti bisa digunakan pada jadwal pelajaran, absen murid, dan input nilai.
    public function isGuru(): bool
    {
        return $this->jenis_pegawai === 'guru';
    }

    // penjelasan: Fungsi ini mengecek apakah data pegawai adalah staff.
    public function isStaff(): bool
    {
        return $this->jenis_pegawai === 'staff';
    }

    // penjelasan: Fungsi ini mengecek apakah data pegawai masih aktif.
    public function isAktif(): bool
    {
        return $this->status === 'aktif';
    }
}
