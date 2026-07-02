{{-- penjelasan: Halaman ini digunakan admin/super admin untuk mengecek bukti dan memproses pengajuan. --}}
{{-- penjelasan: Jika disetujui, sistem membuat absensi resmi otomatis. --}}
{{-- penjelasan: Catatan penolakan wajib diisi. --}}

@extends('admin.layouts.app')

@section('title', 'Detail Persetujuan Absensi')

@section('content')

<div class="row">
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0">
                <h5 class="fw-bold mb-0">Detail Pengajuan</h5>
                <small class="text-muted">Pastikan bukti/alasan sudah benar sebelum ACC.</small>
            </div>

            <div class="card-body">
                <div class="mb-3">
                    <div class="text-muted">Pegawai</div>
                    <div class="fw-semibold">
                        {{ $pengajuanAbsensiPegawai->pegawai?->nama_pegawai ?? '-' }}

                        @if ($pengajuanAbsensiPegawai->pegawai?->nip)
                            <small class="text-muted d-block">NIP: {{ $pengajuanAbsensiPegawai->pegawai->nip }}</small>
                        @endif
                    </div>
                </div>

                <div class="mb-3">
                    <div class="text-muted">Jenis Pengajuan</div>
                    <div class="fw-semibold">{{ $pengajuanAbsensiPegawai->jenis_pengajuan_label }}</div>
                </div>

                <div class="mb-3">
                    <div class="text-muted">Tanggal</div>
                    <div class="fw-semibold">
                        {{ $pengajuanAbsensiPegawai->tanggal_mulai->format('d-m-Y') }}
                        sampai
                        {{ $pengajuanAbsensiPegawai->tanggal_selesai->format('d-m-Y') }}
                    </div>
                </div>

                <div class="mb-3">
                    <div class="text-muted">Judul</div>
                    <div class="fw-semibold">{{ $pengajuanAbsensiPegawai->judul_pengajuan ?? '-' }}</div>
                </div>

                <div class="mb-3">
                    <div class="text-muted">Lokasi Kegiatan</div>
                    <div class="fw-semibold">{{ $pengajuanAbsensiPegawai->lokasi_kegiatan ?? '-' }}</div>
                </div>

                <div class="mb-3">
                    <div class="text-muted">Alasan</div>
                    <div class="fw-semibold">{{ $pengajuanAbsensiPegawai->alasan }}</div>
                </div>

                <div class="mb-3">
                    <div class="text-muted">Bukti File</div>
                    @if ($pengajuanAbsensiPegawai->hasBuktiFile())
                        <a href="{{ asset('storage/' . $pengajuanAbsensiPegawai->bukti_file) }}" target="_blank" class="btn btn-outline-primary">
                            <i class="bi bi-file-earmark-text"></i>
                            Buka Bukti
                        </a>
                    @else
                        <div class="fw-semibold">Tidak ada bukti</div>
                    @endif
                </div>

                @php
                    $statusClass = match ($pengajuanAbsensiPegawai->status_pengajuan) {
                        'menunggu' => 'bg-warning-subtle text-warning',
                        'disetujui' => 'bg-success-subtle text-success',
                        'ditolak' => 'bg-danger-subtle text-danger',
                        'dibatalkan' => 'bg-secondary-subtle text-secondary',
                        default => 'bg-secondary-subtle text-secondary',
                    };
                @endphp

                <div class="mb-3">
                    <div class="text-muted">Status</div>
                    <span class="badge {{ $statusClass }}">
                        {{ $pengajuanAbsensiPegawai->status_pengajuan_label }}
                    </span>
                </div>

                <div class="mb-3">
                    <div class="text-muted">Diproses Oleh</div>
                    <div class="fw-semibold">
                        {{ $pengajuanAbsensiPegawai->disetujuiOleh?->name ?? '-' }}

                        @if ($pengajuanAbsensiPegawai->disetujui_pada)
                            <small class="text-muted d-block">
                                {{ $pengajuanAbsensiPegawai->disetujui_pada->format('d-m-Y H:i') }}
                            </small>
                        @endif
                    </div>
                </div>

                <div class="mb-4">
                    <div class="text-muted">Catatan Admin</div>
                    <div class="fw-semibold">{{ $pengajuanAbsensiPegawai->catatan_admin ?? '-' }}</div>
                </div>

                <div class="d-flex justify-content-end">
                    <a href="{{ route($routePrefix . '.persetujuan-absensi-pegawai.index') }}" class="btn btn-outline-secondary">
                        Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        @if ($pengajuanAbsensiPegawai->isMenunggu())
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0">
                    <h5 class="fw-bold mb-0">Setujui Pengajuan</h5>
                    <small class="text-muted">Jika disetujui, absensi resmi akan dibuat otomatis.</small>
                </div>

                <div class="card-body">
                    <form action="{{ route($routePrefix . '.persetujuan-absensi-pegawai.approve', $pengajuanAbsensiPegawai) }}" method="POST">
                        @csrf
                        @method('PATCH')

                        <div class="mb-3">
                            <label class="form-label">
                                Catatan Admin <span class="text-muted">(Opsional)</span>
                            </label>

                            <textarea
                                name="catatan_admin"
                                class="form-control @error('catatan_admin') is-invalid @enderror"
                                rows="3"
                                placeholder="Tambahkan catatan jika diperlukan"
                            >{{ old('catatan_admin') }}</textarea>

                            @error('catatan_admin')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button
                            type="submit"
                            class="btn btn-success w-100"
                            data-confirm="true"
                            data-confirm-message="Apakah Anda yakin ingin menyetujui pengajuan ini? Absensi resmi akan dibuat otomatis."
                            data-confirm-yes="Ya, Setujui"
                            data-confirm-yes-class="btn-success"
                        >
                            <i class="bi bi-check-circle"></i>
                            Setujui / ACC
                        </button>
                    </form>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <h5 class="fw-bold mb-0">Tolak Pengajuan</h5>
                    <small class="text-muted">Catatan penolakan wajib diisi.</small>
                </div>

                <div class="card-body">
                    <form action="{{ route($routePrefix . '.persetujuan-absensi-pegawai.reject', $pengajuanAbsensiPegawai) }}" method="POST">
                        @csrf
                        @method('PATCH')

                        <div class="mb-3">
                            <label class="form-label">
                                Catatan Penolakan <span class="text-danger">*</span>
                            </label>

                            <textarea
                                name="catatan_admin"
                                class="form-control @error('catatan_admin') is-invalid @enderror"
                                rows="3"
                                placeholder="Tuliskan alasan penolakan"
                                required
                            >{{ old('catatan_admin') }}</textarea>

                            @error('catatan_admin')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button
                            type="submit"
                            class="btn btn-danger w-100"
                            data-confirm="true"
                            data-confirm-message="Apakah Anda yakin ingin menolak pengajuan ini?"
                            data-confirm-yes="Ya, Tolak"
                            data-confirm-yes-class="btn-danger"
                        >
                            <i class="bi bi-x-circle"></i>
                            Tolak
                        </button>
                    </form>
                </div>
            </div>
        @else
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center text-muted">
                    Pengajuan ini sudah diproses dan tidak bisa diubah.
                </div>
            </div>
        @endif
    </div>
</div>

@endsection
