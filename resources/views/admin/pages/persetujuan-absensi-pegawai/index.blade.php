{{-- penjelasan: Halaman ini digunakan admin/super admin untuk melihat semua pengajuan absensi pegawai. --}}
{{-- penjelasan: Alert berhasil, gagal, dan validasi sudah memakai komponen global admin.components.alert. --}}
{{-- penjelasan: Filter tidak diberi label opsional karena filter hanya fitur pencarian. --}}

@extends('admin.layouts.app')

@section('title', 'Persetujuan Absensi Pegawai')

@section('content')

<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h4 class="fw-bold mb-1">Persetujuan Absensi Pegawai</h4>
                <p class="text-muted mb-0">Cek bukti dinas/sakit atau alasan izin sebelum menyetujui pengajuan.</p>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form action="{{ route($routePrefix . '.persetujuan-absensi-pegawai.index') }}" method="GET" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Cari Pegawai</label>
                <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Nama/NIP">
            </div>

            <div class="col-md-3">
                <label class="form-label">Jenis</label>
                <select name="jenis_pengajuan" class="form-select">
                    <option value="">Semua Jenis</option>
                    <option value="dinas" {{ request('jenis_pengajuan') === 'dinas' ? 'selected' : '' }}>Dinas</option>
                    <option value="sakit" {{ request('jenis_pengajuan') === 'sakit' ? 'selected' : '' }}>Sakit</option>
                    <option value="izin" {{ request('jenis_pengajuan') === 'izin' ? 'selected' : '' }}>Izin</option>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select name="status_pengajuan" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="menunggu" {{ request('status_pengajuan') === 'menunggu' ? 'selected' : '' }}>Menunggu</option>
                    <option value="disetujui" {{ request('status_pengajuan') === 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                    <option value="ditolak" {{ request('status_pengajuan') === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                    <option value="dibatalkan" {{ request('status_pengajuan') === 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                </select>
            </div>

            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search me-1"></i>
                    Filter
                </button>
            </div>

            <div class="col-md-1 d-flex align-items-end">
                <a href="{{ route($routePrefix . '.persetujuan-absensi-pegawai.index') }}" class="btn btn-outline-secondary w-100">
                    Reset
                </a>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
        <div>
            <h6 class="fw-bold mb-0">Daftar Pengajuan Absensi Pegawai</h6>
            <small class="text-muted">Pengajuan yang perlu dicek admin atau super admin</small>
        </div>

        <span class="badge bg-primary-subtle text-primary">
            {{ $pengajuans->count() }} data tampil
        </span>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle admin-table">
                <thead class="table-light">
                    <tr>
                        <th>Pegawai</th>
                        <th>Jenis</th>
                        <th>Tanggal</th>
                        <th>Bukti</th>
                        <th>Status</th>
                        <th class="text-end table-action-column">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($pengajuans as $pengajuan)
                        @php
                            $statusClass = match ($pengajuan->status_pengajuan) {
                                'menunggu' => 'bg-warning-subtle text-warning',
                                'disetujui' => 'bg-success-subtle text-success',
                                'ditolak' => 'bg-danger-subtle text-danger',
                                'dibatalkan' => 'bg-secondary-subtle text-secondary',
                                default => 'bg-secondary-subtle text-secondary',
                            };
                        @endphp

                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $pengajuan->pegawai?->nama_pegawai ?? '-' }}</div>
                                <small class="text-muted">
                                    {{ ucfirst($pengajuan->pegawai?->jenis_pegawai ?? '-') }}
                                    @if ($pengajuan->pegawai?->nip)
                                        | NIP: {{ $pengajuan->pegawai->nip }}
                                    @endif
                                </small>
                            </td>

                            <td>{{ $pengajuan->jenis_pengajuan_label }}</td>

                            <td>
                                {{ $pengajuan->tanggal_mulai->format('d-m-Y') }}
                                sampai
                                {{ $pengajuan->tanggal_selesai->format('d-m-Y') }}
                            </td>

                            <td>
                                @if ($pengajuan->hasBuktiFile())
                                    <a href="{{ asset('storage/' . $pengajuan->bukti_file) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-file-earmark-text"></i>
                                        Lihat
                                    </a>
                                @else
                                    <span class="text-muted">Tidak ada</span>
                                @endif
                            </td>

                            <td>
                                <span class="badge {{ $statusClass }}">
                                    {{ $pengajuan->status_pengajuan_label }}
                                </span>
                            </td>

                            <td class="text-end">
                                <a href="{{ route($routePrefix . '.persetujuan-absensi-pegawai.show', $pengajuan) }}" class="btn btn-sm btn-outline-secondary action-btn">
                                    <i class="bi bi-eye"></i>
                                    <span>Detail</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                Belum ada pengajuan absensi pegawai.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $pengajuans->links() }}
        </div>
    </div>
</div>

@endsection
