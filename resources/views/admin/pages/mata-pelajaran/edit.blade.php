{{-- penjelasan: File ini adalah halaman edit mata pelajaran. --}}
{{-- penjelasan: File ini dipanggil oleh MataPelajaranController method edit(). --}}
{{-- penjelasan: Form pada file ini dikirim ke MataPelajaranController method update(). --}}
{{-- penjelasan: Data $mataPelajaran berisi data mata pelajaran yang sedang diedit. --}}

@extends('admin.layouts.app')

@section('title', 'Edit Mata Pelajaran')

@section('content')

    <div class="row justify-content-center">
        <div class="col-lg-8">

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <h5 class="fw-bold mb-0">Edit Mata Pelajaran</h5>
                    <small class="text-muted">Ubah data mata pelajaran.</small>
                </div>

                <div class="card-body">

                    {{-- penjelasan: Bagian ini menampilkan semua error validasi dari controller. --}}
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            @foreach ($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    @endif

                    {{-- penjelasan: Form ini mengirim perubahan data ke route mata-pelajaran.update. --}}
                    {{-- penjelasan: @method('PUT') digunakan karena HTML form tidak mendukung PUT secara langsung. --}}
                    <form action="{{ route($routePrefix . '.mata-pelajaran.update', $mataPelajaran) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label">Kode Mata Pelajaran</label>
                            <input
                                type="text"
                                name="kode_mapel"
                                class="form-control"
                                value="{{ old('kode_mapel', $mataPelajaran->kode_mapel) }}"
                                required
                            >
                            <small class="text-muted">Kode akan otomatis disimpan huruf besar.</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nama Mata Pelajaran</label>
                            <input
                                type="text"
                                name="nama_mapel"
                                class="form-control"
                                value="{{ old('nama_mapel', $mataPelajaran->nama_mapel) }}"
                                required
                            >
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Kelompok</label>
                            <select name="kelompok" class="form-select" required>
                                <option value="umum" {{ old('kelompok', $mataPelajaran->kelompok) === 'umum' ? 'selected' : '' }}>Umum</option>
                                <option value="muatan_lokal" {{ old('kelompok', $mataPelajaran->kelompok) === 'muatan_lokal' ? 'selected' : '' }}>Muatan Lokal</option>
                                <option value="ekstrakurikuler" {{ old('kelompok', $mataPelajaran->kelompok) === 'ekstrakurikuler' ? 'selected' : '' }}>Ekstrakurikuler</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Deskripsi</label>
                            <textarea
                                name="deskripsi"
                                class="form-control"
                                rows="3"
                            >{{ old('deskripsi', $mataPelajaran->deskripsi) }}</textarea>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select" required>
                                <option value="aktif" {{ old('status', $mataPelajaran->status) === 'aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="nonaktif" {{ old('status', $mataPelajaran->status) === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                            </select>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route($routePrefix . '.mata-pelajaran.index') }}" class="btn btn-outline-secondary">
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
