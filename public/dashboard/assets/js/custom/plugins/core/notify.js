"use strict";

/**
 * Notify Helper
 * Single responsibility: show notifications via SweetAlert2
 */
window.PluginNotify = (function () {
    /**
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
     * Show a confirmation dialog, returns a Promise
     * @param {string} title
     * @param {string} text
     * @param {string} confirmText
     * @returns Promise<boolean>
     */
    function confirm(title, text, confirmText) {
        if (typeof Swal === "undefined") return Promise.resolve(true);

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
