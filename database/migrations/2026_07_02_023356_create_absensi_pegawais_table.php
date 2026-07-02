<?php

// penjelasan: Migration ini membuat tabel absensi_pegawais.
// penjelasan: Tabel ini menyimpan absensi resmi guru/staff.
// penjelasan: Untuk dinas, sakit, dan izin, data absensi dibuat setelah pengajuan disetujui admin/super admin.

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * penjelasan: Method up() dijalankan saat php artisan migrate.
     */
    public function up(): void
    {
        Schema::create('absensi_pegawais', function (Blueprint $table) {
            $table->id();

            $table->foreignId('pegawai_id')->constrained('pegawais')->restrictOnDelete();

            $table->date('tanggal_absen');

            $table->time('jam_masuk')->nullable();
            $table->time('jam_pulang')->nullable();

            $table->enum('status_absen', ['hadir', 'terlambat', 'dinas', 'izin', 'sakit', 'alpha'])->default('alpha');

            $table->enum('metode_absen', ['lokasi', 'wifi', 'pengajuan', 'manual'])->default('manual');

            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();

            $table->text('keterangan')->nullable();

            $table->foreignId('pengajuan_absensi_pegawai_id')
                ->nullable()
                ->constrained('pengajuan_absensi_pegawais')
                ->nullOnDelete();

            $table->timestamps();

            $table->unique(['pegawai_id', 'tanggal_absen']);
            $table->index(['tanggal_absen', 'status_absen']);
        });
    }

    /**
     * penjelasan: Method down() dijalankan saat rollback migration.
     */
    public function down(): void
    {
        Schema::dropIfExists('absensi_pegawais');
    }
};
