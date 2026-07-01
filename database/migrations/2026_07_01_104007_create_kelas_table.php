<?php

// penjelasan: File migration ini digunakan untuk membuat tabel kelas.
// penjelasan: Tabel kelas menyimpan data kelas sekolah seperti 7A, 7B, 8A, dan seterusnya.
// penjelasan: Tabel ini terhubung ke tabel pegawais melalui wali_kelas_id.
// penjelasan: wali_kelas_id digunakan untuk menentukan guru yang menjadi wali kelas.

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
     * penjelasan: Isi method ini membuat tabel kelas.
     */
    public function up(): void
    {
        Schema::create('kelas', function (Blueprint $table) {
            $table->id();
            // penjelasan: Kolom id adalah primary key tabel kelas.

            $table->string('nama_kelas')->unique();
            // penjelasan: nama_kelas menyimpan nama kelas, contoh 7A, 7B, 8A.
            // penjelasan: unique digunakan agar nama kelas tidak dobel.

            $table->string('tingkat');
            // penjelasan: tingkat menyimpan level kelas, contoh 7, 8, atau 9.

            $table->foreignId('wali_kelas_id')->nullable()->constrained('pegawais')->nullOnDelete();
            // penjelasan: wali_kelas_id menghubungkan kelas dengan tabel pegawais.
            // penjelasan: nullable artinya kelas boleh dibuat tanpa wali kelas dulu.
            // penjelasan: constrained('pegawais') membuat foreign key ke tabel pegawais.
            // penjelasan: nullOnDelete artinya jika data pegawai wali kelas dihapus, wali_kelas_id menjadi null.

            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            // penjelasan: status digunakan agar kelas bisa dinonaktifkan tanpa harus dihapus permanen.

            $table->timestamps();
            // penjelasan: timestamps membuat kolom created_at dan updated_at otomatis.
        });
    }

    /**
     * penjelasan: Method down() dijalankan saat rollback migration.
     * penjelasan: Isi method ini menghapus tabel kelas.
     */
    public function down(): void
    {
        Schema::dropIfExists('kelas');
    }
};
