{{-- penjelasan: Halaman ini digunakan guru untuk menginput absensi murid pada jadwal hari ini. --}}
{{-- penjelasan: Semua murid aktif pada kelas terkait wajib diberi status absensi. --}}
{{-- penjelasan: Daftar murid yang tampil hanya murid dari kelas pada jadwal yang dipilih. --}}
{{-- penjelasan: Tanggal absensi otomatis dari sistem. --}}
{{-- penjelasan: Status absensi murid terdiri dari hadir, izin, sakit, alpha, dan terlambat. --}}

@extends('admin.layouts.app')

@section('title', 'Input Absensi Murid')

@section('content')

<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div>
                    <h4 class="fw-bold mb-1">Input Absensi Murid</h4>
                    <p class="text-muted mb-0">
                        Kelas {{ $jadwalPelajaran->kelas?->nama_kelas ?? '-' }} - {{ $jadwalPelajaran->mataPelajaran?->nama_mapel ?? '-' }}.
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

@if ($errors->any())
    <div class="alert alert-danger border-0 rounded-3 shadow-sm">
        <div class="fw-semibold mb-1">
            <i class="bi bi-exclamation-triangle me-1"></i>
            Data belum valid
        </div>

        <ul class="mb-0 ps-3">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-0">
        <h6 class="fw-bold mb-0">Informasi Jadwal</h6>
        <small class="text-muted">Pastikan jadwal dan kelas sudah sesuai sebelum menyimpan absensi.</small>
    </div>

    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-3">
                <div class="text-muted small">Kelas</div>
                <div class="fw-semibold">{{ $jadwalPelajaran->kelas?->nama_kelas ?? '-' }}</div>
            </div>

            <div class="col-md-3">
                <div class="text-muted small">Mata Pelajaran</div>
                <div class="fw-semibold">{{ $jadwalPelajaran->mataPelajaran?->nama_mapel ?? '-' }}</div>
            </div>

            <div class="col-md-3">
                <div class="text-muted small">Jam</div>
                <div class="fw-semibold">
                    {{ $jadwalPelajaran->jam_mulai ? substr($jadwalPelajaran->jam_mulai, 0, 5) : '-' }}
                    -
                    {{ $jadwalPelajaran->jam_selesai ? substr($jadwalPelajaran->jam_selesai, 0, 5) : '-' }}
                </div>
            </div>

            <div class="col-md-3">
                <div class="text-muted small">Semester</div>
                <div class="fw-semibold">
                    {{ ucfirst($jadwalPelajaran->semester?->nama_semester ?? '-') }}
                    -
                    {{ $jadwalPelajaran->tahunAjaran?->nama_tahun_ajaran ?? '-' }}
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
        <div>
            <h6 class="fw-bold mb-0">Daftar Murid Kelas {{ $jadwalPelajaran->kelas?->nama_kelas ?? '-' }}</h6>
            <small class="text-muted">
                Status absensi setiap murid wajib dipilih.
            </small>
        </div>

        <span class="badge bg-primary-subtle text-primary">
            {{ $murids->count() }} murid aktif
        </span>
    </div>

    <div class="card-body">
        @if ($murids->count() > 0)
            <form action="{{ route('guru.absensi-murid.store', $jadwalPelajaran) }}" method="POST">
                @csrf

                <div class="table-responsive">
                    <table class="table table-hover align-middle admin-table">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 60px;">No</th>
                                <th>Nama Murid</th>
                                <th>NISN</th>
                                <th style="width: 230px;">Status <span class="text-danger">*</span></th>
                                <th>Keterangan <span class="text-muted">(Opsional)</span></th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($murids as $murid)
                                @php
                                    $absensi = $absensiTersimpan->get($murid->id);
                                    $selectedStatus = old('absensi.' . $loop->index . '.status_absen', $absensi?->status_absen ?? 'hadir');
                                    $keterangan = old('absensi.' . $loop->index . '.keterangan', $absensi?->keterangan);
                                @endphp

                                <tr>
                                    <td>{{ $loop->iteration }}</td>

                                    <td class="fw-semibold">
                                        {{ $murid->nama_murid }}
                                        <input type="hidden" name="absensi[{{ $loop->index }}][murid_id]" value="{{ $murid->id }}">
                                    </td>

                                    <td>{{ $murid->nisn ?? '-' }}</td>

                                    <td>
                                        <select
                                            name="absensi[{{ $loop->index }}][status_absen]"
                                            class="form-select @error('absensi.' . $loop->index . '.status_absen') is-invalid @enderror"
                                            required
                                        >
                                            <option value="hadir" {{ $selectedStatus === 'hadir' ? 'selected' : '' }}>Hadir</option>
                                            <option value="izin" {{ $selectedStatus === 'izin' ? 'selected' : '' }}>Izin</option>
                                            <option value="sakit" {{ $selectedStatus === 'sakit' ? 'selected' : '' }}>Sakit</option>
                                            <option value="alpha" {{ $selectedStatus === 'alpha' ? 'selected' : '' }}>Alpha</option>
                                            <option value="terlambat" {{ $selectedStatus === 'terlambat' ? 'selected' : '' }}>Terlambat</option>
                                        </select>

                                        @error('absensi.' . $loop->index . '.status_absen')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </td>

                                    <td>
                                        <input
                                            type="text"
                                            name="absensi[{{ $loop->index }}][keterangan]"
                                            class="form-control @error('absensi.' . $loop->index . '.keterangan') is-invalid @enderror"
                                            value="{{ $keterangan }}"
                                            placeholder="Contoh: izin keluarga, sakit demam, datang terlambat"
                                        >

                                        @error('absensi.' . $loop->index . '.keterangan')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-3">
                    <a
                        href="{{ route('guru.absensi-murid.index') }}"
                        class="btn btn-outline-secondary"
                        data-confirm="true"
                        data-confirm-message="Batalkan input absensi murid? Perubahan yang belum disimpan akan hilang."
                        data-confirm-yes="Ya, Batalkan"
                        data-confirm-yes-class="btn-danger"
                    >
                        Batal
                    </a>

                    <button
                        type="submit"
                        class="btn btn-primary"
                        data-confirm="true"
                        data-confirm-message="Apakah Anda yakin ingin menyimpan absensi murid kelas {{ $jadwalPelajaran->kelas?->nama_kelas ?? '-' }}?"
                        data-confirm-yes="Ya, Simpan Absensi"
                        data-confirm-yes-class="btn-primary"
                    >
                        <i class="bi bi-save me-1"></i>
                        Simpan Absensi
                    </button>
                </div>
            </form>
        @else
            <div class="alert alert-warning border-0 rounded-3 mb-0">
                <i class="bi bi-exclamation-triangle me-1"></i>
                Tidak ada murid aktif pada kelas ini.
            </div>

            <div class="d-flex justify-content-end mt-3">
                <a href="{{ route('guru.absensi-murid.index') }}" class="btn btn-outline-secondary">
                    Kembali
                </a>
            </div>
        @endif
    </div>
</div>

@endsection
