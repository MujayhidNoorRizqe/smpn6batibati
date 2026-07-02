{{-- penjelasan: Component ini digunakan untuk menampilkan alert global. --}}
{{-- penjelasan: Alert ini dipanggil di layout utama admin.layouts.app agar semua halaman otomatis punya notifikasi yang konsisten. --}}
{{-- penjelasan: Alert mendukung session success, error, warning, info, status, dan error validasi Laravel. --}}
{{-- penjelasan: Semua teks alert memakai Bahasa Indonesia dan tampilan Bootstrap soft agar selaras dengan tema dashboard. --}}

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 rounded-3" role="alert">
        <div class="d-flex align-items-start gap-2">
            <i class="bi bi-check-circle-fill mt-1"></i>

            <div>
                <strong>Berhasil!</strong>
                <div>{{ session('success') }}</div>
            </div>
        </div>

        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
    </div>
@endif

@if (session('status'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 rounded-3" role="alert">
        <div class="d-flex align-items-start gap-2">
            <i class="bi bi-check-circle-fill mt-1"></i>

            <div>
                <strong>Berhasil!</strong>
                <div>{{ session('status') }}</div>
            </div>
        </div>

        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 rounded-3" role="alert">
        <div class="d-flex align-items-start gap-2">
            <i class="bi bi-x-circle-fill mt-1"></i>

            <div>
                <strong>Gagal!</strong>
                <div>{{ session('error') }}</div>
            </div>
        </div>

        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
    </div>
@endif

@if (session('warning'))
    <div class="alert alert-warning alert-dismissible fade show shadow-sm border-0 rounded-3" role="alert">
        <div class="d-flex align-items-start gap-2">
            <i class="bi bi-exclamation-triangle-fill mt-1"></i>

            <div>
                <strong>Perhatian!</strong>
                <div>{{ session('warning') }}</div>
            </div>
        </div>

        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
    </div>
@endif

@if (session('info'))
    <div class="alert alert-info alert-dismissible fade show shadow-sm border-0 rounded-3" role="alert">
        <div class="d-flex align-items-start gap-2">
            <i class="bi bi-info-circle-fill mt-1"></i>

            <div>
                <strong>Informasi!</strong>
                <div>{{ session('info') }}</div>
            </div>
        </div>

        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 rounded-3" role="alert">
        <div class="d-flex align-items-start gap-2">
            <i class="bi bi-exclamation-octagon-fill mt-1"></i>

            <div>
                <strong>Data belum lengkap atau belum sesuai.</strong>

                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>

        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
    </div>
@endif
