<?php

// penjelasan: File ini adalah Model WaliMurid.
// penjelasan: Model ini digunakan Laravel untuk berhubungan dengan tabel wali_murids.
// penjelasan: Data wali murid dihubungkan ke data murid.
// penjelasan: Nomor WhatsApp pada model ini nanti digunakan untuk notifikasi Fonnte.

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

    // penjelasan: Relasi ini menghubungkan wali murid ke banyak murid.
    // penjelasan: Satu wali murid bisa memiliki lebih dari satu anak.
    // penjelasan: Dipanggil misalnya $waliMurid->murids.
    public function murids()
    {
        return $this->hasMany(Murid::class, 'wali_murid_id');
    }

    // penjelasan: Fungsi ini mengecek apakah data wali murid masih aktif.
    // penjelasan: Fungsi ini dipakai saat memilih wali pada form Data Murid.
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
