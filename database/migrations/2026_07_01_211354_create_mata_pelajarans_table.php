<?php

// penjelasan: File migration ini digunakan untuk membuat tabel mata_pelajarans.
// penjelasan: Tabel mata_pelajarans menyimpan data mata pelajaran sekolah.
// penjelasan: Data mata pelajaran nanti digunakan pada modul Jadwal Pelajaran dan Nilai.
// penjelasan: Contoh data mata pelajaran adalah Matematika, IPA, Bahasa Indonesia, PAI, PJOK, dan sebagainya.

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
     * penjelasan: Isi method ini membuat tabel mata_pelajarans.
     */
    public function up(): void
    {
        Schema::create('mata_pelajarans', function (Blueprint $table) {
            $table->id();
            // penjelasan: Kolom id adalah primary key tabel mata_pelajarans.

            $table->string('kode_mapel')->unique();
            // penjelasan: kode_mapel menyimpan kode singkat mata pelajaran.
            // penjelasan: Contoh kode_mapel: MTK, IPA, IPS, BIN, BIG, PAI.
            // penjelasan: unique digunakan agar kode mata pelajaran tidak dobel.

            $table->string('nama_mapel');
            // penjelasan: nama_mapel menyimpan nama lengkap mata pelajaran.
            // penjelasan: Contoh: Matematika, Ilmu Pengetahuan Alam, Bahasa Indonesia.

            $table->enum('kelompok', ['umum', 'muatan_lokal', 'ekstrakurikuler'])->default('umum');
            // penjelasan: kelompok digunakan untuk membedakan jenis mata pelajaran.
            // penjelasan: umum digunakan untuk pelajaran umum sekolah.
            // penjelasan: muatan_lokal digunakan untuk pelajaran lokal daerah/sekolah.
            // penjelasan: ekstrakurikuler digunakan jika sekolah ingin mencatat kegiatan ekstra sebagai mapel/kegiatan akademik tambahan.

            $table->text('deskripsi')->nullable();
            // penjelasan: deskripsi menyimpan keterangan tambahan tentang mata pelajaran.
            // penjelasan: nullable artinya kolom ini boleh kosong.

            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            // penjelasan: status digunakan agar mata pelajaran bisa dinonaktifkan tanpa dihapus permanen.
            // penjelasan: Ini penting agar data lama pada jadwal dan nilai tetap aman.

            $table->timestamps();
            // penjelasan: timestamps membuat kolom created_at dan updated_at otomatis.
        });
    }

    /**
     * penjelasan: Method down() dijalankan saat rollback migration.
     * penjelasan: Isi method ini menghapus tabel mata_pelajarans.
     */
    public function down(): void
    {
        Schema::dropIfExists('mata_pelajarans');
    }
};
