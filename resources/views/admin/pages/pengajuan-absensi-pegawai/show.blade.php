{{-- penjelasan: Halaman ini menampilkan detail pengajuan milik guru/staff. --}}
{{-- penjelasan: Jika pengajuan masih menunggu, user dapat mengedit atau membatalkan pengajuan. --}}

@extends('admin.layouts.app')

@section('title', 'Detail Pengajuan Absensi')

@section('content')

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
                <div>
                    <h5 class="fw-bold mb-0">Detail Pengajuan Absensi</h5>
                    <small class="text-muted">Status dan detail pengajuan absensi.</small>
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

                <span class="badge {{ $statusClass }}">
                    {{ $pengajuanAbsensiPegawai->status_pengajuan_label }}
                </span>
            </div>

            <div class="card-body">
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
                    <div class="text-muted">Bukti</div>

                    @if ($pengajuanAbsensiPegawai->hasBuktiFile())
                        <a href="{{ asset('storage/' . $pengajuanAbsensiPegawai->bukti_file) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-file-earmark-text"></i>
                            Lihat Bukti
                        </a>
                    @else
                        <div class="fw-semibold">Tidak ada bukti</div>
                    @endif
                </div>

                <div class="mb-3">
                    <div class="text-muted">Catatan Admin</div>
                    <div class="fw-semibold">{{ $pengajuanAbsensiPegawai->catatan_admin ?? '-' }}</div>
                </div>

                <div class="mb-4">
                    <div class="text-muted">Dibuat / Diperbarui</div>
                    <div class="fw-semibold">
                        {{ $pengajuanAbsensiPegawai->created_at ? $pengajuanAbsensiPegawai->created_at->format('d-m-Y H:i') : '-' }}
                        /
                        {{ $pengajuanAbsensiPegawai->updated_at ? $pengajuanAbsensiPegawai->updated_at->format('d-m-Y H:i') : '-' }}
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route($routePrefix . '.pengajuan-absensi-pegawai.index') }}" class="btn btn-outline-secondary">
                        Kembali
                    </a>

                    @if ($pengajuanAbsensiPegawai->isMenunggu())
                        <a href="{{ route($routePrefix . '.pengajuan-absensi-pegawai.edit', $pengajuanAbsensiPegawai) }}" class="btn btn-primary">
                            <i class="bi bi-pencil-square me-1"></i>
                            Edit
                        </a>

                        <form action="{{ route($routePrefix . '.pengajuan-absensi-pegawai.cancel', $pengajuanAbsensiPegawai) }}" method="POST">
                            @csrf
                            @method('PATCH')

                            <button
                                type="submit"
                                class="btn btn-danger"
                                data-confirm="true"
                                data-confirm-message="Apakah Anda yakin ingin membatalkan pengajuan ini?"
                                data-confirm-yes="Ya, Batalkan"
                                data-confirm-yes-class="btn-danger"
                            >
                                <i class="bi bi-x-circle me-1"></i>
                                Batalkan
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
