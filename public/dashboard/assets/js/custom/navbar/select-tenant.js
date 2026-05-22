// Select2 Handling

// Format options
const optionFormat = (item) => {
    if (!item.id) {
        return item.text;
    }

    var span = document.createElement('span');
    var template = '';

    template += '<div class="d-flex flex-column">'
    template += '<span class="fs-5 fw-bold lh-1">' + item.text + '</span>';
    template += '<span class="text-muted fs-6">' + item.element.getAttribute(
        'data-kt-rich-content-subcontent') + '</span>';
    template += '</div>';

    span.innerHTML = template;

    return $(span);
}

// Init Select2 --- more info: https://select2.org/
const select = $('#select-tenant');
select.select2({
    placeholder: "Select a tenant",
    allowClear: true,
    minimumResultsForSearch: 0,
    templateSelection: optionFormat,
    templateResult: optionFormat
});

select.on('change', function () {
    $(this).closest('form').submit();
});

