"use strict";

/**
 * PluginInputBuilder
 * Single responsibility: build HTML strings for form inputs.
 * Used by EditPlugin (and any future plugin that renders inputs dynamically).
 *
 * Dependencies: none
 */
window.PluginInputBuilder = (function () {
    var ERROR_DIV = '<div class="invalid-feedback d-block small" data-field-error="{name}"></div>';

    /**
     * Escape HTML special characters to prevent XSS.
     * @param {string} str
     * @returns {string}
     */
    function escapeHtml(str) {
        return String(str)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;");
    }

    /**
     * Build data-rule-* / data-msg-* attribute string from a column's rules/messages.
     * @param {object} col
     * @returns {string}
     */
    function buildRuleAttrs(col) {
        var r = col.rules || {};
        var m = col.messages || {};
        var attrs = "";

        if (r.required) attrs += ' data-rule-required data-msg-required="' + (m.required || "This field is required.") + '"';
        if (r.email)    attrs += ' data-rule-email data-msg-email="'       + (m.email    || "Please enter a valid email.") + '"';
        if (r.min)      attrs += ' data-rule-min="' + r.min + '" data-msg-min="' + (m.min || "Minimum " + r.min + " characters.") + '"';
        if (r.max)      attrs += ' data-rule-max="' + r.max + '" data-msg-max="' + (m.max || "Maximum " + r.max + " characters.") + '"';
        if (r.same)     attrs += ' data-rule-same="' + r.same + '" data-msg-same="' + (m.same || "Values do not match.") + '"';

        return attrs;
    }

    /**
     * Build the inline error div for a field.
     * @param {string} fieldName
     * @returns {string}
     */
    function errorDiv(fieldName) {
        return ERROR_DIV.replace(/{name}/g, fieldName);
    }

    /**
     * Build a single input HTML string from a column definition + current row data.
     * @param {object} col     - column config (field, type, options, rules, messages, placeholder…)
     * @param {object} rowData - current row data object
     * @returns {string}
     */
    function buildInput(col, rowData) {
        var value       = rowData[col.field] !== undefined ? rowData[col.field] : "";
        var placeholder = col.placeholder || "";
        var ruleAttrs   = buildRuleAttrs(col);
        var name        = col.field;

        if (col.type === "select") {
            var parts = [];
            $.each(col.options || [], function (i, opt) {
                var selected = String(opt.value) === String(value) ? "selected" : "";
                parts.push('<option value="' + escapeHtml(opt.value) + '" ' + selected + ">" + escapeHtml(opt.label) + "</option>");
            });
            var optionsHtml = parts.join("");

            var dependsAttrs = "";
            if (col.dependsOn) {
                dependsAttrs = ' data-edit-depends-on="' + col.dependsOn + '" data-edit-depends-url="' + col.dependsUrl + '"';
            }

            return '<select name="' + name + '" class="form-select form-select-sm form-select-solid edit-input"' + ruleAttrs + dependsAttrs + ">" +
                optionsHtml + "</select>" + errorDiv(name);
        }

        if (col.type === "image") {
            var preview = value
                ? '<img src="' + escapeHtml(String(value)) + '" class="edit-img-preview rounded" style="width:36px;height:36px;object-fit:cover;flex-shrink:0;" />'
                : '<span class="edit-img-placeholder rounded d-flex align-items-center justify-content-center bg-light-primary text-primary fw-bold fs-8" style="width:36px;height:36px;flex-shrink:0;">IMG</span>';

            return '<div class="d-flex align-items-center gap-2">' + preview +
                '<input type="file" name="' + name + '" class="form-control form-control-sm form-control-solid edit-input"' +
                ' accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"' + ruleAttrs + ' style="min-width:0;" /></div>' + errorDiv(name);
        }

        if (col.type === "toggle") {
            var checked = (value == 1 || value === true || value === "1") ? "checked" : "";
            return '<div class="d-flex align-items-center" style="min-height:34px;">' +
                '<label class="form-check form-switch form-switch-sm form-check-custom form-check-solid mb-0">' +
                '<input class="form-check-input edit-input" type="checkbox" name="' + name + '" value="1" ' + checked + " /></label></div>" + errorDiv(name);
        }

        if (col.type === "password") {
            return '<input type="password" name="' + name + '" class="form-control form-control-sm form-control-solid edit-input"' +
                ' placeholder="' + (placeholder || "Leave blank to keep current") + '"' + ruleAttrs + ' autocomplete="new-password" />' + errorDiv(name);
        }

        // Default: text
        return '<input type="text" name="' + name + '" class="form-control form-control-sm form-control-solid edit-input"' +
            ' value="' + escapeHtml(String(value)) + '" placeholder="' + placeholder + '"' + ruleAttrs + ' autocomplete="off" />' + errorDiv(name);
    }

    /**
     * Build the child row HTML that holds hidden fields below the main row.
     * @param {Array}  hiddenCols - column definitions with hidden:true
     * @param {object} rowData    - current row data
     * @returns {string|null}
     */
    function buildChildRow(hiddenCols, rowData) {
        if (!hiddenCols.length) return null;

        var parts = [];
        $.each(hiddenCols, function (i, col) {
            var colClass = col.colClass || "col-md-6";
            var label    = col.label || col.field.replace(/_/g, " ").replace(/\b\w/g, function (c) { return c.toUpperCase(); });
            var required = col.optional ? "" : ' <span class="text-danger">*</span>';

            parts.push('<div class="' + colClass + '">' +
                '<label class="form-label fs-7 fw-bold text-gray-600 mb-2 text-uppercase ls-1" style="letter-spacing:0.06em;">' + label + required + "</label>" +
                buildInput(col, rowData) + "</div>");
        });
        var innerHtml = parts.join("");

        return '<tr class="edit-child-row"><td colspan="100%" class="p-0 border-top-0">' +
            '<div class="edit-child-inner d-flex align-items-start gap-3 px-6 py-5"' +
            ' style="background:linear-gradient(135deg,#f8f9ff 0%,#f1f4ff 100%);border-left:3px solid #009ef7;border-bottom:1px solid #e4e6ef;">' +
            '<div class="edit-child-icon d-flex align-items-center justify-content-center rounded-circle bg-light-primary flex-shrink-0 mt-1" style="width:32px;height:32px;">' +
            '<i class="ki-duotone ki-pencil fs-6 text-primary"><span class="path1"></span><span class="path2"></span></i></div>' +
            '<div class="flex-grow-1">' +
            '<div class="text-gray-500 fw-semibold fs-8 mb-3 text-uppercase" style="letter-spacing:0.08em;">Additional Fields</div>' +
            '<div class="row g-5">' + innerHtml + "</div></div></div></td></tr>";
    }

    return { escapeHtml, buildRuleAttrs, buildInput, buildChildRow };
})();
