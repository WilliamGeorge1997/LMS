"use strict";

/**
 * CreatePlugin
 * Handles: accordion open/close + form validation + AJAX submit + datatable refresh
 *
 * Dependencies: jQuery, Bootstrap Collapse, PluginAjax, PluginNotify,
 *               PluginValidator, PluginDependentDropdown
 *
 * Usage:
 *   CreatePlugin.init({
 *       storeUrl: "/admins",
 *       datatable: dt,
 *   });
 */
window.CreatePlugin = (function () {
    // ─── Default config ───────────────────────────────────────────────────────

    var DEFAULTS = {
        // Required
        storeUrl: null,

        // Selectors (override if your HTML IDs are different)
        selectors: {
            toggle: "#create-toggle",
            toggleLabel: "#create-toggle-label",
            collapse: "#create-collapse",
            form: "#create-form",
            submit: "#create-submit",
            cancel: "#create-cancel",
        },

        // Button labels
        labels: {
            create: "Create",
            cancel: "Cancel",
        },

        // Button classes when open vs closed
        toggleClasses: {
            open: "btn-danger",
            closed: "btn-primary",
        },

        // AJAX method
        method: "POST",

        // Datatable instance (pass your `dt` variable here)
        datatable: null,

        // "reload" → dt.ajax.reload()  |  "append" → add new row
        datatableMode: "reload",

        // Called when datatableMode = "append"
        // function(response) → must return a row array for datatable
        mapRow: null,

        // Called after successful submit (optional hook)
        // function(response, datatable)
        onSuccess: null,

        // Called before form data is sent (optional hook)
        // function(formData) → must return the (modified) FormData
        beforeSubmit: null,

        // Notifications
        notifications: {
            successTitle: "Success",
            successText: "Record created successfully.",
            errorTitle: "Error",
            errorText: "Something went wrong. Please try again.",
        },
    };

    // ─── Init ─────────────────────────────────────────────────────────────────

    function init(options) {
        var config = $.extend(true, {}, DEFAULTS, options || {});

        // Validate required option
        if (!config.storeUrl) {
            console.error("CreatePlugin: storeUrl is required.");
            return null;
        }

        // Cache DOM elements
        var $toggle = $(config.selectors.toggle).first();
        var $toggleLabel = $(config.selectors.toggleLabel).first();
        var $collapse = $(config.selectors.collapse).first();
        var $form = $(config.selectors.form).first();
        var $submit = $(config.selectors.submit).first();
        var $cancel = $(config.selectors.cancel).first();

        // Guard: required elements must exist
        if (!$collapse.length || !$form.length) {
            console.error("CreatePlugin: collapse or form element not found.");
            return null;
        }

        // Bootstrap collapse instance
        var collapse = bootstrap.Collapse.getOrCreateInstance($collapse[0], {
            toggle: false,
        });

        // Bind validation live feedback
        PluginValidator.bindLive($form);

        // Bind dependent dropdowns
        PluginDependentDropdown.bind($form);

        // ─── Helpers ──────────────────────────────────────────────────────────

        function setToggleState(isOpen) {
            $toggleLabel.text(
                isOpen ? config.labels.cancel : config.labels.create,
            );
            $toggle
                .toggleClass(config.toggleClasses.open, isOpen)
                .toggleClass(config.toggleClasses.closed, !isOpen);
        }

        function buildPayload() {
            var formData = new FormData($form[0]);

            if (typeof config.beforeSubmit === "function") {
                formData = config.beforeSubmit(formData) || formData;
            }

            return formData;
        }

        function refreshTable(response) {
            if (!config.datatable) return;

            // Custom handler takes full control
            if (typeof config.onSuccess === "function") {
                config.onSuccess(response, config.datatable);
                return;
            }

            // Append mode: map response to a row and add it
            if (
                config.datatableMode === "append" &&
                typeof config.mapRow === "function"
            ) {
                config.datatable.row.add(config.mapRow(response)).draw(false);
                return;
            }

            // Default: reload keeping current page
            config.datatable.ajax.reload(null, false);
        }

        //Old code
        // function setSubmitLoading(loading) {
        //     $submit
        //         .attr("data-kt-indicator", loading ? "on" : null)
        //         .prop("disabled", loading);

        //     if (!loading) {
        //         $submit.removeAttr("data-kt-indicator");
        //     }
        // }
        //Old code

        //New Code
        function setSubmitLoading(loading) {
            if (loading) {
                $submit.attr("data-kt-indicator", "on").prop("disabled", true);
            } else {
                $submit.removeAttr("data-kt-indicator").prop("disabled", false);
            }
        }
        //New Code

        // ─── Collapse events ──────────────────────────────────────────────────

        $collapse.on("show.bs.collapse.create", function () {
            setToggleState(true);
        });
        $collapse.on("hide.bs.collapse.create", function () {
            setToggleState(false);
        });
        $collapse.on("hidden.bs.collapse.create", function () {
            $form[0].reset();
            PluginValidator.clearAll($form);
            PluginDependentDropdown.bind($form); // re-bind after reset
        });

        // ─── Cancel button ────────────────────────────────────────────────────

        $cancel.on("click.create", function () {
            collapse.hide();
        });

        // ─── Form submit ──────────────────────────────────────────────────────

        $form.on("submit.create", function (e) {
            e.preventDefault();

            // Front-end validation
            if (!PluginValidator.validateForm($form)) return;

            setSubmitLoading(true);

            PluginAjax.send(config.storeUrl, config.method, buildPayload())
                .done(function (response) {
                    PluginValidator.clearAll($form);
                    refreshTable(response);
                    collapse.hide();
                    PluginNotify.show(
                        "success",
                        config.notifications.successTitle,
                        config.notifications.successText,
                    );
                })
                .fail(function (xhr) {
                    // Laravel validation errors (422)
                    if (xhr.status === 422) {
                        PluginValidator.showBackendErrors(
                            $form,
                            xhr.responseJSON?.errors || {},
                        );
                        return;
                    }
                    PluginNotify.show(
                        "error",
                        config.notifications.errorTitle,
                        config.notifications.errorText,
                    );
                })
                .always(function () {
                    setSubmitLoading(false);
                });
        });

        // ─── Public API ───────────────────────────────────────────────────────

        return {
            open: function () {
                collapse.show();
            },
            close: function () {
                collapse.hide();
            },
            clearErrors: function () {
                PluginValidator.clearAll($form);
            },
            showErrors: function (errors) {
                PluginValidator.showBackendErrors($form, errors);
            },

            // Clean up all event listeners (important for SPA / Livewire)
            destroy: function () {
                $form.off(".create");
                $cancel.off(".create");
                $collapse.off(".create");
                PluginValidator.unbindLive($form);
                PluginDependentDropdown.unbind($form);
            },
        };
    }

    return { init };
})();
