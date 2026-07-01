<?php

// penjelasan: File ini adalah Model JadwalPelajaran.
// penjelasan: Model ini digunakan Laravel untuk berhubungan dengan tabel jadwal_pelajarans.
// penjelasan: Model ini memiliki relasi ke TahunAjaran, Semester, Kelas, MataPelajaran, dan Pegawai sebagai guru.

namespace App\Models;

// penjelasan: HasFactory digunakan agar model bisa dipakai untuk factory atau data dummy/testing.
use Illuminate\Database\Eloquent\Factories\HasFactory;

// penjelasan: Model adalah class dasar Laravel untuk model database.
use Illuminate\Database\Eloquent\Model;

class JadwalPelajaran extends Model
{
    use HasFactory;

    // penjelasan: Nama tabel diset manual agar jelas bahwa model ini memakai tabel jadwal_pelajarans.
    protected $table = 'jadwal_pelajarans';

    /**
     * penjelasan: Fillable adalah daftar kolom yang boleh diisi dari controller.
     * penjelasan: Kolom ini harus sesuai dengan struktur tabel jadwal_pelajarans.
     */
    protected $fillable = [
        'tahun_ajaran_id',
        'semester_id',
        'kelas_id',
        'mata_pelajaran_id',
        'guru_id',
        'hari',
        'jam_mulai',
        'jam_selesai',
        'status',
    ];

    // penjelasan: Relasi ini menghubungkan jadwal ke tahun ajaran.
    // penjelasan: Dipanggil misalnya $jadwalPelajaran->tahunAjaran.
    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class);
    }

    // penjelasan: Relasi ini menghubungkan jadwal ke semester.
    // penjelasan: Dipanggil misalnya $jadwalPelajaran->semester.
    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    // penjelasan: Relasi ini menghubungkan jadwal ke kelas.
    // penjelasan: Dipanggil misalnya $jadwalPelajaran->kelas.
    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    // penjelasan: Relasi ini menghubungkan jadwal ke mata pelajaran.
    // penjelasan: Dipanggil misalnya $jadwalPelajaran->mataPelajaran.
    public function mataPelajaran()
    {
        return $this->belongsTo(MataPelajaran::class);
    }

    // penjelasan: Relasi ini menghubungkan jadwal ke pegawai sebagai guru pengajar.
    // penjelasan: Foreign key yang digunakan adalah guru_id.
    // penjelasan: Dipanggil misalnya $jadwalPelajaran->guru.
    public function guru()
    {
        return $this->belongsTo(Pegawai::class, 'guru_id');
    }

    // penjelasan: Fungsi ini mengecek apakah jadwal pelajaran masih aktif.
    public function isAktif(): bool
    {
        return $this->status === 'aktif';
    }

    // penjelasan: Accessor ini mengubah hari menjadi label yang rapi.
    // penjelasan: Contoh senin menjadi Senin.
    public function getHariLabelAttribute(): string
    {
        return match ($this->hari) {
            'senin' => 'Senin',
            'selasa' => 'Selasa',
            'rabu' => 'Rabu',
            'kamis' => 'Kamis',
            'jumat' => 'Jumat',
            'sabtu' => 'Sabtu',
            default => '-',
        };
    }

    // penjelasan: Accessor ini menampilkan jam mulai dalam format HH:mm.
    // penjelasan: Data time dari database biasanya berbentuk HH:mm:ss, sehingga dipotong menjadi HH:mm.
    public function getJamMulaiFormatAttribute(): string
    {
        return $this->jam_mulai ? substr($this->jam_mulai, 0, 5) : '-';
    }

    // penjelasan: Accessor ini menampilkan jam selesai dalam format HH:mm.
    public function getJamSelesaiFormatAttribute(): string
    {
        return $this->jam_selesai ? substr($this->jam_selesai, 0, 5) : '-';
    }

    // penjelasan: Accessor ini menampilkan rentang jam pelajaran.
    // penjelasan: Contoh hasil: 07:30 - 08:50.
    public function getJamPelajaranAttribute(): string
    {
        return $this->jam_mulai_format . ' - ' . $this->jam_selesai_format;
    }
}
