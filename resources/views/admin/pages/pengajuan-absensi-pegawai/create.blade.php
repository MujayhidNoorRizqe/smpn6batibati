{{-- penjelasan: Halaman ini digunakan guru/staff untuk membuat pengajuan dinas, sakit, atau izin. --}}
{{-- penjelasan: Alert validasi tidak ditulis lokal karena sudah ditampilkan global dari admin.components.alert. --}}
{{-- penjelasan: Jenis pengajuan, tanggal mulai, tanggal selesai, dan alasan wajib diisi. --}}
{{-- penjelasan: Judul dan lokasi wajib untuk dinas. --}}
{{-- penjelasan: Bukti file wajib untuk dinas dan sakit, tetapi opsional untuk izin. --}}

@extends('admin.layouts.app')

@section('title', 'Buat Pengajuan Absensi')

@section('content')

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0">
                <h5 class="fw-bold mb-0">Buat Pengajuan Absensi</h5>
                <small class="text-muted">Dinas dan sakit wajib upload bukti. Izin cukup isi alasan.</small>
            </div>

            <div class="card-body">
                <div class="alert alert-info border-0 shadow-sm rounded-3">
                    <i class="bi bi-info-circle me-1"></i>
                    Field bertanda <span class="text-danger">*</span> wajib diisi.
                </div>

                <form action="{{ route($routePrefix . '.pengajuan-absensi-pegawai.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">
                            Jenis Pengajuan <span class="text-danger">*</span>
                        </label>

                        <select name="jenis_pengajuan" class="form-select @error('jenis_pengajuan') is-invalid @enderror" required data-jenis-pengajuan>
                            <option value="">Pilih Jenis</option>
                            <option value="dinas" {{ old('jenis_pengajuan') === 'dinas' ? 'selected' : '' }}>Dinas</option>
                            <option value="sakit" {{ old('jenis_pengajuan') === 'sakit' ? 'selected' : '' }}>Sakit</option>
                            <option value="izin" {{ old('jenis_pengajuan') === 'izin' ? 'selected' : '' }}>Izin</option>
                        </select>

                        @error('jenis_pengajuan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">
                            Tanggal Mulai <span class="text-danger">*</span>
                        </label>

                        <input
                            type="date"
                            name="tanggal_mulai"
                            class="form-control @error('tanggal_mulai') is-invalid @enderror"
                            value="{{ old('tanggal_mulai') }}"
                            required
                        >

                        @error('tanggal_mulai')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">
                            Tanggal Selesai <span class="text-danger">*</span>
                        </label>

                        <input
                            type="date"
                            name="tanggal_selesai"
                            class="form-control @error('tanggal_selesai') is-invalid @enderror"
                            value="{{ old('tanggal_selesai') }}"
                            required
                        >

                        @error('tanggal_selesai')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror

                        <small class="text-muted">
                            Tanggal selesai tidak boleh lebih kecil dari tanggal mulai.
                        </small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">
                            Judul Pengajuan / Kegiatan
                            <span class="text-muted" data-label-judul>(Wajib untuk dinas)</span>
                        </label>

                        <input
                            type="text"
                            name="judul_pengajuan"
                            class="form-control @error('judul_pengajuan') is-invalid @enderror"
                            value="{{ old('judul_pengajuan') }}"
                            placeholder="Contoh: Rapat koordinasi, pelatihan, workshop"
                            data-judul-pengajuan
                        >

                        @error('judul_pengajuan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">
                            Lokasi Kegiatan
                            <span class="text-muted" data-label-lokasi>(Wajib untuk dinas)</span>
                        </label>

                        <input
                            type="text"
                            name="lokasi_kegiatan"
                            class="form-control @error('lokasi_kegiatan') is-invalid @enderror"
                            value="{{ old('lokasi_kegiatan') }}"
                            placeholder="Contoh: Dinas Pendidikan Kabupaten"
                            data-lokasi-kegiatan
                        >

                        @error('lokasi_kegiatan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">
                            Alasan / Keterangan <span class="text-danger">*</span>
                        </label>

                        <textarea
                            name="alasan"
                            class="form-control @error('alasan') is-invalid @enderror"
                            rows="4"
                            placeholder="Tuliskan alasan atau keterangan pengajuan"
                            required
                        >{{ old('alasan') }}</textarea>

                        @error('alasan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label">
                            Bukti Foto/File
                            <span class="text-muted" data-label-bukti>(Wajib untuk dinas dan sakit)</span>
                        </label>

                        <input
                            type="file"
                            name="bukti_file"
                            class="form-control @error('bukti_file') is-invalid @enderror"
                            accept=".jpg,.jpeg,.png,.webp,.pdf,.doc,.docx"
                            data-bukti-file
                        >

                        @error('bukti_file')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror

                        <small class="text-muted">
                            Format: jpg, jpeg, png, webp, pdf, doc, docx. Maksimal 4 MB.
                        </small>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a
                            href="{{ route($routePrefix . '.pengajuan-absensi-pegawai.index') }}"
                            class="btn btn-outline-secondary"
                            data-confirm="true"
                            data-confirm-message="Batalkan buat pengajuan? Data yang belum dikirim akan hilang."
                            data-confirm-yes="Ya, Batalkan"
                            data-confirm-yes-class="btn-danger"
                        >
                            Batal
                        </a>

                        <button
                            type="submit"
                            class="btn btn-primary"
                            data-confirm="true"
                            data-confirm-message="Apakah Anda yakin ingin mengirim pengajuan absensi ini?"
                            data-confirm-yes="Ya, Kirim"
                            data-confirm-yes-class="btn-primary"
                        >
                            <i class="bi bi-send me-1"></i>
                            Kirim Pengajuan
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
    // penjelasan: Script ini menyesuaikan field yang wajib berdasarkan jenis pengajuan.
    document.addEventListener('DOMContentLoaded', function () {
        const jenisInput = document.querySelector('[data-jenis-pengajuan]');
        const judulInput = document.querySelector('[data-judul-pengajuan]');
        const lokasiInput = document.querySelector('[data-lokasi-kegiatan]');
        const buktiInput = document.querySelector('[data-bukti-file]');
        const labelJudul = document.querySelector('[data-label-judul]');
        const labelLokasi = document.querySelector('[data-label-lokasi]');
        const labelBukti = document.querySelector('[data-label-bukti]');

        function refreshRequirement() {
            const jenis = jenisInput ? jenisInput.value : '';

            if (judulInput) {
                judulInput.required = jenis === 'dinas';
            }

            if (lokasiInput) {
                lokasiInput.required = jenis === 'dinas';
            }

            if (buktiInput) {
                buktiInput.required = jenis === 'dinas' || jenis === 'sakit';
            }

            if (labelJudul) {
                labelJudul.innerHTML = jenis === 'dinas'
                    ? '<span class="text-danger">*</span>'
                    : '<span class="text-muted">(Wajib untuk dinas)</span>';
            }

            if (labelLokasi) {
                labelLokasi.innerHTML = jenis === 'dinas'
                    ? '<span class="text-danger">*</span>'
                    : '<span class="text-muted">(Wajib untuk dinas)</span>';
            }

            if (labelBukti) {
                labelBukti.innerHTML = jenis === 'dinas' || jenis === 'sakit'
                    ? '<span class="text-danger">*</span>'
                    : '<span class="text-muted">(Wajib untuk dinas dan sakit)</span>';
            }
        }

        if (jenisInput) {
            jenisInput.addEventListener('change', refreshRequirement);
            refreshRequirement();
        }
    });
</script>
@endpush
