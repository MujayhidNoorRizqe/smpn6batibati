<?php

// penjelasan: File ini adalah seeder utama Laravel.
// penjelasan: Dari file ini kita bisa memanggil seeder lain, termasuk SuperAdminSeeder.

namespace Database\Seeders;

// penjelasan: Seeder adalah class bawaan Laravel untuk menjalankan data awal.
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * penjelasan: Method run() dijalankan saat menjalankan php artisan db:seed.
     */
    public function run(): void
    {
        // penjelasan: Baris ini memanggil SuperAdminSeeder.
        // penjelasan: Jadi saat seeder utama dijalankan, akun super admin otomatis dibuat.
        $this->call([
            SuperAdminSeeder::class,
        ]);
    }
}
