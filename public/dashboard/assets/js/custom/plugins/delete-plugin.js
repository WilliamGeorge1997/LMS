"use strict";

/**
 * DeletePlugin
 * Handles: confirm dialog → AJAX delete → remove row from datatable.
 *
 * Usage (HTML — on the delete button inside a datatable row):
 *   <button class="delete-btn" data-id="{{ $row->id }}">Delete</button>
 *
 * Usage (JS):
 *   DeletePlugin.init({
 *       deleteUrl : "/admins/:id",
 *       datatable : dt,
 *   });
 *
 * Dependencies: jQuery, PluginAjax, PluginNotify
 */
window.DeletePlugin = (function () {
    var DEFAULTS = {
        deleteUrl : null,             // required — :id replaced with the button's data-id
        datatable : null,             // DataTables instance
        selector  : ".delete-btn",
        container : "document",       // delegate from document for datatable-rendered rows
        method    : "DELETE",

        confirm: {
            title      : "Are you sure?",
            text       : "This action cannot be undone.",
            confirmText: "Yes, delete it",
        },

        notifications: {
            successTitle: "Deleted",
            successText : "Record deleted successfully.",
            errorTitle  : "Error",
            errorText   : "Could not delete record. Please try again.",
        },
    };

    function init(options) {
        var config = $.extend(true, {}, DEFAULTS, options || {});

        if (!config.deleteUrl) {
            console.error("DeletePlugin: deleteUrl is required.");
            return null;
        }

        var $container = config.container === "document" ? $(document) : $(config.container);

        $container.on("click.delete", config.selector, function () {
            var $btn = $(this);
            var $tr  = $btn.closest("tr");
            var url  = config.deleteUrl.replace(":id", $btn.attr("data-id"));

            PluginNotify.confirm(
                config.confirm.title,
                config.confirm.text,
                config.confirm.confirmText
            ).then(function (confirmed) {
                if (!confirmed) return;

                $btn.prop("disabled", true);

                PluginAjax.json(url, config.method)
                    .done(function () {
                        if (config.datatable) {
                            var row = config.datatable.row($tr);
                            if (row.length) {
                                row.remove().draw(false);
                            } else {
                                config.datatable.ajax.reload(null, false);
                            }
                        }
                        PluginNotify.show("success", config.notifications.successTitle, config.notifications.successText);
                    })
                    .fail(function () {
                        PluginNotify.show("error", config.notifications.errorTitle, config.notifications.errorText);
                    })
                    .always(function () {
                        $btn.prop("disabled", false);
                    });
            });
        });

        return {
            destroy: function () {
                $container.off("click.delete", config.selector);
            },
        };
    }

    return { init };
})();
