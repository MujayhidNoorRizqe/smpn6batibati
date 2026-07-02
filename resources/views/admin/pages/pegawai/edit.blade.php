{{-- penjelasan: File ini adalah halaman edit pegawai. --}}
{{-- penjelasan: File ini dipanggil oleh PegawaiController method edit(). --}}
{{-- penjelasan: Form ini dikirim ke PegawaiController method update(). --}}
{{-- penjelasan: Form memakai multipart/form-data karena bisa mengganti foto pegawai. --}}
{{-- penjelasan: Alert validasi tidak ditulis lokal karena sudah ditampilkan global dari admin.components.alert. --}}
{{-- penjelasan: Semua field wajib diberi tanda (*) dan field opsional diberi label (Opsional). --}}

@extends('admin.layouts.app')

@section('title', 'Edit Pegawai')

@section('content')

    <div class="row justify-content-center">
        <div class="col-lg-9">

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <h5 class="fw-bold mb-0">Edit Pegawai</h5>
                    <small class="text-muted">Ubah data guru atau staff.</small>
                </div>

                <div class="card-body">

                    <div class="alert alert-info border-0 shadow-sm rounded-3">
                        <i class="bi bi-info-circle me-1"></i>
                        Field bertanda <span class="text-danger">*</span> wajib diisi.
                    </div>

                    <form action="{{ route($routePrefix . '.pegawai.update', $pegawai) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label">
                                Akun Login <span class="text-muted">(Opsional)</span>
                            </label>

                            <select name="user_id" class="form-select @error('user_id') is-invalid @enderror">
                                <option value="">Belum dihubungkan</option>

                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}" {{ old('user_id', $pegawai->user_id) == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }} - {{ $user->email }} - {{ ucwords($user->role) }}
                                    </option>
                                @endforeach
                            </select>

                            @error('user_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror

                            <small class="text-muted">
                                Akun login hanya menampilkan user guru/staff yang belum dipakai pegawai lain atau akun yang sedang terhubung.
                            </small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                NIP <span class="text-muted">(Opsional)</span>
                            </label>

                            <input
                                type="text"
                                name="nip"
                                class="form-control @error('nip') is-invalid @enderror"
                                value="{{ old('nip', $pegawai->nip) }}"
                                placeholder="Masukkan NIP jika ada"
                            >

                            @error('nip')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                Nama Pegawai <span class="text-danger">*</span>
                            </label>

                            <input
                                type="text"
                                name="nama_pegawai"
                                class="form-control @error('nama_pegawai') is-invalid @enderror"
                                value="{{ old('nama_pegawai', $pegawai->nama_pegawai) }}"
                                required
                            >

                            @error('nama_pegawai')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                Jenis Pegawai <span class="text-danger">*</span>
                            </label>

                            <select name="jenis_pegawai" class="form-select @error('jenis_pegawai') is-invalid @enderror" required>
                                <option value="guru" {{ old('jenis_pegawai', $pegawai->jenis_pegawai) === 'guru' ? 'selected' : '' }}>Guru</option>
                                <option value="staff" {{ old('jenis_pegawai', $pegawai->jenis_pegawai) === 'staff' ? 'selected' : '' }}>Staff</option>
                            </select>

                            @error('jenis_pegawai')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                Jabatan <span class="text-muted">(Opsional)</span>
                            </label>

                            <input
                                type="text"
                                name="jabatan"
                                class="form-control @error('jabatan') is-invalid @enderror"
                                value="{{ old('jabatan', $pegawai->jabatan) }}"
                                placeholder="Contoh: Guru Matematika, Staff TU"
                            >

                            @error('jabatan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                Jenis Kelamin <span class="text-muted">(Opsional)</span>
                            </label>

                            <select name="jenis_kelamin" class="form-select @error('jenis_kelamin') is-invalid @enderror">
                                <option value="">Pilih Jenis Kelamin</option>
                                <option value="L" {{ old('jenis_kelamin', $pegawai->jenis_kelamin) === 'L' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="P" {{ old('jenis_kelamin', $pegawai->jenis_kelamin) === 'P' ? 'selected' : '' }}>Perempuan</option>
                            </select>

                            @error('jenis_kelamin')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                Nomor HP <span class="text-muted">(Opsional)</span>
                            </label>

                            <input
                                type="text"
                                name="no_hp"
                                class="form-control @error('no_hp') is-invalid @enderror"
                                value="{{ old('no_hp', $pegawai->no_hp) }}"
                                placeholder="Masukkan nomor HP"
                            >

                            @error('no_hp')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                Alamat <span class="text-muted">(Opsional)</span>
                            </label>

                            <textarea
                                name="alamat"
                                class="form-control @error('alamat') is-invalid @enderror"
                                rows="3"
                                placeholder="Masukkan alamat"
                            >{{ old('alamat', $pegawai->alamat) }}</textarea>

                            @error('alamat')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                Foto Pegawai <span class="text-muted">(Opsional)</span>
                            </label>

                            <input
                                type="file"
                                name="foto"
                                class="form-control @error('foto') is-invalid @enderror"
                                accept="image/jpeg,image/png,image/jpg,image/webp"
                            >

                            @error('foto')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror

                            <small class="text-muted d-block">
                                Format jpg, jpeg, png, atau webp. Maksimal 2MB.
                            </small>

                            @if ($pegawai->foto)
                                <small class="text-muted d-block mt-2">Foto saat ini:</small>
                                <img src="{{ asset('storage/' . $pegawai->foto) }}" alt="Foto Pegawai" width="90" class="rounded mt-1">
                            @endif
                        </div>

                        <div class="mb-4">
                            <label class="form-label">
                                Status <span class="text-danger">*</span>
                            </label>

                            <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                <option value="aktif" {{ old('status', $pegawai->status) === 'aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="nonaktif" {{ old('status', $pegawai->status) === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                            </select>

                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a
                                href="{{ route($routePrefix . '.pegawai.index') }}"
                                class="btn btn-outline-secondary"
                                data-confirm="true"
                                data-confirm-message="Batalkan edit pegawai? Perubahan yang belum disimpan akan hilang."
                                data-confirm-yes="Ya, Batalkan"
                                data-confirm-yes-class="btn-danger"
                            >
                                Batal
                            </a>

                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-1"></i>
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>

                </div>
            </div>

        </div>
    </div>

@endsection
