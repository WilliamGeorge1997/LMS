"use strict";

window.CreatePlugin = (function () {
    function pickElement($root, selector) {
        var $inside = $root.find(selector).first();
        return $inside.length ? $inside : $(selector).first();
    }

    function getElements(options) {
        var $root = $(options.root);

        return {
            $root: $root,
            $toggle: pickElement($root, options.selectors.toggle),
            $toggleLabel: pickElement($root, options.selectors.toggleLabel),
            $cancel: pickElement($root, options.selectors.cancel),
            $collapse: pickElement($root, options.selectors.collapse),
            $form: pickElement($root, options.selectors.form),
            $submit: pickElement($root, options.selectors.submit)
        };
    }

    function setFieldError(elements, field, message) {
        var $field = elements.$form.find('[name="' + field + '"]');
        var $error = elements.$form.find('[data-field-error="' + field + '"]');
        $field.addClass('is-invalid');
        $error.text(message || "");
    }

    function clearFieldError(elements, field) {
        var $field = elements.$form.find('[name="' + field + '"]');
        var $error = elements.$form.find('[data-field-error="' + field + '"]');
        $field.removeClass('is-invalid');
        $error.text("");
    }

    function clearErrors(elements) {
        elements.$form.find(".is-invalid").removeClass("is-invalid");
        elements.$form.find("[data-field-error]").text("");
    }

    function normalizeErrors(errors) {
        var result = {};
        $.each(errors || {}, function (field, value) {
            result[field] = Array.isArray(value) ? value[0] : value;
        });
        return result;
    }

    function showErrors(elements, errors, onlyField) {
        var normalized = normalizeErrors(errors);
        if (!onlyField) {
            clearErrors(elements);
        }

        if (onlyField) {
            if (normalized[onlyField]) {
                setFieldError(elements, onlyField, normalized[onlyField]);
            } else {
                clearFieldError(elements, onlyField);
            }
            return;
        }

        $.each(normalized, function (field, message) {
            setFieldError(elements, field, message);
        });
    }

    function setToggleState(elements, options, isOpen) {
        elements.$toggleLabel.text(isOpen ? options.labels.cancel : options.labels.create);
        if (options.toggleClasses) {
            elements.$toggle.toggleClass(options.toggleClasses.open, isOpen);
            elements.$toggle.toggleClass(options.toggleClasses.closed, !isOpen);
        }
    }

    function getPayload(elements) {
        return new FormData(elements.$form[0]);
    }

    function createAjax(options, payload, extraHeaders) {
        return $.ajax({
            url: options.storeUrl,
            method: options.method,
            data: payload,
            processData: false,
            contentType: false,
            headers: $.extend({
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                "X-Requested-With": "XMLHttpRequest"
            }, extraHeaders || {})
        });
    }

    function handleSuccessWithDataTable(options, response) {
        if (!options.datatable) {
            return;
        }

        if (options.onSuccess) {
            options.onSuccess(response, options.datatable);
            return;
        }

        if (options.datatableMode === "reload") {
            options.datatable.ajax.reload(null, false);
            return;
        }

        if (typeof options.mapRow === "function") {
            options.datatable.row.add(options.mapRow(response)).draw(false);
        }
    }

    function init(options) {
        var finalOptions = $.extend(true, {
            root: "[data-create-plugin]",
            selectors: {
                toggle: "[data-create-toggle]",
                toggleLabel: "[data-create-toggle-label]",
                cancel: "[data-create-cancel]",
                collapse: "[data-create-collapse]",
                form: "[data-create-form]",
                submit: "[data-create-submit]"
            },
            labels: {
                create: "Create",
                cancel: "Cancel"
            },
            toggleClasses: {
                open: "btn-danger",
                closed: "btn-primary"
            },
            method: "POST",
            storeUrl: null,
            liveValidate: true,
            liveValidateDelay: 350,
            liveValidateEventSelector: "input[name], select[name], textarea[name]",
            datatable: null,
            datatableMode: "reload",
            mapRow: null,
            onSuccess: null
        }, options || {});

        var elements = getElements(finalOptions);
        var animationDuration = 500;
        elements.$collapse.hide().removeClass("show");
        var liveTimer = null;
        var liveRequest = null;

        function closeCreate() {
            elements.$collapse.stop(true, true).slideUp(animationDuration, function () {
                elements.$collapse.removeClass("show");
            });
            setToggleState(elements, finalOptions, false);
            elements.$form.trigger("reset");
            clearErrors(elements);
        }

        function openCreate() {
            elements.$collapse.stop(true, true).slideDown(animationDuration, function () {
                elements.$collapse.addClass("show");
            });
            setToggleState(elements, finalOptions, true);
        }

        elements.$toggle.on("click", function (event) {
            event.preventDefault();
            var isOpen = elements.$collapse.hasClass("show");
            if (isOpen) {
                closeCreate();
                return;
            }
            openCreate();
        });

        elements.$cancel.on("click", function () {
            closeCreate();
        });

        if (finalOptions.liveValidate) {
            elements.$form.on("input change", finalOptions.liveValidateEventSelector, function (event) {
                var field = event.target.name;
                if (!field) {
                    return;
                }

                clearTimeout(liveTimer);
                liveTimer = setTimeout(function () {
                    if (liveRequest && liveRequest.readyState !== 4) {
                        liveRequest.abort();
                    }

                    liveRequest = createAjax(finalOptions, getPayload(elements), {})
                        .done(function () {
                            clearFieldError(elements, field);
                        })
                        .fail(function (xhr, status) {
                            if (status === "abort") {
                                return;
                            }
                            if (xhr.status === 422) {
                                showErrors(elements, xhr.responseJSON?.errors || {}, field);
                            }
                        });
                }, finalOptions.liveValidateDelay);
            });
        }

        elements.$form.on("submit", function (event) {
            event.preventDefault();
            if (!finalOptions.storeUrl) {
                return;
            }

            elements.$submit.attr("data-kt-indicator", "on");
            elements.$submit.prop("disabled", true);

            createAjax(finalOptions, getPayload(elements), {})
                .done(function (response) {
                    clearErrors(elements);
                    handleSuccessWithDataTable(finalOptions, response);
                    closeCreate();
                })
                .fail(function (xhr) {
                    if (xhr.status === 422) {
                        showErrors(elements, xhr.responseJSON?.errors || {});
                    }
                })
                .always(function () {
                    elements.$submit.removeAttr("data-kt-indicator");
                    elements.$submit.prop("disabled", false);
                });
        });

        return {
            options: finalOptions,
            elements: elements,
            clearErrors: function () { clearErrors(elements); },
            showErrors: function (errors, onlyField) { showErrors(elements, errors, onlyField); },
            open: openCreate,
            close: closeCreate
        };
    }

    return {
        init: init
    };
})();
