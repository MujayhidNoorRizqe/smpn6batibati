<?php

// penjelasan: File ini adalah Model Pegawai.
// penjelasan: Model Pegawai digunakan untuk berhubungan dengan tabel pegawais.
// penjelasan: Sistem saat ini hanya memakai jenis pegawai Guru.
// penjelasan: Data staff lama tidak ditampilkan pada modul pegawai, tetapi method legacy tetap disediakan agar data lama tidak menyebabkan error.

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pegawai extends Model
{
    use HasFactory;

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

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function kelasWali()
    {
        return $this->hasMany(Kelas::class, 'wali_kelas_id');
    }

    public function nilais()
    {
        return $this->hasMany(Nilai::class, 'pegawai_id');
    }

    public function isGuru(): bool
    {
        return $this->jenis_pegawai === 'guru';
    }

    public function isStaff(): bool
    {
        return $this->jenis_pegawai === 'staff';
    }

    public function isAktif(): bool
    {
        return $this->status === 'aktif';
    }
}
