{{-- penjelasan: File ini adalah halaman tambah pegawai. --}}
{{-- penjelasan: File ini dipanggil oleh PegawaiController method create(). --}}
{{-- penjelasan: Form ini dikirim ke PegawaiController method store(). --}}
{{-- penjelasan: enctype multipart/form-data wajib digunakan karena form memiliki upload foto. --}}

@extends('admin.layouts.app')

@section('title', 'Tambah Pegawai')

@section('content')

    <div class="row justify-content-center">
        <div class="col-lg-9">

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <h5 class="fw-bold mb-0">Tambah Pegawai</h5>
                    <small class="text-muted">Tambahkan data guru atau staff.</small>
                </div>

                <div class="card-body">

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            @foreach ($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    @endif

                    <form action="{{ route($routePrefix . '.pegawai.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Akun Login</label>
                            <select name="user_id" class="form-select">
                                <option value="">Belum dihubungkan</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }} - {{ $user->email }} - {{ ucwords($user->role) }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Pilih akun guru/staff jika pegawai sudah memiliki akun login.</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">NIP</label>
                            <input type="text" name="nip" class="form-control" value="{{ old('nip') }}" placeholder="Masukkan NIP jika ada">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nama Pegawai</label>
                            <input type="text" name="nama_pegawai" class="form-control" value="{{ old('nama_pegawai') }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Jenis Pegawai</label>
                            <select name="jenis_pegawai" class="form-select" required>
                                <option value="">Pilih Jenis</option>
                                <option value="guru" {{ old('jenis_pegawai') === 'guru' ? 'selected' : '' }}>Guru</option>
                                <option value="staff" {{ old('jenis_pegawai') === 'staff' ? 'selected' : '' }}>Staff</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Jabatan</label>
                            <input type="text" name="jabatan" class="form-control" value="{{ old('jabatan') }}" placeholder="Contoh: Guru Matematika, Staff TU">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Jenis Kelamin</label>
                            <select name="jenis_kelamin" class="form-select">
                                <option value="">Pilih Jenis Kelamin</option>
                                <option value="L" {{ old('jenis_kelamin') === 'L' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="P" {{ old('jenis_kelamin') === 'P' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nomor HP</label>
                            <input type="text" name="no_hp" class="form-control" value="{{ old('no_hp') }}" placeholder="Masukkan nomor HP">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Alamat</label>
                            <textarea name="alamat" class="form-control" rows="3" placeholder="Masukkan alamat">{{ old('alamat') }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Foto Pegawai</label>
                            <input type="file" name="foto" class="form-control" accept="image/*">
                            <small class="text-muted">Format jpg, jpeg, png, webp. Maksimal 2MB.</small>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select" required>
                                <option value="aktif" {{ old('status') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="nonaktif" {{ old('status') === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                            </select>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route($routePrefix . '.pegawai.index') }}" class="btn btn-outline-secondary">
                                Batal
                            </a>

                            <button type="submit" class="btn btn-primary">
                                Simpan Pegawai
                            </button>
                        </div>
                    </form>

                </div>
            </div>

        </div>
    </div>

@endsection
