"use strict";

/**
 * CreatePlugin
 * Handles: accordion open/close + form validation + AJAX submit + datatable refresh.
 *
 * Dependencies: jQuery, Bootstrap Collapse, PluginAjax, PluginNotify,
 *               PluginValidator, PluginDependentDropdown
 *
 * Usage:
 *   CreatePlugin.init({
 *       storeUrl : "/admins",
 *       datatable: dt,
 *   });
 */
window.CreatePlugin = (function () {
    var DEFAULTS = {
        storeUrl: null,

        selectors: {
            toggle      : "#create-toggle",
            toggleLabel : "#create-toggle-label",
            collapse    : "#create-collapse",
            form        : "#create-form",
            submit      : "#create-submit",
            cancel      : "#create-cancel",
        },

        labels: {
            create: "Create",
            cancel: "Cancel",
        },

        toggleClasses: {
            open  : "btn-danger",
            closed: "btn-primary",
        },

        method       : "POST",
        datatable    : null,
        datatableMode: "reload",   // "reload" | "append"
        mapRow       : null,       // function(response) → row array (used when datatableMode = "append")
        onSuccess    : null,       // function(response, datatable) — overrides default table refresh
        beforeSubmit : null,       // function(formData) → formData — mutate payload before send

        notifications: {
            successTitle: "Success",
            successText : "Record created successfully.",
            errorTitle  : "Error",
            errorText   : "Something went wrong. Please try again.",
        },
    };

    function init(options) {
        var config = $.extend(true, {}, DEFAULTS, options || {});

        if (!config.storeUrl) {
            console.error("CreatePlugin: storeUrl is required.");
            return null;
        }

        var $toggle      = $(config.selectors.toggle).first();
        var $toggleLabel = $(config.selectors.toggleLabel).first();
        var $collapse    = $(config.selectors.collapse).first();
        var $form        = $(config.selectors.form).first();
        var $submit      = $(config.selectors.submit).first();
        var $cancel      = $(config.selectors.cancel).first();

        if (!$collapse.length || !$form.length) {
            console.error("CreatePlugin: collapse or form element not found.");
            return null;
        }

        var collapse = bootstrap.Collapse.getOrCreateInstance($collapse[0], { toggle: false });

        PluginValidator.bindLive($form);
        PluginDependentDropdown.bind($form);

        // ─── Helpers ──────────────────────────────────────────────────────────

        function setToggleState(isOpen) {
            $toggleLabel.text(isOpen ? config.labels.cancel : config.labels.create);
            $toggle
                .toggleClass(config.toggleClasses.open,   isOpen)
                .toggleClass(config.toggleClasses.closed, !isOpen);
        }

        function buildPayload() {
            var formData = new FormData($form[0]);
            return (typeof config.beforeSubmit === "function") ? (config.beforeSubmit(formData) || formData) : formData;
        }

        function refreshTable(response) {
            if (!config.datatable) return;

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

        function setSubmitLoading(loading) {
            if (loading) {
                $submit.attr("data-kt-indicator", "on").prop("disabled", true);
            } else {
                $submit.removeAttr("data-kt-indicator").prop("disabled", false);
            }
        }

        // ─── Collapse events ──────────────────────────────────────────────────

        $collapse.on("show.bs.collapse.create",   function () { setToggleState(true); });
        $collapse.on("hide.bs.collapse.create",   function () { setToggleState(false); });
        $collapse.on("hidden.bs.collapse.create", function () {
            $form[0].reset();
            PluginValidator.clearAll($form);
            PluginDependentDropdown.bind($form); // re-bind after reset
        });

        // ─── Cancel button ────────────────────────────────────────────────────

        $cancel.on("click.create", function () { collapse.hide(); });

        // ─── Form submit ──────────────────────────────────────────────────────

        $form.on("submit.create", function (e) {
            e.preventDefault();

            if (!PluginValidator.validateForm($form)) return;

            setSubmitLoading(true);

            PluginAjax.send(config.storeUrl, config.method, buildPayload())
                .done(function (response) {
                    PluginValidator.clearAll($form);
                    refreshTable(response);
                    collapse.hide();
                    PluginNotify.show("success", config.notifications.successTitle, config.notifications.successText);
                })
                .fail(function (xhr) {
                    if (xhr.status === 422) {
                        PluginValidator.showBackendErrors($form, xhr.responseJSON?.errors || {});
                        return;
                    }
                    PluginNotify.show("error", config.notifications.errorTitle, config.notifications.errorText);
                })
                .always(function () {
                    setSubmitLoading(false);
                });
        });

        // ─── Public API ───────────────────────────────────────────────────────

        return {
            open       : function ()        { collapse.show(); },
            close      : function ()        { collapse.hide(); },
            clearErrors: function ()        { PluginValidator.clearAll($form); },
            showErrors : function (errors)  { PluginValidator.showBackendErrors($form, errors); },
            destroy    : function () {
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
