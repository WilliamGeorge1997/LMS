"use strict";

/**
 * PluginAjax
 * Single responsibility: handle all AJAX calls across plugins.
 *
 * Dependencies: jQuery
 */
window.PluginAjax = (function () {
    /**
     * Always reads fresh from the DOM — avoids stale token after session rotation (419).
     * @returns {string}
     */
    function csrfToken() {
        var meta = document.head.querySelector('meta[name="csrf-token"]');
        return meta ? meta.content : "";
    }

    /**
     * Send a multipart form (supports file uploads).
     * @param {string}   url
     * @param {string}   method  - POST | PUT | PATCH | DELETE
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
     * Send a JSON request (used by toggle / delete).
     * Body is included when data is provided (any method except GET).
     * @param {string} url
     * @param {string} method
     * @param {object} [data]
     * @returns jQuery deferred
     */
    function json(url, method, data) {
        var options = {
            url: url,
            method: method,
            headers: {
                "X-CSRF-TOKEN": csrfToken(),
                "X-Requested-With": "XMLHttpRequest",
            },
        };

        if (method !== "GET" && data != null) {
            options.data = JSON.stringify(data);
            options.contentType = "application/json";
        }

        return $.ajax(options);
    }

    /**
     * Load dropdown options from a URL (GET).
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
