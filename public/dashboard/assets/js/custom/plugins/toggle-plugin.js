"use strict";

/**
 * TogglePlugin
 * Handles: toggle active/inactive status via AJAX on checkbox change
 *
 * Dependencies: jQuery, PluginAjax, PluginNotify
 *
 * Usage in HTML (on the checkbox inside datatable rows):
 *   <input type="checkbox" class="active-toggle" data-id="{{ $row->id }}" checked />
 *
 * Usage in JS:
 *   TogglePlugin.init({
 *       toggleUrl: "/admins/:id/toggle",
 *   });
 */
window.TogglePlugin = (function () {
    //Old code

    // var DEFAULTS = {
    //     // Required — :id is replaced with the row's data-id
    //     toggleUrl: null,

    //     // Selector for the toggle checkboxes (inside datatable or page)
    //     selector: ".active-toggle",

    //     // Container to delegate events from (use "document" if inside datatable)
    //     container: "document",

    //     method: "PATCH",

    //     notifications: {
    //         successTitle: "Success",
    //         errorTitle: "Error",
    //         errorText: "Could not update status. Please try again.",
    //     },
    // };
    //Old code
    var DEFAULTS = {
        toggleUrl: null,
        selector: ".active-toggle",
        container: "document",
        method: "PATCH",
        onSuccess: null,
        notifications: {
            successTitle: "Success",
            errorTitle: "Error",
            errorText: "Could not update status. Please try again.",
        },
    };
    //New code

    //New code
    function init(options) {
        var config = $.extend(true, {}, DEFAULTS, options || {});

        if (!config.toggleUrl) {
            console.error("TogglePlugin: toggleUrl is required.");
            return null;
        }

        var $container =
            config.container === "document" ? $(document) : $(config.container);

        //Old code
        // Delegate click to handle dynamically rendered datatable rows
        // $container.on("change.toggle", config.selector, function () {
        //     var $checkbox = $(this);
        //     var id = $checkbox.attr("data-id");
        //     var isChecked = $checkbox.is(":checked");
        //     var url = config.toggleUrl.replace(":id", id);

        //     // Disable while request is in flight
        //     $checkbox.prop("disabled", true);

        //     PluginAjax.json(url, config.method, {
        //         is_active: isChecked ? 1 : 0,
        //     })
        //         .done(function (response) {
        //             var name = response.data.name;
        //             var isActive = response.data.is_active === 1;

        //             PluginNotify.show(
        //                 "success",
        //                 config.notifications.successTitle,
        //                 name + " is now " + (isActive ? "Active" : "Inactive"),
        //             );
        //         })
        //         .fail(function () {
        //             // Revert checkbox on failure
        //             $checkbox.prop("checked", !isChecked);
        //             PluginNotify.show(
        //                 "error",
        //                 config.notifications.errorTitle,
        //                 config.notifications.errorText,
        //             );
        //         })
        //         .always(function () {
        //             $checkbox.prop("disabled", false);
        //         });
        // });
        //Old code

        //New code
        $container.on("change.toggle", config.selector, function () {
            var $checkbox = $(this);
            var id = $checkbox.attr("data-id");
            var isChecked = $checkbox.is(":checked");
            var url = config.toggleUrl.replace(":id", id);

            $checkbox.prop("disabled", true);

            PluginAjax.json(url, config.method, {
                is_active: isChecked ? 1 : 0,
            })
                .done(function (response) {
                    if (typeof config.onSuccess === "function") {
                        config.onSuccess(response, $checkbox);
                        return;
                    }

                    var name = response?.data?.name || "Record";
                    var isActive = response?.data?.is_active === 1;

                    PluginNotify.show(
                        "success",
                        config.notifications.successTitle,
                        name + " is now " + (isActive ? "Active" : "Inactive"),
                    );
                })
                .fail(function () {
                    $checkbox.prop("checked", !isChecked);
                    PluginNotify.show(
                        "error",
                        config.notifications.errorTitle,
                        config.notifications.errorText,
                    );
                })
                .always(function () {
                    $checkbox.prop("disabled", false);
                });
        });
        //New code

        return {
            destroy: function () {
                $container.off("change.toggle", config.selector);
            },
        };
    }

    return { init };
})();
