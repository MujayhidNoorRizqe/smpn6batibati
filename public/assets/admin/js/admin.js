// penjelasan: File ini adalah JavaScript utama untuk dashboard admin/internal.
// penjelasan: File ini dipanggil dari resources/views/admin/layouts/app.blade.php.
// penjelasan: File ini tidak memakai library eksternal seperti jQuery atau React.
// penjelasan: File ini memakai JavaScript murni dan komponen Bootstrap bawaan.
// penjelasan: File ini mengatur tombol scroll up, modal konfirmasi global, dan validasi custom Bahasa Indonesia.
// penjelasan: Validasi custom ini dibuat agar tidak muncul pesan browser bawaan seperti "Please fill out this field".

document.addEventListener("DOMContentLoaded", function () {
    // =====================================================
    // TOMBOL SCROLL UP
    // =====================================================

    const scrollUpBtn = document.getElementById("scrollUpBtn");

    if (scrollUpBtn) {
        window.addEventListener("scroll", function () {
            if (window.scrollY > 300) {
                scrollUpBtn.classList.add("show");
            } else {
                scrollUpBtn.classList.remove("show");
            }
        });

        scrollUpBtn.addEventListener("click", function () {
            window.scrollTo({
                top: 0,
                behavior: "smooth"
            });
        });
    }

    // =====================================================
    // VALIDASI FORM CUSTOM BAHASA INDONESIA
    // =====================================================

    // penjelasan: Semua form diberi novalidate agar browser tidak menampilkan pesan default Bahasa Inggris.
    // penjelasan: Validasi akan ditangani oleh fungsi validateForm() di bawah.
    document.querySelectorAll("form").forEach(function (form) {
        form.setAttribute("novalidate", "novalidate");
    });

    function getFieldLabel(field) {
        const formGroup = field.closest(".mb-3, .mb-4, .col-md-2, .col-md-3, .col-md-4, .col-md-6, .col-md-8, .col-md-12");
        const label = formGroup ? formGroup.querySelector("label") : null;

        if (label) {
            return label.textContent
                .replace("*", "")
                .replace("(Opsional)", "")
                .trim();
        }

        if (field.getAttribute("placeholder")) {
            return field.getAttribute("placeholder");
        }

        if (field.getAttribute("name")) {
            return field.getAttribute("name").replaceAll("_", " ");
        }

        return "Field ini";
    }

    function getIndonesianValidationMessage(field) {
        const label = getFieldLabel(field);
        const value = (field.value || "").trim();

        if (field.hasAttribute("required") && value === "") {
            return label + " wajib diisi.";
        }

        if (field.tagName.toLowerCase() === "select" && field.hasAttribute("required") && value === "") {
            return label + " wajib dipilih.";
        }

        if (field.getAttribute("type") === "email" && value !== "") {
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            if (!emailPattern.test(value)) {
                return "Format email tidak valid.";
            }
        }

        if (field.getAttribute("type") === "password" && field.hasAttribute("minlength") && value !== "") {
            const minLength = parseInt(field.getAttribute("minlength"), 10);

            if (value.length < minLength) {
                return label + " minimal " + minLength + " karakter.";
            }
        }

        if (field.getAttribute("name") === "password_confirmation") {
            const form = field.closest("form");
            const passwordField = form ? form.querySelector('input[name="password"]') : null;

            if (passwordField && value !== "" && value !== passwordField.value) {
                return "Konfirmasi password tidak sama.";
            }
        }

        return "";
    }

    function clearFieldValidation(field) {
        field.classList.remove("is-invalid");

        const parent = field.parentElement;
        const existingFeedback = parent ? parent.querySelector(".js-validation-feedback") : null;

        if (existingFeedback) {
            existingFeedback.remove();
        }
    }

    function setFieldValidationError(field, message) {
        field.classList.add("is-invalid");

        const parent = field.parentElement;

        if (!parent) {
            return;
        }

        let feedback = parent.querySelector(".js-validation-feedback");

        if (!feedback) {
            feedback = document.createElement("div");
            feedback.className = "invalid-feedback js-validation-feedback";
            parent.appendChild(feedback);
        }

        feedback.textContent = message;
    }

    function showClientAlert(messages) {
        const alertArea = document.getElementById("globalClientAlertArea");

        if (!alertArea) {
            return;
        }

        const listItems = messages.map(function (message) {
            return "<li>" + message + "</li>";
        }).join("");

        alertArea.innerHTML =
            '<div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 rounded-3" role="alert">' +
                '<div class="d-flex align-items-start gap-2">' +
                    '<i class="bi bi-exclamation-octagon-fill mt-1"></i>' +
                    '<div>' +
                        '<strong>Data belum lengkap atau belum sesuai.</strong>' +
                        '<ul class="mb-0 mt-2">' + listItems + '</ul>' +
                    '</div>' +
                '</div>' +
                '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>' +
            '</div>';
    }

    function clearClientAlert() {
        const alertArea = document.getElementById("globalClientAlertArea");

        if (alertArea) {
            alertArea.innerHTML = "";
        }
    }

    function validateForm(form) {
        clearClientAlert();

        const fields = form.querySelectorAll("input, select, textarea");
        const errorMessages = [];
        let firstInvalidField = null;

        fields.forEach(function (field) {
            if (field.disabled || field.type === "hidden" || field.type === "submit" || field.type === "button") {
                return;
            }

            clearFieldValidation(field);

            const message = getIndonesianValidationMessage(field);

            if (message !== "") {
                errorMessages.push(message);
                setFieldValidationError(field, message);

                if (!firstInvalidField) {
                    firstInvalidField = field;
                }
            }
        });

        if (errorMessages.length > 0) {
            showClientAlert(errorMessages);

            if (firstInvalidField) {
                firstInvalidField.focus({ preventScroll: true });

                firstInvalidField.scrollIntoView({
                    behavior: "smooth",
                    block: "center"
                });
            }

            return false;
        }

        return true;
    }

    document.querySelectorAll("form").forEach(function (form) {
        form.addEventListener("submit", function (event) {
            if (!validateForm(form)) {
                event.preventDefault();
            }
        });
    });

    document.addEventListener("input", function (event) {
        const field = event.target;

        if (field.matches("input, textarea")) {
            clearFieldValidation(field);
        }
    });

    document.addEventListener("change", function (event) {
        const field = event.target;

        if (field.matches("select, input[type='file'], input[type='date'], input[type='time']")) {
            clearFieldValidation(field);
        }
    });

    // =====================================================
    // MODAL KONFIRMASI GLOBAL
    // =====================================================

    const confirmModalElement = document.getElementById("globalConfirmModal");
    const confirmMessageElement = document.getElementById("globalConfirmModalMessage");
    const confirmYesButton = document.getElementById("globalConfirmModalYesButton");

    let pendingForm = null;
    let pendingLink = null;

    if (confirmModalElement && confirmMessageElement && confirmYesButton && typeof bootstrap !== "undefined") {
        const confirmModal = new bootstrap.Modal(confirmModalElement);

        document.querySelectorAll('[data-confirm="true"]').forEach(function (element) {
            element.addEventListener("click", function (event) {
                event.preventDefault();

                pendingForm = null;
                pendingLink = null;

                const tagName = element.tagName.toLowerCase();
                const elementType = (element.getAttribute("type") || "").toLowerCase();

                // penjelasan: Link seperti tombol Batal tidak boleh memvalidasi form walaupun posisinya berada di dalam form.
                // penjelasan: Jadi jika elemen adalah <a>, sistem langsung menyimpan href sebagai tujuan redirect setelah konfirmasi.
                if (tagName === "a") {
                    pendingLink = element.getAttribute("href");
                }

                // penjelasan: Button submit baru dianggap sebagai aksi form.
                // penjelasan: Contoh: Simpan, Reset Password, Logout, Setujui, Tolak, Aktifkan, Nonaktifkan.
                if (tagName === "button" && elementType === "submit") {
                    const parentForm = element.closest("form");

                    if (parentForm) {
                        if (!validateForm(parentForm)) {
                            return;
                        }

                        pendingForm = parentForm;
                    }
                }

                const message = element.getAttribute("data-confirm-message") || "Apakah Anda yakin ingin melanjutkan aksi ini?";
                const yesText = element.getAttribute("data-confirm-yes") || "Ya, Lanjutkan";
                const yesClass = element.getAttribute("data-confirm-yes-class") || "btn-primary";

                confirmMessageElement.textContent = message;
                confirmYesButton.textContent = yesText;
                confirmYesButton.className = "btn " + yesClass;

                confirmModal.show();
            });
        });

        confirmYesButton.addEventListener("click", function () {
            if (pendingForm) {
                pendingForm.submit();
                return;
            }

            if (pendingLink && pendingLink !== "#") {
                window.location.href = pendingLink;
            }
        });
    }
});
