@extends('common::layouts.master')
@section('title', config('app.name') . ' - ' . __('category::attributes.categories_list'))

@section('css')
    @if (app()->getLocale() === 'ar')
        <link href="{{ asset('dashboard/assets/plugins/custom/datatables/datatables.bundle.rtl.css') }}" rel="stylesheet"
            type="text/css" />
    @else
        <link href="{{ asset('dashboard/assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet"
            type="text/css" />
    @endif
@endsection

@php($is_super_admin = auth('admin')->user()->hasRole(\Modules\Admin\Enums\Role::SUPER_ADMIN))

@section('toolbar')
    <div id="create-toolbar-area">
        <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex flex-stack my-3">
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                    {{ __('category::attributes.categories') }}
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="index.html"
                            class="text-muted text-hover-primary">{{ __('category::attributes.categories') }}</a>
                    </li>
                    <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
                    <li class="breadcrumb-item text-muted">{{ __('category::attributes.categories') }}</li>
                </ul>
            </div>

            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <a href="#create-collapse" class="btn btn-sm fw-bold btn-primary create-open-btn" data-bs-toggle="collapse"
                    data-bs-target="#create-collapse" aria-expanded="false" aria-controls="create-collapse">
                    {{ __('category::buttons.create') }}
                </a>
                <a href="#create-collapse" class="btn btn-sm fw-bold btn-danger create-close-btn d-none"
                    data-bs-toggle="collapse" data-bs-target="#create-collapse" aria-expanded="true"
                    aria-controls="create-collapse">
                    × {{ __('category::buttons.cancel') }}
                </a>
            </div>
        </div>

        <div class="app-container container-fluid mb-7">
            <div id="create-collapse" class="collapse">
                @include('category::categories.partials.create')
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="card">
        <div class="card-header border-0 pt-6">
            <div class="card-title">
                <div class="d-flex align-items-center position-relative my-1">
                    <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-5">
                        <span class="path1"></span><span class="path2"></span>
                    </i>
                    <input type="text" id="table-search" class="form-control form-control-solid w-250px ps-13"
                        placeholder="{{ __('category::placeholders.search_categories') }}" />
                </div>
            </div>
        </div>

        <div class="card-body py-4">
            <table id="kt_datatable" class="table align-middle table-row-dashed fs-6 gy-5">
                <thead>
                    <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                        <th></th>
                        <th class="w-10px pe-2">
                            <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                <input class="form-check-input" type="checkbox" data-kt-check="true"
                                    data-kt-check-target="#kt_datatable .row-checkbox" value="1" />
                            </div>
                        </th>
                        <th class="min-w-175px">{{ __('category::attributes.name') }}</th>
                        <th class="min-w-125px">{{ __('category::attributes.publisher_id') }}</th>
                        @if ($is_super_admin)
                            <th class="min-w-125px">{{ __('category::attributes.manager_id') }}</th>
                        @endif
                        <th class="min-w-125px">{{ __('category::attributes.is_active') }}</th>
                        <th class="min-w-125px">{{ __('category::attributes.created_at') }}</th>
                        <th class="text-end min-w-100px">{{ __('category::attributes.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 fw-semibold"></tbody>
            </table>
        </div>
    </div>
@endsection

@section('js')
    <script src="{{ asset('dashboard/assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
    <script src="{{ asset('dashboard/assets/js/custom/plugins/actions.js') }}"></script>
    <script>
        "use strict";

        var dt = $('#kt_datatable').DataTable({
            serverSide: true,
            processing: true,
            stateSave: false,
            pageLength: 10,
            lengthMenu: [10, 25, 50],
            order: [
                [0, 'desc']
            ],
            ajax: {
                url: "{{ route('categories.index') }}"
            },
            columns: [{
                    data: 'id'
                },
                {
                    data: 'id'
                },
                {
                    data: 'name_display'
                },
                {
                    data: 'publisher.name'
                },
                @if ($is_super_admin)
                    {
                        data: 'manager.name'
                    },
                @endif
                {
                    data: 'is_active'
                },
                {
                    data: 'created_at'
                },
                {
                    data: null
                },
            ],
            columnDefs: [{
                    targets: 0,
                    visible: false,
                    orderable: true,
                    searchable: false
                },
                {
                    targets: 1,
                    orderable: false,
                    searchable: false,
                    render: function(data) {
                        return `<div class="form-check form-check-sm form-check-custom form-check-solid">
                                <input class="form-check-input row-checkbox" type="checkbox" value="${data}" />
                            </div>`;
                    }
                },
                {
                    targets: 2,
                    orderable: false,
                    searchable: true,
                    render: function(data) {
                        return '<span class="text-gray-800 fw-bold fs-6">' + (data || '') + '</span>';
                    }
                },
                {
                    targets: 3,
                    orderable: false,
                    searchable: true,
                    render: function(data) {
                        return '<span class="fw-bold text-gray-800">' + (data || '') + '</span>';
                    }
                },
                @if ($is_super_admin)
                    {
                        targets: 4,
                        orderable: false,
                        searchable: true,
                        render: function(data) {
                            return '<span class="fw-bold text-gray-800">' + (data || '') + '</span>';
                        }
                    },
                @endif
                {
                    targets: -3,
                    orderable: false,
                    searchable: false,
                    render: function(data, t, row) {
                        return '<label class="form-check form-switch form-switch-sm form-check-custom form-check-solid">' +
                            '<input class="form-check-input active-toggle" type="checkbox" data-id="' + row
                            .id + '"' +
                            (data ? ' checked' : '') + ' />' +
                            '</label>';
                    }
                },
                {
                    targets: -2,
                    orderable: true,
                    searchable: false,
                    render: function(data) {
                        return '<span class="text-gray-700 fw-semibold">' + (data || '') + '</span>';
                    }
                },
                {
                    targets: -1,
                    orderable: false,
                    searchable: false,
                    className: 'text-end',
                    render: function(data, t, row) {
                        return '<button type="button" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1 edit-btn" data-id="' +
                            row.id + '">' +
                            '<span class="indicator-label"><i class="ki-duotone ki-pencil fs-5"><span class="path1"></span><span class="path2"></span></i></span>' +
                            '<span class="indicator-progress"><span class="spinner-border spinner-border-sm"></span></span>' +
                            '</button>' +
                            '<button type="button" class="btn btn-icon btn-bg-light btn-active-color-danger btn-sm delete-btn" data-id="' +
                            row.id + '">' +
                            '<i class="ki-duotone ki-trash fs-5"></i></button>';
                    }
                },
            ],
        });

        var searchTimer;
        $('#table-search').on('input', function() {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(function() {
                dt.search($('#table-search').val()).draw();
            }, 400);
        });

        Actions.initCreate(dt);
        Actions.initEdit(dt, "{{ route('categories.edit', ':id') }}");
        Actions.initDelete(dt, "{{ route('categories.destroy', ':id') }}");
        Actions.initToggle("{{ route('categories.toggle-activate', ':id') }}");

        @if ($is_super_admin)
            PluginDependentDropdown.bind($('#create-form'));
            $(document).ajaxSuccess(function(event, xhr, settings) {
                if (settings.url && settings.url.indexOf('/edit') !== -1) {
                    $('tr.edit-inline-row form').each(function() {
                        PluginDependentDropdown.bind($(this));
                    });
                }
            });
        @endif
    </script>
@endsection
