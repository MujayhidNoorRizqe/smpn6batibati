<?php

// penjelasan: File migration ini digunakan untuk membuat tabel murids.
// penjelasan: Tabel murids menyimpan data siswa/murid SMPN 6 Bati-Bati.
// penjelasan: Tabel ini terhubung ke tabel kelas melalui kelas_id.
// penjelasan: Tabel ini juga terhubung ke tabel wali_murids melalui wali_murid_id.
// penjelasan: Data murid nanti akan dipakai untuk absensi murid, nilai, laporan, dan notifikasi WhatsApp wali murid.

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
     * penjelasan: Isi method ini membuat tabel murids.
     */
    public function up(): void
    {
        Schema::create('murids', function (Blueprint $table) {
            $table->id();
            // penjelasan: Kolom id adalah primary key tabel murids.

            $table->foreignId('kelas_id')->constrained('kelas')->restrictOnDelete();
            // penjelasan: kelas_id menghubungkan murid dengan tabel kelas.
            // penjelasan: Setiap murid wajib masuk ke satu kelas.
            // penjelasan: constrained('kelas') membuat foreign key ke tabel kelas.
            // penjelasan: restrictOnDelete mencegah kelas dihapus jika masih punya murid.

            $table->foreignId('wali_murid_id')->nullable()->constrained('wali_murids')->nullOnDelete();
            // penjelasan: wali_murid_id menghubungkan murid dengan tabel wali_murids.
            // penjelasan: nullable dibuat sebagai pengaman jika suatu saat data wali belum lengkap.
            // penjelasan: nullOnDelete artinya jika data wali murid dihapus, wali_murid_id pada murid menjadi null.
            // penjelasan: Di form aplikasi nanti tetap kita wajibkan memilih wali murid agar data rapi.

            $table->string('nis')->nullable()->unique();
            // penjelasan: nis menyimpan Nomor Induk Siswa.
            // penjelasan: nullable karena bisa saja belum diisi.
            // penjelasan: unique agar NIS tidak dobel.

            $table->string('nisn')->nullable()->unique();
            // penjelasan: nisn menyimpan Nomor Induk Siswa Nasional.
            // penjelasan: nullable karena bisa saja belum diisi.
            // penjelasan: unique agar NISN tidak dobel.

            $table->string('nama_murid');
            // penjelasan: nama_murid menyimpan nama lengkap murid.

            $table->enum('jenis_kelamin', ['L', 'P']);
            // penjelasan: jenis_kelamin menyimpan L untuk laki-laki dan P untuk perempuan.

            $table->string('tempat_lahir')->nullable();
            // penjelasan: tempat_lahir menyimpan tempat lahir murid.

            $table->date('tanggal_lahir')->nullable();
            // penjelasan: tanggal_lahir menyimpan tanggal lahir murid.

            $table->string('agama')->nullable();
            // penjelasan: agama menyimpan agama murid jika diperlukan.

            $table->text('alamat')->nullable();
            // penjelasan: alamat menyimpan alamat murid.

            $table->string('foto')->nullable();
            // penjelasan: foto menyimpan path foto murid.
            // penjelasan: File foto nanti disimpan di storage/app/public/foto/murid.

            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            // penjelasan: status digunakan agar murid bisa dinonaktifkan tanpa menghapus data permanen.

            $table->timestamps();
            // penjelasan: timestamps membuat kolom created_at dan updated_at otomatis.
        });
    }

    /**
     * penjelasan: Method down() dijalankan saat rollback migration.
     * penjelasan: Isi method ini menghapus tabel murids.
     */
    public function down(): void
    {
        Schema::dropIfExists('murids');
    }
};
