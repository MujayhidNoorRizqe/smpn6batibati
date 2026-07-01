<?php

// penjelasan: File ini adalah Model MataPelajaran.
// penjelasan: Model ini digunakan Laravel untuk berhubungan dengan tabel mata_pelajarans.
// penjelasan: Data mata pelajaran nanti dipakai pada modul Jadwal Pelajaran dan Nilai Murid.

namespace App\Models;

// penjelasan: HasFactory digunakan agar model bisa dipakai untuk factory atau data dummy/testing.
use Illuminate\Database\Eloquent\Factories\HasFactory;

// penjelasan: Model adalah class dasar Laravel untuk model database.
use Illuminate\Database\Eloquent\Model;

class MataPelajaran extends Model
{
    use HasFactory;

    // penjelasan: Nama tabel diset manual agar jelas bahwa model ini memakai tabel mata_pelajarans.
    // penjelasan: Sebenarnya Laravel bisa menebak nama tabel, tetapi kita tetap tulis agar lebih mudah dipahami.
    protected $table = 'mata_pelajarans';

    /**
     * penjelasan: Fillable adalah daftar kolom yang boleh diisi secara massal dari controller.
     * penjelasan: Kolom ini harus sama dengan kolom yang tersedia pada tabel mata_pelajarans.
     */
    protected $fillable = [
        'kode_mapel',
        'nama_mapel',
        'kelompok',
        'deskripsi',
        'status',
    ];

    // penjelasan: Fungsi ini mengecek apakah mata pelajaran masih aktif.
    // penjelasan: Fungsi ini nanti berguna saat memilih mata pelajaran pada form jadwal pelajaran dan nilai.
    public function isAktif(): bool
    {
        return $this->status === 'aktif';
    }

    // penjelasan: Fungsi ini mengubah format kelompok agar lebih enak ditampilkan di view.
    // penjelasan: Contoh: muatan_lokal menjadi Muatan Lokal.
    public function getKelompokLabelAttribute(): string
    {
        return match ($this->kelompok) {
            'umum' => 'Umum',
            'muatan_lokal' => 'Muatan Lokal',
            'ekstrakurikuler' => 'Ekstrakurikuler',
            default => '-',
        };
    }
}
