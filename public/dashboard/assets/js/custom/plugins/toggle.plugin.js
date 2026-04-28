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
    var DEFAULTS = {
        // Required — :id is replaced with the row's data-id
        toggleUrl: null,

        // Selector for the toggle checkboxes (inside datatable or page)
        selector: ".active-toggle",

        // Container to delegate events from (use "document" if inside datatable)
        container: "document",

        method: "PATCH",

        notifications: {
            errorTitle: "Error",
            errorText: "Could not update status. Please try again.",
        },
    };

    function init(options) {
        var config = $.extend(true, {}, DEFAULTS, options || {});

        if (!config.toggleUrl) {
            console.error("TogglePlugin: toggleUrl is required.");
            return null;
        }

        var $container =
            config.container === "document" ? $(document) : $(config.container);

        // Delegate click to handle dynamically rendered datatable rows
        $container.on("change.toggle", config.selector, function () {
            var $checkbox = $(this);
            var id = $checkbox.attr("data-id");
            var isChecked = $checkbox.is(":checked");
            var url = config.toggleUrl.replace(":id", id);

            // Disable while request is in flight
            $checkbox.prop("disabled", true);

            PluginAjax.json(url, config.method, {
                is_active: isChecked ? 1 : 0,
            })
                .fail(function () {
                    // Revert checkbox on failure
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

        return {
            destroy: function () {
                $container.off("change.toggle", config.selector);
            },
        };
    }

    return { init };
})();
