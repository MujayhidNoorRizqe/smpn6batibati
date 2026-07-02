{{-- penjelasan: Component ini adalah modal konfirmasi global. --}}
{{-- penjelasan: Modal ini dipakai untuk aksi sensitif seperti logout, hapus data, batalkan pengajuan, setujui, tolak, aktifkan, dan nonaktifkan. --}}
{{-- penjelasan: Modal ini dikendalikan oleh JavaScript di public/assets/admin/js/admin.js. --}}
{{-- penjelasan: Semua tombol/link yang ingin memakai modal cukup menambahkan data-confirm="true". --}}
{{-- penjelasan: Seluruh teks modal memakai Bahasa Indonesia agar selaras dengan tema sistem. --}}

<div class="modal fade" id="globalConfirmModal" tabindex="-1" aria-labelledby="globalConfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header border-0 pb-0">
                <div>
                    <h5 class="modal-title fw-bold" id="globalConfirmModalLabel">
                        Konfirmasi Aksi
                    </h5>

                    <small class="text-muted">
                        Pastikan data dan aksi sudah benar sebelum dilanjutkan.
                    </small>
                </div>

                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>

            <div class="modal-body">
                <div class="d-flex align-items-start gap-3">
                    <div class="rounded-circle bg-warning-subtle text-warning d-flex align-items-center justify-content-center flex-shrink-0" style="width: 42px; height: 42px;">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                    </div>

                    <p class="mb-0" id="globalConfirmModalMessage">
                        Apakah Anda yakin ingin melanjutkan aksi ini?
                    </p>
                </div>
            </div>

            <div class="modal-footer border-0">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    Batal
                </button>

                <button type="button" class="btn btn-primary" id="globalConfirmModalYesButton">
                    Ya, Lanjutkan
                </button>
            </div>
        </div>
    </div>
</div>
