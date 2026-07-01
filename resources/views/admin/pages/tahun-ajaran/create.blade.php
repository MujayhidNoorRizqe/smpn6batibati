{{-- penjelasan: File ini adalah halaman tambah tahun ajaran. --}}
{{-- penjelasan: File ini dipanggil oleh TahunAjaranController method create(). --}}

@extends('admin.layouts.app')

@section('title', 'Tambah Tahun Ajaran')

@section('content')

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <h5 class="fw-bold mb-0">Tambah Tahun Ajaran</h5>
                    <small class="text-muted">Tambahkan periode tahun ajaran baru.</small>
                </div>

                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            @foreach ($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    @endif

                    <form action="{{ route($routePrefix . '.tahun-ajaran.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Nama Tahun Ajaran</label>
                            <input
                                type="text"
                                name="nama_tahun_ajaran"
                                class="form-control"
                                value="{{ old('nama_tahun_ajaran') }}"
                                placeholder="Contoh: 2025/2026"
                                required
                            >
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tanggal Mulai</label>
                            <input type="date" name="tanggal_mulai" class="form-control" value="{{ old('tanggal_mulai') }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tanggal Selesai</label>
                            <input type="date" name="tanggal_selesai" class="form-control" value="{{ old('tanggal_selesai') }}">
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select" required>
                                <option value="nonaktif" {{ old('status') === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                                <option value="aktif" {{ old('status') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                            </select>
                            <small class="text-muted">Jika dibuat aktif, tahun ajaran aktif lainnya otomatis menjadi nonaktif.</small>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route($routePrefix . '.tahun-ajaran.index') }}" class="btn btn-outline-secondary">
                                Batal
                            </a>

                            <button type="submit" class="btn btn-primary">
                                Simpan Tahun Ajaran
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection
