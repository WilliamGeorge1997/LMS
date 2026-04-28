"use strict";

/**
 * DeletePlugin
 * Handles: confirm dialog → AJAX delete → remove row from datatable
 *
 * Dependencies: jQuery, PluginAjax, PluginNotify
 *
 * Usage in HTML (on the delete button inside datatable rows):
 *   <button class="delete-btn" data-id="{{ $row->id }}">Delete</button>
 *
 * Usage in JS:
 *   DeletePlugin.init({
 *       deleteUrl: "/admins/:id",
 *       datatable: dt,
 *   });
 */
window.DeletePlugin = (function () {

    var DEFAULTS = {
        // Required — :id is replaced with the row's data-id
        deleteUrl: null,

        // Datatable instance — if provided, the row is removed instead of full reload
        datatable: null,

        // Selector for delete buttons
        selector: ".delete-btn",

        // Container to delegate events from
        container: "document",

        method: "DELETE",

        confirm: {
            title:       "Are you sure?",
            text:        "This action cannot be undone.",
            confirmText: "Yes, delete it"
        },

        notifications: {
            successTitle: "Deleted",
            successText:  "Record deleted successfully.",
            errorTitle:   "Error",
            errorText:    "Could not delete record. Please try again."
        }
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
            var id   = $btn.attr("data-id");
            var url  = config.deleteUrl.replace(":id", id);

            PluginNotify.confirm(
                config.confirm.title,
                config.confirm.text,
                config.confirm.confirmText
            ).then(function (confirmed) {
                if (!confirmed) return;

                $btn.prop("disabled", true);

                PluginAjax.json(url, config.method)
                    .done(function () {
                        // Remove the row directly if datatable is available
                        if (config.datatable) {
                            var row = config.datatable.row($btn.closest("tr"));
                            if (row.length) {
                                row.remove().draw(false);
                            } else {
                                config.datatable.ajax.reload(null, false);
                            }
                        }

                        PluginNotify.show(
                            "success",
                            config.notifications.successTitle,
                            config.notifications.successText
                        );
                    })
                    .fail(function () {
                        PluginNotify.show(
                            "error",
                            config.notifications.errorTitle,
                            config.notifications.errorText
                        );
                    })
                    .always(function () {
                        $btn.prop("disabled", false);
                    });
            });
        });

        return {
            destroy: function () {
                $container.off("click.delete", config.selector);
            }
        };
    }

    return { init };

})();
