{{-- penjelasan: File ini adalah halaman detail wali murid. --}}
{{-- penjelasan: File ini dipanggil oleh WaliMuridController method show(). --}}
{{-- penjelasan: Halaman ini menampilkan informasi lengkap wali murid. --}}
{{-- penjelasan: Nanti saat modul Data Murid dibuat, halaman ini bisa ditambahkan daftar anak/murid yang terhubung. --}}

@extends('admin.layouts.app')

@section('title', 'Detail Wali Murid')

@section('content')

    <div class="row justify-content-center">
        <div class="col-lg-9">

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
                    <div>
                        <h5 class="fw-bold mb-0">Detail Wali Murid</h5>
                        <small class="text-muted">Informasi lengkap orang tua atau wali murid</small>
                    </div>

                    @if ($waliMurid->status === 'aktif')
                        <span class="badge bg-success-subtle text-success">
                            Aktif
                        </span>
                    @else
                        <span class="badge bg-danger-subtle text-danger">
                            Nonaktif
                        </span>
                    @endif
                </div>

                <div class="card-body">

                    <div class="row mb-3">
                        <div class="col-md-4 text-muted">Nama Wali</div>
                        <div class="col-md-8 fw-semibold">{{ $waliMurid->nama_wali }}</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4 text-muted">Hubungan</div>
                        <div class="col-md-8">
                            <span class="badge bg-primary-subtle text-primary">
                                {{ ucfirst($waliMurid->hubungan) }}
                            </span>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4 text-muted">NIK</div>
                        <div class="col-md-8 fw-semibold">{{ $waliMurid->nik ?? '-' }}</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4 text-muted">Pekerjaan</div>
                        <div class="col-md-8 fw-semibold">{{ $waliMurid->pekerjaan ?? '-' }}</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4 text-muted">Nomor HP</div>
                        <div class="col-md-8 fw-semibold">{{ $waliMurid->no_hp ?? '-' }}</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4 text-muted">Nomor WhatsApp</div>
                        <div class="col-md-8 fw-semibold">
                            <span class="badge bg-success-subtle text-success">
                                {{ $waliMurid->no_whatsapp }}
                            </span>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-4 text-muted">Alamat</div>
                        <div class="col-md-8 fw-semibold">{{ $waliMurid->alamat }}</div>
                    </div>

                    <hr>

                    <div class="row mb-4">
                        <div class="col-md-4 text-muted">Dibuat / Diperbarui</div>
                        <div class="col-md-8 fw-semibold">
                            {{ $waliMurid->created_at ? $waliMurid->created_at->format('d-m-Y H:i') : '-' }}
                            /
                            {{ $waliMurid->updated_at ? $waliMurid->updated_at->format('d-m-Y H:i') : '-' }}
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route($routePrefix . '.wali-murid.index') }}" class="btn btn-outline-secondary">
                            Kembali
                        </a>

                        <a href="{{ route($routePrefix . '.wali-murid.edit', $waliMurid) }}" class="btn btn-primary">
                            <i class="bi bi-pencil-square me-1"></i>
                            Edit Wali Murid
                        </a>
                    </div>

                </div>
            </div>

        </div>
    </div>

@endsection
