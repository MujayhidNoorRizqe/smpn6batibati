<?php

// penjelasan: File ini adalah Model User.
// penjelasan: Model User digunakan Laravel untuk berhubungan dengan tabel users.
// penjelasan: Role aktif pada sistem saat ini adalah super_admin, admin, dan guru.
// penjelasan: Role staff sudah dinonaktifkan dari akses sistem.
// penjelasan: Method isStaff tetap ada sebagai pengecekan legacy untuk data lama.

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function pegawai()
    {
        return $this->hasOne(Pegawai::class);
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isGuru(): bool
    {
        return $this->role === 'guru';
    }

    public function isStaff(): bool
    {
        return $this->role === 'staff';
    }

    public function isAktif(): bool
    {
        return $this->status === 'aktif';
    }
}
