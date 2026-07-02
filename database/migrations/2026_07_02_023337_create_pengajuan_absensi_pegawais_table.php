<?php

// penjelasan: Migration ini membuat tabel pengajuan_absensi_pegawais.
// penjelasan: Tabel ini menyimpan pengajuan dinas, sakit, dan izin dari guru/staff.
// penjelasan: Pengajuan dinas dan sakit wajib memiliki bukti file/foto.
// penjelasan: Pengajuan izin cukup menyertakan alasan izin.
// penjelasan: Sebelum disetujui admin/super admin, pengajuan belum dihitung sebagai absensi resmi.

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
        Schema::create('pengajuan_absensi_pegawais', function (Blueprint $table) {
            $table->id();

            $table->foreignId('pegawai_id')->constrained('pegawais')->restrictOnDelete();
            $table->enum('jenis_pengajuan', ['dinas', 'sakit', 'izin']);

            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');

            $table->string('judul_pengajuan')->nullable();
            $table->string('lokasi_kegiatan')->nullable();
            $table->text('alasan');

            $table->string('bukti_file')->nullable();

            $table->enum('status_pengajuan', ['menunggu', 'disetujui', 'ditolak', 'dibatalkan'])->default('menunggu');

            $table->foreignId('disetujui_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('disetujui_pada')->nullable();
            $table->text('catatan_admin')->nullable();

            $table->timestamps();

            $table->index(['pegawai_id', 'tanggal_mulai', 'tanggal_selesai']);
            $table->index(['jenis_pengajuan', 'status_pengajuan']);
        });
    }

    /**
     * penjelasan: Method down() dijalankan saat rollback migration.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengajuan_absensi_pegawais');
    }
};
