{{-- penjelasan: File ini adalah halaman edit wali murid. --}}
{{-- penjelasan: File ini dipanggil oleh WaliMuridController method edit(). --}}
{{-- penjelasan: Form pada file ini dikirim ke WaliMuridController method update(). --}}
{{-- penjelasan: Data $waliMurid berisi data wali murid yang sedang diedit. --}}

@extends('admin.layouts.app')

@section('title', 'Edit Wali Murid')

@section('content')

    <div class="row justify-content-center">
        <div class="col-lg-8">

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <h5 class="fw-bold mb-0">Edit Wali Murid</h5>
                    <small class="text-muted">Ubah data orang tua atau wali murid.</small>
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

                    {{-- penjelasan: Form ini mengirim perubahan data ke route wali-murid.update. --}}
                    {{-- penjelasan: @method('PUT') digunakan karena HTML form tidak mendukung PUT secara langsung. --}}
                    <form action="{{ route($routePrefix . '.wali-murid.update', $waliMurid) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label">Nama Wali</label>
                            <input
                                type="text"
                                name="nama_wali"
                                class="form-control"
                                value="{{ old('nama_wali', $waliMurid->nama_wali) }}"
                                required
                            >
                        </div>

                        <div class="mb-3">
                            <label class="form-label">NIK</label>
                            <input
                                type="text"
                                name="nik"
                                class="form-control"
                                value="{{ old('nik', $waliMurid->nik) }}"
                            >
                            <small class="text-muted">NIK boleh dikosongkan, tetapi jika diisi tidak boleh sama dengan wali lain.</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Hubungan</label>
                            <select name="hubungan" class="form-select" required>
                                <option value="ayah" {{ old('hubungan', $waliMurid->hubungan) === 'ayah' ? 'selected' : '' }}>Ayah</option>
                                <option value="ibu" {{ old('hubungan', $waliMurid->hubungan) === 'ibu' ? 'selected' : '' }}>Ibu</option>
                                <option value="wali" {{ old('hubungan', $waliMurid->hubungan) === 'wali' ? 'selected' : '' }}>Wali</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Pekerjaan</label>
                            <input
                                type="text"
                                name="pekerjaan"
                                class="form-control"
                                value="{{ old('pekerjaan', $waliMurid->pekerjaan) }}"
                            >
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nomor HP</label>
                            <input
                                type="text"
                                name="no_hp"
                                class="form-control"
                                value="{{ old('no_hp', $waliMurid->no_hp) }}"
                            >
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nomor WhatsApp</label>
                            <input
                                type="text"
                                name="no_whatsapp"
                                class="form-control"
                                value="{{ old('no_whatsapp', $waliMurid->no_whatsapp) }}"
                            >
                            <small class="text-muted">Nomor ini nanti digunakan untuk notifikasi Fonnte.</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Alamat</label>
                            <textarea
                                name="alamat"
                                class="form-control"
                                rows="3"
                            >{{ old('alamat', $waliMurid->alamat) }}</textarea>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select" required>
                                <option value="aktif" {{ old('status', $waliMurid->status) === 'aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="nonaktif" {{ old('status', $waliMurid->status) === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                            </select>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route($routePrefix . '.wali-murid.index') }}" class="btn btn-outline-secondary">
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
