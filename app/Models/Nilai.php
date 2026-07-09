<?php

// penjelasan: File ini adalah Model Nilai.
// penjelasan: Model Nilai digunakan Laravel untuk berhubungan dengan tabel nilais.
// penjelasan: Data nilai yang disimpan adalah nilai ujian semester ganjil dan genap.
// penjelasan: Model ini juga menyediakan predikat otomatis seperti format rapor.

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nilai extends Model
{
    use HasFactory;

    /**
     * penjelasan: Fillable adalah daftar kolom yang boleh diisi dari controller.
     * penjelasan: Kolom ini harus sesuai dengan struktur tabel nilais.
     */
    protected $fillable = [
        'murid_id',
        'pegawai_id',
        'kelas_id',
        'mata_pelajaran_id',
        'tahun_ajaran_id',
        'semester_id',
        'nilai_ujian',
        'keterangan',
    ];

    /**
     * penjelasan: Casts membuat nilai_ujian otomatis dibaca sebagai integer.
     */
    protected function casts(): array
    {
        return [
            'nilai_ujian' => 'integer',
        ];
    }

    // penjelasan: Relasi ini menghubungkan nilai ke murid.
    // penjelasan: Dipanggil misalnya $nilai->murid.
    public function murid()
    {
        return $this->belongsTo(Murid::class, 'murid_id');
    }

    // penjelasan: Relasi ini menghubungkan nilai ke pegawai/guru yang menginput nilai.
    // penjelasan: Dipanggil misalnya $nilai->pegawai.
    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id');
    }

    // penjelasan: Relasi ini menghubungkan nilai ke kelas.
    // penjelasan: Dipanggil misalnya $nilai->kelas.
    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    // penjelasan: Relasi ini menghubungkan nilai ke mata pelajaran.
    // penjelasan: Dipanggil misalnya $nilai->mataPelajaran.
    public function mataPelajaran()
    {
        return $this->belongsTo(MataPelajaran::class, 'mata_pelajaran_id');
    }

    // penjelasan: Relasi ini menghubungkan nilai ke tahun ajaran.
    // penjelasan: Dipanggil misalnya $nilai->tahunAjaran.
    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class, 'tahun_ajaran_id');
    }

    // penjelasan: Relasi ini menghubungkan nilai ke semester.
    // penjelasan: Dipanggil misalnya $nilai->semester.
    public function semester()
    {
        return $this->belongsTo(Semester::class, 'semester_id');
    }

    // penjelasan: Accessor ini membuat predikat otomatis untuk tampilan rapor.
    // penjelasan: Dipanggil misalnya $nilai->predikat.
    public function getPredikatAttribute(): string
    {
        if ($this->nilai_ujian >= 90) {
            return 'A';
        }

        if ($this->nilai_ujian >= 80) {
            return 'B';
        }

        if ($this->nilai_ujian >= 70) {
            return 'C';
        }

        return 'D';
    }

    // penjelasan: Accessor ini membuat keterangan predikat otomatis.
    // penjelasan: Dipanggil misalnya $nilai->keterangan_predikat.
    public function getKeteranganPredikatAttribute(): string
    {
        if ($this->nilai_ujian >= 90) {
            return 'Sangat Baik';
        }

        if ($this->nilai_ujian >= 80) {
            return 'Baik';
        }

        if ($this->nilai_ujian >= 70) {
            return 'Cukup';
        }

        return 'Perlu Bimbingan';
    }
}
