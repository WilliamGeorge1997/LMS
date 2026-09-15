"use strict";

var KTCodesExport = function () {
    var form;
    var submitButton;
    var modal;

    return {
        init: function () {
            modal = new bootstrap.Modal(document.querySelector('#export-modal'));
            form = document.querySelector('#export-form');
            submitButton = form.querySelector('[data-kt-export-modal-action="submit"]');

            submitButton.addEventListener('click', function (e) {
                // Let the form submit naturally (it's a GET request for a file download)
                // We'll just show the loading indicator for a few seconds to prevent multiple clicks
                submitButton.setAttribute('data-kt-indicator', 'on');
                submitButton.disabled = true;

                setTimeout(function () {
                    submitButton.removeAttribute('data-kt-indicator');
                    submitButton.disabled = false;
                    modal.hide();
                }, 3000);
            });
        }
    };
}();

KTUtil.onDOMContentLoaded(function () {
    KTCodesExport.init();
});
