"use strict";

/**
 * TogglePlugin
 * Handles: toggle active/inactive status via AJAX on checkbox change.
 *
 * Usage (HTML — on the checkbox inside a datatable row):
 *   <input type="checkbox" class="active-toggle" data-id="{{ $row->id }}" checked />
 *
 * Usage (JS):
 *   TogglePlugin.init({
 *       toggleUrl: "/admins/:id/toggle",
 *   });
 *
 * Dependencies: jQuery, PluginAjax, PluginNotify
 */
window.TogglePlugin = (function () {
    var DEFAULTS = {
        toggleUrl : null,              // required — :id replaced with the checkbox's data-id
        selector  : ".active-toggle",
        container : "document",
        method    : "PATCH",
        onSuccess : null,              // function(response, $checkbox) — override default notification

        notifications: {
            successTitle: "Success",
            errorTitle  : "Error",
            errorText   : "Could not update status. Please try again.",
        },
    };

    function init(options) {
        var config = $.extend(true, {}, DEFAULTS, options || {});

        if (!config.toggleUrl) {
            console.error("TogglePlugin: toggleUrl is required.");
            return null;
        }

        var $container = config.container === "document" ? $(document) : $(config.container);

        $container.on("change.toggle", config.selector, function () {
            var $checkbox = $(this);
            var isChecked = $checkbox.is(":checked");
            var url       = config.toggleUrl.replace(":id", $checkbox.attr("data-id"));

            $checkbox.prop("disabled", true);

            PluginAjax.json(url, config.method, { is_active: isChecked ? 1 : 0 })
                .done(function (response) {
                    if (typeof config.onSuccess === "function") {
                        config.onSuccess(response, $checkbox);
                        return;
                    }

                    var respData = response && response.data ? response.data : {};
                    var name     = respData.name || "Record";
                    var isActive = respData.is_active === 1;

                    PluginNotify.show(
                        "success",
                        config.notifications.successTitle,
                        name + " is now " + (isActive ? "Active" : "Inactive")
                    );
                })
                .fail(function () {
                    $checkbox.prop("checked", !isChecked); // revert on failure
                    PluginNotify.show("error", config.notifications.errorTitle, config.notifications.errorText);
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
