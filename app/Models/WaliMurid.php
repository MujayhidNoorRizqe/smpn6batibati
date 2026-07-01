<?php

// penjelasan: File ini adalah Model WaliMurid.
// penjelasan: Model ini digunakan Laravel untuk berhubungan dengan tabel wali_murids.
// penjelasan: Data wali murid nanti akan dihubungkan ke data murid.
// penjelasan: Untuk sekarang model ini fokus pada penyimpanan data wali/orang tua.

namespace App\Models;

// penjelasan: HasFactory digunakan agar model bisa dipakai untuk factory atau data dummy/testing.
use Illuminate\Database\Eloquent\Factories\HasFactory;

// penjelasan: Model adalah class dasar Laravel untuk model database.
use Illuminate\Database\Eloquent\Model;

class WaliMurid extends Model
{
    use HasFactory;

    /**
     * penjelasan: Fillable adalah daftar kolom yang boleh diisi secara massal dari controller.
     * penjelasan: Kolom ini harus sesuai dengan kolom yang ada di tabel wali_murids.
     * penjelasan: Jika kolom tidak dimasukkan ke fillable, Laravel tidak akan mengizinkan mass assignment untuk kolom tersebut.
     */
    protected $fillable = [
        'nama_wali',
        'nik',
        'hubungan',
        'pekerjaan',
        'no_hp',
        'no_whatsapp',
        'alamat',
        'status',
    ];

    // penjelasan: Fungsi ini mengecek apakah data wali murid masih aktif.
    // penjelasan: Fungsi ini nanti bisa dipakai saat memilih wali murid pada form Data Murid.
    public function isAktif(): bool
    {
        return $this->status === 'aktif';
    }

    // penjelasan: Fungsi ini mengecek apakah wali murid memiliki nomor WhatsApp.
    // penjelasan: Fungsi ini nanti berguna sebelum sistem mengirim notifikasi Fonnte.
    public function hasWhatsapp(): bool
    {
        return ! empty($this->no_whatsapp);
    }
}
