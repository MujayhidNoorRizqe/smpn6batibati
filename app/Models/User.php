<?php

// penjelasan: File ini adalah Model User.
// penjelasan: Model User digunakan Laravel untuk berhubungan dengan tabel users.
// penjelasan: Tabel users menyimpan akun login seperti super_admin, admin, guru, dan staff.

namespace App\Models;

// penjelasan: HasFactory digunakan agar model bisa dipakai untuk factory atau data dummy.
use Illuminate\Database\Eloquent\Factories\HasFactory;

// penjelasan: Authenticatable adalah class bawaan Laravel agar User bisa dipakai untuk login.
use Illuminate\Foundation\Auth\User as Authenticatable;

// penjelasan: Notifiable digunakan untuk fitur notifikasi Laravel.
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * penjelasan: Fillable adalah daftar kolom yang boleh diisi secara massal.
     * penjelasan: Kolom role dipakai untuk membedakan hak akses user.
     * penjelasan: Kolom status dipakai untuk menentukan akun aktif atau nonaktif.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status',
        'last_login_at',
    ];

    /**
     * penjelasan: Hidden adalah daftar kolom yang tidak boleh ditampilkan.
     * penjelasan: Password tidak boleh ditampilkan demi keamanan.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * penjelasan: Casts digunakan untuk mengubah tipe data otomatis.
     * penjelasan: password memakai hashed agar password otomatis diamankan.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // penjelasan: Relasi ini menghubungkan satu user dengan satu data pegawai.
    // penjelasan: Relasi ini dipakai untuk akun guru dan staff yang wajib punya data pegawai.
    // penjelasan: Dipanggil misalnya dengan auth()->user()->pegawai.
    public function pegawai()
    {
        return $this->hasOne(Pegawai::class);
    }

    // penjelasan: Fungsi ini mengecek apakah user adalah super admin.
    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    // penjelasan: Fungsi ini mengecek apakah user adalah admin.
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    // penjelasan: Fungsi ini mengecek apakah user adalah guru.
    public function isGuru(): bool
    {
        return $this->role === 'guru';
    }

    // penjelasan: Fungsi ini mengecek apakah user adalah staff.
    public function isStaff(): bool
    {
        return $this->role === 'staff';
    }

    // penjelasan: Fungsi ini mengecek apakah akun user aktif.
    public function isAktif(): bool
    {
        return $this->status === 'aktif';
    }
}
