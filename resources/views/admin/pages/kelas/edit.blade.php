{{-- penjelasan: File ini adalah halaman edit kelas. --}}
{{-- penjelasan: File ini dipanggil oleh KelasController method edit(). --}}
{{-- penjelasan: Form ini dikirim ke KelasController method update(). --}}
{{-- penjelasan: Data $kelas berisi data kelas yang sedang diedit. --}}
{{-- penjelasan: Data $gurus berisi pegawai guru aktif untuk pilihan wali kelas. --}}

@extends('admin.layouts.app')

@section('title', 'Edit Kelas')

@section('content')

    <div class="row justify-content-center">
        <div class="col-lg-8">

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <h5 class="fw-bold mb-0">Edit Kelas</h5>
                    <small class="text-muted">Ubah data kelas.</small>
                </div>

                <div class="card-body">

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            @foreach ($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    @endif

                    <form action="{{ route($routePrefix . '.kelas.update', $kelas) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label">Nama Kelas</label>
                            <input
                                type="text"
                                name="nama_kelas"
                                class="form-control"
                                value="{{ old('nama_kelas', $kelas->nama_kelas) }}"
                                required
                            >
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tingkat</label>
                            <select name="tingkat" class="form-select" required>
                                <option value="7" {{ old('tingkat', $kelas->tingkat) === '7' ? 'selected' : '' }}>7</option>
                                <option value="8" {{ old('tingkat', $kelas->tingkat) === '8' ? 'selected' : '' }}>8</option>
                                <option value="9" {{ old('tingkat', $kelas->tingkat) === '9' ? 'selected' : '' }}>9</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Wali Kelas</label>
                            <select name="wali_kelas_id" class="form-select">
                                <option value="">Belum ditentukan</option>
                                @foreach ($gurus as $guru)
                                    <option value="{{ $guru->id }}" {{ old('wali_kelas_id', $kelas->wali_kelas_id) == $guru->id ? 'selected' : '' }}>
                                        {{ $guru->nama_pegawai }} - {{ $guru->jabatan ?? 'Guru' }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Wali kelas hanya bisa dipilih dari pegawai jenis guru yang aktif.</small>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select" required>
                                <option value="aktif" {{ old('status', $kelas->status) === 'aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="nonaktif" {{ old('status', $kelas->status) === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                            </select>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route($routePrefix . '.kelas.index') }}" class="btn btn-outline-secondary">
                                Batal
                            </a>

                            <button type="submit" class="btn btn-primary">
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>

                </div>
            </div>

        </div>
    </div>

@endsection
