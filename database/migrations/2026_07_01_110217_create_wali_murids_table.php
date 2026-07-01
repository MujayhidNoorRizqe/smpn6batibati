<?php

// penjelasan: File migration ini digunakan untuk membuat tabel wali_murids.
// penjelasan: Tabel wali_murids menyimpan data orang tua atau wali murid.
// penjelasan: Data ini nanti akan dihubungkan ke tabel murids saat modul Data Murid dibuat.
// penjelasan: Nomor WhatsApp wali disimpan khusus agar nanti bisa dipakai untuk notifikasi Fonnte.

use Illuminate\Database\Migrations\Migration;
// penjelasan: Migration adalah fitur Laravel untuk membuat atau mengubah struktur tabel database.

use Illuminate\Database\Schema\Blueprint;
// penjelasan: Blueprint digunakan untuk mendefinisikan kolom-kolom tabel.

use Illuminate\Support\Facades\Schema;
// penjelasan: Schema digunakan untuk membuat dan menghapus tabel.

return new class extends Migration
{
    /**
     * penjelasan: Method up() dijalankan saat perintah php artisan migrate.
     * penjelasan: Isi method ini membuat tabel wali_murids.
     */
    public function up(): void
    {
        Schema::create('wali_murids', function (Blueprint $table) {
            $table->id();
            // penjelasan: Kolom id adalah primary key tabel wali_murids.

            $table->string('nama_wali');
            // penjelasan: nama_wali menyimpan nama orang tua atau wali murid.

            $table->string('nik')->nullable()->unique();
            // penjelasan: nik menyimpan Nomor Induk Kependudukan wali murid.
            // penjelasan: nullable artinya boleh kosong.
            // penjelasan: unique artinya jika diisi, NIK tidak boleh sama dengan wali murid lain.

            $table->enum('hubungan', ['ayah', 'ibu', 'wali']);
            // penjelasan: hubungan menjelaskan hubungan wali dengan murid.
            // penjelasan: Pilihannya ayah, ibu, atau wali.

            $table->string('pekerjaan')->nullable();
            // penjelasan: pekerjaan menyimpan pekerjaan wali murid.
            // penjelasan: Kolom ini boleh kosong.

            $table->string('no_hp')->nullable();
            // penjelasan: no_hp menyimpan nomor HP biasa.
            // penjelasan: Nomor ini belum tentu nomor WhatsApp.

            $table->string('no_whatsapp')->nullable();
            // penjelasan: no_whatsapp menyimpan nomor WhatsApp wali murid.
            // penjelasan: Kolom ini penting untuk pengiriman notifikasi absensi dan nilai menggunakan Fonnte.

            $table->text('alamat')->nullable();
            // penjelasan: alamat menyimpan alamat wali murid.

            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            // penjelasan: status digunakan agar data wali murid bisa dinonaktifkan tanpa dihapus permanen.

            $table->timestamps();
            // penjelasan: timestamps membuat kolom created_at dan updated_at otomatis.
        });
    }

    /**
     * penjelasan: Method down() dijalankan saat rollback migration.
     * penjelasan: Isi method ini menghapus tabel wali_murids.
     */
    public function down(): void
    {
        Schema::dropIfExists('wali_murids');
    }
};
