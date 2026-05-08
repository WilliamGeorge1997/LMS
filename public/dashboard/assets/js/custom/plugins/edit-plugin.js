"use strict";

/**
 * EditPlugin
 * Handles: inline row editing in DataTables with a child row for hidden fields.
 *
 * Dependencies: jQuery, DataTables, PluginAjax, PluginNotify, PluginValidator,
 *               PluginInputBuilder, PluginDependentDropdown
 *
 * Usage:
 *   EditPlugin.init({
 *       updateUrl : "/admins/:id",
 *       datatable : dt,
 *       selector  : ".edit-btn",
 *       columns   : [
 *           { field: "name",     type: "text",     target: 2 },
 *           { field: "email",    type: "text",     target: 3 },
 *           { field: "role",     type: "select",   target: 4, options: [{ value: "super_admin", label: "Super Admin" }] },
 *           { field: "city_id",  type: "select",   target: 5, options: [],
 *               dependsOn: "country_id", dependsUrl: "/ajax/cities?country_id=:value" },
 *           { field: "password", type: "password", hidden: true, optional: true },
 *           { field: "image",    type: "image",    hidden: true, optional: true },
 *       ],
 *       mapRow: function(response) { return response.data; },
 *   });
 *
 * Column definition:
 *   field       {string}   — must match datatable data key
 *   type        {string}   — "text" | "password" | "select" | "image" | "toggle"
 *   target      {number}   — datatable column index (omit if hidden: true)
 *   hidden      {boolean}  — rendered in child row grid, not inline
 *   optional    {boolean}  — field is skipped in FormData when empty
 *   options     {array}    — [{ value, label }] for select type
 *   dependsOn   {string}   — parent field name (for dependent selects)
 *   dependsUrl  {string}   — URL with :value placeholder (for dependent selects)
 *   rules       {object}   — { required, min, max, email, same }
 *   messages    {object}   — custom validation messages
 *   placeholder {string}   — input placeholder text
 *   colClass    {string}   — Bootstrap col class for child row (default: "col-md-6")
 */
window.EditPlugin = (function () {
    var DEFAULTS = {
        updateUrl    : null,
        datatable    : null,
        selector     : ".edit-btn",
        container    : "document",
        method       : "POST",
        columns      : [],
        mapRow       : null,
        actionsTarget: null, // auto-detected from last visible column if null

        notifications: {
            successTitle: "Updated",
            successText : "Record updated successfully.",
            errorTitle  : "Error",
            errorText   : "Something went wrong. Please try again.",
        },
    };

    // ─── DataTable helpers ────────────────────────────────────────────────────

    /** Returns the last visible column index (for the actions cell). */
    function detectActionsTarget(dt) {
        for (var i = dt.columns().count() - 1; i >= 0; i--) {
            if (dt.column(i).visible()) return i;
        }
        return dt.columns().count() - 1;
    }

    /**
     * Build a map of { dtColumnIndex → visible <td> index } once at init time.
     * Avoids an O(n) loop on every cell access during edit mode.
     */
    function buildVisibleCellMap(dt) {
        var map = {};
        var visible = 0;
        for (var i = 0; i < dt.columns().count(); i++) {
            if (dt.column(i).visible()) {
                map[i] = visible++;
            }
        }
        return map;
    }

    // ─── Actions cell HTML ────────────────────────────────────────────────────

    function buildActionsEdit() {
        return '<div class="d-flex justify-content-end align-items-center gap-1">' +
            '<button type="button" class="btn btn-icon btn-sm btn-light-success edit-save-btn" title="Save">' +
            '<i class="ki-duotone ki-check fs-4"><span class="path1"></span><span class="path2"></span></i></button>' +
            '<button type="button" class="btn btn-icon btn-sm btn-light-danger edit-cancel-btn" title="Cancel">' +
            '<i class="ki-duotone ki-cross fs-4"><span class="path1"></span><span class="path2"></span></i></button>' +
            '</div>';
    }

    function restoreSaveBtn($btn) {
        $btn.prop("disabled", false).html(
            '<i class="ki-duotone ki-check fs-4"><span class="path1"></span><span class="path2"></span></i>'
        );
    }

    // ─── Pseudo-form (wraps $tr + $childTr so PluginValidator can work on them) ──

    /**
     * Returns a proxy object with .find() / .on() / .off() that searches
     * both the main row and the child row.
     * No DOM cloning — delegates directly to the live elements.
     */
    function buildPseudoForm($tr, $childTr) {
        function inBoth(selector, method) {
            var $result = $tr[method](selector);
            if ($childTr && $childTr.length) {
                $result = $result.add($childTr[method](selector));
            }
            return $result;
        }

        return {
            find: function (selector) { return inBoth(selector, "find"); },
            on  : function (events, selector, handler) {
                $tr.on(events, selector, handler);
                if ($childTr && $childTr.length) $childTr.on(events, selector, handler);
            },
            off : function (events, selector) {
                $tr.off(events, selector);
                if ($childTr && $childTr.length) $childTr.off(events, selector);
            },
        };
    }

    // ─── Dependent dropdowns ──────────────────────────────────────────────────

    /** Bind change listeners on parent selects for all dependent columns in a row. */
    function bindDependentDropdowns($tr, $childTr, allCols) {
        $.each(allCols, function (i, col) {
            if (!col.dependsOn) return;

            var $parent = $tr.find('[name="' + col.dependsOn + '"]');
            if (!$parent.length && $childTr && $childTr.length) {
                $parent = $childTr.find('[name="' + col.dependsOn + '"]');
            }

            var $child = $tr.find('[name="' + col.field + '"]');
            if (!$child.length && $childTr && $childTr.length) {
                $child = $childTr.find('[name="' + col.field + '"]');
            }

            if (!$parent.length || !$child.length) return;

            $parent.on("change.editDepends", function () {
                PluginDependentDropdown.reloadSelect($child, col.dependsUrl, $(this).val(), null);
            });
        });
    }

    /**
     * Pre-fetch dependent select options for all columns that have a dependsOn relationship.
     * Resolves when all fetches complete (never rejects).
     * @returns jQuery deferred
     */
    function prefetchDependents(allCols, rowData) {
        var promises = [];

        $.each(allCols, function (i, col) {
            if (!col.dependsOn || col.type !== "select" || !col.dependsUrl) return;

            var parentVal  = rowData[col.dependsOn];
            var currentVal = rowData[col.field];

            if (!parentVal) return;

            var url      = col.dependsUrl.replace(":value", encodeURIComponent(parentVal));
            var deferred = $.Deferred();
            promises.push(deferred.promise());

            PluginAjax.loadOptions(url)
                .done(function (response) {
                    col._fetchedOptions      = response || [];
                    col._fetchedCurrentValue = currentVal;
                    deferred.resolve();
                })
                .fail(function () {
                    col._fetchedOptions      = [];
                    col._fetchedCurrentValue = currentVal;
                    deferred.resolve(); // don't block on failure
                });
        });

        return promises.length ? $.when.apply($, promises) : $.when();
    }

    /** Apply pre-fetched options back onto columns so buildInput renders them correctly. */
    function applyFetchedOptions(allCols, rowData) {
        $.each(allCols, function (i, col) {
            if (col._fetchedOptions === undefined) return;
            col.options = col._fetchedOptions;
            rowData[col.field] = col._fetchedCurrentValue;
            delete col._fetchedOptions;
            delete col._fetchedCurrentValue;
        });
    }

    // ─── Init ─────────────────────────────────────────────────────────────────

    function init(options) {
        var config = $.extend(true, {}, DEFAULTS, options || {});

        if (!config.updateUrl)       { console.error("EditPlugin: updateUrl is required.");  return null; }
        if (!config.datatable)       { console.error("EditPlugin: datatable is required.");  return null; }
        if (!config.columns.length)  { console.error("EditPlugin: columns are required.");   return null; }

        var dt         = config.datatable;
        var $container = config.container === "document" ? $(document) : $(config.container);

        var visibleCols = $.grep(config.columns, function (c) { return !c.hidden; });
        var hiddenCols  = $.grep(config.columns, function (c) { return c.hidden; });

        // O(1) column lookup by field name
        var columnMap = {};
        $.each(config.columns, function (i, col) { columnMap[col.field] = col; });

        var $editingTr    = null;
        var $editingChild = null;

        // Clean up edit state when DataTables redraws (e.g. search, pagination)
        dt.on("draw.dt", function () {
            exitEditMode();
        });

        // ─── Enter edit mode ──────────────────────────────────────────────────

        function enterEditMode($tr, rowData) {
            var cellMap = buildVisibleCellMap(dt);
            var activeActionsTarget = config.actionsTarget !== null ? config.actionsTarget : detectActionsTarget(dt);

            function getVisibleCellIndex(dtIndex) {
                return cellMap[dtIndex] !== undefined ? cellMap[dtIndex] : dtIndex;
            }
            // Deep-clone ALL columns so pre-fetched data doesn't pollute the shared config
            var allCols = visibleCols.concat(hiddenCols).map(function (col) {
                return $.extend(true, {}, col);
            });

            $tr.addClass("editing-row");
            $editingTr = $tr;

            prefetchDependents(allCols, rowData).always(function () {
                applyFetchedOptions(allCols, rowData);

                // Inject inputs into visible column cells
                var $cells = $tr.find("td");
                $.each(allCols, function (i, col) {
                    if (col.hidden || col.target === undefined) return;

                    var $cell = $cells.eq(getVisibleCellIndex(col.target));
                    if (!$cell.length) return;

                    $cell.attr("data-original-html", $cell.html());
                    $cell.html(PluginInputBuilder.buildInput(col, rowData));
                });

                // Replace actions cell with save/cancel buttons
                var $actionsCell = $cells.eq(getVisibleCellIndex(activeActionsTarget));
                $actionsCell.attr("data-original-html", $actionsCell.html());
                $actionsCell.html(buildActionsEdit());

                // Inject child row for hidden fields
                var childHtml = PluginInputBuilder.buildChildRow(hiddenCols, rowData);
                if (childHtml) {
                    $editingChild = $(childHtml);
                    $tr.after($editingChild);
                } else {
                    $editingChild = null;
                }

                // Bind dependent dropdowns
                bindDependentDropdowns($tr, $editingChild, allCols);

                // Bind live validation
                PluginValidator.bindLive(buildPseudoForm($tr, $editingChild));
            });
        }

        // ─── Exit edit mode (cancel) ──────────────────────────────────────────

        function exitEditMode() {
            if (!$editingTr) return;

            var $tr = $editingTr;

            $tr.find("td[data-original-html]").each(function () {
                $(this).html($(this).attr("data-original-html")).removeAttr("data-original-html");
            });

            if ($editingChild && $editingChild.length) {
                $editingChild.find("select").off("change.editDepends");
                $editingChild.remove();
            }

            $tr.find("select").off("change.editDepends");
            $tr.removeClass("editing-row");
            $editingTr    = null;
            $editingChild = null;
        }

        // ─── Validation ───────────────────────────────────────────────────────

        /**
         * Validate all edit inputs in the row (and child row).
         * Builds the pseudo-form once and reuses it for every field.
         */
        function validateEditRow($tr, $childTr) {
            var $pseudoForm = buildPseudoForm($tr, $childTr);
            var valid       = true;

            var $allInputs = $tr.find(".edit-input");
            if ($childTr && $childTr.length) {
                $allInputs = $allInputs.add($childTr.find(".edit-input"));
            }

            $allInputs.each(function () {
                var $el  = $(this);
                var name = $el.attr("name");
                var col  = columnMap[name];

                // Skip optional empty text/select fields
                if (col && col.optional && ($el.val() || "").trim() === "" && $el.attr("type") !== "file") return;
                // Skip optional file inputs with no file selected
                if (col && col.type === "file" && !this.files.length && col.optional) return;

                if (!PluginValidator.validateField($pseudoForm, this)) {
                    valid = false;
                }
            });

            return valid;
        }

        function showRowErrors($tr, $childTr, errors) {
            $.each(errors, function (field, messages) {
                var message = Array.isArray(messages) ? messages[0] : messages;

                var $errDiv = $tr.find('[data-field-error="' + field + '"]');
                if (!$errDiv.length && $childTr && $childTr.length) {
                    $errDiv = $childTr.find('[data-field-error="' + field + '"]');
                }

                var $input = $tr.find('[name="' + field + '"]');
                if (!$input.length && $childTr && $childTr.length) {
                    $input = $childTr.find('[name="' + field + '"]');
                }

                $input.addClass("is-invalid");
                $errDiv.text(message);
            });
        }

        // ─── FormData builder ─────────────────────────────────────────────────

        function appendField(formData, el) {
            var $el  = $(el);
            var name = $el.attr("name");
            var col  = columnMap[name];

            if ($el.attr("type") === "file") {
                if (el.files && el.files.length) {
                    formData.append(name, el.files[0]);
                }
                return; // optional + no file → skip (keep existing on server)
            }

            if ($el.attr("type") === "checkbox") {
                formData.append(name, $el.is(":checked") ? "1" : "0");
                return;
            }

            if ($el.attr("type") === "password" && col && col.optional && !$el.val()) {
                return; // leave blank → keep current password on server
            }

            var val = $el.val();
            if (Array.isArray(val)) {
                $.each(val, function (i, v) {
                    formData.append(name + "[]", v);
                });
                return;
            }

            formData.append(name, val || "");
        }

        // ─── Save ─────────────────────────────────────────────────────────────

        function saveRow(id) {
            if (!$editingTr) return;

            var $tr      = $editingTr;
            var $childTr = $editingChild;

            if (!validateEditRow($tr, $childTr)) return;

            var formData = new FormData();
            formData.append("_method", config.method);

            $tr.find(".edit-input").each(function ()      { appendField(formData, this); });
            if ($childTr && $childTr.length) {
                $childTr.find(".edit-input").each(function () { appendField(formData, this); });
            }

            var $saveBtn = $tr.find(".edit-save-btn");
            $saveBtn.prop("disabled", true).html('<span class="spinner-border spinner-border-sm"></span>');

            PluginAjax.send(config.updateUrl.replace(":id", id), "POST", formData)
                .done(function (response) {
                    exitEditMode();

                    if (typeof config.mapRow === "function") {
                        dt.row($tr).data(config.mapRow(response)).invalidate().draw(false);
                    } else {
                        dt.ajax.reload(null, false);
                    }

                    PluginNotify.show("success", config.notifications.successTitle, config.notifications.successText);
                })
                .fail(function (xhr) {
                    if (xhr.status === 422) {
                        var errors = xhr.responseJSON && xhr.responseJSON.errors ? xhr.responseJSON.errors : {};
                        showRowErrors($tr, $childTr, errors);
                    } else {
                        PluginNotify.show("error", config.notifications.errorTitle, config.notifications.errorText);
                    }
                    restoreSaveBtn($saveBtn);
                });
        }

        // ─── Event delegation ─────────────────────────────────────────────────

        $container.on("click.edit", config.selector, function () {
            var $btn = $(this);
            var $tr  = $btn.closest("tr");

            if ($editingTr && $editingTr[0] !== $tr[0]) {
                PluginNotify.show("warning", "Warning", "Please save or cancel the current edit first.");
                return;
            }

            if ($tr.hasClass("editing-row")) return;

            var rowData = dt.row($tr).data();
            if (!rowData) return;

            var id = $btn.attr("data-id") || rowData.id;
            $tr.attr("data-edit-id", id);
            enterEditMode($tr, rowData);
        });

        $container.on("click.edit", ".edit-save-btn", function () {
            if (!$editingTr) return;
            saveRow($editingTr.attr("data-edit-id"));
        });

        $container.on("click.edit", ".edit-cancel-btn", function () {
            exitEditMode();
        });

        // ─── Public API ───────────────────────────────────────────────────────

        return {
            exitEditMode: exitEditMode,
            destroy: function () {
                $container.off("click.edit");
            },
        };
    }

    return { init };
})();
