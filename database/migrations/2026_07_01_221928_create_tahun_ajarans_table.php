<?php

// penjelasan: File migration ini digunakan untuk membuat tabel tahun_ajarans.
// penjelasan: Tabel tahun_ajarans menyimpan periode tahun ajaran sekolah, contoh 2025/2026.
// penjelasan: Data ini nanti digunakan oleh Semester, Jadwal Pelajaran, Absensi Murid, dan Nilai.
// penjelasan: Sistem akan dibuat hanya boleh ada satu tahun ajaran aktif.

use Illuminate\Database\Migrations\Migration;
// penjelasan: Migration digunakan Laravel untuk membuat atau mengubah struktur tabel database.

use Illuminate\Database\Schema\Blueprint;
// penjelasan: Blueprint digunakan untuk mendefinisikan kolom-kolom tabel.

use Illuminate\Support\Facades\Schema;
// penjelasan: Schema digunakan untuk membuat dan menghapus tabel.

return new class extends Migration
{
    /**
     * penjelasan: Method up() dijalankan saat perintah php artisan migrate.
     * penjelasan: Method ini membuat tabel tahun_ajarans.
     */
    public function up(): void
    {
        Schema::create('tahun_ajarans', function (Blueprint $table) {
            $table->id();
            // penjelasan: Kolom id adalah primary key tabel tahun_ajarans.

            $table->string('nama_tahun_ajaran')->unique();
            // penjelasan: nama_tahun_ajaran menyimpan nama periode tahun ajaran.
            // penjelasan: Contoh: 2025/2026.
            // penjelasan: unique digunakan agar nama tahun ajaran tidak dobel.

            $table->date('tanggal_mulai')->nullable();
            // penjelasan: tanggal_mulai menyimpan tanggal mulai tahun ajaran.
            // penjelasan: nullable artinya boleh dikosongkan jika belum ditentukan.

            $table->date('tanggal_selesai')->nullable();
            // penjelasan: tanggal_selesai menyimpan tanggal selesai tahun ajaran.
            // penjelasan: nullable artinya boleh dikosongkan jika belum ditentukan.

            $table->enum('status', ['aktif', 'nonaktif'])->default('nonaktif');
            // penjelasan: status digunakan untuk menentukan tahun ajaran aktif.
            // penjelasan: Hanya satu tahun ajaran yang boleh aktif pada sistem.

            $table->timestamps();
            // penjelasan: timestamps membuat kolom created_at dan updated_at otomatis.
        });
    }

    /**
     * penjelasan: Method down() dijalankan saat rollback migration.
     * penjelasan: Method ini menghapus tabel tahun_ajarans.
     */
    public function down(): void
    {
        Schema::dropIfExists('tahun_ajarans');
    }
};
