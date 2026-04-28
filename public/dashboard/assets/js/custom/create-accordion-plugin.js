"use strict";

window.CreatePlugin = (function () {
    function init(options) {
        var config = $.extend({
            root: "body",
            selectors: {
                toggle: "#create-toggle",
                toggleLabel: "#create-toggle-label",
                collapse: "#create-collapse",
                form: "#create-form",
                submit: "#create-submit",
                cancel: "#create-cancel"
            },
            labels: { create: "Create", cancel: "Cancel" },
            toggleClasses: { open: "btn-danger", closed: "btn-primary" },
            storeUrl: null,
            method: "POST",
            animationDuration: 350,
            liveValidate: false,
            frontValidate: true,
            frontValidateEventSelector: "input[name], select[name], textarea[name]",
            datatable: null,
            datatableMode: "reload",
            mapRow: null,
            onSuccess: null,
            notifications: {
                enabled: true,
                successTitle: "Success",
                successText: "Record created successfully.",
                errorTitle: "Error",
                errorText: "Something went wrong. Please try again."
            }
        }, options || {});

        var $root = $(config.root);
        var $toggle = $root.find(config.selectors.toggle).first();
        var $toggleLabel = $root.find(config.selectors.toggleLabel).first();
        var $collapse = $root.find(config.selectors.collapse).first();
        var $form = $root.find(config.selectors.form).first();
        var $submit = $root.find(config.selectors.submit).first();
        var $cancel = $root.find(config.selectors.cancel).first();

        if (!$collapse.length || !$form.length || !config.storeUrl) {
            return null;
        }

        var collapseId = $collapse.attr("id");
        if (collapseId) {
            var duration = typeof config.animationDuration === "number"
                ? config.animationDuration + "ms"
                : config.animationDuration;
            var styleId = "create-plugin-duration-" + collapseId;
            $("#" + styleId).remove();
            $("head").append('<style id="' + styleId + '">#' + collapseId + '.collapsing{transition:height ' + duration + ' ease !important;}</style>');
        }

        var collapse = bootstrap.Collapse.getOrCreateInstance($collapse[0], { toggle: false });

        function buildPayload() {
            return new FormData($form[0]);
        }

        function clearErrors() {
            $form.find(".is-invalid").removeClass("is-invalid");
            $form.find("[data-field-error]").text("");
        }

        function showBackendErrors(errors) {
            var normalized = {};
            $.each(errors || {}, function (field, value) {
                normalized[field] = Array.isArray(value) ? value[0] : value;
            });

            clearErrors();

            $.each(normalized, function (field, message) {
                $form.find('[name="' + field + '"]').addClass("is-invalid");
                $form.find('[data-field-error="' + field + '"]').text(message || "");
            });
        }

        function clearField(name) {
            $form.find('[name="' + name + '"]').removeClass("is-invalid");
            $form.find('[data-field-error="' + name + '"]').text("");
        }

        function setField(name, message) {
            $form.find('[name="' + name + '"]').addClass("is-invalid");
            $form.find('[data-field-error="' + name + '"]').text(message || "");
        }

        function validateField(el) {
            if (!el || !el.name) {
                return true;
            }

            var $el = $(el);

            var sameSelector = $el.attr("data-rule-same");
            if (sameSelector) {
                var $other = $(sameSelector);
                if ($other.length && $other.val() !== $el.val()) {
                    setField(el.name, $el.attr("data-msg-same") || "Values do not match.");
                    return false;
                }
            }

            var minValue = $el.attr("data-rule-min");
            if (minValue && ($el.val() || "").length < parseInt(minValue, 10)) {
                setField(el.name, $el.attr("data-msg-min") || ("Minimum " + minValue + " characters."));
                return false;
            }

            var maxValue = $el.attr("data-rule-max");
            if (maxValue && ($el.val() || "").length > parseInt(maxValue, 10)) {
                setField(el.name, $el.attr("data-msg-max") || ("Maximum " + maxValue + " characters."));
                return false;
            }

            if (el.checkValidity()) {
                clearField(el.name);
                return true;
            }

            setField(el.name, el.validationMessage || "Invalid value.");
            return false;
        }

        function validateForm() {
            clearErrors();
            var ok = true;
            $form.find(config.frontValidateEventSelector).each(function () {
                if (!validateField(this)) {
                    ok = false;
                }
            });
            return ok;
        }

        function ajax(payload) {
            return $.ajax({
                url: config.storeUrl,
                method: config.method,
                data: payload,
                processData: false,
                contentType: false,
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                    "X-Requested-With": "XMLHttpRequest"
                }
            });
        }

        function refreshTable(response) {
            if (!config.datatable) {
                return;
            }
            if (typeof config.onSuccess === "function") {
                config.onSuccess(response, config.datatable);
                return;
            }
            if (config.datatableMode === "append" && typeof config.mapRow === "function") {
                config.datatable.row.add(config.mapRow(response)).draw(false);
                return;
            }
            config.datatable.ajax.reload(null, false);
        }

        function setToggleState(isOpen) {
            $toggleLabel.text(isOpen ? config.labels.cancel : config.labels.create);
            $toggle.toggleClass(config.toggleClasses.open, isOpen);
            $toggle.toggleClass(config.toggleClasses.closed, !isOpen);
        }

        function notify(type, title, text) {
            if (!config.notifications || !config.notifications.enabled) {
                return;
            }

            if (typeof Swal !== "undefined" && Swal.fire) {
                Swal.fire({
                    icon: type,
                    title: title,
                    text: text,
                    confirmButtonText: "OK"
                });
            }
        }

        $collapse.on("show.bs.collapse", function () { setToggleState(true); });
        $collapse.on("hide.bs.collapse", function () { setToggleState(false); });
        $collapse.on("hidden.bs.collapse", function () {
            $form.trigger("reset");
            clearErrors();
        });

        $cancel.on("click", function () { collapse.hide(); });

        if (config.frontValidate) {
            $form.on("input change blur", config.frontValidateEventSelector, function (event) {
                validateField(event.target);
            });
        }

        $form.on("submit", function (event) {
            event.preventDefault();

            if (config.frontValidate && !validateForm()) {
                return;
            }

            $submit.attr("data-kt-indicator", "on").prop("disabled", true);

            ajax(buildPayload())
                .done(function (response) {
                    clearErrors();
                    refreshTable(response);
                    collapse.hide();
                    notify("success", config.notifications.successTitle, config.notifications.successText);
                })
                .fail(function (xhr) {
                    if (xhr.status === 422) {
                        showBackendErrors(xhr.responseJSON?.errors || {});
                        return;
                    }
                    notify("error", config.notifications.errorTitle, config.notifications.errorText);
                })
                .always(function () {
                    $submit.removeAttr("data-kt-indicator").prop("disabled", false);
                });
        });

        return {
            open: function () { collapse.show(); },
            close: function () { collapse.hide(); },
            clearErrors: clearErrors,
            showErrors: showBackendErrors
        };
    }

    return {
        init: init
    };
})();
