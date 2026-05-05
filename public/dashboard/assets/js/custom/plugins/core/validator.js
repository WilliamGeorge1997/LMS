"use strict";

/**
 * PluginValidator
 * Single responsibility: validate form fields using data-rule-* HTML attributes.
 *
 * Supported rules (set as HTML attributes on the input):
 *   data-rule-required        → field must not be empty
 *   data-rule-email           → must be a valid email
 *   data-rule-min="6"         → minimum character length
 *   data-rule-max="255"       → maximum character length
 *   data-rule-same="#other"   → must match value of another field (CSS selector)
 *
 * Custom messages (optional):
 *   data-msg-required | data-msg-email | data-msg-min | data-msg-max | data-msg-same
 *
 * Dependencies: jQuery
 */
window.PluginValidator = (function () {
    var EMAIL_REGEX = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    /** Selector that matches every field that has at least one validation rule. */
    var RULE_SELECTOR = "[data-rule-required], [data-rule-email], [data-rule-min], [data-rule-max], [data-rule-same]";

    // ─── Error display ────────────────────────────────────────────────────────

    function setError($form, name, message, $el) {
        ($el || $form.find('[name="' + name + '"]')).addClass("is-invalid");
        $form.find('[data-field-error="' + name + '"]').text(message || "");
    }

    function clearError($form, name, $el) {
        ($el || $form.find('[name="' + name + '"]')).removeClass("is-invalid");
        $form.find('[data-field-error="' + name + '"]').text("");
    }

    function clearAll($form) {
        $form.find(".is-invalid").removeClass("is-invalid");
        $form.find("[data-field-error]").text("");
    }

    function showBackendErrors($form, errors) {
        clearAll($form);
        $.each(errors || {}, function (field, messages) {
            setError($form, field, Array.isArray(messages) ? messages[0] : messages);
        });
    }

    // ─── Single field validation ──────────────────────────────────────────────

    /**
     * Validate one field against its data-rule-* attributes.
     * @returns {boolean} true if valid
     */
    function validateField($form, el) {
        if (!el || !el.name) return true;

        var $el   = $(el);
        var name  = el.name;
        var value = ($el.val() || "").trim();

        if ($el.is("[data-rule-required]") && value === "") {
            setError($form, name, $el.attr("data-msg-required") || "This field is required.", $el);
            return false;
        }

        // Skip remaining rules when the field is empty and not required
        if (value === "") {
            clearError($form, name, $el);
            return true;
        }

        if ($el.is("[data-rule-email]") && !EMAIL_REGEX.test(value)) {
            setError($form, name, $el.attr("data-msg-email") || "Please enter a valid email.", $el);
            return false;
        }

        var min = $el.attr("data-rule-min");
        if (min && value.length < parseInt(min, 10)) {
            setError($form, name, $el.attr("data-msg-min") || "Minimum " + min + " characters.", $el);
            return false;
        }

        var max = $el.attr("data-rule-max");
        if (max && value.length > parseInt(max, 10)) {
            setError($form, name, $el.attr("data-msg-max") || "Maximum " + max + " characters.", $el);
            return false;
        }

        var sameSelector = $el.attr("data-rule-same");
        if (sameSelector) {
            var $other = $(sameSelector);
            if ($other.length && $other.val() !== $el.val()) {
                setError($form, name, $el.attr("data-msg-same") || "Values do not match.", $el);
                return false;
            }
        }

        clearError($form, name, $el);
        return true;
    }

    // ─── Full form validation ─────────────────────────────────────────────────

    /**
     * Validate all rule-bearing fields in a form.
     * @returns {boolean} true if all fields are valid
     */
    function validateForm($form) {
        clearAll($form);
        var valid = true;

        $form.find(RULE_SELECTOR).each(function () {
            if (!validateField($form, this)) {
                valid = false;
            }
        });

        return valid;
    }

    // ─── Live validation binding ──────────────────────────────────────────────

    /**
     * Attach live input/change/blur validation to a form.
     * Uses namespaced events so unbindLive() only removes our listeners.
     * @param {jQuery} $form
     */
    function bindLive($form) {
        $form.on(
            "input.validator change.validator blur.validator",
            RULE_SELECTOR,
            function () {
                validateField($form, this);
            }
        );
    }

    function unbindLive($form) {
        $form.off("input.validator change.validator blur.validator");
    }

    return { validateField, validateForm, clearAll, clearError, setError, showBackendErrors, bindLive, unbindLive };
})();
