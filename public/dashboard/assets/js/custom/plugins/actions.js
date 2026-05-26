"use strict";

window.Actions = {

    // ── Create ────────────────────────────────────────────────────────────────

    initCreate: function (dataTable) {
        $('#create-cancel').on('click', function () {
            $('#create-collapse').collapse('hide');
            Actions._resetForm($('#create-form'));
        });

        $('#create-form').on('submit', function (e) {
            e.preventDefault();
            var $form = $(this);
            Actions._clearErrors($form);
            var $submit = $('#create-submit').attr('data-kt-indicator', 'on').prop('disabled', true);
            $.ajax({
                url: $form.attr('action'),
                method: 'POST',
                data: Actions._formData($form),
                processData: false,
                contentType: false
            })
                .done(function () {
                    $('#create-collapse').collapse('hide');
                    Actions._resetForm($('#create-form'));
                    dataTable.ajax.reload(null, false);
                    Actions._success();
                })
                .fail(function (xhr) {
                    Actions._handleFail(xhr, $('#create-form'));
                })
                .always(function () {
                    $submit.removeAttr('data-kt-indicator').prop('disabled', false);
                });
        });
    },

    // ── Edit ──────────────────────────────────────────────────────────────────

    initEdit: function (dataTable, editUrl) {
        var $table = $(dataTable.table().node());

        //Get Form
        $(document).on('click', '.edit-btn', function () {
            var $btn = $(this);
            var $tr = $btn.closest('tr');

            if ($table.find('tbody [data-extra-for]').length) {
                Actions._warning('Warning', 'Please save or cancel the current edit first.');
                return;
            }

            var url = editUrl.replace(':id', $btn.data('id'));
            $btn.attr('data-kt-indicator', 'on').prop('disabled', true);

            $.get(url)
                .done(function (html) { $tr.replaceWith(html); })
                .fail(function () { Actions._fail(); })
                .always(function () {
                    $btn.removeAttr('data-kt-indicator').prop('disabled', false);
                });
        });

        //Submit
        $(document).on('click', '.save-btn', function () {
            var $btn = $(this);
            var $tr = $btn.closest('tr');
            var id = $tr.data('edit-form-id');
            var $form = $('#edit-form-' + id);
            var $extra = $('[data-extra-edit-form-for="' + id + '"]');
            var $scope = $tr.add($extra);

            Actions._clearErrors($scope);
            $btn.attr('data-kt-indicator', 'on').prop('disabled', true);

            $.ajax({
                url: $form.attr('action'),
                method: 'POST',
                data: Actions._formData($scope),
                processData: false,
                contentType: false
            })
                .done(function () {
                    dataTable.ajax.reload(null, false);
                    Actions._success();
                })
                .fail(function (xhr) {
                    Actions._handleFail(xhr, $scope);
                })
                .always(function () {
                    $btn.removeAttr('data-kt-indicator').prop('disabled', false);
                });
        });

        $(document).on('click', '.cancel-btn', function () {
            dataTable.ajax.reload(null, false);
        });
    },

    // ── Toggle ────────────────────────────────────────────────────────────────

    initToggle: function (toggleUrl) {
        $(document).on('change', '.active-toggle', function () {
            var $checkbox = $(this);
            var checked = $checkbox.prop('checked');
            var url = toggleUrl.replace(':id', $checkbox.data('id'));
            $checkbox.prop('disabled', true);
            $.ajax({ url: url, method: 'PATCH' })
                .done(function () {
                    Actions._success();
                })
                .fail(function () {
                    $checkbox.prop('checked', !checked);
                    Actions._fail();
                })
                .always(function () {
                    $checkbox.prop('disabled', false);
                });
        });
    },

    // ── Delete ────────────────────────────────────────────────────────────────

    initDelete: function (dataTable, deleteUrl, confirmMsg) {
        $(document).on('click', '.delete-btn', function () {
            var $tr = $(this).closest('tr');
            var url = deleteUrl.replace(':id', $(this).data('id'));
            Swal.fire({
                title: confirmMsg || 'Are you sure?',
                text: confirmMsg || 'Are you sure you want to delete this item?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then(function (result) {
                if (result.isConfirmed) {
                    $.ajax({ url: url, method: 'DELETE' })
                        .done(function () {
                            dataTable.row($tr).remove().draw(false);
                            Actions._success();
                        })
                        .fail(function () {
                            Actions._fail();
                        });
                }
            });
        });
    },

    // ── Helpers ───────────────────────────────────────────────────────────────

    _success: function (title, text) {
        Swal.fire({
            icon: 'success',
            title: title || 'Done',
            text: text || 'Operation completed successfully.',
        });
    },

    _fail: function (title, text) {
        Swal.fire({
            icon: 'error',
            title: title || 'Error',
            text: text || 'Something went wrong.',
        });
    },

    _warning: function (title, text) {
        Swal.fire({
            icon: 'warning',
            title: title || 'Warning',
            text: text || '',
        });
    },

    _handleFail: function (xhr, $ctx) {
        if (xhr.status === 422) {
            var errors = xhr.responseJSON.errors || {};
            if (errors.tenant_id) {
                Actions._warning('Warning', errors.tenant_id[0]);
                return;
            }
            Actions._showErrors($ctx, errors);
        } else if (xhr.responseJSON && xhr.responseJSON.message) {
            Actions._fail('Error', xhr.responseJSON.message);
        } else {
            Actions._fail();
        }
    },

    _clearErrors: function ($ctx) {
        $ctx.find('[data-error]').text('');
        $ctx.find('.is-invalid').removeClass('is-invalid');
    },

    _showErrors: function ($ctx, errors) {
        Actions._clearErrors($ctx);
        $.each(errors, function (field, messages) {
            $ctx.find('[data-error="' + field + '"]').text(messages[0]);
            $ctx.find('[name="' + field + '"]').addClass('is-invalid');
        });
    },

    _resetForm: function ($form) {
        $form[0].reset();
        Actions._clearErrors($form);
    },

    _formData: function ($root) {
        var fd = new FormData();
        $root.find('[name]').each(function () {
            if (this.disabled || !this.name) return;
            if (this.type === 'file') {
                if (this.files.length) fd.append(this.name, this.files[0]);
                return;
            }
            if (this.type === 'checkbox' || this.type === 'radio') {
                if (this.checked) fd.append(this.name, this.value);
                return;
            }
            fd.append(this.name, this.value);
        });
        return fd;
    },
};