<?php

// penjelasan: File migration ini digunakan untuk membuat tabel jadwal_pelajarans.
// penjelasan: Tabel ini menyimpan jadwal mengajar guru untuk setiap kelas.
// penjelasan: Jadwal pelajaran terhubung ke tahun ajaran, semester, kelas, mata pelajaran, dan guru.
// penjelasan: Data ini nanti dipakai oleh modul Absensi Murid, Jadwal Mengajar Guru, dan Nilai.

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
     * penjelasan: Method ini membuat tabel jadwal_pelajarans.
     */
    public function up(): void
    {
        Schema::create('jadwal_pelajarans', function (Blueprint $table) {
            $table->id();
            // penjelasan: Kolom id adalah primary key tabel jadwal_pelajarans.

            $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajarans')->restrictOnDelete();
            // penjelasan: tahun_ajaran_id menghubungkan jadwal ke tabel tahun_ajarans.
            // penjelasan: restrictOnDelete mencegah tahun ajaran dihapus jika masih dipakai jadwal.

            $table->foreignId('semester_id')->constrained('semesters')->restrictOnDelete();
            // penjelasan: semester_id menghubungkan jadwal ke tabel semesters.
            // penjelasan: Jadwal pelajaran harus berada pada semester tertentu.

            $table->foreignId('kelas_id')->constrained('kelas')->restrictOnDelete();
            // penjelasan: kelas_id menghubungkan jadwal ke tabel kelas.
            // penjelasan: Ini menunjukkan kelas mana yang menerima pelajaran.

            $table->foreignId('mata_pelajaran_id')->constrained('mata_pelajarans')->restrictOnDelete();
            // penjelasan: mata_pelajaran_id menghubungkan jadwal ke tabel mata_pelajarans.
            // penjelasan: Ini menunjukkan mata pelajaran apa yang diajarkan.

            $table->foreignId('guru_id')->constrained('pegawais')->restrictOnDelete();
            // penjelasan: guru_id menghubungkan jadwal ke tabel pegawais.
            // penjelasan: Guru pengajar diambil dari pegawai dengan jenis_pegawai guru.

            $table->enum('hari', ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu']);
            // penjelasan: hari menyimpan hari pelaksanaan jadwal pelajaran.
            // penjelasan: Pilihan dibuat Senin sampai Sabtu sesuai kebutuhan sekolah.

            $table->time('jam_mulai');
            // penjelasan: jam_mulai menyimpan waktu mulai pelajaran.

            $table->time('jam_selesai');
            // penjelasan: jam_selesai menyimpan waktu selesai pelajaran.
            // penjelasan: Di controller nanti divalidasi agar jam selesai lebih besar dari jam mulai.

            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            // penjelasan: status digunakan agar jadwal bisa dinonaktifkan tanpa dihapus.
            // penjelasan: Jadwal nonaktif tidak dihitung sebagai bentrok pada validasi jadwal aktif.

            $table->timestamps();
            // penjelasan: timestamps membuat kolom created_at dan updated_at otomatis.

            $table->index(['semester_id', 'kelas_id', 'hari']);
            // penjelasan: Index ini mempercepat pencarian jadwal berdasarkan semester, kelas, dan hari.

            $table->index(['semester_id', 'guru_id', 'hari']);
            // penjelasan: Index ini mempercepat pengecekan bentrok jadwal guru.
        });
    }

    /**
     * penjelasan: Method down() dijalankan saat rollback migration.
     * penjelasan: Method ini menghapus tabel jadwal_pelajarans.
     */
    public function down(): void
    {
        Schema::dropIfExists('jadwal_pelajarans');
    }
};
