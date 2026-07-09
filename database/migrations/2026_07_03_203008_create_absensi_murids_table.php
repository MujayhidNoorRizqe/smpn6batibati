<?php

// penjelasan: Migration ini membuat tabel absensi_murids.
// penjelasan: Tabel ini menyimpan data absensi murid berdasarkan jadwal pelajaran.
// penjelasan: Absensi murid diinput oleh guru sesuai jadwal mengajar hari ini.
// penjelasan: Tanggal absensi dibuat otomatis dari sistem, bukan dipilih manual oleh guru.
// penjelasan: Nama index dibuat pendek agar aman dari batas panjang identifier MySQL.
// penjelasan: Status absensi murid terdiri dari hadir, izin, sakit, alpha, dan terlambat.

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * penjelasan: Method up dijalankan saat php artisan migrate.
     * penjelasan: Method ini membuat struktur tabel absensi_murids.
     */
    public function up(): void
    {
        Schema::create('absensi_murids', function (Blueprint $table) {
            $table->id();

            // penjelasan: murid_id menghubungkan absensi dengan data murid.
            $table->foreignId('murid_id')
                ->constrained('murids')
                ->cascadeOnDelete();

            // penjelasan: jadwal_pelajaran_id menghubungkan absensi dengan jadwal pelajaran.
            $table->foreignId('jadwal_pelajaran_id')
                ->constrained('jadwal_pelajarans')
                ->cascadeOnDelete();

            // penjelasan: guru_id menyimpan pegawai guru yang melakukan input absensi.
            $table->foreignId('guru_id')
                ->constrained('pegawais')
                ->cascadeOnDelete();

            // penjelasan: kelas_id disimpan agar rekap lebih mudah difilter.
            $table->foreignId('kelas_id')
                ->constrained('kelas')
                ->cascadeOnDelete();

            // penjelasan: mata_pelajaran_id disimpan agar rekap per mata pelajaran mudah dibuat.
            $table->foreignId('mata_pelajaran_id')
                ->constrained('mata_pelajarans')
                ->cascadeOnDelete();

            // penjelasan: tanggal_absen otomatis memakai tanggal hari ini dari sistem.
            $table->date('tanggal_absen');

            // penjelasan: Status absensi murid yang dipakai pada sistem.
            // penjelasan: Terlambat ditambahkan agar sesuai kebutuhan final absensi murid.
            $table->enum('status_absen', ['hadir', 'izin', 'sakit', 'alpha', 'terlambat']);

            // penjelasan: Keterangan opsional, misalnya alasan izin/sakit jika diketahui guru.
            $table->string('keterangan')->nullable();

            // penjelasan: created_by menyimpan user guru yang melakukan input dari akun login.
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            // penjelasan: Satu murid hanya boleh punya satu absensi pada jadwal dan tanggal yang sama.
            $table->unique(
                ['murid_id', 'jadwal_pelajaran_id', 'tanggal_absen'],
                'abs_murid_unique'
            );

            // penjelasan: Index pendek untuk mempercepat filter rekap absensi murid.
            $table->index(['tanggal_absen', 'kelas_id'], 'abs_murid_tgl_kelas_idx');
            $table->index(['guru_id', 'tanggal_absen'], 'abs_murid_guru_tgl_idx');
            $table->index('status_absen', 'abs_murid_status_idx');
        });
    }

    /**
     * penjelasan: Method down dijalankan saat rollback migration.
     * penjelasan: Method ini menghapus tabel absensi_murids.
     */
    public function down(): void
    {
        Schema::dropIfExists('absensi_murids');
    }
};
