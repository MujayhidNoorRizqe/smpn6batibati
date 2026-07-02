{{-- penjelasan: File ini adalah halaman edit murid. --}}
{{-- penjelasan: File ini dipanggil oleh MuridController method edit(). --}}
{{-- penjelasan: Form pada file ini dikirim ke MuridController method update(). --}}
{{-- penjelasan: enctype multipart/form-data wajib digunakan karena form ini bisa mengganti foto murid. --}}
{{-- penjelasan: Alert validasi tidak ditulis lokal karena sudah ditampilkan global dari admin.components.alert. --}}
{{-- penjelasan: Nama murid, kelas, jenis kelamin, wali murid, NISN, tanggal lahir, dan status wajib diisi. --}}
{{-- penjelasan: NIS, tempat lahir, agama, alamat, dan foto bersifat opsional. --}}
{{-- penjelasan: Wali murid dipilih dari data wali murid memakai kolom pencarian langsung. --}}

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

                    <div class="alert alert-info border-0 shadow-sm rounded-3">
                        <i class="bi bi-info-circle me-1"></i>
                        Field bertanda <span class="text-danger">*</span> wajib diisi.
                    </div>

                    @php
                        // penjelasan: Mengambil wali murid yang sedang terhubung atau nilai lama saat validasi gagal.
                        $selectedWaliMurid = $waliMurids->firstWhere('id', old('wali_murid_id', $murid->wali_murid_id));

                        // penjelasan: Label ini ditampilkan pada input pencarian wali murid.
                        $selectedWaliMuridLabel = $selectedWaliMurid
                            ? $selectedWaliMurid->nama_wali . ' - ' . ucfirst($selectedWaliMurid->hubungan) . ' - WA: ' . $selectedWaliMurid->no_whatsapp
                            : '';
                    @endphp

                    <form action="{{ route($routePrefix . '.murid.update', $murid) }}" method="POST" enctype="multipart/form-data" data-murid-form="edit">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label">
                                Nama Murid <span class="text-danger">*</span>
                            </label>

                            <input
                                type="text"
                                name="nama_murid"
                                class="form-control @error('nama_murid') is-invalid @enderror"
                                value="{{ old('nama_murid', $murid->nama_murid) }}"
                                placeholder="Masukkan nama lengkap murid"
                                required
                            >

                            @error('nama_murid')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                Kelas <span class="text-danger">*</span>
                            </label>

                            <select name="kelas_id" class="form-select @error('kelas_id') is-invalid @enderror" required>
                                <option value="">Pilih Kelas</option>

                                @foreach ($kelasList as $kelas)
                                    <option value="{{ $kelas->id }}" {{ old('kelas_id', $murid->kelas_id) == $kelas->id ? 'selected' : '' }}>
                                        {{ $kelas->nama_kelas }} - Tingkat {{ $kelas->tingkat }}
                                    </option>
                                @endforeach
                            </select>

                            @error('kelas_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                Jenis Kelamin <span class="text-danger">*</span>
                            </label>

                            <select name="jenis_kelamin" class="form-select @error('jenis_kelamin') is-invalid @enderror" required>
                                <option value="">Pilih Jenis Kelamin</option>
                                <option value="L" {{ old('jenis_kelamin', $murid->jenis_kelamin) === 'L' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="P" {{ old('jenis_kelamin', $murid->jenis_kelamin) === 'P' ? 'selected' : '' }}>Perempuan</option>
                            </select>

                            @error('jenis_kelamin')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3 position-relative">
                            <label class="form-label">
                                Wali Murid <span class="text-danger">*</span>
                            </label>

                            {{-- penjelasan: Input ini adalah kolom pencarian sekaligus tampilan pilihan wali murid. --}}
                            <input
                                type="text"
                                class="form-control @error('wali_murid_id') is-invalid @enderror"
                                value="{{ $selectedWaliMuridLabel }}"
                                placeholder="Ketik nama wali murid lalu pilih dari daftar"
                                autocomplete="off"
                                required
                                data-wali-keyword="edit"
                            >

                            {{-- penjelasan: Input hidden ini menyimpan id wali murid yang dipilih. --}}
                            <input
                                type="hidden"
                                name="wali_murid_id"
                                value="{{ old('wali_murid_id', $murid->wali_murid_id) }}"
                                data-wali-id="edit"
                            >

                            <div class="invalid-feedback" data-wali-feedback="edit">
                                @error('wali_murid_id')
                                    {{ $message }}
                                @else
                                    Wali murid wajib dipilih dari daftar.
                                @enderror
                            </div>

                            <div
                                class="list-group position-absolute w-100 shadow-sm d-none"
                                style="z-index: 1050; max-height: 240px; overflow-y: auto;"
                                data-wali-options="edit"
                            >
                                @foreach ($waliMurids as $waliMurid)
                                    @php
                                        $waliLabel = $waliMurid->nama_wali . ' - ' . ucfirst($waliMurid->hubungan) . ' - WA: ' . $waliMurid->no_whatsapp;
                                    @endphp

                                    <button
                                        type="button"
                                        class="list-group-item list-group-item-action"
                                        data-wali-option="edit"
                                        data-value="{{ $waliMurid->id }}"
                                        data-label="{{ $waliLabel }}"
                                        data-search="{{ strtolower($waliLabel) }}"
                                    >
                                        {{ $waliLabel }}
                                    </button>
                                @endforeach

                                <div class="list-group-item text-muted d-none" data-wali-empty="edit">
                                    Data wali murid tidak ditemukan.
                                </div>
                            </div>

                            <small class="text-muted">
                                Wali murid wajib dipilih dari data wali murid yang aktif atau wali yang sedang terhubung dengan murid ini.
                            </small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                NIS <span class="text-muted">(Opsional)</span>
                            </label>

                            <input
                                type="text"
                                name="nis"
                                class="form-control @error('nis') is-invalid @enderror"
                                value="{{ old('nis', $murid->nis) }}"
                                placeholder="Masukkan NIS jika ada"
                            >

                            @error('nis')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                NISN <span class="text-danger">*</span>
                            </label>

                            <input
                                type="text"
                                name="nisn"
                                class="form-control @error('nisn') is-invalid @enderror"
                                value="{{ old('nisn', $murid->nisn) }}"
                                placeholder="Masukkan NISN"
                                required
                            >

                            @error('nisn')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                Tanggal Lahir <span class="text-danger">*</span>
                            </label>

                            <input
                                type="date"
                                name="tanggal_lahir"
                                class="form-control @error('tanggal_lahir') is-invalid @enderror"
                                value="{{ old('tanggal_lahir', $murid->tanggal_lahir ? $murid->tanggal_lahir->format('Y-m-d') : '') }}"
                                required
                            >

                            @error('tanggal_lahir')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror

                            <small class="text-muted">
                                Tanggal lahir dipilih manual sesuai data murid.
                            </small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                Tempat Lahir <span class="text-muted">(Opsional)</span>
                            </label>

                            <input
                                type="text"
                                name="tempat_lahir"
                                class="form-control @error('tempat_lahir') is-invalid @enderror"
                                value="{{ old('tempat_lahir', $murid->tempat_lahir) }}"
                                placeholder="Masukkan tempat lahir jika ada"
                            >

                            @error('tempat_lahir')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                Agama <span class="text-muted">(Opsional)</span>
                            </label>

                            <input
                                type="text"
                                name="agama"
                                class="form-control @error('agama') is-invalid @enderror"
                                value="{{ old('agama', $murid->agama) }}"
                                placeholder="Contoh: Islam"
                            >

                            @error('agama')
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
                                placeholder="Masukkan alamat murid jika ada"
                            >{{ old('alamat', $murid->alamat) }}</textarea>

                            @error('alamat')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                Foto Murid <span class="text-muted">(Opsional)</span>
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

                            @if ($murid->foto)
                                <small class="text-muted d-block mt-2">Foto saat ini:</small>
                                <img src="{{ asset('storage/' . $murid->foto) }}" alt="Foto Murid" width="90" class="rounded mt-1">
                            @endif
                        </div>

                        <div class="mb-4">
                            <label class="form-label">
                                Status <span class="text-danger">*</span>
                            </label>

                            <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                <option value="aktif" {{ old('status', $murid->status) === 'aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="nonaktif" {{ old('status', $murid->status) === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                            </select>

                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a
                                href="{{ route($routePrefix . '.murid.index') }}"
                                class="btn btn-outline-secondary"
                                data-confirm="true"
                                data-confirm-message="Batalkan edit murid? Perubahan yang belum disimpan akan hilang."
                                data-confirm-yes="Ya, Batalkan"
                                data-confirm-yes-class="btn-danger"
                            >
                                Batal
                            </a>

                            <button
                                type="submit"
                                class="btn btn-primary"
                                data-confirm="true"
                                data-confirm-message="Apakah Anda yakin ingin menyimpan perubahan data murid ini?"
                                data-confirm-yes="Ya, Simpan Perubahan"
                                data-confirm-yes-class="btn-primary"
                                data-murid-submit="edit"
                            >
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

@push('scripts')
    <script>
        // penjelasan: Script ini membuat input wali murid menjadi searchable dropdown custom.
        // penjelasan: User mengetik langsung pada kolom Wali Murid, lalu memilih data yang muncul.
        document.addEventListener('DOMContentLoaded', function () {
            const key = 'edit';
            const keywordInput = document.querySelector('[data-wali-keyword="' + key + '"]');
            const hiddenIdInput = document.querySelector('[data-wali-id="' + key + '"]');
            const optionBox = document.querySelector('[data-wali-options="' + key + '"]');
            const optionButtons = document.querySelectorAll('[data-wali-option="' + key + '"]');
            const emptyMessage = document.querySelector('[data-wali-empty="' + key + '"]');
            const feedback = document.querySelector('[data-wali-feedback="' + key + '"]');
            const submitButton = document.querySelector('[data-murid-submit="' + key + '"]');

            if (!keywordInput || !hiddenIdInput || !optionBox) {
                return;
            }

            function showClientAlert(message) {
                const alertArea = document.getElementById('globalClientAlertArea');

                if (!alertArea) {
                    return;
                }

                alertArea.innerHTML =
                    '<div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 rounded-3" role="alert">' +
                        '<div class="d-flex align-items-start gap-2">' +
                            '<i class="bi bi-exclamation-octagon-fill mt-1"></i>' +
                            '<div>' +
                                '<strong>Data belum lengkap atau belum sesuai.</strong>' +
                                '<ul class="mb-0 mt-2"><li>' + message + '</li></ul>' +
                            '</div>' +
                        '</div>' +
                        '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>' +
                    '</div>';
            }

            function openOptions() {
                optionBox.classList.remove('d-none');
            }

            function closeOptions() {
                optionBox.classList.add('d-none');
            }

            function setInvalid(message) {
                keywordInput.classList.add('is-invalid');

                if (feedback) {
                    feedback.textContent = message;
                    feedback.style.display = 'block';
                }
            }

            function clearInvalid() {
                keywordInput.classList.remove('is-invalid');

                if (feedback) {
                    feedback.style.display = '';
                }
            }

            function filterOptions() {
                const keyword = keywordInput.value.toLowerCase().trim();
                let visibleCount = 0;

                optionButtons.forEach(function (button) {
                    const searchText = button.getAttribute('data-search') || '';

                    if (keyword === '' || searchText.includes(keyword)) {
                        button.classList.remove('d-none');
                        visibleCount++;
                    } else {
                        button.classList.add('d-none');
                    }
                });

                if (emptyMessage) {
                    if (visibleCount === 0) {
                        emptyMessage.classList.remove('d-none');
                    } else {
                        emptyMessage.classList.add('d-none');
                    }
                }

                openOptions();
            }

            keywordInput.addEventListener('input', function () {
                hiddenIdInput.value = '';
                clearInvalid();
                filterOptions();
            });

            keywordInput.addEventListener('focus', function () {
                filterOptions();
            });

            optionButtons.forEach(function (button) {
                button.addEventListener('click', function () {
                    keywordInput.value = button.getAttribute('data-label');
                    hiddenIdInput.value = button.getAttribute('data-value');
                    clearInvalid();
                    closeOptions();
                });
            });

            document.addEventListener('click', function (event) {
                if (!optionBox.contains(event.target) && event.target !== keywordInput) {
                    closeOptions();
                }
            });

            if (submitButton) {
                submitButton.addEventListener('click', function (event) {
                    if (!hiddenIdInput.value) {
                        event.preventDefault();
                        event.stopImmediatePropagation();

                        const message = 'Wali murid wajib dipilih dari daftar yang tersedia.';
                        setInvalid(message);
                        showClientAlert(message);

                        keywordInput.focus();
                        keywordInput.scrollIntoView({
                            behavior: 'smooth',
                            block: 'center'
                        });
                    }
                }, true);
            }
        });
    </script>
@endpush
