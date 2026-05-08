"use strict";

/**
 * PluginDependentDropdown
 * Single responsibility: manage select elements that reload options based on a parent select.
 *
 * Usage (HTML — on the child select):
 *   <select name="city_id"
 *       data-depends-on="country_id"
 *       data-depends-url="/ajax/cities?country_id=:value"
 *       data-depends-placeholder="Select city">
 *   </select>
 *
 * API response shape: [{ value: 1, label: "Cairo" }, ...]
 *
 * Dependencies: jQuery, PluginAjax, PluginInputBuilder
 */
window.PluginDependentDropdown = (function () {
    /**
     * Bind all dependent dropdowns inside a form.
     * Calls unbind() first to prevent duplicate listeners.
     * @param {jQuery} $form
     */
    function bind($form) {
        unbind($form);

        $form.find("[data-depends-on]").each(function () {
            var $child      = $(this);
            var parentName  = $child.attr("data-depends-on");
            var $parent     = $form.find('[name="' + parentName + '"]');

            if (!$parent.length) return;

            $parent.on("change.depends", function () {
                reload($child, $parent.val());
            });

            reset($child);
        });
    }

    /**
     * Remove all dependent dropdown listeners inside a form.
     * @param {jQuery} $form
     */
    function unbind($form) {
        var seen = {};
        $form.find("[data-depends-on]").each(function () {
            var parentName = $(this).attr("data-depends-on");
            if (seen[parentName]) return;
            seen[parentName] = true;
            $form.find('[name="' + parentName + '"]').off("change.depends");
        });
    }

    /**
     * Reset a child dropdown to its placeholder (disabled).
     * @param {jQuery} $child
     */
    function reset($child) {
        var placeholder = $child.attr("data-depends-placeholder") || "Select option";
        $child.html('<option value="" disabled selected>' + placeholder + "</option>").prop("disabled", true);
    }

    /**
     * Reload a child dropdown based on the parent's current value.
     * Used internally by bind() and publicly by EditPlugin.
     *
     * @param {jQuery} $child      - child select element
     * @param {string} url         - URL with :value placeholder
     * @param {string} parentVal   - current parent value
     * @param {*}      [currentVal] - value to pre-select after load (null = none, undefined = none)
     * @returns jQuery deferred
     */
    function reloadSelect($child, url, parentVal, currentVal) {
        if (!parentVal) {
            reset($child);
            return $.when();
        }

        var resolvedUrl = url.replace(":value", encodeURIComponent(parentVal));
        var placeholder = $child.attr("data-depends-placeholder") || "Select option";

        $child.html('<option value="" disabled selected>Loading...</option>').prop("disabled", true);

        return PluginAjax.loadOptions(resolvedUrl)
            .done(function (response) {
                var parts = ['<option value="" disabled selected>' + placeholder + "</option>"];
                $.each(response || [], function (i, item) {
                    var selected = currentVal != null && String(item.value) === String(currentVal) ? "selected" : "";
                    parts.push('<option value="' + PluginInputBuilder.escapeHtml(item.value) + '" ' + selected + ">" +
                        PluginInputBuilder.escapeHtml(item.label) + "</option>");
                });
                $child.html(parts.join("")).prop("disabled", false);
            })
            .fail(function () {
                reset($child);
            });
    }

    /**
     * Internal reload triggered by the parent change event (no pre-select needed).
     * @param {jQuery} $child
     * @param {string} parentVal
     */
    function reload($child, parentVal) {
        var url = $child.attr("data-depends-url");
        if (!url) return;
        reloadSelect($child, url, parentVal, null);
    }

    return { bind, unbind, reset, reloadSelect };
})();
