<?php

// penjelasan: Migration ini membuat tabel pengajuan_absensi_pegawais.
// penjelasan: Tabel ini menyimpan pengajuan absensi pegawai seperti dinas, sakit, dan izin.
// penjelasan: Pengajuan dibuat oleh guru/staff dan diproses oleh admin/super admin.
// penjelasan: Dinas dan sakit wajib memiliki bukti file, sedangkan izin cukup alasan.
// penjelasan: Nama index dibuat pendek agar tidak error di MySQL karena batas panjang nama index.

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * penjelasan: Method up dijalankan saat php artisan migrate.
     * penjelasan: Method ini membuat struktur tabel pengajuan_absensi_pegawais.
     */
    public function up(): void
    {
        Schema::create('pengajuan_absensi_pegawais', function (Blueprint $table) {
            $table->id();

            // penjelasan: pegawai_id menghubungkan pengajuan dengan data pegawai.
            $table->foreignId('pegawai_id')
                ->constrained('pegawais')
                ->cascadeOnDelete();

            // penjelasan: Jenis pengajuan hanya boleh dinas, sakit, atau izin.
            $table->enum('jenis_pengajuan', ['dinas', 'sakit', 'izin']);

            // penjelasan: Tanggal mulai dan selesai dipilih oleh pegawai sesuai kebutuhan pengajuan.
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');

            // penjelasan: Judul pengajuan wajib untuk dinas lewat validasi controller, tetapi nullable di database agar fleksibel.
            $table->string('judul_pengajuan')->nullable();

            // penjelasan: Lokasi kegiatan digunakan untuk pengajuan dinas.
            $table->string('lokasi_kegiatan')->nullable();

            // penjelasan: Alasan wajib diisi untuk semua jenis pengajuan.
            $table->text('alasan');

            // penjelasan: Bukti file digunakan untuk dinas dan sakit.
            $table->string('bukti_file')->nullable();

            // penjelasan: Status pengajuan menunjukkan proses persetujuan.
            $table->enum('status_pengajuan', ['menunggu', 'disetujui', 'ditolak', 'dibatalkan'])
                ->default('menunggu');

            // penjelasan: disetujui_oleh menyimpan user admin/super admin yang memproses pengajuan.
            $table->foreignId('disetujui_oleh')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // penjelasan: Waktu ketika pengajuan disetujui atau ditolak.
            $table->timestamp('disetujui_pada')->nullable();

            // penjelasan: Catatan admin digunakan saat approve/reject pengajuan.
            $table->text('catatan_admin')->nullable();

            $table->timestamps();

            // penjelasan: Nama index dibuat pendek agar tidak melewati batas panjang identifier MySQL.
            $table->index(['pegawai_id', 'tanggal_mulai', 'tanggal_selesai'], 'peng_abs_peg_tgl_idx');

            // penjelasan: Index ini mempercepat filter status pengajuan.
            $table->index('status_pengajuan', 'peng_abs_status_idx');

            // penjelasan: Index ini mempercepat filter jenis pengajuan.
            $table->index('jenis_pengajuan', 'peng_abs_jenis_idx');
        });
    }

    /**
     * penjelasan: Method down dijalankan saat rollback migration.
     * penjelasan: Method ini menghapus tabel pengajuan_absensi_pegawais.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengajuan_absensi_pegawais');
    }
};
