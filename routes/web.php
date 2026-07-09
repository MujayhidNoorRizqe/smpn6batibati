<?php

use App\Http\Controllers\Admin\JadwalPelajaranController;
use App\Http\Controllers\Admin\KelasController;
use App\Http\Controllers\Admin\LaporanController;
use App\Http\Controllers\Admin\MataPelajaranController;
use App\Http\Controllers\Admin\MuridController;
use App\Http\Controllers\Admin\NilaiController as AdminNilaiController;
use App\Http\Controllers\Admin\PegawaiController;
use App\Http\Controllers\Admin\PersetujuanAbsensiPegawaiController;
use App\Http\Controllers\Admin\RekapAbsensiMuridController;
use App\Http\Controllers\Admin\RekapAbsensiPegawaiController;
use App\Http\Controllers\Admin\SemesterController;
use App\Http\Controllers\Admin\TahunAjaranController;
use App\Http\Controllers\Admin\WaliMuridController;
use App\Http\Controllers\Admin\WhatsAppFonnteController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Guru\AbsensiMuridController;
use App\Http\Controllers\Guru\JadwalMengajarController;
use App\Http\Controllers\Guru\NilaiController as GuruNilaiController;
use App\Http\Controllers\Guru\PasswordController as GuruPasswordController;
use App\Http\Controllers\Guru\RekapAbsensiMuridController as GuruRekapAbsensiMuridController;
use App\Http\Controllers\Pegawai\AbsensiPegawaiController;
use App\Http\Controllers\Pegawai\PengajuanAbsensiPegawaiController;
use App\Http\Controllers\SuperAdmin\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (! auth()->check()) {
        return redirect()->route('login');
    }

    $user = auth()->user();

    if ($user->role === 'super_admin') {
        return redirect()->route('super-admin.dashboard');
    }

    if ($user->role === 'admin') {
        return redirect()->route('admin.dashboard');
    }

    if ($user->role === 'guru') {
        return redirect()->route('guru.dashboard');
    }

    return redirect()->route('login');
})->name('public.home');

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.process');
});

Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::middleware(['auth', 'role:super_admin'])
    ->prefix('super-admin')
    ->name('super-admin.')
    ->group(function () {
        Route::get('/dashboard', function () {
            return view('admin.pages.dashboard.super-admin.index');
        })->name('dashboard');

        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::get('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
        Route::put('/users/{user}/reset-password', [UserController::class, 'updatePassword'])->name('users.update-password');
        Route::patch('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');

        Route::get('/pegawai', [PegawaiController::class, 'index'])->name('pegawai.index');
        Route::get('/pegawai/create', [PegawaiController::class, 'create'])->name('pegawai.create');
        Route::post('/pegawai', [PegawaiController::class, 'store'])->name('pegawai.store');
        Route::get('/pegawai/{pegawai}', [PegawaiController::class, 'show'])->name('pegawai.show');
        Route::get('/pegawai/{pegawai}/edit', [PegawaiController::class, 'edit'])->name('pegawai.edit');
        Route::put('/pegawai/{pegawai}', [PegawaiController::class, 'update'])->name('pegawai.update');
        Route::patch('/pegawai/{pegawai}/toggle-status', [PegawaiController::class, 'toggleStatus'])->name('pegawai.toggle-status');

        Route::get('/kelas', [KelasController::class, 'index'])->name('kelas.index');
        Route::get('/kelas/create', [KelasController::class, 'create'])->name('kelas.create');
        Route::post('/kelas', [KelasController::class, 'store'])->name('kelas.store');
        Route::get('/kelas/{kelas}', [KelasController::class, 'show'])->name('kelas.show');
        Route::get('/kelas/{kelas}/edit', [KelasController::class, 'edit'])->name('kelas.edit');
        Route::put('/kelas/{kelas}', [KelasController::class, 'update'])->name('kelas.update');
        Route::patch('/kelas/{kelas}/toggle-status', [KelasController::class, 'toggleStatus'])->name('kelas.toggle-status');

        Route::get('/wali-murid', [WaliMuridController::class, 'index'])->name('wali-murid.index');
        Route::get('/wali-murid/create', [WaliMuridController::class, 'create'])->name('wali-murid.create');
        Route::post('/wali-murid', [WaliMuridController::class, 'store'])->name('wali-murid.store');
        Route::get('/wali-murid/{waliMurid}', [WaliMuridController::class, 'show'])->name('wali-murid.show');
        Route::get('/wali-murid/{waliMurid}/edit', [WaliMuridController::class, 'edit'])->name('wali-murid.edit');
        Route::put('/wali-murid/{waliMurid}', [WaliMuridController::class, 'update'])->name('wali-murid.update');
        Route::patch('/wali-murid/{waliMurid}/toggle-status', [WaliMuridController::class, 'toggleStatus'])->name('wali-murid.toggle-status');

        Route::get('/murid', [MuridController::class, 'index'])->name('murid.index');
        Route::get('/murid/create', [MuridController::class, 'create'])->name('murid.create');
        Route::post('/murid', [MuridController::class, 'store'])->name('murid.store');
        Route::get('/murid/{murid}', [MuridController::class, 'show'])->name('murid.show');
        Route::get('/murid/{murid}/edit', [MuridController::class, 'edit'])->name('murid.edit');
        Route::put('/murid/{murid}', [MuridController::class, 'update'])->name('murid.update');
        Route::patch('/murid/{murid}/toggle-status', [MuridController::class, 'toggleStatus'])->name('murid.toggle-status');

        Route::get('/mata-pelajaran', [MataPelajaranController::class, 'index'])->name('mata-pelajaran.index');
        Route::get('/mata-pelajaran/create', [MataPelajaranController::class, 'create'])->name('mata-pelajaran.create');
        Route::post('/mata-pelajaran', [MataPelajaranController::class, 'store'])->name('mata-pelajaran.store');
        Route::get('/mata-pelajaran/{mataPelajaran}', [MataPelajaranController::class, 'show'])->name('mata-pelajaran.show');
        Route::get('/mata-pelajaran/{mataPelajaran}/edit', [MataPelajaranController::class, 'edit'])->name('mata-pelajaran.edit');
        Route::put('/mata-pelajaran/{mataPelajaran}', [MataPelajaranController::class, 'update'])->name('mata-pelajaran.update');
        Route::patch('/mata-pelajaran/{mataPelajaran}/toggle-status', [MataPelajaranController::class, 'toggleStatus'])->name('mata-pelajaran.toggle-status');

        Route::get('/tahun-ajaran', [TahunAjaranController::class, 'index'])->name('tahun-ajaran.index');
        Route::get('/tahun-ajaran/create', [TahunAjaranController::class, 'create'])->name('tahun-ajaran.create');
        Route::post('/tahun-ajaran', [TahunAjaranController::class, 'store'])->name('tahun-ajaran.store');
        Route::get('/tahun-ajaran/{tahunAjaran}', [TahunAjaranController::class, 'show'])->name('tahun-ajaran.show');
        Route::get('/tahun-ajaran/{tahunAjaran}/edit', [TahunAjaranController::class, 'edit'])->name('tahun-ajaran.edit');
        Route::put('/tahun-ajaran/{tahunAjaran}', [TahunAjaranController::class, 'update'])->name('tahun-ajaran.update');
        Route::patch('/tahun-ajaran/{tahunAjaran}/toggle-status', [TahunAjaranController::class, 'toggleStatus'])->name('tahun-ajaran.toggle-status');

        Route::get('/semester', [SemesterController::class, 'index'])->name('semester.index');
        Route::get('/semester/create', [SemesterController::class, 'create'])->name('semester.create');
        Route::post('/semester', [SemesterController::class, 'store'])->name('semester.store');
        Route::get('/semester/{semester}', [SemesterController::class, 'show'])->name('semester.show');
        Route::get('/semester/{semester}/edit', [SemesterController::class, 'edit'])->name('semester.edit');
        Route::put('/semester/{semester}', [SemesterController::class, 'update'])->name('semester.update');
        Route::patch('/semester/{semester}/toggle-status', [SemesterController::class, 'toggleStatus'])->name('semester.toggle-status');

        Route::get('/jadwal-pelajaran', [JadwalPelajaranController::class, 'index'])->name('jadwal-pelajaran.index');
        Route::get('/jadwal-pelajaran/create', [JadwalPelajaranController::class, 'create'])->name('jadwal-pelajaran.create');
        Route::get('/jadwal-pelajaran/create/kelas/{kelas}', [JadwalPelajaranController::class, 'createKelas'])->name('jadwal-pelajaran.create-kelas');
        Route::get('/jadwal-pelajaran/create/kelas/{kelas}/hari/{hari}', [JadwalPelajaranController::class, 'createHari'])->name('jadwal-pelajaran.create-hari');
        Route::post('/jadwal-pelajaran/create/kelas/{kelas}/hari/{hari}', [JadwalPelajaranController::class, 'storeHari'])->name('jadwal-pelajaran.store-hari');
        Route::post('/jadwal-pelajaran', [JadwalPelajaranController::class, 'store'])->name('jadwal-pelajaran.store');
        Route::get('/jadwal-pelajaran/kelas/{kelas}', [JadwalPelajaranController::class, 'kelas'])->name('jadwal-pelajaran.kelas');
        Route::get('/jadwal-pelajaran/{jadwalPelajaran}', [JadwalPelajaranController::class, 'show'])->name('jadwal-pelajaran.show');
        Route::get('/jadwal-pelajaran/{jadwalPelajaran}/edit', [JadwalPelajaranController::class, 'edit'])->name('jadwal-pelajaran.edit');
        Route::put('/jadwal-pelajaran/{jadwalPelajaran}', [JadwalPelajaranController::class, 'update'])->name('jadwal-pelajaran.update');
        Route::patch('/jadwal-pelajaran/{jadwalPelajaran}/toggle-status', [JadwalPelajaranController::class, 'toggleStatus'])->name('jadwal-pelajaran.toggle-status');

        Route::get('/absensi-pegawai', [RekapAbsensiPegawaiController::class, 'index'])->name('absensi-pegawai.index');
        Route::post('/absensi-pegawai/generate-alpha', [RekapAbsensiPegawaiController::class, 'generateAlpha'])->name('absensi-pegawai.generate-alpha');
        Route::get('/absensi-pegawai/{absensiPegawai}', [RekapAbsensiPegawaiController::class, 'show'])->name('absensi-pegawai.show');

        Route::get('/rekap-absensi-murid', [RekapAbsensiMuridController::class, 'index'])->name('rekap-absensi-murid.index');
        Route::get('/rekap-absensi-murid/kelas/{kelas}', [RekapAbsensiMuridController::class, 'kelas'])->name('rekap-absensi-murid.kelas');
        Route::get('/rekap-absensi-murid/murid/{murid}', [RekapAbsensiMuridController::class, 'murid'])->name('rekap-absensi-murid.murid');

        Route::get('/nilai', [AdminNilaiController::class, 'index'])->name('nilai.index');
        Route::get('/nilai/kelas/{kelas}', [AdminNilaiController::class, 'kelas'])->name('nilai.kelas');
        Route::get('/nilai/murid/{murid}', [AdminNilaiController::class, 'murid'])->name('nilai.murid');

        Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
        Route::get('/laporan/daftar/{jenis}', [LaporanController::class, 'daftar'])->name('laporan.daftar');
        Route::get('/laporan/daftar/{jenis}/kelas/{kelas}', [LaporanController::class, 'daftarMurid'])->name('laporan.daftar-murid');
        Route::get('/laporan/periode/{jenis}/{targetType}/{targetId}', [LaporanController::class, 'periode'])->name('laporan.periode');
        Route::get('/laporan/preview/{jenis}/{targetType}/{targetId}', [LaporanController::class, 'preview'])->name('laporan.preview');
        Route::get('/laporan/export/{jenis}/{targetType}/{targetId}', [LaporanController::class, 'exportCsv'])->name('laporan.export.csv');
        Route::get('/laporan/cetak/{jenis}/{targetType}/{targetId}', [LaporanController::class, 'cetak'])->name('laporan.cetak');

        Route::get('/whatsapp-fonnte', [WhatsAppFonnteController::class, 'index'])->name('whatsapp-fonnte.index');
        Route::get('/whatsapp-fonnte/{waliMurid}', [WhatsAppFonnteController::class, 'show'])->name('whatsapp-fonnte.show');
        Route::post('/whatsapp-fonnte/{waliMurid}/send', [WhatsAppFonnteController::class, 'send'])->name('whatsapp-fonnte.send');

        Route::get('/persetujuan-absensi-pegawai', [PersetujuanAbsensiPegawaiController::class, 'index'])->name('persetujuan-absensi-pegawai.index');
        Route::get('/persetujuan-absensi-pegawai/{pengajuanAbsensiPegawai}', [PersetujuanAbsensiPegawaiController::class, 'show'])->name('persetujuan-absensi-pegawai.show');
        Route::patch('/persetujuan-absensi-pegawai/{pengajuanAbsensiPegawai}/approve', [PersetujuanAbsensiPegawaiController::class, 'approve'])->name('persetujuan-absensi-pegawai.approve');
        Route::patch('/persetujuan-absensi-pegawai/{pengajuanAbsensiPegawai}/reject', [PersetujuanAbsensiPegawaiController::class, 'reject'])->name('persetujuan-absensi-pegawai.reject');
    });

Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', function () {
            return view('admin.pages.dashboard.admin.index');
        })->name('dashboard');

        Route::get('/pegawai', [PegawaiController::class, 'index'])->name('pegawai.index');
        Route::get('/pegawai/create', [PegawaiController::class, 'create'])->name('pegawai.create');
        Route::post('/pegawai', [PegawaiController::class, 'store'])->name('pegawai.store');
        Route::get('/pegawai/{pegawai}', [PegawaiController::class, 'show'])->name('pegawai.show');
        Route::get('/pegawai/{pegawai}/edit', [PegawaiController::class, 'edit'])->name('pegawai.edit');
        Route::put('/pegawai/{pegawai}', [PegawaiController::class, 'update'])->name('pegawai.update');
        Route::patch('/pegawai/{pegawai}/toggle-status', [PegawaiController::class, 'toggleStatus'])->name('pegawai.toggle-status');

        Route::get('/kelas', [KelasController::class, 'index'])->name('kelas.index');
        Route::get('/kelas/create', [KelasController::class, 'create'])->name('kelas.create');
        Route::post('/kelas', [KelasController::class, 'store'])->name('kelas.store');
        Route::get('/kelas/{kelas}', [KelasController::class, 'show'])->name('kelas.show');
        Route::get('/kelas/{kelas}/edit', [KelasController::class, 'edit'])->name('kelas.edit');
        Route::put('/kelas/{kelas}', [KelasController::class, 'update'])->name('kelas.update');
        Route::patch('/kelas/{kelas}/toggle-status', [KelasController::class, 'toggleStatus'])->name('kelas.toggle-status');

        Route::get('/wali-murid', [WaliMuridController::class, 'index'])->name('wali-murid.index');
        Route::get('/wali-murid/create', [WaliMuridController::class, 'create'])->name('wali-murid.create');
        Route::post('/wali-murid', [WaliMuridController::class, 'store'])->name('wali-murid.store');
        Route::get('/wali-murid/{waliMurid}', [WaliMuridController::class, 'show'])->name('wali-murid.show');
        Route::get('/wali-murid/{waliMurid}/edit', [WaliMuridController::class, 'edit'])->name('wali-murid.edit');
        Route::put('/wali-murid/{waliMurid}', [WaliMuridController::class, 'update'])->name('wali-murid.update');
        Route::patch('/wali-murid/{waliMurid}/toggle-status', [WaliMuridController::class, 'toggleStatus'])->name('wali-murid.toggle-status');

        Route::get('/murid', [MuridController::class, 'index'])->name('murid.index');
        Route::get('/murid/create', [MuridController::class, 'create'])->name('murid.create');
        Route::post('/murid', [MuridController::class, 'store'])->name('murid.store');
        Route::get('/murid/{murid}', [MuridController::class, 'show'])->name('murid.show');
        Route::get('/murid/{murid}/edit', [MuridController::class, 'edit'])->name('murid.edit');
        Route::put('/murid/{murid}', [MuridController::class, 'update'])->name('murid.update');
        Route::patch('/murid/{murid}/toggle-status', [MuridController::class, 'toggleStatus'])->name('murid.toggle-status');

        Route::get('/mata-pelajaran', [MataPelajaranController::class, 'index'])->name('mata-pelajaran.index');
        Route::get('/mata-pelajaran/create', [MataPelajaranController::class, 'create'])->name('mata-pelajaran.create');
        Route::post('/mata-pelajaran', [MataPelajaranController::class, 'store'])->name('mata-pelajaran.store');
        Route::get('/mata-pelajaran/{mataPelajaran}', [MataPelajaranController::class, 'show'])->name('mata-pelajaran.show');
        Route::get('/mata-pelajaran/{mataPelajaran}/edit', [MataPelajaranController::class, 'edit'])->name('mata-pelajaran.edit');
        Route::put('/mata-pelajaran/{mataPelajaran}', [MataPelajaranController::class, 'update'])->name('mata-pelajaran.update');
        Route::patch('/mata-pelajaran/{mataPelajaran}/toggle-status', [MataPelajaranController::class, 'toggleStatus'])->name('mata-pelajaran.toggle-status');

        Route::get('/tahun-ajaran', [TahunAjaranController::class, 'index'])->name('tahun-ajaran.index');
        Route::get('/tahun-ajaran/create', [TahunAjaranController::class, 'create'])->name('tahun-ajaran.create');
        Route::post('/tahun-ajaran', [TahunAjaranController::class, 'store'])->name('tahun-ajaran.store');
        Route::get('/tahun-ajaran/{tahunAjaran}', [TahunAjaranController::class, 'show'])->name('tahun-ajaran.show');
        Route::get('/tahun-ajaran/{tahunAjaran}/edit', [TahunAjaranController::class, 'edit'])->name('tahun-ajaran.edit');
        Route::put('/tahun-ajaran/{tahunAjaran}', [TahunAjaranController::class, 'update'])->name('tahun-ajaran.update');
        Route::patch('/tahun-ajaran/{tahunAjaran}/toggle-status', [TahunAjaranController::class, 'toggleStatus'])->name('tahun-ajaran.toggle-status');

        Route::get('/semester', [SemesterController::class, 'index'])->name('semester.index');
        Route::get('/semester/create', [SemesterController::class, 'create'])->name('semester.create');
        Route::post('/semester', [SemesterController::class, 'store'])->name('semester.store');
        Route::get('/semester/{semester}', [SemesterController::class, 'show'])->name('semester.show');
        Route::get('/semester/{semester}/edit', [SemesterController::class, 'edit'])->name('semester.edit');
        Route::put('/semester/{semester}', [SemesterController::class, 'update'])->name('semester.update');
        Route::patch('/semester/{semester}/toggle-status', [SemesterController::class, 'toggleStatus'])->name('semester.toggle-status');

        Route::get('/jadwal-pelajaran', [JadwalPelajaranController::class, 'index'])->name('jadwal-pelajaran.index');
        Route::get('/jadwal-pelajaran/create', [JadwalPelajaranController::class, 'create'])->name('jadwal-pelajaran.create');
        Route::get('/jadwal-pelajaran/create/kelas/{kelas}', [JadwalPelajaranController::class, 'createKelas'])->name('jadwal-pelajaran.create-kelas');
        Route::get('/jadwal-pelajaran/create/kelas/{kelas}/hari/{hari}', [JadwalPelajaranController::class, 'createHari'])->name('jadwal-pelajaran.create-hari');
        Route::post('/jadwal-pelajaran/create/kelas/{kelas}/hari/{hari}', [JadwalPelajaranController::class, 'storeHari'])->name('jadwal-pelajaran.store-hari');
        Route::post('/jadwal-pelajaran', [JadwalPelajaranController::class, 'store'])->name('jadwal-pelajaran.store');
        Route::get('/jadwal-pelajaran/kelas/{kelas}', [JadwalPelajaranController::class, 'kelas'])->name('jadwal-pelajaran.kelas');
        Route::get('/jadwal-pelajaran/{jadwalPelajaran}', [JadwalPelajaranController::class, 'show'])->name('jadwal-pelajaran.show');
        Route::get('/jadwal-pelajaran/{jadwalPelajaran}/edit', [JadwalPelajaranController::class, 'edit'])->name('jadwal-pelajaran.edit');
        Route::put('/jadwal-pelajaran/{jadwalPelajaran}', [JadwalPelajaranController::class, 'update'])->name('jadwal-pelajaran.update');
        Route::patch('/jadwal-pelajaran/{jadwalPelajaran}/toggle-status', [JadwalPelajaranController::class, 'toggleStatus'])->name('jadwal-pelajaran.toggle-status');

        Route::get('/absensi-pegawai', [RekapAbsensiPegawaiController::class, 'index'])->name('absensi-pegawai.index');
        Route::post('/absensi-pegawai/generate-alpha', [RekapAbsensiPegawaiController::class, 'generateAlpha'])->name('absensi-pegawai.generate-alpha');
        Route::get('/absensi-pegawai/{absensiPegawai}', [RekapAbsensiPegawaiController::class, 'show'])->name('absensi-pegawai.show');

        Route::get('/rekap-absensi-murid', [RekapAbsensiMuridController::class, 'index'])->name('rekap-absensi-murid.index');
        Route::get('/rekap-absensi-murid/kelas/{kelas}', [RekapAbsensiMuridController::class, 'kelas'])->name('rekap-absensi-murid.kelas');
        Route::get('/rekap-absensi-murid/murid/{murid}', [RekapAbsensiMuridController::class, 'murid'])->name('rekap-absensi-murid.murid');

        Route::get('/nilai', [AdminNilaiController::class, 'index'])->name('nilai.index');
        Route::get('/nilai/kelas/{kelas}', [AdminNilaiController::class, 'kelas'])->name('nilai.kelas');
        Route::get('/nilai/murid/{murid}', [AdminNilaiController::class, 'murid'])->name('nilai.murid');

        Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
        Route::get('/laporan/daftar/{jenis}', [LaporanController::class, 'daftar'])->name('laporan.daftar');
        Route::get('/laporan/daftar/{jenis}/kelas/{kelas}', [LaporanController::class, 'daftarMurid'])->name('laporan.daftar-murid');
        Route::get('/laporan/periode/{jenis}/{targetType}/{targetId}', [LaporanController::class, 'periode'])->name('laporan.periode');
        Route::get('/laporan/preview/{jenis}/{targetType}/{targetId}', [LaporanController::class, 'preview'])->name('laporan.preview');
        Route::get('/laporan/export/{jenis}/{targetType}/{targetId}', [LaporanController::class, 'exportCsv'])->name('laporan.export.csv');
        Route::get('/laporan/cetak/{jenis}/{targetType}/{targetId}', [LaporanController::class, 'cetak'])->name('laporan.cetak');

        Route::get('/whatsapp-fonnte', [WhatsAppFonnteController::class, 'index'])->name('whatsapp-fonnte.index');
        Route::get('/whatsapp-fonnte/{waliMurid}', [WhatsAppFonnteController::class, 'show'])->name('whatsapp-fonnte.show');
        Route::post('/whatsapp-fonnte/{waliMurid}/send', [WhatsAppFonnteController::class, 'send'])->name('whatsapp-fonnte.send');

        Route::get('/persetujuan-absensi-pegawai', [PersetujuanAbsensiPegawaiController::class, 'index'])->name('persetujuan-absensi-pegawai.index');
        Route::get('/persetujuan-absensi-pegawai/{pengajuanAbsensiPegawai}', [PersetujuanAbsensiPegawaiController::class, 'show'])->name('persetujuan-absensi-pegawai.show');
        Route::patch('/persetujuan-absensi-pegawai/{pengajuanAbsensiPegawai}/approve', [PersetujuanAbsensiPegawaiController::class, 'approve'])->name('persetujuan-absensi-pegawai.approve');
        Route::patch('/persetujuan-absensi-pegawai/{pengajuanAbsensiPegawai}/reject', [PersetujuanAbsensiPegawaiController::class, 'reject'])->name('persetujuan-absensi-pegawai.reject');
    });

Route::middleware(['auth', 'role:guru'])
    ->prefix('guru')
    ->name('guru.')
    ->group(function () {
        Route::get('/dashboard', function () {
            return view('admin.pages.dashboard.guru.index');
        })->name('dashboard');

        Route::get('/absensi-pegawai', [AbsensiPegawaiController::class, 'index'])->name('absensi-pegawai.index');
        Route::post('/absensi-pegawai/masuk', [AbsensiPegawaiController::class, 'absenMasuk'])->name('absensi-pegawai.masuk');
        Route::patch('/absensi-pegawai/pulang', [AbsensiPegawaiController::class, 'absenPulang'])->name('absensi-pegawai.pulang');

        Route::get('/jadwal-mengajar', [JadwalMengajarController::class, 'index'])->name('jadwal-mengajar.index');

        Route::get('/absensi-murid', [AbsensiMuridController::class, 'index'])->name('absensi-murid.index');
        Route::get('/absensi-murid/jadwal/{jadwalPelajaran}', [AbsensiMuridController::class, 'create'])->name('absensi-murid.create');
        Route::post('/absensi-murid/jadwal/{jadwalPelajaran}', [AbsensiMuridController::class, 'store'])->name('absensi-murid.store');
        Route::get('/absensi-murid/jadwal/{jadwalPelajaran}/detail', [AbsensiMuridController::class, 'show'])->name('absensi-murid.show');

        Route::get('/rekap-absen-murid', [GuruRekapAbsensiMuridController::class, 'index'])->name('rekap-absen-murid.index');
        Route::get('/rekap-absen-murid/kelas/{kelas}', [GuruRekapAbsensiMuridController::class, 'kelas'])->name('rekap-absen-murid.kelas');
        Route::get('/rekap-absen-murid/murid/{murid}', [GuruRekapAbsensiMuridController::class, 'murid'])->name('rekap-absen-murid.murid');

        Route::get('/input-nilai', [GuruNilaiController::class, 'index'])->name('input-nilai.index');
        Route::get('/input-nilai/murid/{murid}', [GuruNilaiController::class, 'inputMurid'])->name('input-nilai.murid');
        Route::post('/input-nilai/murid/{murid}', [GuruNilaiController::class, 'storeMurid'])->name('input-nilai.murid.store');

        Route::get('/rekap-nilai', [GuruNilaiController::class, 'rekap'])->name('rekap-nilai.index');
        Route::get('/rekap-nilai/kelas/{kelas}', [GuruNilaiController::class, 'rekapKelas'])->name('rekap-nilai.kelas');
        Route::get('/rekap-nilai/murid/{murid}', [GuruNilaiController::class, 'rekapMurid'])->name('rekap-nilai.murid');

        Route::get('/pengajuan-absensi-pegawai', [PengajuanAbsensiPegawaiController::class, 'index'])->name('pengajuan-absensi-pegawai.index');
        Route::get('/pengajuan-absensi-pegawai/create', [PengajuanAbsensiPegawaiController::class, 'create'])->name('pengajuan-absensi-pegawai.create');
        Route::post('/pengajuan-absensi-pegawai', [PengajuanAbsensiPegawaiController::class, 'store'])->name('pengajuan-absensi-pegawai.store');
        Route::get('/pengajuan-absensi-pegawai/{pengajuanAbsensiPegawai}/edit', [PengajuanAbsensiPegawaiController::class, 'edit'])->name('pengajuan-absensi-pegawai.edit');
        Route::put('/pengajuan-absensi-pegawai/{pengajuanAbsensiPegawai}', [PengajuanAbsensiPegawaiController::class, 'update'])->name('pengajuan-absensi-pegawai.update');
        Route::get('/pengajuan-absensi-pegawai/{pengajuanAbsensiPegawai}', [PengajuanAbsensiPegawaiController::class, 'show'])->name('pengajuan-absensi-pegawai.show');
        Route::patch('/pengajuan-absensi-pegawai/{pengajuanAbsensiPegawai}/cancel', [PengajuanAbsensiPegawaiController::class, 'cancel'])->name('pengajuan-absensi-pegawai.cancel');

        Route::get('/ganti-password', [GuruPasswordController::class, 'edit'])->name('password.edit');
        Route::put('/ganti-password', [GuruPasswordController::class, 'update'])->name('password.update');
    });
