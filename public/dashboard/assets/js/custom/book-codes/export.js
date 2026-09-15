"use strict";

var KTCodesExport = function () {
    return {
        init: function () {
            var modal = new bootstrap.Modal($('#export-modal')[0]);
            var $form = $('#export-form');
            var $submit = $form.find('[data-kt-export-modal-action="submit"]');

            $form.on('submit', function (e) {
                e.preventDefault();
                $submit.attr('data-kt-indicator', 'on').prop('disabled', true);

                $.ajax({
                    url: $form.attr('action'),
                    method: 'GET',
                    data: $form.serialize(),
                    xhrFields: { responseType: 'blob' },
                })
                    .done(function (blob) {
                        var url = URL.createObjectURL(blob);
                        $('<a>').attr({ href: url, download: 'book-codes.xlsx' }).appendTo('body')[0].click();
                        URL.revokeObjectURL(url);
                        modal.hide();
                    })
                    .fail(function (xhr) {
                        (xhr.response instanceof Blob ? xhr.response.text() : Promise.resolve(xhr.responseText))
                            .then(function (text) {
                                try { xhr.responseJSON = JSON.parse(text); } catch (e) {}
                                Actions._handleFail(xhr, $form);
                            });
                    })
                    .always(function () {
                        $submit.removeAttr('data-kt-indicator').prop('disabled', false);
                    });
            });
        }
    };
}();

KTUtil.onDOMContentLoaded(function () {
    KTCodesExport.init();
});
