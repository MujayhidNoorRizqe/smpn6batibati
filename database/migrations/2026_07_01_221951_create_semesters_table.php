<?php

// penjelasan: File migration ini digunakan untuk membuat tabel semesters.
// penjelasan: Tabel semesters menyimpan data semester Ganjil dan Genap.
// penjelasan: Setiap semester terhubung ke satu tahun ajaran.
// penjelasan: Data semester nanti dipakai oleh Jadwal Pelajaran, Absensi Murid, dan Nilai.
// penjelasan: Sistem akan dibuat hanya boleh ada satu semester aktif.

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
     * penjelasan: Method ini membuat tabel semesters.
     */
    public function up(): void
    {
        Schema::create('semesters', function (Blueprint $table) {
            $table->id();
            // penjelasan: Kolom id adalah primary key tabel semesters.

            $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajarans')->restrictOnDelete();
            // penjelasan: tahun_ajaran_id menghubungkan semester ke tabel tahun_ajarans.
            // penjelasan: restrictOnDelete mencegah tahun ajaran dihapus jika masih memiliki semester.

            $table->enum('nama_semester', ['ganjil', 'genap']);
            // penjelasan: nama_semester menyimpan pilihan semester, yaitu ganjil atau genap.

            $table->date('tanggal_mulai')->nullable();
            // penjelasan: tanggal_mulai menyimpan tanggal mulai semester.

            $table->date('tanggal_selesai')->nullable();
            // penjelasan: tanggal_selesai menyimpan tanggal selesai semester.

            $table->enum('status', ['aktif', 'nonaktif'])->default('nonaktif');
            // penjelasan: status digunakan untuk menentukan semester aktif.
            // penjelasan: Hanya satu semester yang boleh aktif pada sistem.

            $table->timestamps();
            // penjelasan: timestamps membuat kolom created_at dan updated_at otomatis.

            $table->unique(['tahun_ajaran_id', 'nama_semester']);
            // penjelasan: Kombinasi tahun_ajaran_id dan nama_semester harus unik.
            // penjelasan: Dalam satu tahun ajaran tidak boleh ada dua semester ganjil atau dua semester genap.
        });
    }

    /**
     * penjelasan: Method down() dijalankan saat rollback migration.
     * penjelasan: Method ini menghapus tabel semesters.
     */
    public function down(): void
    {
        Schema::dropIfExists('semesters');
    }
};
