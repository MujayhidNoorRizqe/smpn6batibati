<?php

// penjelasan: Model ini digunakan untuk tabel pengajuan_absensi_pegawais.
// penjelasan: Model ini menyimpan pengajuan dinas, sakit, dan izin dari guru/staff.

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengajuanAbsensiPegawai extends Model
{
    use HasFactory;

    protected $fillable = [
        'pegawai_id',
        'jenis_pengajuan',
        'tanggal_mulai',
        'tanggal_selesai',
        'judul_pengajuan',
        'lokasi_kegiatan',
        'alasan',
        'bukti_file',
        'status_pengajuan',
        'disetujui_oleh',
        'disetujui_pada',
        'catatan_admin',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_mulai' => 'date',
            'tanggal_selesai' => 'date',
            'disetujui_pada' => 'datetime',
        ];
    }

    // penjelasan: Relasi ke pegawai yang mengajukan.
    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }

    // penjelasan: Relasi ke user admin/super admin yang memproses pengajuan.
    public function disetujuiOleh()
    {
        return $this->belongsTo(User::class, 'disetujui_oleh');
    }

    // penjelasan: Relasi ke absensi resmi yang dibuat dari pengajuan ini.
    public function absensiPegawais()
    {
        return $this->hasMany(AbsensiPegawai::class);
    }

    // penjelasan: Label jenis pengajuan untuk tampilan.
    public function getJenisPengajuanLabelAttribute(): string
    {
        return match ($this->jenis_pengajuan) {
            'dinas' => 'Dinas',
            'sakit' => 'Sakit',
            'izin' => 'Izin',
            default => '-',
        };
    }

    // penjelasan: Label status pengajuan untuk tampilan.
    public function getStatusPengajuanLabelAttribute(): string
    {
        return match ($this->status_pengajuan) {
            'menunggu' => 'Menunggu',
            'disetujui' => 'Disetujui',
            'ditolak' => 'Ditolak',
            'dibatalkan' => 'Dibatalkan',
            default => '-',
        };
    }

    // penjelasan: Mengecek apakah pengajuan masih menunggu.
    public function isMenunggu(): bool
    {
        return $this->status_pengajuan === 'menunggu';
    }

    // penjelasan: Mengecek apakah pengajuan punya bukti file.
    public function hasBuktiFile(): bool
    {
        return ! empty($this->bukti_file);
    }
}
