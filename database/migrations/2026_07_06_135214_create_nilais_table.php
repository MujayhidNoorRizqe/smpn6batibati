<?php

// penjelasan: File migration ini membuat tabel nilais.
// penjelasan: Tabel nilais digunakan untuk menyimpan nilai ujian semester murid.
// penjelasan: Nilai yang disimpan hanya nilai ujian semester ganjil dan genap, bukan nilai tugas atau nilai harian.

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * penjelasan: Fungsi up dijalankan saat perintah php artisan migrate.
     * penjelasan: Fungsi ini membuat tabel nilais beserta relasi ke murid, guru, kelas, mapel, tahun ajaran, dan semester.
     */
    public function up(): void
    {
        Schema::create('nilais', function (Blueprint $table) {
            $table->id();

            $table->foreignId('murid_id')
                ->constrained('murids')
                ->cascadeOnDelete();

            $table->foreignId('pegawai_id')
                ->constrained('pegawais')
                ->cascadeOnDelete();

            $table->foreignId('kelas_id')
                ->constrained('kelas')
                ->cascadeOnDelete();

            $table->foreignId('mata_pelajaran_id')
                ->constrained('mata_pelajarans')
                ->cascadeOnDelete();

            $table->foreignId('tahun_ajaran_id')
                ->constrained('tahun_ajarans')
                ->cascadeOnDelete();

            $table->foreignId('semester_id')
                ->constrained('semesters')
                ->cascadeOnDelete();

            // penjelasan: nilai_ujian adalah nilai ujian semester, skala 0 sampai 100.
            $table->unsignedTinyInteger('nilai_ujian');

            // penjelasan: keterangan bersifat opsional untuk catatan guru.
            $table->text('keterangan')->nullable();

            $table->timestamps();

            // penjelasan: Aturan unik agar satu murid hanya punya satu nilai untuk mapel, semester, dan tahun ajaran yang sama.
            $table->unique(
                ['murid_id', 'mata_pelajaran_id', 'semester_id', 'tahun_ajaran_id'],
                'nilai_unik_per_murid_mapel_semester'
            );
        });
    }

    /**
     * penjelasan: Fungsi down dijalankan saat migration di-rollback.
     */
    public function down(): void
    {
        Schema::dropIfExists('nilais');
    }
};
