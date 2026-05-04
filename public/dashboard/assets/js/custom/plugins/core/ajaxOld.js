"use strict";

/**
 * Ajax Helper
 * Single responsibility: handle all AJAX calls across plugins
 */
window.PluginAjax = (function () {
    //Old code
    // function csrfToken() {
    //     return $('meta[name="csrf-token"]').attr("content");
    // }
    //Old code

    // New code
    var _csrfToken = null;
    function csrfToken() {
        if (!_csrfToken) {
            _csrfToken = $('meta[name="csrf-token"]').attr("content");
        }
        return _csrfToken;
    }
    // New code

    /**
     * Send a form via AJAX
     * @param {string} url
     * @param {string} method  - POST, PUT, PATCH, DELETE
     * @param {FormData} payload
     * @returns jQuery deferred
     */
    function send(url, method, payload) {
        return $.ajax({
            url: url,
            method: method,
            data: payload,
            processData: false,
            contentType: false,
            headers: {
                "X-CSRF-TOKEN": csrfToken(),
                "X-Requested-With": "XMLHttpRequest",
            },
        });
    }

    /**
     * Send a simple JSON request (used by toggle/delete)
     * @param {string} url
     * @param {string} method
     * @param {object} data
     * @returns jQuery deferred
     */
    //Old code
    // function json(url, method, data) {
    //     return $.ajax({
    //         url: url,
    //         method: method,
    //         data: JSON.stringify(data || {}),
    //         contentType: "application/json",
    //         headers: {
    //             "X-CSRF-TOKEN": csrfToken(),
    //             "X-Requested-With": "XMLHttpRequest",
    //         },
    //     });
    // }
    //Old code

    //New Code
    function json(url, method, data) {
        var isBodyless = method === "DELETE" || method === "GET";
        return $.ajax({
            url: url,
            method: method,
            data: isBodyless ? undefined : JSON.stringify(data || {}),
            contentType: isBodyless ? false : "application/json",
            headers: {
                "X-CSRF-TOKEN": csrfToken(),
                "X-Requested-With": "XMLHttpRequest",
            },
        });
    }
    //New Code

    /**
     * Load dropdown options from URL
     * @param {string} url
     * @returns jQuery deferred
     */
    function loadOptions(url) {
        return $.ajax({
            url: url,
            method: "GET",
            headers: {
                "X-Requested-With": "XMLHttpRequest",
            },
        });
    }

    return { send, json, loadOptions };
})();
