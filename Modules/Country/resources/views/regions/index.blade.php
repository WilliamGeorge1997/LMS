@extends('common::layouts.master')
@section('title', config('app.name') . ' - Regions')

@section('css')
    <link href="{{ asset('dashboard/assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet"
        type="text/css" />
@endsection

@section('toolbar')
    <div id="create-toolbar-area">
        <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex flex-stack my-3">
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                    Regions
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('countries.index') }}" class="text-muted text-hover-primary">Locations</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-500 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">Regions</li>
                </ul>
            </div>
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <a href="#create-collapse" class="btn btn-sm fw-bold btn-primary create-open-btn" data-bs-toggle="collapse"
                    data-bs-target="#create-collapse" aria-expanded="false" aria-controls="create-collapse">
                    Create Region
                </a>
                <a href="#create-collapse" class="btn btn-sm fw-bold btn-danger create-close-btn d-none"
                    data-bs-toggle="collapse" data-bs-target="#create-collapse" aria-expanded="true"
                    aria-controls="create-collapse">
                    × Cancel
                </a>
            </div>
        </div>

        <div class="app-container container-fluid mb-7">
            <div id="create-collapse" class="collapse">
                @include('country::regions.partials.create')
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
                        placeholder="Search" />
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
                        <th class="min-w-125px">Title (EN)</th>
                        <th class="min-w-125px">Title (AR)</th>
                        <th class="min-w-125px">Country</th>
                        <th class="min-w-125px">City</th>
                        <th class="min-w-125px">{{ __('attributes.is_active') }}</th>
                        <th class="min-w-125px">{{ __('attributes.created_at') }}</th>
                        <th class="text-end min-w-100px">Actions</th>
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
    <script src="{{ asset('dashboard/assets/js/custom/plugins/core/ajax.js') }}"></script>
    <script src="{{ asset('dashboard/assets/js/custom/plugins/core/input-builder.js') }}"></script>
    <script src="{{ asset('dashboard/assets/js/custom/plugins/core/dependent-dropdown.js') }}"></script>
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
                url: "{{ route('regions.index') }}"
            },
            columns: [{
                    data: 'id'
                },
                {
                    data: 'id'
                },
                {
                    data: 'title_en'
                },
                {
                    data: 'title_ar'
                },
                {
                    data: 'country_id'
                },
                {
                    data: 'city_id'
                },
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
                    searchable: false,
                    render: function(data) {
                        return '<span class="fw-bold text-gray-800">' + (data ?? '') + '</span>';
                    }
                },
                {
                    targets: 3,
                    orderable: false,
                    searchable: false,
                    render: function(data) {
                        return '<span class="fw-bold text-gray-800">' + (data ?? '') + '</span>';
                    }
                },
                {
                    targets: 4,
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        return '<span class="badge badge-light-info fw-bold">' + (row.country_label ?? '') +
                            '</span>';
                    }
                },
                {
                    targets: 5,
                    orderable: true,
                    searchable: true,
                    render: function(data, type, row) {
                        return '<span class="badge badge-light-primary fw-bold">' + (row.city_label ?? '') +
                            '</span>';
                    }
                },
                {
                    targets: 6,
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
                    targets: 7,
                    orderable: true,
                    searchable: false,
                    render: function(data) {
                        return '<span class="text-gray-700 fw-semibold">' + (data ?? '') + '</span>';
                    }
                },
                {
                    targets: 8,
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
        Actions.initEdit(dt, "{{ route('regions.edit', ':id') }}");
        Actions.initDelete(dt, "{{ route('regions.destroy', ':id') }}");
        Actions.initToggle("{{ route('regions.toggle-activate', ':id') }}");

        PluginDependentDropdown.bind($('#create-form'));
        $(document).ajaxSuccess(function(event, xhr, settings) {
            if (settings.url && settings.url.indexOf('/edit') !== -1) {
                $('tr.edit-inline-row form').each(function() {
                    PluginDependentDropdown.bind($(this));
                });
            }
        });
    </script>
@endsection
