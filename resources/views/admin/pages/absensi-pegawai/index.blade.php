{{-- penjelasan: Halaman ini digunakan guru/staff untuk melakukan absen masuk dan absen pulang. --}}
{{-- penjelasan: Tanggal dan jam absensi otomatis dari sistem, bukan input manual. --}}
{{-- penjelasan: Lokasi GPS browser dipakai untuk validasi radius sekolah. --}}
{{-- penjelasan: Jika pegawai sudah memiliki status dinas/sakit/izin dari pengajuan, tombol absen masuk/pulang dikunci. --}}

@extends('admin.layouts.app')

@section('title', 'Absen Saya')

@section('content')

@php
    $statusHariIni = $absensiHariIni?->status_absen;
    $isStatusPengajuan = $absensiHariIni && in_array($statusHariIni, ['dinas', 'sakit', 'izin'], true);

    $canAbsenMasuk = ! $absensiHariIni || (! $absensiHariIni->jam_masuk && ! $isStatusPengajuan);

    $canAbsenPulang = $absensiHariIni
        && $absensiHariIni->jam_masuk
        && ! $absensiHariIni->jam_pulang
        && in_array($statusHariIni, ['hadir', 'terlambat'], true);

    $statusClass = match ($statusHariIni) {
        'hadir' => 'bg-success-subtle text-success',
        'terlambat' => 'bg-warning-subtle text-warning',
        'dinas' => 'bg-primary-subtle text-primary',
        'izin' => 'bg-info-subtle text-info',
        'sakit' => 'bg-danger-subtle text-danger',
        'alpha' => 'bg-danger-subtle text-danger',
        default => 'bg-secondary-subtle text-secondary',
    };
@endphp

<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div>
                    <h4 class="fw-bold mb-1">Absen Saya</h4>
                    <p class="text-muted mb-0">
                        Absen masuk dan pulang menggunakan lokasi GPS sekolah.
                    </p>
                </div>

                <div class="text-md-end">
                    <div class="fw-semibold">{{ now()->format('d-m-Y') }}</div>
                    <small class="text-muted">Tanggal otomatis dari sistem</small>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-0">
                <h5 class="fw-bold mb-0">Status Hari Ini</h5>
                <small class="text-muted">Ringkasan absensi Anda hari ini</small>
            </div>

            <div class="card-body">
                <div class="mb-3">
                    <div class="text-muted">Nama Pegawai</div>
                    <div class="fw-semibold">{{ $pegawai->nama_pegawai }}</div>
                </div>

                <div class="mb-3">
                    <div class="text-muted">Status</div>

                    @if ($absensiHariIni)
                        <span class="badge {{ $statusClass }}">
                            {{ $absensiHariIni->status_absen_label }}
                        </span>
                    @else
                        <span class="badge bg-secondary-subtle text-secondary">
                            Belum Absen
                        </span>
                    @endif
                </div>

                <div class="row mb-3">
                    <div class="col-6">
                        <div class="text-muted">Jam Masuk</div>
                        <div class="fw-semibold">
                            {{ $absensiHariIni?->jam_masuk_format ?? '-' }}
                        </div>
                    </div>

                    <div class="col-6">
                        <div class="text-muted">Jam Pulang</div>
                        <div class="fw-semibold">
                            {{ $absensiHariIni?->jam_pulang_format ?? '-' }}
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="text-muted">Metode</div>
                    <div class="fw-semibold">
                        {{ $absensiHariIni?->metode_absen_label ?? '-' }}
                    </div>
                </div>

                <div class="mb-0">
                    <div class="text-muted">Keterangan</div>
                    <div class="fw-semibold">
                        {{ $absensiHariIni?->keterangan ?? '-' }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-0">
                <h5 class="fw-bold mb-0">Aksi Absensi</h5>
                <small class="text-muted">Pastikan lokasi aktif sebelum melakukan absen</small>
            </div>

            <div class="card-body">
                <div class="alert alert-info border-0 shadow-sm rounded-3">
                    <i class="bi bi-info-circle me-1"></i>
                    Jam masuk normal {{ $pengaturanAbsensi['jam_masuk'] }},
                    batas terlambat {{ $pengaturanAbsensi['batas_terlambat'] }},
                    dan absen pulang minimal pukul {{ $pengaturanAbsensi['jam_pulang_minimal'] }}.
                </div>

                <div class="alert alert-secondary border-0 shadow-sm rounded-3" id="locationStatus">
                    <i class="bi bi-geo-alt me-1"></i>
                    Mengambil lokasi perangkat...
                </div>

                @if ($isStatusPengajuan)
                    <div class="alert alert-warning border-0 shadow-sm rounded-3">
                        <i class="bi bi-exclamation-triangle me-1"></i>
                        Anda sudah memiliki status {{ $absensiHariIni->status_absen_label }} dari pengajuan yang disetujui. Absen masuk dan pulang tidak diperlukan.
                    </div>
                @endif

                <div class="row g-3">
                    <div class="col-md-6">
                        <form action="{{ route($routePrefix . '.absensi-pegawai.masuk') }}" method="POST" data-location-form>
                            @csrf

                            <input type="hidden" name="latitude" data-latitude>
                            <input type="hidden" name="longitude" data-longitude>

                            <button
                                type="submit"
                                class="btn btn-primary w-100"
                                data-attendance-submit
                                data-confirm="true"
                                data-confirm-message="Apakah Anda yakin ingin melakukan absen masuk sekarang?"
                                data-confirm-yes="Ya, Absen Masuk"
                                data-confirm-yes-class="btn-primary"
                                {{ $canAbsenMasuk ? '' : 'disabled' }}
                            >
                                <i class="bi bi-box-arrow-in-right me-1"></i>
                                Absen Masuk
                            </button>
                        </form>
                    </div>

                    <div class="col-md-6">
                        <form action="{{ route($routePrefix . '.absensi-pegawai.pulang') }}" method="POST" data-location-form>
                            @csrf
                            @method('PATCH')

                            <input type="hidden" name="latitude" data-latitude>
                            <input type="hidden" name="longitude" data-longitude>

                            <button
                                type="submit"
                                class="btn btn-success w-100"
                                data-attendance-submit
                                data-confirm="true"
                                data-confirm-message="Apakah Anda yakin ingin melakukan absen pulang sekarang?"
                                data-confirm-yes="Ya, Absen Pulang"
                                data-confirm-yes-class="btn-success"
                                {{ $canAbsenPulang ? '' : 'disabled' }}
                            >
                                <i class="bi bi-box-arrow-left me-1"></i>
                                Absen Pulang
                            </button>
                        </form>
                    </div>
                </div>

                <div class="mt-3 text-muted small">
                    Radius validasi lokasi sekolah: {{ $pengaturanAbsensi['radius_meter'] }} meter.
                    Browser akan meminta izin akses lokasi saat absen.
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
        <div>
            <h6 class="fw-bold mb-0">Riwayat Absensi Saya</h6>
            <small class="text-muted">Data absensi resmi yang sudah tercatat</small>
        </div>

        <span class="badge bg-primary-subtle text-primary">
            {{ $riwayatAbsensi->count() }} data tampil
        </span>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle admin-table">
                <thead class="table-light">
                    <tr>
                        <th>Tanggal</th>
                        <th>Status</th>
                        <th>Jam Masuk</th>
                        <th>Jam Pulang</th>
                        <th>Metode</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($riwayatAbsensi as $absensi)
                        @php
                            $badgeClass = match ($absensi->status_absen) {
                                'hadir' => 'bg-success-subtle text-success',
                                'terlambat' => 'bg-warning-subtle text-warning',
                                'dinas' => 'bg-primary-subtle text-primary',
                                'izin' => 'bg-info-subtle text-info',
                                'sakit' => 'bg-danger-subtle text-danger',
                                'alpha' => 'bg-danger-subtle text-danger',
                                default => 'bg-secondary-subtle text-secondary',
                            };
                        @endphp

                        <tr>
                            <td>{{ $absensi->tanggal_absen->format('d-m-Y') }}</td>

                            <td>
                                <span class="badge {{ $badgeClass }}">
                                    {{ $absensi->status_absen_label }}
                                </span>
                            </td>

                            <td>{{ $absensi->jam_masuk_format }}</td>
                            <td>{{ $absensi->jam_pulang_format }}</td>
                            <td>{{ $absensi->metode_absen_label }}</td>
                            <td>{{ Str::limit($absensi->keterangan, 70) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                Riwayat absensi belum tersedia.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $riwayatAbsensi->links() }}
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // penjelasan: Script ini mengambil lokasi GPS dari browser.
    // penjelasan: Latitude dan longitude dimasukkan ke hidden input sebelum form absen dikirim.
    document.addEventListener('DOMContentLoaded', function () {
        const locationStatus = document.getElementById('locationStatus');
        const attendanceButtons = document.querySelectorAll('[data-attendance-submit]');
        const latitudeInputs = document.querySelectorAll('[data-latitude]');
        const longitudeInputs = document.querySelectorAll('[data-longitude]');

        let currentLatitude = null;
        let currentLongitude = null;

        function showClientAlert(message) {
            const alertArea = document.getElementById('globalClientAlertArea');

            if (!alertArea) {
                alert(message);
                return;
            }

            alertArea.innerHTML = `
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3" role="alert">
                    <i class="bi bi-exclamation-triangle me-1"></i>
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                </div>
            `;

            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function setLocationStatus(type, message) {
            if (!locationStatus) {
                return;
            }

            locationStatus.className = `alert alert-${type} border-0 shadow-sm rounded-3`;
            locationStatus.innerHTML = `<i class="bi bi-geo-alt me-1"></i>${message}`;
        }

        function fillLocationInputs() {
            latitudeInputs.forEach(function (input) {
                input.value = currentLatitude ?? '';
            });

            longitudeInputs.forEach(function (input) {
                input.value = currentLongitude ?? '';
            });
        }

        function requestLocation() {
            if (!navigator.geolocation) {
                setLocationStatus('danger', 'Browser tidak mendukung akses lokasi. Gunakan browser terbaru.');
                return;
            }

            navigator.geolocation.getCurrentPosition(
                function (position) {
                    currentLatitude = position.coords.latitude;
                    currentLongitude = position.coords.longitude;

                    fillLocationInputs();

                    setLocationStatus(
                        'success',
                        'Lokasi berhasil terbaca. Anda bisa melakukan absen.'
                    );
                },
                function () {
                    currentLatitude = null;
                    currentLongitude = null;

                    setLocationStatus(
                        'danger',
                        'Lokasi belum diizinkan. Aktifkan izin lokasi browser sebelum absen.'
                    );
                },
                {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 0
                }
            );
        }

        attendanceButtons.forEach(function (button) {
            button.addEventListener('click', function (event) {
                if (button.disabled) {
                    return;
                }

                if (currentLatitude === null || currentLongitude === null) {
                    event.preventDefault();
                    event.stopPropagation();

                    showClientAlert('Lokasi belum terbaca. Aktifkan izin lokasi browser, lalu coba lagi.');
                    requestLocation();
                    return;
                }

                fillLocationInputs();
            }, true);
        });

        requestLocation();
    });
</script>
@endpush
