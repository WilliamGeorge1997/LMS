"use strict";

/**
 * Validator
 * Single responsibility: validate form fields using data-rule-* HTML attributes
 *
 * Supported rules (set as HTML attributes on the input):
 *
 *   data-rule-required        → field must not be empty
 *   data-rule-email           → must be valid email
 *   data-rule-min="6"         → minimum character length
 *   data-rule-max="255"       → maximum character length
 *   data-rule-same="#other"   → must match value of another field (selector)
 *
 * Custom messages (optional):
 *   data-msg-required="..."
 *   data-msg-email="..."
 *   data-msg-min="..."
 *   data-msg-max="..."
 *   data-msg-same="..."
 */
window.PluginValidator = (function () {
    var EMAIL_REGEX = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    // ─── Error display ────────────────────────────────────────────────────────

    function setError($form, name, message) {
        $form.find('[name="' + name + '"]').addClass("is-invalid");
        $form.find('[data-field-error="' + name + '"]').text(message || "");
    }

    function clearError($form, name) {
        $form.find('[name="' + name + '"]').removeClass("is-invalid");
        $form.find('[data-field-error="' + name + '"]').text("");
    }

    function clearAll($form) {
        $form.find(".is-invalid").removeClass("is-invalid");
        $form.find("[data-field-error]").text("");
    }

    function showBackendErrors($form, errors) {
        clearAll($form);
        $.each(errors || {}, function (field, messages) {
            var message = Array.isArray(messages) ? messages[0] : messages;
            setError($form, field, message);
        });
    }

    // ─── Single field validation ──────────────────────────────────────────────

    /**
     * Validate a single field element against its data-rule-* attributes
     * Returns true if valid, false if invalid
     */
    function validateField($form, el) {
        if (!el || !el.name) return true;

        var $el = $(el);
        var name = el.name;
        var value = ($el.val() || "").trim();

        // required
        if ($el.is("[data-rule-required]") && value === "") {
            setError(
                $form,
                name,
                $el.attr("data-msg-required") || "This field is required.",
            );
            return false;
        }

        // skip remaining rules if empty and not required
        if (value === "") {
            clearError($form, name);
            return true;
        }

        // email
        if ($el.is("[data-rule-email]") && !EMAIL_REGEX.test(value)) {
            setError(
                $form,
                name,
                $el.attr("data-msg-email") || "Please enter a valid email.",
            );
            return false;
        }

        // min length
        var min = $el.attr("data-rule-min");
        if (min && value.length < parseInt(min, 10)) {
            setError(
                $form,
                name,
                $el.attr("data-msg-min") || "Minimum " + min + " characters.",
            );
            return false;
        }

        // max length
        var max = $el.attr("data-rule-max");
        if (max && value.length > parseInt(max, 10)) {
            setError(
                $form,
                name,
                $el.attr("data-msg-max") || "Maximum " + max + " characters.",
            );
            return false;
        }

        // same as another field
        var sameSelector = $el.attr("data-rule-same");
        if (sameSelector) {
            var $other = $(sameSelector);
            if ($other.length && $other.val() !== $el.val()) {
                setError(
                    $form,
                    name,
                    $el.attr("data-msg-same") || "Values do not match.",
                );
                return false;
            }
        }

        clearError($form, name);
        return true;
    }

    // ─── Full form validation ─────────────────────────────────────────────────

    /**
     * Validate all fields in the form that have at least one data-rule-* attr
     * Returns true if all valid
     */
    function validateForm($form) {
        clearAll($form);
        var valid = true;

        $form
            .find(
                "[data-rule-required], [data-rule-email], [data-rule-min], [data-rule-max], [data-rule-same]",
            )
            .each(function () {
                if (!validateField($form, this)) {
                    valid = false;
                }
            });

        return valid;
    }

    // ─── Live validation binding ──────────────────────────────────────────────

    /**
     * Attach live (input/change/blur) validation to form fields
     * Call once during plugin init
     */
    function bindLive($form) {
        $form.on(
            "input change blur",
            "[data-rule-required], [data-rule-email], [data-rule-min], [data-rule-max], [data-rule-same]",
            function () {
                validateField($form, this);
            },
        );
    }

    function unbindLive($form) {
        $form.off("input change blur");
    }

    return {
        validateField,
        validateForm,
        clearAll,
        clearError,
        setError,
        showBackendErrors,
        bindLive,
        unbindLive,
    };
})();
