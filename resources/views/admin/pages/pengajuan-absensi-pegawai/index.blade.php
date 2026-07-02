{{-- penjelasan: Halaman ini menampilkan riwayat pengajuan absensi milik guru/staff yang sedang login. --}}
{{-- penjelasan: Alert berhasil, gagal, dan validasi sudah memakai komponen global admin.components.alert. --}}
{{-- penjelasan: Filter tidak diberi label opsional karena filter hanya fitur pencarian. --}}
{{-- penjelasan: Pengajuan yang masih menunggu dapat diedit atau dibatalkan. --}}

@extends('admin.layouts.app')

@section('title', 'Pengajuan Absensi')

@section('content')

<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div>
                    <h4 class="fw-bold mb-1">Pengajuan Absensi</h4>
                    <p class="text-muted mb-0">Ajukan dinas, sakit, atau izin dari luar jaringan sekolah.</p>
                </div>

                <a href="{{ route($routePrefix . '.pengajuan-absensi-pegawai.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-1"></i>
                    Buat Pengajuan
                </a>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form action="{{ route($routePrefix . '.pengajuan-absensi-pegawai.index') }}" method="GET" class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Jenis Pengajuan</label>
                <select name="jenis_pengajuan" class="form-select">
                    <option value="">Semua Jenis</option>
                    <option value="dinas" {{ request('jenis_pengajuan') === 'dinas' ? 'selected' : '' }}>Dinas</option>
                    <option value="sakit" {{ request('jenis_pengajuan') === 'sakit' ? 'selected' : '' }}>Sakit</option>
                    <option value="izin" {{ request('jenis_pengajuan') === 'izin' ? 'selected' : '' }}>Izin</option>
                </select>
            </div>

            <div class="col-md-4">
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

            <div class="col-md-2 d-flex align-items-end">
                <a href="{{ route($routePrefix . '.pengajuan-absensi-pegawai.index') }}" class="btn btn-outline-secondary w-100">
                    Reset
                </a>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
        <div>
            <h6 class="fw-bold mb-0">Riwayat Pengajuan Absensi</h6>
            <small class="text-muted">Data pengajuan dinas, sakit, dan izin yang pernah dibuat</small>
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
                        <th>Jenis</th>
                        <th>Tanggal</th>
                        <th>Alasan</th>
                        <th>Bukti</th>
                        <th>Status</th>
                        <th class="text-end table-action-column">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($pengajuans as $pengajuan)
                        @php
                            // penjelasan: Warna badge disesuaikan dengan status pengajuan.
                            $statusClass = match ($pengajuan->status_pengajuan) {
                                'menunggu' => 'bg-warning-subtle text-warning',
                                'disetujui' => 'bg-success-subtle text-success',
                                'ditolak' => 'bg-danger-subtle text-danger',
                                'dibatalkan' => 'bg-secondary-subtle text-secondary',
                                default => 'bg-secondary-subtle text-secondary',
                            };
                        @endphp

                        <tr>
                            <td class="fw-semibold">{{ $pengajuan->jenis_pengajuan_label }}</td>

                            <td>
                                {{ $pengajuan->tanggal_mulai->format('d-m-Y') }}
                                sampai
                                {{ $pengajuan->tanggal_selesai->format('d-m-Y') }}
                            </td>

                            <td>{{ Str::limit($pengajuan->alasan, 60) }}</td>

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
                                <div class="action-buttons">
                                    <a href="{{ route($routePrefix . '.pengajuan-absensi-pegawai.show', $pengajuan) }}" class="btn btn-sm btn-outline-secondary action-btn">
                                        <i class="bi bi-eye"></i>
                                        <span>Detail</span>
                                    </a>

                                    @if ($pengajuan->isMenunggu())
                                        <a href="{{ route($routePrefix . '.pengajuan-absensi-pegawai.edit', $pengajuan) }}" class="btn btn-sm btn-outline-primary action-btn">
                                            <i class="bi bi-pencil-square"></i>
                                            <span>Edit</span>
                                        </a>

                                        <form action="{{ route($routePrefix . '.pengajuan-absensi-pegawai.cancel', $pengajuan) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')

                                            <button
                                                type="submit"
                                                class="btn btn-sm btn-outline-danger action-btn"
                                                data-confirm="true"
                                                data-confirm-message="Apakah Anda yakin ingin membatalkan pengajuan ini?"
                                                data-confirm-yes="Ya, Batalkan"
                                                data-confirm-yes-class="btn-danger"
                                            >
                                                <i class="bi bi-x-circle"></i>
                                                <span>Batalkan</span>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                Belum ada pengajuan absensi.
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
