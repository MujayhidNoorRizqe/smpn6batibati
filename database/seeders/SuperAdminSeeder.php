<?php

// penjelasan: File ini adalah Seeder untuk membuat akun super admin pertama.
// penjelasan: Akun super admin dibuat dari seeder karena sebelum ada dashboard, belum ada cara membuat user dari sistem.

namespace Database\Seeders;

// penjelasan: Model User digunakan untuk memasukkan data ke tabel users.
use App\Models\User;

// penjelasan: Seeder adalah class bawaan Laravel untuk mengisi data awal ke database.
use Illuminate\Database\Seeder;

// penjelasan: Hash digunakan untuk mengamankan password sebelum disimpan ke database.
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * penjelasan: Method run() dijalankan saat perintah php artisan db:seed atau migrate:fresh --seed.
     */
    public function run(): void
    {
        // penjelasan: updateOrCreate digunakan agar data super admin tidak dobel.
        // penjelasan: Jika email sudah ada, datanya diperbarui.
        // penjelasan: Jika email belum ada, data baru dibuat.
        User::updateOrCreate(
            [
                'email' => 'superadmin@smpn6batibati.sch.id',
            ],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password123'),
                'role' => 'super_admin',
                'status' => 'aktif',
            ]
        );
    }
}
