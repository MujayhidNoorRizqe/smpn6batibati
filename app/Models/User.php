<?php

// penjelasan: File ini adalah Model User.
// penjelasan: Model digunakan Laravel untuk berhubungan dengan tabel users di database.

namespace App\Models;

// penjelasan: HasFactory dipakai agar model User bisa digunakan untuk factory atau data dummy.
use Illuminate\Database\Eloquent\Factories\HasFactory;

// penjelasan: Authenticatable adalah class bawaan Laravel agar User bisa digunakan untuk login.
use Illuminate\Foundation\Auth\User as Authenticatable;

// penjelasan: Notifiable dipakai jika nanti user menerima notifikasi.
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    // penjelasan: Trait HasFactory dan Notifiable menambahkan fitur factory dan notifikasi ke model User.
    use HasFactory, Notifiable;

    /**
     * penjelasan: Fillable adalah daftar kolom yang boleh diisi secara massal dari controller atau seeder.
     * penjelasan: Kolom role dipakai untuk membedakan super_admin, admin, guru, dan staff.
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
     * penjelasan: Hidden adalah daftar kolom yang disembunyikan saat data user ditampilkan.
     * penjelasan: Password dan remember_token tidak boleh ditampilkan demi keamanan.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * penjelasan: Casts digunakan untuk mengubah tipe data otomatis.
     * penjelasan: email_verified_at dan last_login_at dianggap sebagai data tanggal.
     * penjelasan: password dengan cast hashed akan otomatis diamankan oleh Laravel.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // penjelasan: Fungsi ini mengecek apakah user memiliki role super_admin.
    // penjelasan: Fungsi ini bisa dipanggil dengan auth()->user()->isSuperAdmin().
    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    // penjelasan: Fungsi ini mengecek apakah user memiliki role admin.
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    // penjelasan: Fungsi ini mengecek apakah user memiliki role guru.
    public function isGuru(): bool
    {
        return $this->role === 'guru';
    }

    // penjelasan: Fungsi ini mengecek apakah user memiliki role staff.
    public function isStaff(): bool
    {
        return $this->role === 'staff';
    }

    // penjelasan: Fungsi ini mengecek apakah akun user masih aktif.
    // penjelasan: User dengan status nonaktif tidak boleh login ke sistem.
    public function isAktif(): bool
    {
        return $this->status === 'aktif';
    }
}
