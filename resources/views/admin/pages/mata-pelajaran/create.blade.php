{{-- penjelasan: File ini adalah halaman tambah mata pelajaran. --}}
{{-- penjelasan: File ini dipanggil oleh MataPelajaranController method create(). --}}
{{-- penjelasan: Form pada file ini dikirim ke MataPelajaranController method store(). --}}
{{-- penjelasan: Halaman ini bisa dipakai oleh Super Admin dan Admin karena route memakai routePrefix. --}}

@extends('admin.layouts.app')

@section('title', 'Tambah Mata Pelajaran')

@section('content')

    <div class="row justify-content-center">
        <div class="col-lg-8">

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <h5 class="fw-bold mb-0">Tambah Mata Pelajaran</h5>
                    <small class="text-muted">Tambahkan data mata pelajaran baru.</small>
                </div>

                <div class="card-body">

                    {{-- penjelasan: Bagian ini menampilkan error validasi dari controller. --}}
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            @foreach ($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    @endif

                    {{-- penjelasan: Form ini mengirim data mata pelajaran baru ke route mata-pelajaran.store. --}}
                    {{-- penjelasan: Method POST digunakan karena form ini membuat data baru. --}}
                    <form action="{{ route($routePrefix . '.mata-pelajaran.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Kode Mata Pelajaran</label>
                            <input
                                type="text"
                                name="kode_mapel"
                                class="form-control"
                                value="{{ old('kode_mapel') }}"
                                placeholder="Contoh: A1"
                                required
                            >
                            <small class="text-muted">Kode opsional</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nama Mata Pelajaran</label>
                            <input
                                type="text"
                                name="nama_mapel"
                                class="form-control"
                                value="{{ old('nama_mapel') }}"
                                placeholder="Contoh: Matematika"
                                required
                            >
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Kelompok</label>
                            <select name="kelompok" class="form-select" required>
                                <option value="">Pilih Kelompok</option>
                                <option value="umum" {{ old('kelompok') === 'umum' ? 'selected' : '' }}>Umum</option>
                                <option value="muatan_lokal" {{ old('kelompok') === 'muatan_lokal' ? 'selected' : '' }}>Muatan Lokal</option>
                                <option value="ekstrakurikuler" {{ old('kelompok') === 'ekstrakurikuler' ? 'selected' : '' }}>Ekstrakurikuler</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Deskripsi</label>
                            <textarea
                                name="deskripsi"
                                class="form-control"
                                rows="3"
                                placeholder="Masukkan deskripsi jika diperlukan"
                            >{{ old('deskripsi') }}</textarea>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select" required>
                                <option value="aktif" {{ old('status') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="nonaktif" {{ old('status') === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                            </select>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route($routePrefix . '.mata-pelajaran.index') }}" class="btn btn-outline-secondary">
                                Batal
                            </a>

                            <button type="submit" class="btn btn-primary">
                                Simpan Mata Pelajaran
                            </button>
                        </div>
                    </form>

                </div>
            </div>

        </div>
    </div>

@endsection
