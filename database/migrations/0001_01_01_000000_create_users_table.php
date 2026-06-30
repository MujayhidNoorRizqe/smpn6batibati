<?php

// penjelasan: File migration ini digunakan untuk membuat tabel users, password_reset_tokens, dan sessions.
// penjelasan: Migration adalah file Laravel untuk membuat atau mengubah struktur tabel database.

// penjelasan: Migration adalah class bawaan Laravel untuk menjalankan perubahan database.
use Illuminate\Database\Migrations\Migration;

// penjelasan: Blueprint digunakan untuk mendefinisikan kolom-kolom tabel.
use Illuminate\Database\Schema\Blueprint;

// penjelasan: Schema digunakan untuk membuat dan menghapus tabel.
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * penjelasan: Method up() dijalankan saat perintah php artisan migrate.
     * penjelasan: Isi method ini adalah proses pembuatan tabel.
     */
    public function up(): void
    {
        // penjelasan: Membuat tabel users untuk menyimpan data akun login.
        Schema::create('users', function (Blueprint $table) {
            $table->id(); // penjelasan: Membuat kolom id sebagai primary key.

            $table->string('name'); // penjelasan: Menyimpan nama user.
            $table->string('email')->unique(); // penjelasan: Menyimpan email login dan harus unik.
            $table->timestamp('email_verified_at')->nullable(); // penjelasan: Menyimpan waktu verifikasi email, boleh kosong.

            $table->string('password'); // penjelasan: Menyimpan password dalam bentuk hash.

            $table->enum('role', [
                'super_admin',
                'admin',
                'guru',
                'staff',
            ])->default('staff');
            // penjelasan: Kolom role dipakai untuk membedakan hak akses user.
            // penjelasan: Nilainya hanya boleh super_admin, admin, guru, atau staff.

            $table->enum('status', [
                'aktif',
                'nonaktif',
            ])->default('aktif');
            // penjelasan: Kolom status dipakai untuk menentukan akun bisa login atau tidak.

            $table->timestamp('last_login_at')->nullable();
            // penjelasan: Menyimpan waktu terakhir user berhasil login.

            $table->rememberToken(); // penjelasan: Token untuk fitur remember me saat login.
            $table->timestamps(); // penjelasan: Membuat kolom created_at dan updated_at.
        });

        // penjelasan: Tabel ini digunakan Laravel untuk fitur reset password.
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary(); // penjelasan: Email user yang meminta reset password.
            $table->string('token'); // penjelasan: Token reset password.
            $table->timestamp('created_at')->nullable(); // penjelasan: Waktu token dibuat.
        });

        // penjelasan: Tabel ini digunakan jika SESSION_DRIVER=database.
        // penjelasan: Tabel sessions menyimpan data session login user.
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary(); // penjelasan: ID session.
            $table->foreignId('user_id')->nullable()->index(); // penjelasan: ID user yang sedang login, boleh kosong.
            $table->string('ip_address', 45)->nullable(); // penjelasan: Menyimpan alamat IP user.
            $table->text('user_agent')->nullable(); // penjelasan: Menyimpan informasi browser user.
            $table->longText('payload'); // penjelasan: Isi data session.
            $table->integer('last_activity')->index(); // penjelasan: Waktu aktivitas terakhir session.
        });
    }

    /**
     * penjelasan: Method down() dijalankan saat rollback migration.
     * penjelasan: Isi method ini adalah proses menghapus tabel yang dibuat di method up().
     */
    public function down(): void
    {
        // penjelasan: Urutan penghapusan dibuat dari tabel pendukung dulu, lalu users.
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};
