"use strict";

window.Actions = {

    // ── Create ────────────────────────────────────────────────────────────────

    initCreate: function (dataTable) {
        $('#create-toggle').on('click', function () {
            $('#create-collapse').collapse('toggle');
        });

        $('#create-cancel').on('click', function () {
            $('#create-collapse').collapse('hide');
            Actions._resetForm($('#create-form'));
        });

        $('#create-form').on('submit', function (e) {
            e.preventDefault();
            var $form = $(this);
            Actions._clearErrors($form);
            var $submit = $('#create-submit').attr('data-kt-indicator', 'on').prop('disabled', true);
            $.post($form.attr('action'), $form.serialize())
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

    initEdit: function (dataTable, editUrl, updateUrl) {
        var $table = $(dataTable.table().node());

        $(document).on('click', '.edit-btn', function () {
            var $btn = $(this);
            var $tr = $btn.closest('tr');
            var $openEdit = $table.find('tbody form.edit-inline-form').closest('tr');
            if ($openEdit.length && !$openEdit.is($tr)) {
                Actions._warning('Warning', 'Please save or cancel the current edit first.');
                return;
            }
            var url = editUrl.replace(':id', $btn.data('id'));
            $btn.attr('data-kt-indicator', 'on').prop('disabled', true);
            $.get(url)
                .done(function (html) { $tr.replaceWith(html); })
                .fail(function () {
                    Actions._fail();
                })
                .always(function () {
                    $btn.removeAttr('data-kt-indicator').prop('disabled', false);
                });
        });

        $(document).on('click', '.save-btn', function () {
            var $btn = $(this);
            var $tr = $btn.closest('tr').filter(function () {
                return $(this).find('form.edit-inline-form').length;
            });
            if (!$tr.length) {
                $tr = $btn.closest('tr');
            }
            var $scope = Actions._editScope($tr);
            var $form = $tr.find('form.edit-inline-form');
            if (!$form.length) {
                $form = $('#edit-form-' + $tr.data('id'));
            }
            Actions._clearErrors($scope);
            $btn.attr('data-kt-indicator', 'on').prop('disabled', true);
            $.post($form.attr('action'), Actions._serializeForm($form, $scope))
                .done(function () {
                    dataTable.ajax.reload(null, false);
                    Actions._success();
                })
                .fail(function (xhr) { Actions._handleFail(xhr, $scope); })
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
            Actions._showErrors($ctx, xhr.responseJSON.errors);
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

    _editScope: function ($tr) {
        var $extra = $tr.next('tr.edit-extra-row');
        return $extra.length ? $tr.add($extra) : $tr;
    },

    _serializeForm: function ($form, $scope) {
        if ($scope && $scope.length) {
            return $scope.find('[name]').serialize();
        }
        return $form.find('[name]').serialize();
    },
};