// penjelasan: File ini adalah JavaScript utama untuk dashboard admin/internal.
// penjelasan: File ini dipanggil dari resources/views/admin/layouts/app.blade.php.
// penjelasan: File ini tidak memakai library eksternal seperti jQuery atau React.
// penjelasan: File ini memakai JavaScript murni dan fitur bawaan browser seperti document, window, dan addEventListener.

// penjelasan: DOMContentLoaded memastikan kode JavaScript dijalankan setelah struktur HTML selesai dibaca browser.
document.addEventListener("DOMContentLoaded", function () {
    // penjelasan: Mengambil tombol scroll up berdasarkan id scrollUpBtn.
    const scrollUpBtn = document.getElementById("scrollUpBtn");

    // penjelasan: Jika tombol scrollUpBtn tidak ditemukan, kode dihentikan agar tidak error.
    if (!scrollUpBtn) {
        return;
    }

    // penjelasan: Event scroll dijalankan setiap halaman discroll.
    window.addEventListener("scroll", function () {
        // penjelasan: Jika posisi scroll lebih dari 300px, tombol scroll up ditampilkan.
        if (window.scrollY > 300) {
            scrollUpBtn.classList.add("show");
        } else {
            // penjelasan: Jika posisi scroll kurang dari atau sama dengan 300px, tombol disembunyikan.
            scrollUpBtn.classList.remove("show");
        }
    });

    // penjelasan: Event click dijalankan saat tombol scroll up diklik.
    scrollUpBtn.addEventListener("click", function () {
        // penjelasan: window.scrollTo digunakan untuk mengarahkan halaman kembali ke atas.
        // penjelasan: behavior smooth membuat scroll bergerak halus, bukan langsung lompat.
        window.scrollTo({
            top: 0,
            behavior: "smooth"
        });
    });
});
