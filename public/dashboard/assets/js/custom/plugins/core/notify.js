"use strict";

/**
 * PluginNotify
 * Single responsibility: show notifications via SweetAlert2.
 *
 * Dependencies: SweetAlert2 (Swal)
 */
window.PluginNotify = (function () {
    /**
     * Show a toast notification.
     * @param {"success"|"error"|"warning"} type
     * @param {string} title
     * @param {string} text
     */
    function show(type, title, text) {
        if (typeof Swal === "undefined") return;

        Swal.fire({
            icon: type,
            title: title,
            text: text,
            confirmButtonText: "OK",
        });
    }

    /**
     * Show a confirmation dialog.
     * Resolves to true if the user confirms, false otherwise.
     * @param {string} title
     * @param {string} text
     * @param {string} [confirmText]
     * @returns {Promise<boolean>}
     */
    function confirm(title, text, confirmText) {
        if (typeof Swal === "undefined") return Promise.resolve(false);

        return Swal.fire({
            icon: "warning",
            title: title,
            text: text,
            showCancelButton: true,
            confirmButtonText: confirmText || "Yes",
            cancelButtonText: "Cancel",
            reverseButtons: true,
        }).then(function (result) {
            return result.isConfirmed;
        });
    }

    return { show, confirm };
})();
