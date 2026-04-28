"use strict";

/**
 * DependentDropdown Helper
 * Single responsibility: handle dropdowns that reload options based on another dropdown
 *
 * Usage in HTML (on the child dropdown):
 *
 *   <select name="city_id"
 *       data-depends-on="country_id"
 *       data-depends-url="/ajax/cities?country_id=:value"
 *       data-depends-placeholder="Select city">
 *   </select>
 *
 *   data-depends-on          → name of the parent field
 *   data-depends-url         → URL to call; :value is replaced with the parent's value
 *   data-depends-placeholder → placeholder option text (optional)
 *
 * The AJAX response must return a JSON array:
 *   [ { "value": 1, "label": "Cairo" }, ... ]
 */
window.PluginDependentDropdown = (function () {
    /**
     * Bind all dependent dropdowns inside a form
     * @param {jQuery} $form
     */
    function bind($form) {
        $form.find("[data-depends-on]").each(function () {
            var $child = $(this);
            var parentName = $child.attr("data-depends-on");
            var $parent = $form.find('[name="' + parentName + '"]');

            if (!$parent.length) return;

            // When parent changes → reload child options
            $parent.on("change.depends", function () {
                reload($child, $parent.val());
            });

            // Reset child on init
            reset($child);
        });
    }

    /**
     * Unbind all dependent dropdown listeners inside a form
     * @param {jQuery} $form
     */
    function unbind($form) {
        $form.find("[data-depends-on]").each(function () {
            var parentName = $(this).attr("data-depends-on");
            $form.find('[name="' + parentName + '"]').off("change.depends");
        });
    }

    /**
     * Reset child dropdown to placeholder only
     * @param {jQuery} $child
     */
    function reset($child) {
        var placeholder =
            $child.attr("data-depends-placeholder") || "Select option";
        $child.html(
            '<option value="" disabled selected>' + placeholder + "</option>",
        );
        $child.prop("disabled", true);
    }

    /**
     * Reload child options based on parent value
     * @param {jQuery} $child
     * @param {string} parentValue
     */
    function reload($child, parentValue) {
        if (!parentValue) {
            reset($child);
            return;
        }

        var url = $child.attr("data-depends-url");
        if (!url) return;

        url = url.replace(":value", encodeURIComponent(parentValue));

        // Show loading state
        $child.html('<option value="" disabled selected>Loading...</option>');
        $child.prop("disabled", true);

        PluginAjax.loadOptions(url)
            .done(function (response) {
                var placeholder =
                    $child.attr("data-depends-placeholder") || "Select option";
                var html =
                    '<option value="" disabled selected>' +
                    placeholder +
                    "</option>";

                $.each(response || [], function (i, item) {
                    html +=
                        '<option value="' +
                        item.value +
                        '">' +
                        item.label +
                        "</option>";
                });

                $child.html(html);
                $child.prop("disabled", false);
            })
            .fail(function () {
                reset($child);
            });
    }

    return { bind, unbind, reset };
})();
