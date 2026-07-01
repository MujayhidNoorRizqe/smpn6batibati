{{-- penjelasan: File ini adalah halaman edit murid. --}}
{{-- penjelasan: File ini dipanggil oleh MuridController method edit(). --}}
{{-- penjelasan: Form pada file ini dikirim ke MuridController method update(). --}}
{{-- penjelasan: enctype multipart/form-data wajib digunakan karena form ini bisa mengganti foto murid. --}}

@extends('admin.layouts.app')

@section('title', 'Edit Murid')

@section('content')

    <div class="row justify-content-center">
        <div class="col-lg-9">

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <h5 class="fw-bold mb-0">Edit Murid</h5>
                    <small class="text-muted">Ubah data siswa.</small>
                </div>

                <div class="card-body">

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            @foreach ($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    @endif

                    <form action="{{ route($routePrefix . '.murid.update', $murid) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label">Kelas</label>
                            <select name="kelas_id" class="form-select" required>
                                @foreach ($kelasList as $kelas)
                                    <option value="{{ $kelas->id }}" {{ old('kelas_id', $murid->kelas_id) == $kelas->id ? 'selected' : '' }}>
                                        {{ $kelas->nama_kelas }} - Tingkat {{ $kelas->tingkat }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Wali Murid</label>
                            <select name="wali_murid_id" class="form-select" required>
                                @foreach ($waliMurids as $waliMurid)
                                    <option value="{{ $waliMurid->id }}" {{ old('wali_murid_id', $murid->wali_murid_id) == $waliMurid->id ? 'selected' : '' }}>
                                        {{ $waliMurid->nama_wali }} - {{ ucfirst($waliMurid->hubungan) }} - WA: {{ $waliMurid->no_whatsapp ?? '-' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">NIS</label>
                            <input type="text" name="nis" class="form-control" value="{{ old('nis', $murid->nis) }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">NISN</label>
                            <input type="text" name="nisn" class="form-control" value="{{ old('nisn', $murid->nisn) }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nama Murid</label>
                            <input type="text" name="nama_murid" class="form-control" value="{{ old('nama_murid', $murid->nama_murid) }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Jenis Kelamin</label>
                            <select name="jenis_kelamin" class="form-select" required>
                                <option value="L" {{ old('jenis_kelamin', $murid->jenis_kelamin) === 'L' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="P" {{ old('jenis_kelamin', $murid->jenis_kelamin) === 'P' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tempat Lahir</label>
                            <input type="text" name="tempat_lahir" class="form-control" value="{{ old('tempat_lahir', $murid->tempat_lahir) }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tanggal Lahir</label>
                            <input
                                type="date"
                                name="tanggal_lahir"
                                class="form-control"
                                value="{{ old('tanggal_lahir', $murid->tanggal_lahir ? $murid->tanggal_lahir->format('Y-m-d') : '') }}"
                            >
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Agama</label>
                            <input type="text" name="agama" class="form-control" value="{{ old('agama', $murid->agama) }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Alamat</label>
                            <textarea name="alamat" class="form-control" rows="3">{{ old('alamat', $murid->alamat) }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Foto Murid</label>
                            <input type="file" name="foto" class="form-control" accept="image/*">

                            @if ($murid->foto)
                                <small class="text-muted d-block mt-2">Foto saat ini:</small>
                                <img src="{{ asset('storage/' . $murid->foto) }}" alt="Foto Murid" width="90" class="rounded mt-1">
                            @endif
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select" required>
                                <option value="aktif" {{ old('status', $murid->status) === 'aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="nonaktif" {{ old('status', $murid->status) === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                            </select>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route($routePrefix . '.murid.index') }}" class="btn btn-outline-secondary">
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
