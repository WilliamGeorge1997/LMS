"use strict";

/**
 * EditPlugin
 * Handles: inline row editing in DataTables with child row for hidden fields
 *
 * Dependencies: jQuery, DataTables, PluginAjax, PluginNotify, PluginValidator
 *
 * Usage:
 *   EditPlugin.init({
 *       updateUrl : "/admins/:id",
 *       datatable : dt,
 *       selector  : ".edit-btn",
 *       columns   : [
 *           { field: "name",      type: "text",     target: 2 },
 *           { field: "email",     type: "text",     target: 3 },
 *           { field: "role",      type: "select",   target: 4, options: [{value:"super_admin", label:"Super Admin"}] },
 *           { field: "country_id",type: "select",   target: 5, options: [{value:1, label:"Egypt"}] },
 *           { field: "city_id",   type: "select",   target: 6, options: [],
 *               dependsOn: "country_id", dependsUrl: "/ajax/cities?country_id=:value" },
 *           { field: "password",  type: "password", hidden: true, optional: true },
 *           { field: "image",     type: "image",    hidden: true, optional: true },
 *       ],
 *       mapRow : function(response) { return response.data; },
 *   });
 *
 * Column definition:
 *   field       {string}   — field name (must match datatable data key)
 *   type        {string}   — "text" | "password" | "select" | "image" | "toggle"
 *   target      {number}   — datatable column index (omit if hidden: true)
 *   hidden      {boolean}  — if true → rendered in child row grid, not inline
 *   optional    {boolean}  — if true → field is skipped in FormData when empty
 *   options     {array}    — [{value, label}] for select type
 *   dependsOn   {string}   — parent field name (for dependent selects)
 *   dependsUrl  {string}   — URL with :value placeholder (for dependent selects)
 *   rules       {object}   — validation rules e.g. { required: true, min: 6, max: 255, email: true }
 *   messages    {object}   — custom messages e.g. { required: "Name is required." }
 *   placeholder {string}   — input placeholder text
 *   colClass    {string}   — Bootstrap col class for child row (default: "col-md-6")
 */
window.EditPlugin = (function () {
    // ─── Defaults ─────────────────────────────────────────────────────────────

    var DEFAULTS = {
        updateUrl: null,
        datatable: null,
        selector: ".edit-btn",
        container: "document",
        method: "POST",
        columns: [],
        mapRow: null,

        // Actions column target index (the column that holds edit/delete buttons)
        actionsTarget: null, // auto-detected if null

        notifications: {
            successTitle: "Updated",
            successText: "Record updated successfully.",
            errorTitle: "Error",
            errorText: "Something went wrong. Please try again.",
        },
    };

    // ─── Helpers ──────────────────────────────────────────────────────────────

    /**
     * Build data-rule-* and data-msg-* attribute string from rules/messages config
     */
    function buildRuleAttrs(col) {
        var rules = col.rules || {};
        var messages = col.messages || {};
        var attrs = "";

        if (rules.required)
            attrs +=
                ' data-rule-required data-msg-required="' +
                (messages.required || "This field is required.") +
                '"';
        if (rules.email)
            attrs +=
                ' data-rule-email data-msg-email="' +
                (messages.email || "Please enter a valid email.") +
                '"';
        if (rules.min)
            attrs +=
                ' data-rule-min="' +
                rules.min +
                '" data-msg-min="' +
                (messages.min || "Minimum " + rules.min + " characters.") +
                '"';
        if (rules.max)
            attrs +=
                ' data-rule-max="' +
                rules.max +
                '" data-msg-max="' +
                (messages.max || "Maximum " + rules.max + " characters.") +
                '"';
        if (rules.same)
            attrs +=
                ' data-rule-same="' +
                rules.same +
                '" data-msg-same="' +
                (messages.same || "Values do not match.") +
                '"';

        return attrs;
    }

    /**
     * Build a single input HTML string for a column definition and current row value
     */
    function buildInput(col, rowData) {
        var value = rowData[col.field] !== undefined ? rowData[col.field] : "";
        var placeholder = col.placeholder || "";
        var ruleAttrs = buildRuleAttrs(col);
        var fieldName = col.field;

        if (col.type === "select") {
            var optionsHtml = "";
            $.each(col.options || [], function (i, opt) {
                var selected =
                    String(opt.value) === String(value) ? "selected" : "";
                optionsHtml +=
                    '<option value="' +
                    opt.value +
                    '" ' +
                    selected +
                    ">" +
                    opt.label +
                    "</option>";
            });

            var dependsAttrs = "";
            if (col.dependsOn) {
                dependsAttrs += ' data-edit-depends-on="' + col.dependsOn + '"';
                dependsAttrs +=
                    ' data-edit-depends-url="' + col.dependsUrl + '"';
            }

            return (
                '<select name="' +
                fieldName +
                '" class="form-select form-select-sm form-select-solid edit-input"' +
                ruleAttrs +
                dependsAttrs +
                ">" +
                optionsHtml +
                "</select>" +
                '<div class="invalid-feedback d-block small" data-field-error="' +
                fieldName +
                '"></div>'
            );
        }

        if (col.type === "image") {
            var preview = value
                ? '<img src="' +
                  value +
                  '" class="edit-img-preview rounded" style="width:36px;height:36px;object-fit:cover;flex-shrink:0;" />'
                : '<span class="edit-img-placeholder rounded d-flex align-items-center justify-content-center bg-light-primary text-primary fw-bold fs-8" style="width:36px;height:36px;flex-shrink:0;">IMG</span>';
            return (
                '<div class="d-flex align-items-center gap-2">' +
                preview +
                '<input type="file" name="' +
                fieldName +
                '" class="form-control form-control-sm form-control-solid edit-input" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"' +
                ruleAttrs +
                ' style="min-width:0;" />' +
                "</div>" +
                '<div class="invalid-feedback d-block small" data-field-error="' +
                fieldName +
                '"></div>'
            );
        }

        if (col.type === "toggle") {
            var isChecked =
                value == 1 || value === true || value === "1" ? "checked" : "";
            return (
                '<div class="d-flex align-items-center" style="min-height:34px;">' +
                '<label class="form-check form-switch form-switch-sm form-check-custom form-check-solid mb-0">' +
                '<input class="form-check-input edit-input" type="checkbox" name="' +
                fieldName +
                '" value="1" ' +
                isChecked +
                " />" +
                "</label>" +
                "</div>" +
                '<div class="invalid-feedback d-block small" data-field-error="' +
                fieldName +
                '"></div>'
            );
        }

        if (col.type === "password") {
            return (
                '<input type="password" name="' +
                fieldName +
                '" class="form-control form-control-sm form-control-solid edit-input" placeholder="' +
                (placeholder || "Leave blank to keep current") +
                '"' +
                ruleAttrs +
                ' autocomplete="new-password" />' +
                '<div class="invalid-feedback d-block small" data-field-error="' +
                fieldName +
                '"></div>'
            );
        }

        // Default: text
        return (
            '<input type="text" name="' +
            fieldName +
            '" class="form-control form-control-sm form-control-solid edit-input" value="' +
            $("<div>").text(value).html() +
            '" placeholder="' +
            placeholder +
            '"' +
            ruleAttrs +
            ' autocomplete="off" />' +
            '<div class="invalid-feedback d-block small" data-field-error="' +
            fieldName +
            '"></div>'
        );
    }

    /**
     * Build the child row HTML for hidden fields
     */
    function buildChildRow(hiddenCols, rowData) {
        if (!hiddenCols.length) return null;

        var innerHtml = "";
        $.each(hiddenCols, function (i, col) {
            var colClass = col.colClass || "col-md-6";
            var label =
                col.label ||
                col.field.replace(/_/g, " ").replace(/\b\w/g, function (c) {
                    return c.toUpperCase();
                });
            var required = !col.optional
                ? ' <span class="text-danger">*</span>'
                : "";

            innerHtml +=
                '<div class="' +
                colClass +
                '">' +
                '<label class="form-label fs-7 fw-bold text-gray-600 mb-2 text-uppercase ls-1" style="letter-spacing:0.06em;">' +
                label +
                required +
                "</label>" +
                buildInput(col, rowData) +
                "</div>";
        });

        return (
            '<tr class="edit-child-row">' +
            '<td colspan="100%" class="p-0 border-top-0">' +
            '<div class="edit-child-inner d-flex align-items-start gap-3 px-6 py-5"' +
            ' style="background:linear-gradient(135deg,#f8f9ff 0%,#f1f4ff 100%);border-left:3px solid #009ef7;border-bottom:1px solid #e4e6ef;">' +
            '<div class="edit-child-icon d-flex align-items-center justify-content-center rounded-circle bg-light-primary flex-shrink-0 mt-1" style="width:32px;height:32px;">' +
            '<i class="ki-duotone ki-pencil fs-6 text-primary"><span class="path1"></span><span class="path2"></span></i>' +
            "</div>" +
            '<div class="flex-grow-1">' +
            '<div class="text-gray-500 fw-semibold fs-8 mb-3 text-uppercase" style="letter-spacing:0.08em;">Additional Fields</div>' +
            '<div class="row g-5">' +
            innerHtml +
            "</div>" +
            "</div>" +
            "</div>" +
            "</td>" +
            "</tr>"
        );
    }

    /**
     * Detect the actions column target index (last visible column)
     */
    function detectActionsTarget(dt) {
        var count = dt.columns().count();
        // Walk backwards, skip hidden columns
        for (var i = count - 1; i >= 0; i--) {
            if (dt.column(i).visible()) return i;
        }
        return count - 1;
    }

    /**
     * Build actions cell HTML for edit mode
     */
    function buildActionsEdit() {
        return (
            '<div class="d-flex justify-content-end align-items-center gap-1">' +
            '<button type="button" class="btn btn-icon btn-sm btn-light-success edit-save-btn" title="Save">' +
            '<i class="ki-duotone ki-check fs-4"><span class="path1"></span><span class="path2"></span></i>' +
            "</button>" +
            '<button type="button" class="btn btn-icon btn-sm btn-light-danger edit-cancel-btn" title="Cancel">' +
            '<i class="ki-duotone ki-cross fs-4"><span class="path1"></span><span class="path2"></span></i>' +
            "</button>" +
            "</div>"
        );
    }

    /**
     * Collect all inputs from a row and its child row into a jQuery wrapper
     * so PluginValidator can work on them as a pseudo-form
     */

    //Old code
    // function buildPseudoForm($tr, $childTr) {
    //     var $inputs = $tr.find(".edit-input");
    //     if ($childTr && $childTr.length) {
    //         $inputs = $inputs.add($childTr.find(".edit-input"));
    //     }
    //     // PluginValidator expects a jQuery object with .find() — wrap in a div
    //     var $wrapper = $("<div>").append($inputs.clone(true, true));
    //     // Return a proxy: find works on both $tr and $childTr
    //     return {
    //         _$tr: $tr,
    //         _$childTr: $childTr,
    //         find: function (selector) {
    //             var $result = $tr.find(selector);
    //             if ($childTr && $childTr.length) {
    //                 $result = $result.add($childTr.find(selector));
    //             }
    //             return $result;
    //         },
    //         on: function () {},
    //         off: function () {},
    //     };
    // }
    //Old code

    //New Code
    function buildPseudoForm($tr, $childTr) {
        var $container = $("<div>");
        $container.append($tr.find(".edit-input").clone(true, true));
        if ($childTr && $childTr.length) {
            $container.append($childTr.find(".edit-input").clone(true, true));
        }

        return {
            find: function (selector) {
                var $result = $tr.find(selector);
                if ($childTr && $childTr.length) {
                    $result = $result.add($childTr.find(selector));
                }
                return $result;
            },
            on: function (events, selector, handler) {
                $tr.on(events, selector, handler);
                if ($childTr && $childTr.length) {
                    $childTr.on(events, selector, handler);
                }
            },
            off: function (events, selector) {
                $tr.off(events, selector);
                if ($childTr && $childTr.length) {
                    $childTr.off(events, selector);
                }
            },
        };
    }
    //New Code

    /**
     * Bind dependent dropdown behavior inside a row after inputs are injected
     */
    function bindDependentDropdowns($tr, $childTr, visibleCols, hiddenCols) {
        var allCols = visibleCols.concat(hiddenCols);

        $.each(allCols, function (i, col) {
            if (!col.dependsOn) return;

            // Find parent select — could be in $tr or $childTr
            var $parent = $tr.find('[name="' + col.dependsOn + '"]');
            if (!$parent.length && $childTr && $childTr.length) {
                $parent = $childTr.find('[name="' + col.dependsOn + '"]');
            }

            // Find child select
            var $child = $tr.find('[name="' + col.field + '"]');
            if (!$child.length && $childTr && $childTr.length) {
                $child = $childTr.find('[name="' + col.field + '"]');
            }

            if (!$parent.length || !$child.length) return;

            $parent.on("change.editDepends", function () {
                var parentVal = $(this).val();
                reloadDependentSelect($child, col.dependsUrl, parentVal, null);
            });
        });
    }

    /**
     * Reload a dependent select's options via AJAX
     * @param {jQuery}  $child      - the child select element
     * @param {string}  url         - URL with :value placeholder
     * @param {string}  parentVal   - current parent value
     * @param {*}       currentVal  - value to pre-select after load (null = none)
     */
    function reloadDependentSelect($child, url, parentVal, currentVal) {
        if (!parentVal) {
            $child
                .html(
                    '<option value="" disabled selected>Select option</option>',
                )
                .prop("disabled", true);
            return;
        }

        var resolvedUrl = url.replace(":value", encodeURIComponent(parentVal));

        $child
            .html('<option value="" disabled selected>Loading...</option>')
            .prop("disabled", true);

        PluginAjax.loadOptions(resolvedUrl)
            .done(function (response) {
                var html =
                    '<option value="" disabled selected>Select option</option>';
                $.each(response || [], function (i, item) {
                    var selected =
                        currentVal !== null &&
                        String(item.value) === String(currentVal)
                            ? "selected"
                            : "";
                    html +=
                        '<option value="' +
                        item.value +
                        '" ' +
                        selected +
                        ">" +
                        item.label +
                        "</option>";
                });
                $child.html(html).prop("disabled", false);
            })
            .fail(function () {
                $child
                    .html(
                        '<option value="" disabled selected>Select option</option>',
                    )
                    .prop("disabled", true);
            });
    }

    /**
     * Pre-fetch all dependent selects for a row on edit open
     * Resolves when all fetches are complete
     * @returns Promise
     */
    function prefetchDependents(allCols, rowData) {
        var promises = [];

        $.each(allCols, function (i, col) {
            if (!col.dependsOn || col.type !== "select") return;

            var parentValue = rowData[col.dependsOn];
            var currentValue = rowData[col.field];

            if (!parentValue || !col.dependsUrl) return;

            var url = col.dependsUrl.replace(
                ":value",
                encodeURIComponent(parentValue),
            );

            var deferred = $.Deferred();
            promises.push(deferred.promise());

            PluginAjax.loadOptions(url)
                .done(function (response) {
                    // Mutate the column's options so buildInput renders them correctly
                    col._fetchedOptions = response || [];
                    col._fetchedCurrentValue = currentValue;
                    deferred.resolve();
                })
                .fail(function () {
                    col._fetchedOptions = [];
                    col._fetchedCurrentValue = currentValue;
                    deferred.resolve(); // resolve anyway so we don't block
                });
        });

        return promises.length ? $.when.apply($, promises) : $.when();
    }

    /**
     * Apply pre-fetched options back onto columns before rendering
     */
    function applyFetchedOptions(allCols) {
        $.each(allCols, function (i, col) {
            if (col._fetchedOptions !== undefined) {
                col.options = col._fetchedOptions;
                col._savedCurrentValue = col._fetchedCurrentValue;
                delete col._fetchedOptions;
                delete col._fetchedCurrentValue;
            }
        });
    }

    // ─── Init ─────────────────────────────────────────────────────────────────

    function init(options) {
        var config = $.extend(true, {}, DEFAULTS, options || {});

        if (!config.updateUrl) {
            console.error("EditPlugin: updateUrl is required.");
            return null;
        }
        if (!config.datatable) {
            console.error("EditPlugin: datatable is required.");
            return null;
        }
        if (!config.columns.length) {
            console.error("EditPlugin: columns are required.");
            return null;
        }

        var dt = config.datatable;
        var $container =
            config.container === "document" ? $(document) : $(config.container);
        var actionsTarget =
            config.actionsTarget !== null
                ? config.actionsTarget
                : detectActionsTarget(dt);

        //Old code
        // Separate columns into visible (inline) and hidden (child row)
        // var visibleCols = $.grep(config.columns, function (c) {
        //     return !c.hidden;
        // });
        // var hiddenCols = $.grep(config.columns, function (c) {
        //     return c.hidden;
        // });

        // // Track which row is currently being edited
        // var $editingTr = null;
        // var $editingChild = null;
        //Old code

        //New code
        // Separate columns into visible (inline) and hidden (child row)
        var visibleCols = $.grep(config.columns, function (c) {
            return !c.hidden;
        });
        var hiddenCols = $.grep(config.columns, function (c) {
            return c.hidden;
        });

        // Build column lookup map for O(1) access
        var columnMap = {};
        $.each(config.columns, function (i, col) {
            columnMap[col.field] = col;
        });

        // Track which row is currently being edited
        var $editingTr = null;
        var $editingChild = null;
        //New code

        // ─── Enter edit mode ──────────────────────────────────────────────────

        function enterEditMode($tr, rowData) {
            //Old Code
            // var allCols = visibleCols.concat(hiddenCols);
            //Old Code

            //New Code
            var allCols = $.extend(true, [], visibleCols.concat(hiddenCols));
            //New Code

            // Mark row
            $tr.addClass("editing-row");
            $editingTr = $tr;

            // Pre-fetch dependent selects before rendering
            prefetchDependents(allCols, rowData).always(function () {
                applyFetchedOptions(allCols);

                // ── Inject inputs into visible column cells ────────────────────
                $.each(visibleCols, function (i, col) {
                    if (col.target === undefined) return;

                    var $cell = $tr
                        .find("td")
                        .eq(getVisibleCellIndex(dt, col.target));

                    if (!$cell.length) return;

                    // Store original HTML for cancel
                    $cell.attr("data-original-html", $cell.html());

                    // Override options with pre-fetched if available
                    var renderCol = col;
                    if (col._savedCurrentValue !== undefined) {
                        renderCol = $.extend({}, col);
                        // options already applied via applyFetchedOptions
                        // just ensure the value is correct
                        rowData[col.field] = col._savedCurrentValue;
                        delete col._savedCurrentValue;
                    }

                    $cell.html(buildInput(renderCol, rowData));
                });

                // ── Replace actions cell ───────────────────────────────────────
                var $actionsCell = $tr
                    .find("td")
                    .eq(getVisibleCellIndex(dt, actionsTarget));
                $actionsCell.attr("data-original-html", $actionsCell.html());
                $actionsCell.html(buildActionsEdit());

                // ── Inject child row for hidden fields ────────────────────────
                var childHtml = buildChildRow(hiddenCols, rowData);
                if (childHtml) {
                    var $child = $(childHtml);
                    $tr.after($child);
                    $editingChild = $child;
                } else {
                    $editingChild = null;
                }

                // ── Bind dependent dropdowns ──────────────────────────────────
                bindDependentDropdowns(
                    $tr,
                    $editingChild,
                    visibleCols,
                    hiddenCols,
                );

                // ── Bind live validation ──────────────────────────────────────
                var $pseudoForm = buildPseudoForm($tr, $editingChild);
                PluginValidator.bindLive($pseudoForm);
            });
        }

        // ─── Exit edit mode (cancel) ──────────────────────────────────────────

        function exitEditMode() {
            if (!$editingTr) return;

            var $tr = $editingTr;

            // Restore all cells
            $tr.find("td[data-original-html]").each(function () {
                $(this)
                    .html($(this).attr("data-original-html"))
                    .removeAttr("data-original-html");
            });

            // Remove child row
            //Old code
            // if ($editingChild && $editingChild.length) {
            //     $editingChild.remove();
            // }
            //Old code

            //New Code
            if ($editingChild && $editingChild.length) {
                $editingChild.find("select").off("change.editDepends");
                $editingChild.remove();
            }
            //New Code

            // Unbind dependent dropdown listeners
            $tr.find("select").off("change.editDepends");

            $tr.removeClass("editing-row");
            $editingTr = null;
            $editingChild = null;
        }

        // ─── Save ─────────────────────────────────────────────────────────────

        function saveRow(id) {
            if (!$editingTr) return;

            var $tr = $editingTr;
            var $childTr = $editingChild;
            var $pseudoForm = buildPseudoForm($tr, $childTr);

            // Validate
            if (!validatePseudoForm($tr, $childTr)) return;

            // Build FormData
            var formData = new FormData();
            formData.append("_method", config.method);

            // Visible inputs
            $tr.find(".edit-input").each(function () {
                appendField(formData, this, config.columns);
            });

            // Hidden (child row) inputs
            if ($childTr && $childTr.length) {
                $childTr.find(".edit-input").each(function () {
                    appendField(formData, this, config.columns);
                });
            }

            var url = config.updateUrl.replace(":id", id);
            var $saveBtn = $tr.find(".edit-save-btn");

            // Loading state
            $saveBtn
                .prop("disabled", true)
                .html('<span class="spinner-border spinner-border-sm"></span>');

            PluginAjax.send(url, "POST", formData)
                .done(function (response) {
                    exitEditMode();

                    // Update datatable row data and redraw
                    if (typeof config.mapRow === "function") {
                        var newData = config.mapRow(response);
                        dt.row($tr).data(newData).invalidate().draw(false);
                    } else {
                        dt.ajax.reload(null, false);
                    }

                    PluginNotify.show(
                        "success",
                        config.notifications.successTitle,
                        config.notifications.successText,
                    );
                })
                .fail(function (xhr) {
                    if (xhr.status === 422) {
                        showRowErrors(
                            $tr,
                            $childTr,
                            xhr.responseJSON?.errors || {},
                        );
                        $saveBtn
                            .prop("disabled", false)
                            .html(
                                '<i class="ki-duotone ki-check fs-4"><span class="path1"></span><span class="path2"></span></i>',
                            );
                        return;
                    }
                    PluginNotify.show(
                        "error",
                        config.notifications.errorTitle,
                        config.notifications.errorText,
                    );
                    $saveBtn
                        .prop("disabled", false)
                        .html(
                            '<i class="ki-duotone ki-check fs-4"><span class="path1"></span><span class="path2"></span></i>',
                        );
                });
        }

        // ─── Validation helpers ───────────────────────────────────────────────

        function validatePseudoForm($tr, $childTr) {
            var valid = true;

            var $allInputs = $tr.find(".edit-input");
            if ($childTr && $childTr.length) {
                $allInputs = $allInputs.add($childTr.find(".edit-input"));
            }

            $allInputs.each(function () {
                var $el = $(this);
                var name = $el.attr("name");
                var col = getColumnByField(name);
                var value = ($el.val() || "").trim();

                // Skip optional + empty
                if (
                    col &&
                    col.optional &&
                    value === "" &&
                    $el.attr("type") !== "file"
                )
                    return;
                if (
                    col &&
                    col.type === "file" &&
                    !this.files.length &&
                    col.optional
                )
                    return;

                if (!validateInlineField($tr, $childTr, this)) {
                    valid = false;
                }
            });

            return valid;
        }

        function validateInlineField($tr, $childTr, el) {
            var $pseudoForm = buildPseudoForm($tr, $childTr);
            return PluginValidator.validateField($pseudoForm, el);
        }

        function showRowErrors($tr, $childTr, errors) {
            $.each(errors, function (field, messages) {
                var message = Array.isArray(messages) ? messages[0] : messages;
                var $errDiv = $tr.find('[data-field-error="' + field + '"]');

                if (!$errDiv.length && $childTr && $childTr.length) {
                    $errDiv = $childTr.find(
                        '[data-field-error="' + field + '"]',
                    );
                }

                var $input = $tr.find('[name="' + field + '"]');
                if (!$input.length && $childTr && $childTr.length) {
                    $input = $childTr.find('[name="' + field + '"]');
                }

                $input.addClass("is-invalid");
                $errDiv.text(message);
            });
        }

        // ─── FormData helpers ─────────────────────────────────────────────────

        function appendField(formData, el, columns) {
            var $el = $(el);
            var name = $el.attr("name");
            var col = getColumnByField(name);

            // File input
            if ($el.attr("type") === "file") {
                if (el.files && el.files.length) {
                    formData.append(name, el.files[0]);
                }
                // optional + no file → skip (keep existing on server)
                return;
            }

            // Checkbox / toggle — send 1 or 0 explicitly
            if ($el.attr("type") === "checkbox") {
                formData.append(name, $el.is(":checked") ? "1" : "0");
                return;
            }

            // Password optional → skip if empty
            if (
                $el.attr("type") === "password" &&
                col &&
                col.optional &&
                !$el.val()
            ) {
                return;
            }

            formData.append(name, $el.val() || "");
        }

        //Old code
        // function getColumnByField(field) {
        //     var found = null;
        //     $.each(config.columns, function (i, col) {
        //         if (col.field === field) {
        //             found = col;
        //             return false;
        //         }
        //     });
        //     return found;
        // }
        //Old code

        //New code
        function getColumnByField(field) {
            return columnMap[field] || null;
        }
        //New code

        // ─── DataTable utility ────────────────────────────────────────────────

        /**
         * Convert a DataTables column index to the actual visible <td> index
         * (DataTables may have hidden columns that don't render a <td>)
         */
        function getVisibleCellIndex(dt, columnIndex) {
            var visibleIndex = 0;
            for (var i = 0; i < columnIndex; i++) {
                if (dt.column(i).visible()) visibleIndex++;
            }
            return visibleIndex;
        }

        // ─── Event delegation ─────────────────────────────────────────────────

        // Edit button clicked
        $container.on("click.edit", config.selector, function () {
            var $btn = $(this);
            var $tr = $btn.closest("tr");

            // Block if another row is already being edited
            if ($editingTr && $editingTr[0] !== $tr[0]) {
                PluginNotify.show(
                    "warning",
                    "Warning",
                    "Please save or cancel the current edit first.",
                );
                return;
            }

            // Already editing this row → ignore
            if ($tr.hasClass("editing-row")) return;

            var rowData = dt.row($tr).data();
            if (!rowData) return;

            var id = $btn.attr("data-id") || rowData.id;
            $tr.attr("data-edit-id", id);

            enterEditMode($tr, rowData);
        });

        // Save button clicked
        $container.on("click.edit", ".edit-save-btn", function () {
            if (!$editingTr) return;
            var id = $editingTr.attr("data-edit-id");
            saveRow(id);
        });

        // Cancel button clicked
        $container.on("click.edit", ".edit-cancel-btn", function () {
            exitEditMode();
        });

        // ─── Public API ───────────────────────────────────────────────────────

        return {
            destroy: function () {
                $container.off("click.edit");
            },
            exitEditMode: exitEditMode,
        };
    }

    return { init };
})();
