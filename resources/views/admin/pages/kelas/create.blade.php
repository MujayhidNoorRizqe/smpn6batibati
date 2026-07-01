{{-- penjelasan: File ini adalah halaman tambah kelas. --}}
{{-- penjelasan: File ini dipanggil oleh KelasController method create(). --}}
{{-- penjelasan: Form ini dikirim ke KelasController method store(). --}}
{{-- penjelasan: Data $gurus berisi pegawai jenis guru aktif untuk pilihan wali kelas. --}}

@extends('admin.layouts.app')

@section('title', 'Tambah Kelas')

@section('content')

    <div class="row justify-content-center">
        <div class="col-lg-8">

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <h5 class="fw-bold mb-0">Tambah Kelas</h5>
                    <small class="text-muted">Tambahkan data kelas baru.</small>
                </div>

                <div class="card-body">

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            @foreach ($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    @endif

                    <form action="{{ route($routePrefix . '.kelas.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Nama Kelas</label>
                            <input
                                type="text"
                                name="nama_kelas"
                                class="form-control"
                                value="{{ old('nama_kelas') }}"
                                placeholder="Contoh: 7A"
                                required
                            >
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tingkat</label>
                            <select name="tingkat" class="form-select" required>
                                <option value="">Pilih Tingkat</option>
                                <option value="7" {{ old('tingkat') === '7' ? 'selected' : '' }}>7</option>
                                <option value="8" {{ old('tingkat') === '8' ? 'selected' : '' }}>8</option>
                                <option value="9" {{ old('tingkat') === '9' ? 'selected' : '' }}>9</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Wali Kelas</label>
                            <select name="wali_kelas_id" class="form-select">
                                <option value="">Belum ditentukan</option>
                                @foreach ($gurus as $guru)
                                    <option value="{{ $guru->id }}" {{ old('wali_kelas_id') == $guru->id ? 'selected' : '' }}>
                                        {{ $guru->nama_pegawai }} - {{ $guru->jabatan ?? 'Guru' }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Wali kelas hanya bisa dipilih dari pegawai jenis guru yang aktif.</small>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select" required>
                                <option value="aktif" {{ old('status') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="nonaktif" {{ old('status') === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                            </select>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route($routePrefix . '.kelas.index') }}" class="btn btn-outline-secondary">
                                Batal
                            </a>

                            <button type="submit" class="btn btn-primary">
                                Simpan Kelas
                            </button>
                        </div>
                    </form>

                </div>
            </div>

        </div>
    </div>

@endsection
