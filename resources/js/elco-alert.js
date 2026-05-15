window.elcoConfirm = function ({
    title = "Yakin?",
    text = "",
    confirmText = "Ya, Lanjutkan",
    confirmColor = "#5C3D2E",
    icon = "warning",
    onConfirm,
}) {
    Swal.fire({
        title,
        text,
        icon,
        showCancelButton: true,
        confirmButtonText: confirmText,
        cancelButtonText: "Batal",
        confirmButtonColor: confirmColor,
        cancelButtonColor: "#f3f4f6",
        customClass: {
            popup: "swal-elco-popup",
            confirmButton: "swal-elco-confirm",
            cancelButton: "swal-elco-cancel",
        },
    }).then((result) => {
        if (result.isConfirmed && onConfirm) onConfirm();
    });
};

// Notifikasi sukses
window.elcoSuccess = function (title, text = "") {
    Swal.fire({
        title,
        text,
        icon: "success",
        confirmButtonText: "OK",
        confirmButtonColor: "#5C3D2E",
        customClass: {
            popup: "swal-elco-popup",
            confirmButton: "swal-elco-confirm",
        },
    });
};

// Notifikasi error
window.elcoError = function (title, text = "") {
    Swal.fire({
        title,
        text,
        icon: "error",
        confirmButtonText: "OK",
        confirmButtonColor: "#ef4444",
        customClass: {
            popup: "swal-elco-popup",
            confirmButton: "swal-elco-confirm",
        },
    });
};
