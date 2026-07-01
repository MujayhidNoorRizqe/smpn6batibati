{{-- penjelasan: File ini adalah halaman edit semester. --}}
{{-- penjelasan: File ini dipanggil oleh SemesterController method edit(). --}}

@extends('admin.layouts.app')

@section('title', 'Edit Semester')

@section('content')

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <h5 class="fw-bold mb-0">Edit Semester</h5>
                    <small class="text-muted">Ubah data semester.</small>
                </div>

                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            @foreach ($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    @endif

                    <form action="{{ route($routePrefix . '.semester.update', $semester) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label">Tahun Ajaran</label>
                            <select name="tahun_ajaran_id" class="form-select" required>
                                @foreach ($tahunAjarans as $tahunAjaran)
                                    <option value="{{ $tahunAjaran->id }}" {{ old('tahun_ajaran_id', $semester->tahun_ajaran_id) == $tahunAjaran->id ? 'selected' : '' }}>
                                        {{ $tahunAjaran->nama_tahun_ajaran }} - {{ ucfirst($tahunAjaran->status) }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Semester aktif harus berada pada tahun ajaran yang aktif.</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Semester</label>
                            <select name="nama_semester" class="form-select" required>
                                <option value="ganjil" {{ old('nama_semester', $semester->nama_semester) === 'ganjil' ? 'selected' : '' }}>Ganjil</option>
                                <option value="genap" {{ old('nama_semester', $semester->nama_semester) === 'genap' ? 'selected' : '' }}>Genap</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tanggal Mulai</label>
                            <input
                                type="date"
                                name="tanggal_mulai"
                                class="form-control"
                                value="{{ old('tanggal_mulai', $semester->tanggal_mulai ? $semester->tanggal_mulai->format('Y-m-d') : '') }}"
                            >
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tanggal Selesai</label>
                            <input
                                type="date"
                                name="tanggal_selesai"
                                class="form-control"
                                value="{{ old('tanggal_selesai', $semester->tanggal_selesai ? $semester->tanggal_selesai->format('Y-m-d') : '') }}"
                            >
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select" required>
                                <option value="nonaktif" {{ old('status', $semester->status) === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                                <option value="aktif" {{ old('status', $semester->status) === 'aktif' ? 'selected' : '' }}>Aktif</option>
                            </select>
                            <small class="text-muted">Jika dibuat aktif, semester aktif lainnya otomatis menjadi nonaktif.</small>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route($routePrefix . '.semester.index') }}" class="btn btn-outline-secondary">
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
