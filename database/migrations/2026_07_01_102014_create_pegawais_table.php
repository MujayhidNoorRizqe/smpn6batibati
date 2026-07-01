<?php

// penjelasan: File migration ini digunakan untuk membuat tabel pegawais.
// penjelasan: Tabel pegawais menyimpan data guru dan staff.
// penjelasan: Tabel ini terhubung ke tabel users melalui kolom user_id.
// penjelasan: user_id boleh kosong karena ada kemungkinan data pegawai dibuat sebelum akun login dihubungkan.

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
     * penjelasan: Isi method ini membuat tabel pegawais.
     */
    public function up(): void
    {
        Schema::create('pegawais', function (Blueprint $table) {
            $table->id();
            // penjelasan: Kolom id adalah primary key tabel pegawais.

            $table->foreignId('user_id')->nullable()->unique()->constrained('users')->nullOnDelete();
            // penjelasan: user_id menghubungkan pegawai dengan akun login pada tabel users.
            // penjelasan: nullable artinya boleh kosong.
            // penjelasan: unique artinya satu akun user hanya boleh terhubung ke satu data pegawai.
            // penjelasan: constrained('users') membuat relasi foreign key ke tabel users.
            // penjelasan: nullOnDelete artinya jika akun user dihapus, user_id pada pegawai menjadi null.

            $table->string('nip')->nullable()->unique();
            // penjelasan: nip menyimpan Nomor Induk Pegawai.
            // penjelasan: nullable karena tidak semua staff/guru mungkin punya NIP.
            // penjelasan: unique agar NIP tidak dobel.

            $table->string('nama_pegawai');
            // penjelasan: nama_pegawai menyimpan nama guru atau staff.

            $table->enum('jenis_pegawai', ['guru', 'staff']);
            // penjelasan: jenis_pegawai menentukan apakah pegawai adalah guru atau staff.
            // penjelasan: Guru nanti bisa punya jadwal mengajar, absen murid, dan input nilai.
            // penjelasan: Staff hanya untuk absensi dan pengajuan dinas.

            $table->string('jabatan')->nullable();
            // penjelasan: jabatan menyimpan posisi pegawai, misalnya Guru Matematika, Staff TU, Kepala Sekolah.

            $table->enum('jenis_kelamin', ['L', 'P'])->nullable();
            // penjelasan: L berarti laki-laki dan P berarti perempuan.

            $table->string('no_hp')->nullable();
            // penjelasan: no_hp menyimpan nomor HP pegawai.

            $table->text('alamat')->nullable();
            // penjelasan: alamat menyimpan alamat pegawai.

            $table->string('foto')->nullable();
            // penjelasan: foto menyimpan path file foto pegawai.
            // penjelasan: File fotonya nanti disimpan di storage/app/public/foto/pegawai.

            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            // penjelasan: status menentukan apakah pegawai masih aktif atau tidak.

            $table->timestamps();
            // penjelasan: timestamps membuat kolom created_at dan updated_at otomatis.
        });
    }

    /**
     * penjelasan: Method down() dijalankan saat rollback migration.
     * penjelasan: Method ini menghapus tabel pegawais.
     */
    public function down(): void
    {
        Schema::dropIfExists('pegawais');
    }
};
